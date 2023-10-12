<?php

namespace App\Http\Controllers;

use App\Models\WorksSkills;
use Illuminate\Http\Request;
use App\Models\Skill;
use App\Models\Video;
use App\Models\Image;
use App\Models\Work;
use App\Filters\SkillFilter;

class SkillsController extends Controller
{
    public function getSkillData($skill)
    {
        if (is_numeric($skill->video_id))
            $skill->video = Video::find($skill->video_id);
        if (is_numeric($skill->image_id))
            $skill->image = Image::find($skill->image_id);

        $skill->links = $this->getWorksLinks($skill);
        return $skill;
    }

    public function getWorksLinks($skill)
    {
        $worksSkills = WorksSkills::where('skill_id', $skill->id)->get();
        $links = [];
        foreach ($worksSkills as $obj) {
            $work = Work::find($obj->work_id);
            if (!$work)
                continue;

            // если не указывать рядом с описанием ссылку на навык, будет взята ссылка на работу, связанную с этим навыком
            $link = $obj->url ?? $work->url;

            $links[] = [
                'title' => $work->title,
                'link' => $link,
                'description' => $obj->description
            ];
        }

        return $links;
    }

    public function all()
    {
        $skills = Skill::all();
        foreach ($skills as $key => $skill) {
            $skills[$key] = $this->getSkillData($skill);
        }
        return $skills;
    }

    public function allFiltered(SkillFilter $request)
    {
        $queries = $request->queries();
        $limit = array_key_exists('limit', $queries) ? $queries['limit'] : 0;
        $offset = array_key_exists('offset', $queries) ? $queries['offset'] : 0;

        $skills = Skill::filter($request)->offsetLimit($limit, $offset)->get();
        foreach ($skills as $key => $skill) {
            $skills[$key] = $this->getSkillData($skill);
        }
        return $skills;
    }

    public function count()
    {
        return Skill::count();
    }

    public function single($id)
    {
        $skill = Skill::find($id);
        if (!$skill)
            return response(['not_found' => true]);

        $skill = $this->getSkillData($skill);

        return response($skill);
    }

    public function validateFields(Request $request)
    {
        return $request->validate([
            'title' => 'required|min:3|string',
            'image_id' => 'nullable|numeric',
            'video_id' => 'nullable|numeric'
        ]);
    }

    public function store(Request $request)
    {
        $fields = $this->validateFields($request);

        return Skill::create($fields);
    }

    public function update(Request $request, $id)
    {
        $fields = $this->validateFields($request);

        $skill = Skill::find($id);

        if ($skill) {
            $skill->update($fields);
            return $skill;
        }

        return Skill::create($fields);
    }

    public function destroy($id)
    {
        $skill = Skill::find($id);
        if (!$skill)
            return response(['error' => true]);

        $skill->delete();
    }
}