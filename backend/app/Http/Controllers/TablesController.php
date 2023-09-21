<?php

namespace App\Http\Controllers;

use App\Models\WorksSkills;
use App\Models\WorksStack;
use App\Models\WorksTypes;
use Illuminate\Http\Request;
use App\Models\Type;
use App\Models\Tag;
use App\Models\Stack;
use App\Models\Skill;

class TablesController extends Controller
{
    public static $tables = [
        'types' => [
            'eloquent' => Type::class
        ],
        'tag' => [
            'eloquent' => Tag::class
        ],
        'stack' => [
            'eloquent' => Stack::class
        ],
        'skills' => [
            'eloquent' => Skill::class
        ],
        'works_types' => [
            'eloquent' => WorksTypes::class
        ],
        'works_skills' => [
            'eloquent' => WorksSkills::class
        ],
        'works_stack' => [
            'eloquent' => WorksStack::class
        ],
    ];

    public static function getTableEloquent($tableName)
    {
        if (!array_key_exists($tableName, self::$tables))
            return null;

        $data = self::$tables[$tableName];
        if (!is_array($data))
            return null;

        return $data['eloquent'];
    }
}