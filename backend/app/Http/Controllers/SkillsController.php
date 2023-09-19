<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Skill;
use App\Models\Video;
use App\Models\Image;

class SkillsController extends Controller
{
    public function all()
    {
        $skills = Skill::all();
        foreach ($skills as $skill) {
            if (is_numeric($skill->video_id))
                $skill->video = Video::find($skill->video_id);
            if (is_numeric($skill->image_id))
                $skill->image = Image::find($skill->image_id);
        }
        return $skills;
    }

    public function single($id)
    {
        $skill = Skill::find($id);
        if (!$skill)
            return response(['not_found' => true]);

        if (is_numeric($skill->video_id)) {
            $video = Video::find($skill->video_id);
            if ($video) {
                $skill->video = $video;
            } else {
                $skill->update(['video_id' => null]);
            }
        }
        if (is_numeric($skill->image_id)) {
            $image = Image::find($skill->image_id);
            if ($image) {
                $skill->image = $image;
            } else {
                $skill->update(['image_id' => null]);
            }
        }

        // сюда добавить $skill->links, в которую сложить данные из таблицы works_skills, предварительно получив только данные о конкретном навыке

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
        \Illuminate\Support\Facades\Log::info($id);
        $skill = Skill::find($id);
        if (!$skill)
            return response(['not_found' => true]);

        $skill->delete();
        return response(['deleted' => true]);
    }
}