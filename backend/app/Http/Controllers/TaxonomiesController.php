<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Type;
use App\Models\Stack;
use App\Models\Tag;

class TaxonomiesController extends Controller
{
    public function all($taxonomyTitle)
    {
        $model = $this->getTaxModel($taxonomyTitle);
        if (!$model)
            return response(['error' => true]);

        return $model::all();
    }

    public function validateRequest(Request $request)
    {
        return $request->validate([
            'title' => 'string|min:2'
        ]);
    }

    public function getTaxModel($taxonomyTitle)
    {
        switch ($taxonomyTitle) {
            case 'type':
            case 'types':
                return Type::class;
            case 'tag':
            case 'tags':
                return Tag::class;
            case 'stack':
                return Stack::class;
        }

        return null;
    }

    public function store(Request $request, $taxonomyTitle)
    {
        $fields = $this->validateRequest($request);

        $model = $this->getTaxModel($taxonomyTitle);
        if (!$model)
            return response(['error' => true]);

        $existingTaxonomy = $model::where('title', $request['title'])->first();
        if ($existingTaxonomy)
            return response([
                'error' => true,
                'exists' => true
            ]);

        return $model::create($fields);
    }

    public function update(Request $request, $taxonomyTitle, $id)
    {
        $fields = $this->validateRequest($request);

        $model = $this->getTaxModel($taxonomyTitle);
        if (!$model)
            return response(['error' => true]);

        $taxonomy = $model::find($id);

        if ($taxonomy) {
            $taxonomy->update($fields);
            return $taxonomy;
        }

        return $taxonomy::create($fields);
    }

    public function updateSeveral(Request $request, $taxonomyTitle)
    {
        $array = $request['taxonomies'];
        if (!is_array($array))
            return response(['error' => true]);

        $model = $this->getTaxModel($taxonomyTitle);
        if (!$model)
            return response(['error' => true]);

        foreach ($array as $key => $obj) {
            $requestInst = new Request();
            $requestInst->replace($obj);
            $array[$key] = $this->update($requestInst, $taxonomyTitle, $obj['id']);
        }

        return $array;
    }

    public function destroy($taxonomyTitle, $id)
    {
        $model = $this->getTaxModel($taxonomyTitle);
        if (!$model)
            return response(['error' => true]);

        $taxonomy = $model::find($id);

        $taxonomy = $model::find($id);
        if (!$taxonomy)
            return response(['not_found' => true]);

        $taxonomy->delete();
        return response(['deleted' => true]);
    }
}