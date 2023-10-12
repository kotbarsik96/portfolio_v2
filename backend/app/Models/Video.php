<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;
    
    protected $table = 'video';

    protected $fillable = [
        'original_name',
        'path',
        'size',
        'duration',
        'width',
        'height'
    ];

    public static function existsInTables()
    {
        return [
            'skills' => [
                'video_id'
            ]
        ];
    }
}
