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

class WorksController extends Controller
{
    public function getWorkData($work)
    {
        function getData($array, $dataKey, $model)
        {
            foreach ($array as $key => $data) {
                $obj = $model::find($data->$dataKey);
                if (!$obj)
                    continue;

                if ($data->description)
                    $obj->description = $data->description;
                $array[$key] = $obj;
            }
            return $array;
        }

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
            $work->types = getData($work->types, 'type_id', Type::class);
        if (count($work->skills) > 0)
            $work->skills = getData($work->skills, 'skill_id', Skill::class);
        if (count($work->stack) > 0)
            $work->stack = getData($work->stack, 'stack_id', Stack::class);

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

    public function validateRequest(Request $request)
    {
        $tag = Tag::where('title', trim($request['tag']))->first();
        $tagId = $tag
            ? $tag->id : null;
        $request['tag_id'] = $tagId;

        $fields = $request->validate([
            'title' => 'required|min:3|string',
            'tag_id' => 'required|numeric',
            'image_desktop_id' => 'numeric|nullable',
            'image_mobile_id' => 'numeric|nullable'
        ]);
        $fields['title'] = trim($request['title']);
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

            $params['modelToCreate']::create($fields);
        }
        return true;
    }

    public function store(Request $request)
    {
        $fields = $this->validateRequest($request);

        $isTitleExists = Work::where('title', $fields['title'])->first();
        if ($isTitleExists)
            return response(['exists' => true]);

        $work = Work::create([
            'title' => $fields['title'],
            'tag_id' => $fields['tag_id'],
            'image_desktop_id' => $fields['image_desktop_id'],
            'image_mobile_id' => $fields['image_mobile_id']
        ]);

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

    public function update()
    {
    }

    public function destroy()
    {
    }
}