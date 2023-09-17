<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Video;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Pion\Laravel\ChunkUpload\Handler\HandlerFactory;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;
use Pion\Laravel\ChunkUpload\Exceptions\UploadMissingFileException;
use Owenoj\LaravelGetId3\GetId3;

class VideoController extends Controller
{
    private $storeFolderName = 'video';

    public function upload(Request $request)
    {
        \Illuminate\Support\Facades\Log::info($request->allFiles());
        $receiver = new FileReceiver('file', $request, HandlerFactory::classFromRequest($request));

        if ($receiver->isUploaded() === false) {
            throw new UploadMissingFileException();
        }

        $save = $receiver->receive();
        if ($save->isFinished())
            return $this->store($save->getFile());

        $handler = $save->handler();

        return response()->json([
            'done' => $handler->getPercentageDone()
        ]);
    }

    public function store(UploadedFile $file)
    {
        $filename = $this->getFilename($file);

        $file->move(public_path($this->storeFolderName), $filename);

        // return Video::create([
        //     'path' => public_path($this->storeFolderName . '/' . $filename),
        //     'size' => $file->getSize() / 1024,
        //     'width' => $file->width(),
        //     'height' => $file->height(),
        //     'duration' => $file->getPlayTime()
        // ]);

        return response()->json([
            'path' => public_path($this->storeFolderName . '/' . $filename),
            // 'size' => $file->getSize() / 1024,
            // 'width' => $file->width(),
            // 'height' => $file->height(),
            // 'duration' => $video->getPlayTime()
        ]);
    }

    public function getFilename(UploadedFile $file){
        $extension = $file->getClientOriginalExtension();
        $filename = str_replace('.' . $extension, '', $file->getClientOriginalName());

        $filename .= '_' . md5(time()) . '.' . $extension;

        return $filename;
    }
}