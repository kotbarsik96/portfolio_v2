<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Image;
use App\Models\Video;
use Illuminate\Support\Facades\DB;

class ScheduleTask extends Model
{
    use HasFactory;

    public static function clearRemovedMedia()
    {
        $models = [
            Image::class,
            Video::class
        ];

        foreach ($models as $model) {
            $allMedia = $model::all();
            foreach ($allMedia as $mediaModel) {
                $path = public_path() . '/' . $mediaModel->path;
                if (file_exists($path))
                    continue;

                // файла не существует - удалить из бд
                $mediaModel->delete();
            }
        }
    }
    public static function clearOldMedia()
    {
        $models = [
            Image::class,
            Video::class
        ];

        foreach ($models as $model) {
            $period = 'NOW() - INTERVAL 7 DAY';
            $tables = $model::existsInTables();
            $oldMedia = $model::where('updated_at', '<', DB::raw($period))
                ->get();

            foreach ($oldMedia as $mediaModel) {
                // установить, есть ли хотя в колонке хотя бы какой либо таблицы это медиа
                $existsInAnyTable = false;
                foreach ($tables as $tableName => $columnNames) {
                    foreach ($columnNames as $colName) {
                        if (DB::table($tableName)->where($colName, $mediaModel->id)->first()) {
                            $existsInAnyTable = true;
                            break;
                        }
                    }

                    if ($existsInAnyTable)
                        break;
                }

                // существует в какой-либо из таблиц - оставить
                if ($existsInAnyTable)
                    continue;

                // отсутствует в таблицах - удалить
                $path = public_path() . '/' . $mediaModel->path;
                if (file_exists($path))
                    unlink($path);

                $mediaModel->delete();
            }
        }
    }
}