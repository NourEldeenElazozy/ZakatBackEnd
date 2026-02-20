<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\FirebaseNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log; // لا تنسى استيراد Log لاستخدامه في السجلات
use App\Models\Notification; // ✅ استيراد Notification Model

class NotificationControllerApi extends Controller
{
 // ... في نفس الـ Controller الذي يحتوي على sendNotification ...

 public function getUserNotifications(Request $request)
    {
        // التحقق من صلاحية user_id إذا تم تمريره
        $request->validate([
            'user_id' => 'nullable|integer|exists:users,id',
        ]);

        $userId = $request->input('user_id');

        $query = Notification::orderBy('created_at', 'desc');

        if ($userId) {
            // إذا تم تمرير user_id، قم بجلب إشعارات هذا المستخدم والإشعارات العامة
            $query->where(function ($q) use ($userId) {
                $q->where('user_id', $userId)
                  ->orWhereNull('user_id');
            });
        } else {
            // إذا لم يتم تمرير user_id، قم بجلب الإشعارات العامة فقط
            $query->whereNull('user_id');
        }

        $notifications = $query->get();

        return response()->json([
            'status' => 'success',
            'notifications' => $notifications,
        ]);
    }
}
