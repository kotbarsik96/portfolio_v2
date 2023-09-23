<?php

namespace App\Http\Controllers;

use App\Models\WorksStack;
use Illuminate\Http\Request;
use App\Models\Work;
use App\Models\Type;
use App\Models\Skill;
use App\Models\WorksSkills;
use App\Models\WorksTypes;
use App\Models\Tag;
use App\Models\Stack;
use App\Models\Image;
use App\Http\Controllers\TablesController;

class WorksController extends Controller
{
    public function getWorkDataByModel($array, $dataKey, $model)
    {
        foreach ($array as $key => $data) {
            $obj = $model::find($data->$dataKey);
            if (!$obj)
                continue;

            if ($data->description)
                $obj->description = $data->description;
            if ($data->url)
                $obj->url = $data->url;

            $array[$key] = $obj;
        }
        return $array;
    }

    public function getWorkData($work)
    {

        if (is_numeric($work->tag_id))
            $work->tag = $this->getColumnById($work->tag_id, Tag::class);
        if (is_numeric($work->image_desktop_id))
            $work->image_desktop = Image::find($work->image_desktop_id);
        if (is_numeric($work->image_mobile_id))
            $work->image_mobile = Image::find($work->image_mobile_id);

        $work->types = WorksTypes::where('work_id', $work->id)->get();
        $work->skills = WorksSkills::where('work_id', $work->id)->get();
        $work->stack = WorksStack::where('work_id', $work->id)->get();

        if (count($work->types) > 0)
            $work->types = $this->getWorkDataByModel($work->types, 'type_id', Type::class);
        if (count($work->skills) > 0)
            $work->skills = $this->getWorkDataByModel($work->skills, 'skill_id', Skill::class);
        if (count($work->stack) > 0)
            $work->stack = $this->getWorkDataByModel($work->stack, 'stack_id', Stack::class);

        return $work;
    }

    public function getColumnById($id, $model, $column = 'title')
    {
        if (!$model)
            return false;

        $obj = $model::find($id);
        if (!$obj)
            return false;

        return $obj->$column;
    }

    public function all()
    {
        $works = Work::all();
        foreach ($works as $key => $work) {
            $works[$key] = $this->getWorkData($work);
        }
        return $works;
    }

    public function single($id)
    {
        $work = Work::find($id);
        if (!$work)
            return ['not_found' => true];

        return $this->getWorkData($work);
    }

    /* validateRequest вернет только то, что относится к таблице "works"; данных для остальных таблиц в возвращенном массиве не будет 
     */
    public function validateRequest(Request $request)
    {
        $tag = Tag::where('title', trim($request['tag']))->first();
        $tagId = $tag
            ? $tag->id : null;
        $request['tag_id'] = $tagId;

        $fields = $request->validate([
            'title' => 'required|min:3|string',
            'url' => 'active_url',
            'tag_id' => 'required|numeric',
            'image_desktop_id' => 'numeric|nullable',
            'image_mobile_id' => 'numeric|nullable'
        ]);
        $fields = [
            'title' => trim($request['title']),
            'url' => trim($request['url']),
            'tag_id' => $request['tag_id'],
            'image_desktop_id' => $request['image_desktop_id'],
            'image_mobile_id' => $request['image_mobile_id'],
        ];
        return $fields;
    }

    /* Делает связь "многие-ко-многим", создавая записи для таблиц, вроде works_skills/works_types.
    В $params принимает: 
        workId, записывая в works_skills.work_id;
        addingColumnName, например, skill_id, записывая works_skills.[addingColumnName];
        modelToFind - модель, например, Skill, где нужно найти айдишник записи по передаваемому внутри массива $array значению;
        modelToCreate - модель, например WorksSkills, по которой создать запись в нужной таблице
     */
    public function storeCheckedValues($array, $params = [])
    {
        if (
            !array_key_exists('workId', $params)
            || !array_key_exists('addingColumnName', $params)
            || !array_key_exists('modelToFind', $params)
            || !array_key_exists('modelToCreate', $params)
        )
            return false;

        if (!$params['modelToCreate'])
            return false;

        $array = array_map(function ($dataObj) use ($params) {
            if (!$dataObj)
                return null;

            $title = $dataObj['value'];
            $fromDbObj = $params['modelToFind']::where('title', $title)->first();
            if (!$fromDbObj)
                return null;

            $dataObj['id'] = $fromDbObj->id;
            return $dataObj;
        }, $array);
        $array = array_filter($array, fn($data) => !empty($data));

        foreach ($array as $obj) {
            $fields = [
                'work_id' => $params['workId'],
                $params['addingColumnName'] => $obj['id']
            ];

            if (array_key_exists('description', $obj))
                $fields['description'] = $obj['description'];

            if (array_key_exists('url', $obj))
                $fields['url'] = $obj['url'];

            $existing = $params['modelToCreate']::where('work_id', $params['workId'])
                ->where($params['addingColumnName'], $obj['id'])
                ->first();
            // если запись существует, обновить
            if ($existing)
                $existing->update($fields);
            // иначе создать новую
            else
                $params['modelToCreate']::create($fields);
        }
        return true;
    }

