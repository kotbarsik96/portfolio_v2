<?php

namespace App\Http\Controllers;

use App\Models\Image as ImageModel;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image as ImageIntervention;
use Illuminate\Support\Facades\File;

class ImagesController extends Controller
{
    private $storeFolderName = 'images';

    public function validateRequest(Request $request)
    {
        return $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,svg,webp|max:2048'
        ]);
    }

    public function uploadToFolder(Request $request)
    {
        $this->validateRequest($request);
        $image = $request->file('image');
        $imgName = time() . '.' . $image->extension();
        $image->move(public_path($this->storeFolderName), $imgName);

        $imageData = ImageIntervention::make(public_path($this->storeFolderName) . '/' . $imgName);
        $imageHeight = $imageData->height();
        $imageWidth = $imageData->width();
        $path = $this->storeFolderName . '/' . $imgName;

        return [
            'path' => $path,
            'original_name' => $image->getClientOriginalName(),
            'size' => $imageData->filesize() / 1024,
            'width' => $imageWidth,
            'height' => $imageHeight,
        ];
    }

    public function store(Request $request)
    {
        $fields = $this->uploadToFolder($request);
        return ImageModel::create($fields);
    }

    public function update(Request $request, $id)
    {
        $image = ImageModel::find($id);
        if (!$image)
            return response(['error' => true]);

        $path = public_path($image->path);
        File::delete($path);

        $fields = $this->uploadToFolder($request);

        $image->update($fields);
        return response($image);
    }

    public function destroy($id)
    {
        $image = ImageModel::find($id);
        if (!$image)
            return response(['not_found' => true]);

        $path = public_path($image->path);
        File::delete($path);

        $image->delete();
        return response(['deleted' => true]);
    }
}