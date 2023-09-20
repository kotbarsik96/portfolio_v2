<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Work;
use App\Models\Type;
use App\Models\Skill;
use App\Models\WorksSkills;
use App\Models\WorksTypes;
use App\Models\Tag;

class WorksController extends Controller
{
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