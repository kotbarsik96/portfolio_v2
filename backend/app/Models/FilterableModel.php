<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Filters\QueryFilter;

class FilterableModel extends Model
{
    public function scopeFilter(Builder $builder, QueryFilter $request)
    {
        return $request->apply($builder);
    }

    public function scopeGetOffset(Builder $query, $limit, $offset)
    {
        if (!$limit && !$offset)
            return $query->get();

        if (!$limit && $offset)
            return $query->offset($offset)->get();

        if ($limit && !$offset)
            return $query->limit($limit)->get();

        return $query->offset($offset)->limit($limit)->get();
    }
}