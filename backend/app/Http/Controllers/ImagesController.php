<?php

namespace App\Http\Controllers;

use App\Models\Image as ImageModel;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image as ImageIntervention;

class ImagesController extends Controller
{
    private $storeFolderName = 'images';

    public function validateRequest(Request $request)
    {
        return $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,svg,webp|max:2048'
        ]);
    }

    public function store(Request $request)
    {
        $this->validateRequest($request);
        $image = $request->file('image');
        $imgName = time() . '.' . $image->extension();
        $image->move(public_path($this->storeFolderName), $imgName);

        $imageData = ImageIntervention::make(public_path($this->storeFolderName) . '/' . $imgName);
        $imageHeight = $imageData->height();
        $imageWidth = $imageData->width();
        $path = $this->storeFolderName . '/' . $imgName;
        return ImageModel::create([
            'path' => $path,
            'size' => $imageData->filesize() / 1024, // кб
            'width' => $imageWidth,
            'height' => $imageHeight,
        ]);
    }

    public function update(Request $request, $id)
    {
        $fields = $this->validateRequest($request);

        $image = ImageModel::find($id);
        if (!$image)
            return response(['error' => true]);

        $image->update($this->createOrUpdateValues($fields));
        return $image;
    }

    public function destroy($id)
    {
        $image = ImageModel::find($id);
        if (!$image)
            return response(['not_found' => true]);

        $image->delete();
        return response(['deleted' => true]);
    }
}