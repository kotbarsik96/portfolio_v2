<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\SkillsController;
use App\Http\Controllers\TaxonomiesController;

class DataController extends Controller
{
    public function all()
    {
        $loadable = [
            'stack' => function () {
                $controller = new TaxonomiesController();
                return $controller->all('stack');
            },
            'tags' => function () {
                $controller = new TaxonomiesController();
                return $controller->all('tags');
            },
            'types' => function () {
                $controller = new TaxonomiesController();
                return $controller->all('types');
            },
            'skills' => function () {
                $controller = new SkillsController();
                return $controller->all();
            },
        ];

        foreach ($loadable as $key => $callback) {
            $loadable[$key] = $callback();
        }

        return response($loadable);
    }
}