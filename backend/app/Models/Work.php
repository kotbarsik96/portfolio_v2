<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Filters\QueryFilter;

class Work extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'url',
        'tag_id',
        'image_desktop_id',
        'image_mobile_id'
    ];

    public function scopeFilter(Builder $builder, QueryFilter $request)
    {
        return $request->apply($builder);
    }
}
