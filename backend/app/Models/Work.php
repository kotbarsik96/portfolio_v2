<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Work extends FilterableModel
{
    use HasFactory;

    protected $fillable = [
        'title',
        'url',
        'tag_id',
        'image_desktop_id',
        'image_mobile_id'
    ];
}