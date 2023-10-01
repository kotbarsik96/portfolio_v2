<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorksSkills extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $fillable = [
        'work_id',
        'skill_id',
        'description',
        'url'
    ];
}
