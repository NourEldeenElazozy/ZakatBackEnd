<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Achievement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AchievementController extends Controller
{
    /**
     * GET /api/achievements/all
     * تستخدمه شاشة "إنجازات صندوق الزكاة" في تطبيق الجوال.
     * يرجع فقط العناصر المُفعّلة، مرتبة حسب sort_order.
     */
    public function index()
    {
        $achievements = Achievement::where('is_active', true)
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->get(['id', 'title', 'description', 'image_path', 'sort_order']);

        return response()->json([
            'data' => $achievements,
        ]);
    }

    /**
     * POST /api/achievements
     * لإضافة إنجاز جديد من لوحة التحكم. أضف Middleware المصادقة المناسب
     * على المسار (نفس الحارس الذي تحمون به مسارات إدارة الحملات/الصور).
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title'       => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image'       => 'required|image|max:5120', // 5MB
            'sort_order'  => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        $path = $request->file('image')->store('achievements', 'public');

        $achievement = Achievement::create([
            'title'       => $request->input('title'),
            'description' => $request->input('description'),
            'image_path'  => $path,
            'sort_order'  => $request->input('sort_order', 0),
        ]);

        return response()->json(['status' => 'success', 'data' => $achievement], 201);
    }

    /**
     * POST /api/achievements/{achievement}
     * (استخدم _method=PUT في الفورم عند الإرسال من لوحة تحكم HTML)
     * تعديل إنجاز موجود، مع إمكانية استبدال الصورة.
     */
    public function update(Request $request, Achievement $achievement)
    {
        $validator = Validator::make($request->all(), [
            'title'       => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image'       => 'nullable|image|max:5120',
            'sort_order'  => 'nullable|integer',
            'is_active'   => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        if ($request->hasFile('image')) {
            if ($achievement->image_path) {
                Storage::disk('public')->delete($achievement->image_path);
            }
            $achievement->image_path = $request->file('image')->store('achievements', 'public');
        }

        $achievement->fill($request->only(['title', 'description', 'sort_order', 'is_active']));
        $achievement->save();

        return response()->json(['status' => 'success', 'data' => $achievement]);
    }

    /**
     * DELETE /api/achievements/{achievement}
     */
    public function destroy(Achievement $achievement)
    {
        if ($achievement->image_path) {
            Storage::disk('public')->delete($achievement->image_path);
        }
        $achievement->delete();

        return response()->json(['status' => 'success']);
    }
}
