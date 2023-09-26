<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use App\Filters\QueryFilter;

class Skill extends FilterableModel
{
    use HasFactory;

    protected $fillable = [
        'title',
        'image_id',
        'video_id'
    ];
}