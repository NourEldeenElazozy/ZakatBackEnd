<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    public function index()
{
    $images = Image::all();
    return view('upload_image', compact('images'));
}
    // رفع صورة مع وصف
    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // شروط الصورة
            'description' => 'required|string|max:255',
        ]);

        // حفظ الصورة في مجلد 'images' داخل storage
        $imagePath = $request->file('image')->store('images', 'public');

        // حفظ البيانات في قاعدة البيانات
        $image = Image::create([
            'image_path' => $imagePath,
            'description' => $request->description,
        ]);

        return redirect()->back()->with('success', 'تم رفع الصورة بنجاح!');
    }

    public function delete($id)
    {
        $image = Image::findOrFail($id);

        // حذف الصورة من storage
        if (\Storage::disk('public')->exists($image->image_path)) {
            \Storage::disk('public')->delete($image->image_path);
        }

        // حذف السجل من قاعدة البيانات
        $image->delete();

        return redirect()->back()->with('success', 'تم حذف الصورة بنجاح!');
    }

    // عرض جميع الصور مع وصفها
    public function fetchAllImages()
    {
        // جلب جميع الصور من قاعدة البيانات
        $images = Image::all();

        // تضمين مسار URL للصورة
        $images->map(function ($image) {
            $image->image_url = asset('storage/' . $image->image_path);
            return $image;
        });

        // إرجاع البيانات بتنسيق JSON
        return response()->json([
            'status' => 'success',
            'data' => $images
        ], 200);
    }
}
