<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Works extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'tag',
        'type',
        'image_desktop_id',
        'image_mobile_id'
    ];
}