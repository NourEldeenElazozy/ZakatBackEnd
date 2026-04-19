<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\mobipayments;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Models\EzonpayYusser; // ✅ تأكد من استدعاء الموديل

class OnlinePaymentController extends Controller
{
 public function signin(Request $request)
{
    $data = [
        'userId' => 142558,
        'pin' => 'U3f@Zh',
        'providerId' => 3636,
        'authUserType' => 0,
    ];

    $signinResponse = Http::post('http://160.19.103.122:8888/OnlinePayment.3.0.1/api/OnlinePaymentServices/Signin', $data);

    return response()->json($signinResponse->json(), $signinResponse->status());
}
public function createEzoneLink(Request $request)
{
    // 1. التحقق من المدخلات 
    $request->validate([
        'amount' => 'required|numeric|min:1',
        'order_ref' => 'required|string',
        'title' => 'nullable|string',
        'user_id' => 'nullable|integer',
        'campaign_id' => 'nullable|integer',
        'purpose' => 'nullable|string',
    ]);

    $orderRef = $request->input('order_ref');
    $amount = (float)$request->input('amount');
    $userId = $request->input('user_id');
    $campaignId = $request->input('campaign_id');
$purpose = $request->input('purpose');
$paymentMethodType = $request->input('payment_method', 'yosrpay');
// 🌟 2. معالجة اسم العميل وتقسيمه ليطابق شروط البوابة
    $fullName = trim($request->input('customer_name', 'فاعل خير'));
    $nameParts = explode(' ', $fullName, 2); // فصل الاسم من أول مسافة
    $firstName = $nameParts[0];
    // إذا لم يكتب المستخدم اسم أخير، نمرر نقطة أو "غير محدد" لتجنب خطأ الحقول الفارغة
    $lastName = $nameParts[1] ?? '.'; 
    
    $phoneNumber = $request->input('customer_phone', '');
    $excludedMethods = [];
    if ($paymentMethodType === 'moamalat') {
        // إذا اختار معاملات: نستثني الجميع ما عدا 101 (بطاقة مصرفية)
        $excludedMethods = [125, 107, 106, 105, 104, 102];
    } else {
        // إذا اختار يسر باي: نستثني الجميع ما عدا 105 (يسر باي)
        $excludedMethods = [125, 107, 106, 104, 102, 101];
    }
    // 2. حفظ العملية مبدئياً
    $transaction = EzonpayYusser::create([
        'order_ref' => $orderRef,
        'amount' => $amount,
        'status' => 'pending', 
    ]);

    $url = "https://api.ezonepay.ly/payment-link/new";
    $expiresAt = now()->addHour()->format('Y-m-d H:i');

    $redirectUrl = url('/api/ezone/payment-callback?order_ref=' . $orderRef . '&user_id=' . $userId . '&campaign_id=' . $campaignId . '&purpose=' . urlencode($purpose));
    
   $payload = [
        "Title" => $request->input('title', "تبرع"),
        "OrderReference" => $orderRef,
        "ShopId" => 1025, 
        "Amount" => $amount,
        "Note" => "Payment for order " . $orderRef,
        "IsEnabled" => true,
        "ExpiresAt" => $expiresAt, 
        "MaxUsageCount" => 2, 
     "ExcludedPaymentMethods" => $excludedMethods, // 🌟 تمرير المصفوفة الديناميكية هنا
        "RedirectUrl" => $redirectUrl ,
        "customer" => [
            "firstName" => $firstName,
            "lastName" => $lastName,
            "phoneNumber" => $phoneNumber
        ]
    ];

    try {
        $token = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJhdWQiOiJlem9uZS1wYXktdXNlcnMiLCJpc3MiOiJlem9uZS1wYXktYXBpIiwiZXhwIjoxNzg1MTQ1OTExLCJpYXQiOjE3Njk1OTM5MTEsInN1YiI6IjIwIiwicm9sZSI6IjIiLCJqdGkiOiIwNjk3OWRjMy03MWIzLTc1OTItODAwMC1jYTYzNWZkYWFmZmQiLCJtaWQiOjE3LCJtdHlwZSI6MX0.0LyBWvzo2dXJWy13vxpu0DsX7o_3ZDxEHLJE85G3kU8';

        $response = \Illuminate\Support\Facades\Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->post($url, $payload);

        $responseData = $response->json(); 

        if ($response->successful()) {
            
            // 🚨 التعديل الهام جداً هنا 🚨
            // يجب أن تظل الحالة pending (أو نضعها created) لأن العميل لم يدفع بعد!
            $transaction->update([
                'status' => 'pending', // تم التعديل من success إلى pending
                'payment_link' => $responseData['link'] ?? null,
                'response_data' => $responseData, 
            ]);

            return response()->json([
                'status' => 'success',
                'link' => $responseData['link'] ?? null,
            ], 200);
            
        } else {
            $transaction->update(['status' => 'failed', 'response_data' => $responseData]);
            return response()->json(['status' => 'error', 'message' => 'فشل الإنشاء'], $response->status());
        }

    } catch (\Exception $e) {
        $transaction->update(['status' => 'error', 'response_data' => ['error_msg' => $e->getMessage()]]);
        return response()->json(['status' => 'error', 'message' => 'خطأ اتصال'], 500);
    }
}
public function openSession(Request $request)
{
    // الخطوة 1: بيانات تسجيل الدخول
    $signinData = [
        'userId' => 142558,
        'pin' => 'U3f@Zh',
        'providerId' => 3636,
        'authUserType' => 0,
    ];

    // إرسال طلب تسجيل الدخول
    $signinResponse = Http::post(
        'http://160.19.103.122:8888/OnlinePayment.3.0.1/api/OnlinePaymentServices/Signin',
        $signinData
    );

    if (!$signinResponse->ok()) {
        return response()->json(['error' => 'فشل تسجيل الدخول إلى بوابة الدفع'], $signinResponse->status());
    }

    $signinJson = $signinResponse->json();
    $token = $signinJson['content']['value'] ?? null;
    $transactionId = $signinJson['traceId'] ?? null;
   
    if (!$token || $transactionId === null) {
        return response()->json(['error' => 'البيانات الراجعة من Signin غير مكتملة'], 500);
    }
    
    // الخطوة 2: إعداد بيانات فتح الجلسة
    $openSessionData = [
        'amount' => $request->input('amount', 0),
        'identityCard' => "223125010",
        'transactionId' => $transactionId,
        'onlineOperation' => $request->input('onlineOperation', 1),
    ];
   

    // إرسال طلب فتح الجلسة
    $openSessionResponse = Http::withHeaders([
        'Authorization' => "Bearer {$token}",
        'Content-Type' => 'application/json',
    ])->post(
        'http://160.19.103.122:8888/OnlinePayment.3.0.1/api/OnlinePaymentServices/OpenSession',
        $openSessionData
    );

    return response()->json($openSessionResponse->json(), $openSessionResponse->status());
}
public function completeSession(Request $request)
{
    $otp = $request->input('otp');
    $token = $request->input('token'); // استقبال التوكن

    if (!$otp || !$token) {
        return response()->json(['error' => 'OTP والتوكن مطلوبان'], 400);
    }

    $data = [
        'otp' => $otp,
    ];

    // إرسال الطلب باستخدام التوكن
    $response = Http::withHeaders([
        'Authorization' => "Bearer {$token}",
        'Content-Type' => 'application/json',
    ])->post(
        'http://160.19.103.122:8888/OnlinePayment.3.0.1/api/OnlinePaymentServices/CompleteSession?culture=ar-LY',
        $data
    );


    return response()->json([
        'status' => $response->status(),
        'response' => $response->json()
    ], $response->status());
}
 public function mobiCash(Request $request)
    {
         $request->validate([
            'card_number' => 'required|string',
            'amount' => 'required|numeric|min:0.1',
        ]);

        try {
            $payload = [
                'api_key' => config('services.mobicash.api_key'),
                'card_number' => $request->card_number,
                'amount' => $request->amount,
                'description' => 'سداد قيمة لصالح تطبيق الزكاة',
            ];

            $response = Http::timeout(15)->post(
                config('services.mobicash.base_url') . '/payments/card',
                $payload
            );

            $responseData = $response->json();
            $statusCode = $response->status();

            // 🧾 حفظ العملية سواء نجحت أو فشلت
            $payment = mobipayments::create([
                'payment_uuid' => $responseData['data']['payment_uuid'] ?? null,
                'amount' => $request->amount,
                'status' => $response->successful() 
                    ? ($responseData['data']['status'] ?? 'PENDING') 
                    : 'FAILED',
                'card_number' => $request->card_number,
                'description' => $payload['description'],
                'response_data' => json_encode($responseData),
            ]);

            if ($response->successful()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'تم تنفيذ عملية الدفع بنجاح',
                    'data' => $responseData['data'],
                ], 200);
            }

