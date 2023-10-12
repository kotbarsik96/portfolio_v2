<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;

class Work extends FilterableModel
{
    use HasFactory;

    protected $fillable = [
        'title',
        'url',
        'pages_count',
        'tag_id',
        'image_desktop_id',
        'image_mobile_id',
    ];

    public static function scopeSort(Builder $query)
    {
        return $query->orderBy('pages_count', 'desc');
    }
}