<?php

namespace App\Http\Controllers;

use App\Models\Images;
use Illuminate\Http\Request;

class ImagesController extends Controller
{
    public function validateRequest(Request $request)
    {
        return $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,svg,webp|max:2048'
        ]);
    }

    public function store(Request $request)
    {
        return response(['success' => true]);

        // $this->validateRequest($request);
        // $image = $request->file('image');
        // $imgName = time() . '.' . $image->extension();
        // $image->move(public_path('images'), $imgName);

        // return Images::create([
        //     'path' => public_path('images/' . $imgName),
        //     'size' => $image->getMaxFilesize()
        // ]);
    }

    public function update(Request $request, $id)
    {
        $fields = $this->validateRequest($request);

        $image = Images::find($id);
        if (!$image)
            return response(['error' => true]);

        $image->update($this->createOrUpdateValues($fields));
        return $image;
    }

    public function destroy($id)
    {
        $image = Images::find($id);
        if (!$image)
            return response(['not_found' => true]);

        $image->delete();
        return response(['deleted' => true]);
    }
}