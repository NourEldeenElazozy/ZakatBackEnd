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
      // احصل على رقم التصنيف من الطلب
    $categoryId = $request->input('category_id');
  

    // إذا كان التصنيف موجودًا، قم بتصفية الحملات بناءً عليه
  if ($categoryId) {
    $campaigns = Campaign::with(['categorie', 'donation'])
        ->where('categorie_id', $categoryId)
       
        ->get();
} else {
    // إذا لم يتم تمرير التصنيف، احصل على جميع الحملات
    $campaigns = Campaign::with(['categorie', 'donation'])
         ->where('total', '>', 0)
        ->get();
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
  public function soon (Request $request)
    {
    $campaigns = Campaign::with(['categorie', 'donation'])
          ->where('total', '>', 0)
        ->get();

// تحديث قيمة paid_up بناءً على مجموع التبرعات
foreach ($campaigns as $campaign) {
    $totalDonations = $campaign->donation->sum('amount');
    $campaign->paid_up = $totalDonations;
}

// ترشيح الحملات التي المتبقي لها 10% أو أقل من إجمالي المبلغ
$filteredCampaigns = $campaigns->filter(function ($campaign) {
    $remainingAmount = $campaign->total - $campaign->paid_up;
    $tenPercentOfTotal = $campaign->total * 0.10;
    return $remainingAmount <= $tenPercentOfTotal;
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
