<?php

namespace App\Filters;

class SkillFilter extends QueryFilter
{
    public function search($query = '')
    {
        $this->builder->where('title', 'LIKE', '%'.$query.'%');
    }
}