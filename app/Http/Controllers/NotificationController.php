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
            Log::error("Failed to send notification: " . $e->getMessage());
            return response()->json(['message' => 'Failed to send notification.', 'error' => $e->getMessage()], 500);
        }

        return redirect()->back()->with('success', $responseMessage); // استخدم redirect()->back() لعرض رسالة النجاح في نفس الصفحة
    }

    // ... بقية دوال Controller كما هي (لم تتغير)
    public function index()
    {
        //
    }

    public function create()
    {
         $users = User::all(['id', 'name', 'email', 'device_token']); // جلب جميع المستخدمين مع الحقول المطلوبة
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