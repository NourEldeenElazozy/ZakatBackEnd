<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Services\FirebaseNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log; // لا تنسى استيراد Log لاستخدامه في السجلات
use App\Models\Notification; // ✅ استيراد Notification Model
class NotificationController extends Controller
{
    protected $firebaseService;

    public function __construct(FirebaseNotificationService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    public function sendNotification(Request $request)
    {
        $request->validate([
            // الآن يمكننا استخدام user_id بدلاً من device_token مباشرةً
            'user_id' => 'nullable|integer|exists:users,id', // يجب أن يكون عدد صحيح ويوجد في جدول users
            'topic' => 'nullable|string',
            'title' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        $userId = $request->input('user_id');
        $topic = $request->input('topic');
        $title = $request->input('title');
        $body = $request->input('body');

        $targetType = null;
        $targetValue = null; // سيحتوي على device_token أو topic

        // المنطق لتحديد الوجهة النهائية:
        if (!empty($userId)) {
            // المستخدم محدد، جلب رمز جهازه
            $user = User::find($userId);
            if (!$user || empty($user->device_token)) {
                return response()->json([
                    'message' => 'Selected user not found or does not have a device token.'
                ], 404);
            }
            $targetType = 'device';
            $targetValue = $user->device_token;
            Log::info("Sending notification to specific user (ID: {$userId}, Token: {$targetValue})");
        } elseif (!empty($topic)) {
            // الموضوع محدد (قد يكون 'all' أو غيره)
            $targetType = 'topic';
            $targetValue = $topic;
            Log::info("Sending notification to topic: {$topic}");
        } else {
            // لا يوجد userId ولا topic محدد، إرسال إلى التوبيك الافتراضي 'all'
            $targetType = 'topic';
            $targetValue = 'all';
       
            Log::info("Neither user_id nor topic provided. Defaulting to topic 'all'.");
        }


        try {
            if ($targetType === 'device') {
                $this->firebaseService->sendNotificationToDevice($targetValue, $title, $body);
                $responseMessage = 'تم ارسال الاشعار للمستخدم';
            } else { // $targetType === 'topic'
                
                $this->firebaseService->sendNotificationToTopic($targetValue, $title, $body);
                $responseMessage = "تم ارسال الاشعار للجميع";
            }
             // ✅ حفظ الإشعار في قاعدة البيانات بعد نجاح الإرسال أو محاولة الإرسال
             Notification::create([
                'user_id' => ($targetType === 'device') ? $userId : null, // فقط إذا كان موجهًا لمستخدم معين
                'title' => $title,
                'body' => $body,
                'target_type' => $targetType,
                'target_value' => $targetValue,
                'is_read' => false, // جديد دائماً
            ]);

        } catch (\Exception $e) {
            $errMsg = $e->getMessage();
            Log::error("Failed to send notification: {$errMsg}");

            // token منتهي → احذفه تلقائياً
            if (!empty($userId) && (str_contains($errMsg, 'NotRegistered') || str_contains($errMsg, 'InvalidRegistration'))) {
                User::where('id', $userId)->update(['device_token' => null]);
                Log::info("Auto-cleared expired device_token for user #{$userId}");
                return redirect()->back()->with('warning', 'رمز جهاز المستخدم منتهي الصلاحية (قد يكون حذف التطبيق أو أعاد تثبيته). تم حذفه تلقائياً.');
            }

            return redirect()->back()->with('error', 'فشل إرسال الإشعار: ' . $errMsg);
        }

        return redirect()->back()->with('success', $responseMessage); // استخدم redirect()->back() لعرض رسالة النجاح في نفس الصفحة
    }

    /**
     * إرسال إشعار لمستخدمين محددين (متعددين)
     */
    public function sendToMultiple(Request $request)
    {
        $request->validate([
            'user_ids'   => 'required|array|min:1',
            'user_ids.*' => 'integer|exists:users,id',
            'title'      => 'required|string|max:255',
            'body'       => 'required|string',
        ], [
            'user_ids.required' => 'يجب اختيار مستخدم واحد على الأقل.',
            'user_ids.min'      => 'يجب اختيار مستخدم واحد على الأقل.',
            'title.required'    => 'العنوان مطلوب.',
            'body.required'     => 'النص مطلوب.',
        ]);

        $title   = $request->input('title');
        $body    = $request->input('body');
        $userIds = $request->input('user_ids');

        $users = User::whereIn('id', $userIds)->whereNotNull('device_token')->get();

        if ($users->isEmpty()) {
            return redirect()->back()
                ->with('error', 'لا يوجد لدى المستخدمين المحددين رمز جهاز (device token).')
                ->withInput();
        }

        $sentCount   = 0;
        $failedCount = 0;
        $failedNames = [];

        foreach ($users as $user) {
            try {
                $this->firebaseService->sendNotificationToDevice(
                    $user->device_token, $title, $body
                );
                Notification::create([
                    'user_id'      => $user->id,
                    'title'        => $title,
                    'body'         => $body,
                    'target_type'  => 'device',
                    'target_value' => $user->device_token,
                    'is_read'      => false,
                ]);
                $sentCount++;
            } catch (\Exception $e) {
                $failedCount++;
                $failedNames[] = $user->name;
                $errMsg = $e->getMessage();
                Log::error("Failed to notify user #{$user->id} ({$user->name}): {$errMsg}");

                // إذا كان الـ token منتهياً أو ملغياً → احذفه من قاعدة البيانات تلقائياً
                if (str_contains($errMsg, 'NotRegistered') || str_contains($errMsg, 'InvalidRegistration')) {
                    $user->update(['device_token' => null]);
                    Log::info("Auto-cleared expired device_token for user #{$user->id} ({$user->name})");
                }
            }
        }

        $message    = "تم إرسال الإشعار بنجاح لـ {$sentCount} مستخدم.";
        if ($failedCount > 0) {
            $message .= " فشل الإرسال لـ: " . implode('، ', $failedNames);
        }

        return redirect()->back()->with($failedCount === 0 ? 'success' : 'warning', $message);
    }

    /**
     * AJAX: بحث عن مستخدمين لديهم device_token
     */
    public function searchUsers(Request $request)
    {
        $q = $request->input('q', '');

        $users = User::where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                      ->orWhere('phone', 'like', "%{$q}%")
                      ->orWhere('email', 'like', "%{$q}%");
            })
            ->whereNotNull('device_token')
            ->select('id', 'name', 'phone', 'email')
            ->orderBy('name')
            ->limit(20)
            ->get();

        return response()->json($users);
    }

    // ... بقية دوال Controller كما هي (لم تتغير)
    public function index()
    {
        //
    }

    public function create()
    {
        $users = User::whereNotNull('device_token')
            ->select('id', 'name', 'email', 'phone', 'device_token')
            ->orderBy('name')
            ->get();
        return view('send_notification', compact('users'));
    }

    public function store(Request $request)
    {
        //
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        //
    }
}