<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;

    protected $fillable = [
        'original_name',
        'path',
        'size',
        'width',
        'height'
    ];

    public static function existsInTables()
    {
        return [
            'skills' => [
                'image_id'
            ],
            'works' => [
                'image_mobile_id',
                'image_desktop_id'
            ]
        ];
    }
}
