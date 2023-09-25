<?php

namespace App\Filters;

use Illuminate\Database\Query\Builder;
use DB;

class SkillFilter extends QueryFilter
{
    public function search($query)
    {
        $this->builder->where('title', 'LIKE', '%'.$query.'%');
    }
}