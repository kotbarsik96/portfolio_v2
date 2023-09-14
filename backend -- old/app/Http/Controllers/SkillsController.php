<?php

namespace App\Http\Controllers;

use App\Models\Skills;
use Illuminate\Http\Request;

class SkillsController extends Controller
{
    public function validateRequest(Request $request)
    {
        return $request->validate([
            'title' => 'required|min:2|string|unique:App\Models\Skills,title',
            'image_id' => 'numeric',
            'video_id' => 'numeric',
        ]);
    }

    public function createOrUpdateValues($fields)
    {
        return [
            'title' => e($fields['title']),
            'image_id' => is_numeric($fields['image_id']) ? intval($fields['image_id']) : null,
            'video_id' => is_numeric($fields['video_id']) ? intval($fields['video_id']) : null
        ];
    }

    public function store(Request $request)
    {
        $fields = $this->validateRequest($request);

        return Skills::create($this->createOrUpdateValue($fields));
    }

    public function update(Request $request, $id)
    {
        $fields = $this->validateRequest($request);

        $skill = Skills::find($id);
        if (!$skill)
            return response(['error' => true]);

        $skill->update($this->createOrUpdateValues($fields));
        return $skill;
    }

    public function destroy($id)
    {
        $skill = Skills::find($id);
        if (!$skill)
            return response(['not_found' => true]);

        $skill->delete();
        return response(['deleted' => true]);
    }
}