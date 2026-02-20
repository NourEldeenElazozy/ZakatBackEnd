<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\campaign;
use Illuminate\Http\Request;

class CampaignController extends Controller
{
    /**
     * Display a listing of the resource.
     */
public function index(Request $request)
{
    $categoryId = $request->input('category_id');

    // 1. تعريف دالة الفلترة للتبرعات (نستخدمها لضمان عدم تكرار الكود)
    // هذه الدالة ستجعل مصفوفة 'donation' في الـ JSON تحتوي فقط على التبرعات المكتملة
    $donationFilter = function ($query) {
        $query->where('status', 1);
    };

    // 2. بناء الاستعلام الأساسي
    $query = Campaign::with(['categorie', 'donation' => $donationFilter]);

    // 3. تطبيق الشروط حسب وجود التصنيف
    if ($categoryId) {
        $query->where('categorie_id', $categoryId);
    } else {
        $query->where('total', '>', 0);
    }

    // 4. جلب النتائج (للحملات المستمرة فقط)
    $campaigns = $query->where('state_campaign', 'مستمره')->get();

    // 5. التحديث والجمع
    foreach ($campaigns as $campaign) {
        // بما أننا قمنا بفلترة العلاقة في الأعلى، فإن $campaign->donation
        // تحتوي الآن فقط على التبرعات المكتملة، لذا الجمع هنا سيكون صحيحاً تلقائياً
        $totalPaid = $campaign->donation->sum('amount');

        // (اختياري) لتحديث القيمة المعروضة في الـ JSON لتظهر المبلغ المدفوع حالياً
        $campaign->paid_up = $totalPaid; 

        if ($totalPaid >= $campaign->total) {
            $campaign->state_campaign = 'مكتملة';
            $campaign->save();
        }
    }

    return response()->json($campaigns);
}public function fixPreviousCampaigns()
{
    // 1. جلب الحملات المكتملة فقط، مع تبرعاتها المكتملة فقط
    $campaigns = Campaign::with(['donation' => function ($q) {
            $q->where('status', 1);
        }])
        ->where('state_campaign', 'مكتملة') // نبحث في المكتملة
        ->get();

    $fixedCount = 0;

    foreach ($campaigns as $campaign) {
        // حساب المبلغ الفعلي (المقبول فقط)
        $totalPaid = $campaign->donation->sum('amount');

        // إذا كان المبلغ الفعلي أقل من المطلوب، نعيدها مستمرة
        if ($totalPaid < $campaign->total) {
            $campaign->state_campaign = 'مستمره';
            $campaign->save();
            $fixedCount++;
        }
    }

    return "تم إصلاح $fixedCount حملة وإعادتها إلى الحالة (مستمره).";
}
public function completedCampaigns(Request $request)
{
    $categoryId = $request->input('category_id');

    // فلترة التبرعات: فقط status = 1 (مكتملة)
    $donationFilter = function ($query) {
        $query->where('status', 1);
    };

    // بناء الاستعلام الأساسي
    $query = Campaign::with(['categorie', 'donation' => $donationFilter])
        ->where('state_campaign', 'مكتملة');

    // فلترة حسب التصنيف إذا تم إرساله
    if ($categoryId) {
        $query->where('categorie_id', $categoryId);
    }

    $campaigns = $query->get();

    // تحديث paid_up بناءً على التبرعات المكتملة فقط
    foreach ($campaigns as $campaign) {
        $campaign->paid_up = $campaign->donation->sum('amount');
    }

    return response()->json($campaigns);
}

        public function open_campaign(Request $request)
    {
       $campaigns = Campaign::with(['categorie', 'donation'])
      ->where('total', '=', 0)
        ->get();

return response()->json($campaigns);
    }
public function soon(Request $request)
{
    // فلترة التبرعات: نطلب فقط التبرعات التي حالتها 1 (مكتملة)
    $donationFilter = function ($query) {
        $query->where('status', 1);
    };

    $campaigns = Campaign::with(['categorie', 'donation' => $donationFilter])
        ->where('total', '>', 0)
        ->get();

    // تحديث قيمة paid_up بناءً على مجموع التبرعات المكتملة فقط
    foreach ($campaigns as $campaign) {
        // بما أن العلاقة "donation" تم فلترتها في الأعلى، فإن sum تحسب المكتمل فقط
        $totalDonations = $campaign->donation->sum('amount');
        
        // تحديث قيمة paid_up في الكائن لكي تستخدم في الفلترة أدناه
        $campaign->paid_up = $totalDonations; 
    }

    // ترشيح الحملات المستمرة فقط التي المتبقي لها 10% أو أقل من إجمالي المبلغ
    $filteredCampaigns = $campaigns->filter(function ($campaign) {
        // نحسب المتبقي بناءً على الـ paid_up الذي تم حسابه من التبرعات المكتملة
        $remainingAmount = $campaign->total - $campaign->paid_up;
        $tenPercentOfTotal = $campaign->total * 0.30;

        // نستبعد الحملات المكتملة ونعرض التي اقتربت من الاكتمال (المتبقي 10% أو أقل)
        return $remainingAmount <= $tenPercentOfTotal && $campaign->state_campaign !== 'مكتملة';
    });

    return response()->json($filteredCampaigns);
}
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
