<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

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
        return $query
            ->select('*', DB::raw('pages_count + skills_count AS pages_skills_count'))
            ->from(function ($query) {
                $query->select('works.*', DB::raw('COUNT(*) AS skills_count'))
                    ->from('works')
                    ->leftJoin('works_skills', 'works_skills.work_id', '=', 'works.id')
                    ->groupBy('works.id');
            })
            ->orderBy('pages_skills_count', 'desc');
    }
}