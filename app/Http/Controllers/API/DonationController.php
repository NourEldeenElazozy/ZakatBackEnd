<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\campaign_donation;
use App\Models\CashPayment;
use App\Models\donation;
use App\Models\user_donation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
class DonationController extends Controller
{
    public function updateStatus($id)
    {
        // البحث عن التبرع
        $donation = Donation::findOrFail($id);

        // تحديث حالة الدفع
        $donation->update(['status' => 1]);

        // إعادة التوجيه مع رسالة نجاح
        return redirect()->back()->with('success', 'تم تحديث حالة الدفع إلى مدفوع.');
    }
     public function store(Request $request)
    {
        // التحقق من صحة المدخلات
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'campaign_id' => 'nullable|exists:campaigns,id', // ✅ تم التعديل: أصبح nullable
            'amount' => 'required|numeric|min:1',
            'status' => 'nullable|integer',
            'type' => 'required|string|in:نقدي,ادفع لي,موبي كاش,يسر باي',
            'phone' => 'nullable|string', // ✅ إضافة phone للتحقق إذا كنت تستخدمه في CashPayment
            'donation_purpose' => 'nullable|required_without:campaign_id|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'فشل التحقق من البيانات.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();
        $validated['status'] = $validated['status'] ?? 1; // تعيين الحالة الافتراضية إلى 1 إذا لم يتم توفيرها

        try {
            $date = $request->input('date', now()->format('Y-m-d')); // استخدام التاريخ الحالي كافتراضي
            $purpose = null;
       if (isset($validated['campaign_id']) && !empty($validated['campaign_id'])) {
                $purpose = 'campaign'; // أو تتركها null حسب تصميمك
            } else {
                $purpose = $validated['donation_purpose']; // هنا سيتم تخزين (زكاة، صدقة، إلخ)
            }
            // إنشاء سجل جديد في جدول donations
            $donation = Donation::create([
                'amount' => $validated['amount'],
                'status' => $validated['status'],
                'type' => $validated['type'],
                'date' => $date,
                'donation_purpose' => $purpose, // ✅ حفظ نوع الزكاة أو الغرض هنا (تأكد من إضافته في الموديل وقاعدة البيانات)
            ]);

            // ربط التبرع بالمستخدم في جدول users_donations
            $userDonation = User_Donation::create([
                'user_id' => $validated['user_id'],
                'donation_id' => $donation->id,
            ]);

            // ✅ ربط التبرع بالحملة في جدول campaigns_donations (فقط إذا تم توفير campaign_id)
            $campaignDonation = null; // تهيئة بـ null
            if (isset($validated['campaign_id']) && !empty($validated['campaign_id'])) {
                $campaignDonation = Campaign_Donation::create([
                    'donation_id' => $donation->id,
                    'campaign_id' => $validated['campaign_id'],
                ]);
            }

            // إنشاء سجل الدفع النقدي إذا كان النوع 'نقدي'
            if ($validated['type'] === 'نقدي') {
                CashPayment::create([
                    'user_id' => $validated['user_id'],
                    'phone' => $request->input('phone', 'غير معروف'), // جلب رقم الهاتف من الطلب أو تعيين 'غير معروف'
                    'amount' => $validated['amount'],
                    'donation_id' => $donation->id,
                    'status' => 'pending',
                ]);
            }

            return response()->json([
                'message' => 'تم تسجيل التبرع بنجاح.',
                'donation' => $donation,
                'user_donation' => $userDonation,
                'purpose' => $purpose, // إرجاع الغرض للتأكيد
                'campaign_donation' => $campaignDonation, // ستكون null إذا لم يتم توفير campaign_id
            ], 201);
        } catch (\Exception $e) {
            // تسجيل الخطأ لمراجعة لاحقة
            \Log::error('Failed to record donation: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['error' => 'فشل تسجيل التبرع: ' . $e->getMessage()], 500);
        }
    }
    public function getUserDonations($userId)
{
    $donations = DB::table('donations')
        ->leftJoin('campaigns_donations', 'donations.id', '=', 'campaigns_donations.donation_id')
        ->leftJoin('campaigns', 'campaigns_donations.campaign_id', '=', 'campaigns.id')
        ->leftJoin('users_donations', 'donations.id', '=', 'users_donations.donation_id')
        ->leftJoin('users', 'users_donations.user_id', '=', 'users.id')
        ->select(
            'donations.*',
            'users.name as username',
            'campaigns.name as campaign_name'
        )
        ->where('users.id', $userId)
        ->orderBy('donations.created_at', 'desc') // ترتيب تنازلي حسب التاريخ // تصفية حسب المستخدم
        ->get();

    return response()->json([
        'success' => true,
        'data' => $donations
    ]);
}
        }


