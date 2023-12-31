<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Video;
use Illuminate\Http\UploadedFile;
use Pion\Laravel\ChunkUpload\Handler\HandlerFactory;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;
use Pion\Laravel\ChunkUpload\Exceptions\UploadMissingFileException;
use FFMpeg\FFProbe;
use Illuminate\Support\Facades\File;

class VideoController extends Controller
{
    private $storeFolderName = 'video';

    public function show()
    {
        return response(Video::all());
    }

    public function upload(Request $request)
    {
        $receiver = new FileReceiver('file', $request, HandlerFactory::classFromRequest($request));

        if ($receiver->isUploaded() === false) {
            throw new UploadMissingFileException();
        }

        $save = $receiver->receive();
        if ($save->isFinished()) {
            // если в request передан id, значит нужно не создать новую запись в БД, а обновить существующую под этим id
            $id = $request['videoId'];
            if (!is_numeric($id))
                $id = null;

            return $this->store($save->getFile(), $id, $request['subfolder']);
        }

        $handler = $save->handler();

        return response()->json([
            'done' => $handler->getPercentageDone()
        ]);
    }

    public function getFields($file, $requestSubfolder = null)
    {
        $filename = $this->getFilename($file);

        $foldersPath = $this->storeFolderName;
        if ($requestSubfolder)
            $foldersPath .= '/' . $requestSubfolder;

        $file->move(public_path($foldersPath), $filename);
        $ffprobe = FFProbe::create([
            'ffmpeg.binaries' => '/usr/bin/ffmpeg', // which ffmpeg
            'ffprobe.binaries' => '/usr/bin/ffprobe' // which ffprobe
        ]);
        $filepath = public_path($foldersPath) . '/' . $filename;
        $formatted = $ffprobe
            ->format($filepath);
        $videoDimensions = $ffprobe
            ->streams($filepath)
            ->videos()
            ->first()
            ->getDimensions();

        return [
            'original_name' => $file->getClientOriginalName(),
            'path' => $foldersPath . '/' . $filename,
            'size' => intval($formatted->get('size') / 1024),
            'width' => $videoDimensions->getWidth(),
            'height' => $videoDimensions->getHeight(),
            'duration' => intval($formatted->get('duration'))
        ];
    }

    public function store(UploadedFile $file, $id = null, $requestSubfolder = null)
    {
        $fields = $this->getFields($file, $requestSubfolder);

        // если нужно обновить существующую запись в БД
        if (is_numeric($id)) {
            $existingFile = Video::find($id);
            if ($existingFile) {
                // удалить старое видео из папки
                File::delete(public_path($existingFile->path));
                $existingFile->update($fields);
                return $existingFile;
            }
        }

        // иначе, добавить новую запись в БД
        return Video::create($fields);
    }

    public function getFilename(UploadedFile $file)
    {
        $extension = $file->getClientOriginalExtension();
        return md5(time()) . '.' . $extension;
    }

    public function destroy($id)
    {
        $video = Video::find($id);
        if (empty($video))
            return response(['not_found' => true]);

        $path = public_path($video->path);
        File::delete($path);

        $video->delete();
        return response(['deleted' => true]);
    }
}