    /* Удаляет связи "многие-ко-многим"; делает обратное storeCheckedValues. В $exceptions можно передать id или title тех значений из таблиц, которые не нужно удалять в формате:
        $exceptions = ['types' => [3, 15, 'Верстка по макету']] - здесь из таблицы works_types не будут удалены записи, где type_id равен числу из works_types, либо 
    */
    public function removeCheckedValues($work, $exceptions = [])
    {
        $deletable = [
            ['binderTable' => 'works_types', 'table' => 'types', 'id_column_name' => 'type_id'],
            ['binderTable' => 'works_skills', 'table' => 'skills', 'id_column_name' => 'skill_id'],
            ['binderTable' => 'works_stack', 'table' => 'stack', 'id_column_name' => 'stack_id'],
        ];

        // превратить все title, переданные в exceptions, в id-шники
        foreach ($exceptions as $tableName => $array) {
            $eloquent = TablesController::getTableEloquent($tableName);
            if (!$eloquent)
                continue;

            foreach ($array as $key => $titleOrId) {
                if (is_numeric($titleOrId))
                    continue;

                $data = $eloquent::where('title', $titleOrId)->first();
                if (!$data)
                    continue;

                $array[$key] = $data->id;
            }
            $exceptions[$tableName] = $array;
        }

        foreach ($deletable as $data) {
            $binderModel = TablesController::getTableEloquent($data['binderTable']);

            // коллекция строк из works_<taxonomy>
            $collection = $binderModel::where('work_id', $work->id)->get();
            // исключения для текущей таблицы
            $currentExceptions = array_key_exists($data['table'], $exceptions)
                ? $exceptions[$data['table']]
                : [];

            foreach ($collection as $obj) {
                $idColumnName = $data['id_column_name'];
                $checkedValueId = $obj->$idColumnName;

                // если есть в исключениях, не удалять
                if (is_numeric(array_search($checkedValueId, $currentExceptions)))
                    continue;

                $obj->delete();
            }
        }
    }

    /* метод может: 
        1. создать новую запись work: в таком случае НЕ передается $id
        2. обновить существующую запись work: в таком случае передается $id (вызывается в update)
    */
    public function store(Request $request, $work = null)
    {
        $fields = $this->validateRequest($request);

        // если передан $work, обновить запись work
        if ($work) {
            $work->update($fields);
        }
        // добавить новую запись work, предварительно проверив, что такой работы нет
        else {
            $isTitleExists = Work::where('title', $fields['title'])->first();
            if ($isTitleExists)
                return response(['exists' => true]);

            $work = Work::create($fields);
        }

        // добавляет таксономии, прикрепленные скрины и навыки
        $checkedValues = $request['checkedValues'];
        if (is_array($checkedValues)) {
            foreach ($checkedValues as $obj) {
                if (!array_key_exists('name', $obj))
                    continue;
                if (!array_key_exists('values', $obj))
                    continue;
                if (!is_array($obj['values']))
                    continue;

                switch ($obj['name']) {
                    case 'types':
                        $this->storeCheckedValues($obj['values'], [
                            'workId' => $work->id,
                            'addingColumnName' => 'type_id',
                            'modelToFind' => Type::class,
                            'modelToCreate' => WorksTypes::class,
                        ]);
                        break;
                    case 'skills':
                        $this->storeCheckedValues($obj['values'], [
                            'workId' => $work->id,
                            'addingColumnName' => 'skill_id',
                            'modelToFind' => Skill::class,
                            'modelToCreate' => WorksSkills::class,
                        ]);
                        break;
                    case 'stack':
                        $this->storeCheckedValues($obj['values'], [
                            'workId' => $work->id,
                            'addingColumnName' => 'stack_id',
                            'modelToFind' => Stack::class,
                            'modelToCreate' => WorksStack::class,
                        ]);
                        break;
                }
            }
        }

        return $work;
    }

    public function update(Request $request, $id)
    {
        $work = Work::find($id);
        if (!$work)
            return response(['error' => true]);

        // удалить лишние "многие-ко-многим" связи
        $exceptions = [];
        foreach ($request['checkedValues'] as $assoc) {
            $exceptions[$assoc['name']] = array_map(fn($valueAssoc) => $valueAssoc['value'], $assoc['values']);
        }
        $this->removeCheckedValues($work, $exceptions);

        $updatedWork = $this->store($request, $work);
        if (is_array($updatedWork))
            return $this->single($id);

        return $this->single($updatedWork->id);
    }

    public function destroy($id)
    {
        $work = Work::find($id);
        if (!$work)
            return ['error' => true];

        $this->removeCheckedValues($work);
        $work->delete();
    }
}