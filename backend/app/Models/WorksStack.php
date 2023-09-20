<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorksStack extends Model
{
    use HasFactory;

    protected $table = 'works_stack';
    public $timestamps = false;
    protected $fillable = [
        'work_id',
        'stack_id'
    ];
}