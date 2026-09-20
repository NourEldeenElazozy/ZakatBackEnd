<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Achievement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * كنترولر لوحة التحكم (Blade) لإدارة إنجازات صندوق الزكاة.
 * يختلف عن App\Http\Controllers\Api\AchievementController الذي يخدم تطبيق الجوال فقط
 * (ذاك يرجع JSON، وهذا يرجع Views + Redirects).
 *
 * عدّلوا الـ namespace/الـ layout/الـ middleware ليطابق نمط باقي كنترولرات
 * لوحة التحكم عندكم إن كانت مختلفة عن هذا القالب.
 */
class AchievementController extends Controller
{
    public function index()
    {
        $achievements = Achievement::orderBy('sort_order')->orderByDesc('id')->paginate(20);

        return view('achievements.index', compact('achievements'));
    }

    public function create()
    {
        return view('achievements.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image'       => 'required|image|max:5120',
            'sort_order'  => 'nullable|integer',
            'is_active'   => 'nullable|boolean',
        ]);

        $validated['image_path'] = $request->file('image')->store('achievements', 'public');
        $validated['is_active'] = $request->boolean('is_active', true);

        Achievement::create($validated);

        return redirect()
            ->route('achievements.index')
            ->with('success', 'تمت إضافة الإنجاز بنجاح.');
    }

    public function edit(Achievement $achievement)
    {
        return view('achievements.edit', compact('achievement'));
    }

    public function update(Request $request, Achievement $achievement)
    {
        $validated = $request->validate([
            'title'       => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image'       => 'nullable|image|max:5120',
            'sort_order'  => 'nullable|integer',
            'is_active'   => 'nullable|boolean',
        ]);

        if ($request->hasFile('image')) {
            if ($achievement->image_path) {
                Storage::disk('public')->delete($achievement->image_path);
            }
            $validated['image_path'] = $request->file('image')->store('achievements', 'public');
        }

        $validated['is_active'] = $request->boolean('is_active', false);

        $achievement->update($validated);

        return redirect()
            ->route('achievements.index')
            ->with('success', 'تم تحديث الإنجاز بنجاح.');
    }

    public function destroy(Achievement $achievement)
    {
        if ($achievement->image_path) {
            Storage::disk('public')->delete($achievement->image_path);
        }
        $achievement->delete();

        return redirect()
            ->route('achievements.index')
            ->with('success', 'تم حذف الإنجاز.');
    }
}
