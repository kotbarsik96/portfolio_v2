<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Video;
use Illuminate\Support\Facades\Storage;

class VideoController extends Controller
{
    private $storeFolderName = 'video';

    public function validateRequest(Request $request)
    {
        return $request->validate([
            'image' => 'required|video|mimes:mp4|max:2048'
        ]);
    }

    public function store(Request $request)
    {
        \Illuminate\Support\Facades\Log::debug($request);

        // $this->validateRequest($request);
        // $video = $request->file('video');
        // $imgName = time() . '.' . $video->extension();
        // $video->move(public_path($this->storeFolderName), $imgName);

        // $path = $this->storeFolderName . '/' . $imgName;
        // return Video::create([
        //     'path' => $path,
        //     'size' => $video->getMaxFilesize()
        // ]);
    }
}