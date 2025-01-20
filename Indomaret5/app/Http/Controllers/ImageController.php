<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Image;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    public function uploadImage(Request $request)
    {
        $request->validate([
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'description' => 'nullable|string',
        ]);

        $uploadedImages = [];

        foreach ($request->file('images') as $image) {
            $imagePath = $image->store('images', 'public');

            $uploadedImage = Image::create([
                'path' => $imagePath,
                'description' => $request->input('description'),
            ]);
            $uploadedImages[] = $uploadedImage;
        }

        return response()->json(['message' => 'Images uploaded successfully', 'images' => $uploadedImages], 201);
    }

    public function getImage(Request $request)
    {
        $request->validate([
            'path' => 'required|string',
        ]);

        $filePath = $request->input('path');

        if (Storage::disk('public')->exists($filePath)) {
            return response()->json(['message' => 'File exists']);
        } else {
            return response()->json(['message' => 'File not found'], 404);
        }
    }

    public function getImageByPath($path)
    {
        $image = Image::where('path', "images/".$path)->first();

        if (!$image) {
            return response()->json(['message' => 'Image not found'], 404);
        }

        $imageUrl = Storage::url($image->path);

        return response()->json(['message' => 'Image is available in database'], 201);
    }
}
