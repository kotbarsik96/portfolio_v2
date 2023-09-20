<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorksTypes extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $fillable = [
        'work_id',
        'type_id'
    ];
}