            return response()->json([
                'status' => 'error',
                'message' => $responseData['message'] ?? 'فشل تنفيذ العملية من الخادم الخارجي',
                'response' => $responseData,
            ], $statusCode);

        } catch (\Exception $e) {
            // 🧾 حفظ حتى في حالة الاستثناء
            mobipayments::create([
                'amount' => $request->amount,
                'status' => 'ERROR',
                'card_number' => $request->card_number,
                'description' => 'سداد قيمة لصالح تطبيق الزكاة',
                'response_data' => json_encode(['exception' => $e->getMessage()]),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ أثناء تنفيذ الطلب',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function verifymobiCash(Request $request)
 {
        $request->validate([
            'payment_uuid' => 'required|string',
            'otp' => 'required|string',
        ]);

        try {
            $payload = [
                'api_key' => config('services.mobicash.api_key'),
                'payment_uuid' => $request->payment_uuid,
                'otp' => $request->otp,
            ];

            $response = Http::timeout(15)->post(
                config('services.mobicash.base_url') . '/payments/card/verify-otp',
                $payload
            );

            $responseData = $response->json();
            $statusCode = $response->status();

            // 🔄 تحديث أو حفظ حالة الدفع حتى لو فشلت العملية
            $payment = mobipayments::where('payment_uuid', $request->payment_uuid)->first();

            if ($payment) {
                $payment->update([
                    'status' => $response->successful()
                        ? ($responseData['data']['status'] ?? 'SUCCESS')
                        : 'FAILED',
                    'verified_at' => Carbon::now(),
                    'response_data' => json_encode($responseData),
                     'amount' => $response->successful() ? $responseData['data']['amount'] : $payment->amount,
                ]);
            } else {
                // إذا لم يتم إنشاء العملية سابقًا، نحفظها هنا أيضًا
                 mobipayments::create([
        'payment_uuid' => $request->payment_uuid,
        'status' => $response->successful()
            ? ($responseData['data']['status'] ?? 'SUCCESS')
            : 'FAILED',
        'amount' => $response->successful() ? $responseData['data']['amount'] : 0,
        'response_data' => json_encode($responseData),
        'description' => $response->successful()
            ? ($responseData['data']['description'] ?? 'تحقق من رمز OTP')
            : 'تحقق من رمز OTP',
    ]);
            }

            if ($response->successful()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'تم التحقق من رمز OTP بنجاح',
                    'data' => $responseData['data'],
                ], 200);
            }

            return response()->json([
                'status' => 'error',
                'message' => $responseData['message'] ?? 'فشل التحقق من رمز OTP',
                'error' => $responseData['error'] ?? null,
            ], $statusCode);

        } catch (\Exception $e) {
            // 🧾 حفظ حالة الفشل أيضًا
            mobipayments::where('payment_uuid', $request->payment_uuid)
                ->update([
                    'status' => 'ERROR',
                    'response_data' => json_encode(['exception' => $e->getMessage()]),
                    'verified_at' => Carbon::now(),
                ]);

            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ أثناء التحقق من OTP',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
public function paymentCallback(Request $request)
    {
        // دالة داخلية لتوليد تصميم HTML احترافي وموحد لجميع الحالات
        $generateHtml = function ($title, $message, $type = 'success') {
            $color = $type == 'success' ? '#4caf50' : ($type == 'warning' ? '#ff9800' : '#f44336');
            $iconBg = $type == 'success' ? '#e8f5e9' : ($type == 'warning' ? '#fff3e0' : '#ffebee');
            
            // أيقونة النجاح أو الخطأ
            $icon = $type == 'success' 
                ? '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>'
                : '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>';

            return <<<HTML
            <!DOCTYPE html>
            <html lang="ar" dir="rtl">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>{$title}</title>
                <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
                <style>
                    body { font-family: 'Cairo', sans-serif; background-color: #f4f7f6; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
                    .success-card { background: white; padding: 40px 30px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); text-align: center; max-width: 350px; width: 90%; }
                    .icon-wrapper { width: 80px; height: 80px; background-color: {$iconBg}; border-radius: 50%; display: flex; justify-content: center; align-items: center; margin: 0 auto 20px; }
                    .icon-wrapper svg { width: 40px; height: 40px; color: {$color}; }
                    h1 { color: #333; font-size: 24px; font-weight: 700; margin: 0 0 10px; }
                    p { color: #666; font-size: 15px; line-height: 1.6; margin: 0 0 30px; }
                    .btn { background-color: {$color}; color: white; border: none; padding: 12px 30px; border-radius: 10px; font-size: 16px; font-family: 'Cairo', sans-serif; font-weight: 600; cursor: pointer; width: 100%; transition: opacity 0.3s; }
                    .btn:hover { opacity: 0.8; }
                </style>
            </head>
            <body>
                <div class="success-card">
                    <div class="icon-wrapper">{$icon}</div>
                    <h1>{$title}</h1>
                    <p>{$message}</p>
                    <button class="btn" onclick="closeAppWindow()">العودة للتطبيق</button>
                </div>
              <script>
    function closeAppWindow() {
        // التحقق مما إذا كانت الصفحة مفتوحة داخل تطبيق فلاتر
        if (window.FlutterApp) {
            // إرسال رسالة لفلاتر لإغلاق الشاشة
            window.FlutterApp.postMessage('closeApp');
        } else {
            // إذا كان يفتح من متصفح عادي
            window.close();
            setTimeout(function() { alert("يرجى إغلاق هذه الصفحة والعودة لتطبيق الزكاة."); }, 300);
        }
    }
</script>
            </body>
            </html>
HTML;
        };

        // 1. استقبال البارامترات
        $orderRef = $request->query('order_ref');
        $userId = $request->query('user_id');
        $campaignId = $request->query('campaign_id');
$purpose = $request->query('purpose'); // 🌟 استقبال الغرض
        if (!$orderRef) {
            return response($generateHtml("رقم الطلب مفقود!", "لا يمكن معالجة هذه العملية بسبب نقص في البيانات.", "error"), 400);
        }

        $transaction = \App\Models\EzonpayYusser::where('order_ref', $orderRef)->first();
        if (!$transaction) {
            return response($generateHtml("العملية غير موجودة!", "لم يتم العثور على المعاملة المطلوبة في سجلاتنا.", "error"), 404);
        }

        // 2. إذا تم الدفع مسبقاً
        if ($transaction->status === 'success') {
            return response($generateHtml("تم الدفع مسبقاً!", "لقد تم استلام تبرعك لهذه العملية بنجاح. في ميزان حسناتك إن شاء الله.", "success"));
        }

        $linkId = $transaction->response_data['id'] ?? null;
        if (!$linkId) {
            return response($generateHtml("خطأ فني", "معرف رابط الدفع غير موجود للتحقق.", "error"), 400);
        }

        $url = "https://api.ezonepay.ly/payment-link/" . $linkId;
        $token = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJhdWQiOiJlem9uZS1wYXktdXNlcnMiLCJpc3MiOiJlem9uZS1wYXktYXBpIiwiZXhwIjoxNzg1MTQ1OTExLCJpYXQiOjE3Njk1OTM5MTEsInN1YiI6IjIwIiwicm9sZSI6IjIiLCJqdGkiOiIwNjk3OWRjMy03MWIzLTc1OTItODAwMC1jYTYzNWZkYWFmZmQiLCJtaWQiOjE3LCJtdHlwZSI6MX0.0LyBWvzo2dXJWy13vxpu0DsX7o_3ZDxEHLJE85G3kU8';

        try {
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ])->get($url);

            if ($response->successful()) {
                $data = $response->json();
                $totalPaid = $data['totalAmountPaid'] ?? 0;
              //  return response()->json($data); // يمكنك إزالة هذا السطر بعد التأكد من البيانات الراجعة

               // if ($totalPaid >= $transaction->amount) {
                  
                    
                    // 🌟 بدء عملية حفظ البيانات (Transaction) لضمان عدم حدوث حفظ جزئي
                    \Illuminate\Support\Facades\DB::beginTransaction();
                    
                    try {
                        // ✅ 1. تحديث حالة بوابة يسر باي
                        $transaction->update(['status' => 'success', 'updated_at' => now()]);
                        $finalPurpose = ($campaignId != 0 && $campaignId !== null && $campaignId !== 'null') 
                                        ? 'campaign' 
                                        : ($purpose ?? 'اخراج زكاة');

                        // ✅ 2. تسجيل التبرع رسمياً
                        $donationId = \Illuminate\Support\Facades\DB::table('donations')->insertGetId([
                            'amount' => $transaction->amount,
                            'type' => 'بطافة مصرفيه',
                            'status' => 1,
                            'donation_purpose' => $finalPurpose,
                           'created_at' => now()->format('Y-m-d H:i:s'),
                            'updated_at' => now()->format('Y-m-d H:i:s'),
                            'date' => now()->format('Y-m-d'),
                        ]);

                        // ربط المتبرع (يجب التأكد من أن القيمة ليست فارغة ولا تساوي النص "null" ولا الصفر)
                        if ($userId && $userId !== 'null' && $userId != 0) {
                            \Illuminate\Support\Facades\DB::table('users_donations')->insert([
                                'user_id' => $userId,
                                'donation_id' => $donationId,
                              'created_at' => now()->format('Y-m-d H:i:s'),
                            'updated_at' => now()->format('Y-m-d H:i:s'),

                            ]);
                        }

                        // ربط الحملة
                        if ($campaignId && $campaignId !== 'null') {
                            \Illuminate\Support\Facades\DB::table('campaigns_donations')->insert([
                                'campaign_id' => $campaignId,
                                'donation_id' => $donationId,
                              'created_at' => now()->format('Y-m-d H:i:s'),
                            'updated_at' => now()->format('Y-m-d H:i:s'),
                            ]);
                        }

                        // 🌟 تأكيد الحفظ
                        \Illuminate\Support\Facades\DB::commit();

                        return response($generateHtml("تم الدفع بنجاح!", "شكراً لتبرعك السخي. تمت إضافة مساهمتك بنجاح.", "success"));

                    } catch (\Exception $dbException) {
                        // ❌ إلغاء كل ما تم إدخاله في قاعدة البيانات بسبب وجود خطأ
                        \Illuminate\Support\Facades\DB::rollBack();
                        
                        \Log::error("DB Insertion Error in YosrPay Callback: " . $dbException->getMessage());
                        
                        // عرض الخطأ الفعلي بوضوح للمستخدم للتمكن من حله
                        return response($generateHtml("خطأ أثناء الحفظ", "تم سحب المبلغ ولكن حدث خطأ في النظام: " . $dbException->getMessage(), "error"), 500);
                    }

                /*
                } else {
                    return response($generateHtml("العملية غير مكتملة", "لم يتم التأكد من سداد المبلغ بالكامل. يرجى المحاولة مرة أخرى.", "warning"));
                }
                    */
            }
            return response($generateHtml("فشل الاتصال", "لا يمكننا التحقق من البوابة في الوقت الحالي.", "error"), 500);

        } catch (\Exception $e) {
            \Log::error("YosrPay Callback Connection Error: " . $e->getMessage());
            return response($generateHtml("حدث خطأ", "حدث خطأ غير متوقع أثناء المعالجة: " . $e->getMessage(), "error"), 500);
        }
    }
}

