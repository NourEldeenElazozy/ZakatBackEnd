<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\campaign_donation;
use App\Models\donation;
use App\Models\user_donation;
use Illuminate\Http\Request;

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
                $validated = $request->validate([
                    'user_id' => 'required|exists:users,id',
                    'campaign_id' => 'required|exists:campaigns,id',
                    'amount' => 'required|numeric|min:1', // قيمة التبرع
                    'status' => 'nullable|integer', // الحقل اختياري ويجب أن يكون رقمًا صحيحًا
    'type' => 'required|string|in:نقدي,ادفع لي,موبي كاش,يسر باي', // الحقل إجباري ويجب أن يكون إحدى القيم المحددة

                ]);
                $validated['status'] = $validated['status'] ?? 1;
                try {
                    $date = $request->input('date', now()->format('Y-m-d'));
                    // إنشاء سجل جديد في جدول donations
                    $donation = donation::create([
                        'amount' => $validated['amount'],
                        'status' => $validated['status'],
                        'type' => $validated['type'],
                        'date' => $date,
                    ]);

                    // ربط التبرع بالمستخدم في جدول users_donations
                    $userDonation = user_donation::create([
                        'user_id' => $validated['user_id'],
                        'donation_id' => $donation->id,
                    ]);

                    // ربط التبرع بالحملة في جدول campaigns_donations
                    $campaignDonation = campaign_donation::create([
                        'donation_id' => $donation->id,
                        'campaign_id' => $validated['campaign_id'],
                    ]);

                    return response()->json([
                        'message' => 'Donation recorded successfully.',
                        'donation' => $donation,
                        'user_donation' => $userDonation,
                        'campaign_donation' => $campaignDonation,
                    ], 201);
                } catch (\Exception $e) {
                    return response()->json(['error' => 'Failed to record donation: ' . $e->getMessage()], 500);
                }
            }
        }


