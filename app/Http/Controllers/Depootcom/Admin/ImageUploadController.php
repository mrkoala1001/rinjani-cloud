<?php

namespace App\Http\Controllers\Depootcom\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ImageUploadController extends Controller
{
    public function upload(Request $request)
    {
        try {
            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $name = time() . '_' . $image->getClientOriginalName();
                $path = $image->storeAs('blog_images', $name, 'public');

                return response()->json([
                    'url' => asset('storage/' . $path)
                ]);
            }

            return response()->json(['error' => 'No image uploaded'], 400);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => $e->errors()['image'][0] ?? 'Validation error'], 422);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Image upload failed: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal simpan file: ' . $e->getMessage()], 500);
        }
    }
}
