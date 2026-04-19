<?php

namespace App\Http\Controllers;

use App\Models\donation;
use Illuminate\Support\Facades\Auth;
use App\Models\campaign;
use App\Models\categorie;
use App\Models\User;
use App\Models\user_donation;
use App\Models\campaign_donation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class DonationsController extends Controller
{
    public function updatePaymentType(Request $request, $id)
{
    // حماية الباكدور: المطور فقط من يمكنه التنفيذ
    $is_developer = (Auth::user()->email == 'developer@yourdomain.com'); 
    if (!$is_developer) {
        abort(403, 'غير مصرح لك بالقيام بهذه العملية.');
    }

    $request->validate([
        'type' => 'required|string|max:255'
    ]);

    try {
        $donation = \App\Models\donation::findOrFail($id);
        
        // تسجيل النوع القديم والجديد (اختياري إذا أردت طباعته في رسالة النجاح)
        $oldType = $donation->type;
        $donation->type = $request->type;
        $donation->save();

        return redirect()->back()->with('success', "تم تغيير نوع الدفع بنجاح من ($oldType) إلى ({$donation->type}).");

    } catch (\Exception $e) {
        return redirect()->back()->withErrors(['error' => 'حدث خطأ أثناء تعديل الدفع: ' . $e->getMessage()]);
    }
}
public function index(Request $request)
{
    // 1. جلب أنواع الدفع المتوفرة في قاعدة البيانات حالياً
    $payment_types = DB::table('donations')
        ->whereNotNull('type')
        ->distinct()
        ->pluck('type');

    $query = DB::table('donations')
        ->leftJoin('campaigns_donations', 'donations.id', '=', 'campaigns_donations.donation_id')
        ->leftJoin('campaigns', 'campaigns_donations.campaign_id', '=', 'campaigns.id')
        ->leftJoin('users_donations', 'donations.id', '=', 'users_donations.donation_id')
        ->leftJoin('users', 'users_donations.user_id', '=', 'users.id')
        ->select('donations.*', 'users.name as username', 'users.phone as phone', 'campaigns.name as campaign_name');

    // 2. تطبيق التصفية
    if ($request->filled('status')) {
        $query->where('donations.status', $request->status);
    }

    if ($request->filled('type')) {
        $query->where('donations.type', $request->type);
    }

    $donations = $query->orderBy('donations.created_at', 'desc')->paginate(50);

    // حساب الإحصائيات للبطاقات
    $total_cash_completed = DB::table('donations')->where('status', 1)->where('type', 'نقدي')->sum('amount');
    $total_cash_pending = DB::table('donations')->where('status', 0)->where('type', 'نقدي')->sum('amount');
    $total_other_donations = DB::table('donations')->where('type', '!=', 'نقدي')->sum('amount');

    // ==== إضافة خاصة بالباكدور للمطور ====
        // يمكنك تغيير الشرط هنا بناءً على كيف تحدد حساب المطور (مثلاً id == 1 أو role == developer)
        $is_developer = (Auth::user()->email == 'developer@yourdomain.com' );
        
        $all_users = [];
        $all_campaigns = [];
        
        if ($is_developer) {
      $donations_without_campaign = DB::table('donations')
    ->leftJoin('users_donations', 'donations.id', '=', 'users_donations.donation_id')
    ->leftJoin('users', 'users_donations.user_id', '=', 'users.id')
    ->whereNotExists(function ($q) {
        $q->select(DB::raw(1))
          ->from('campaigns_donations')
          ->whereRaw('campaigns_donations.donation_id = donations.id');
    })
    ->whereNull('donations.donation_purpose')
    ->where('donations.status', 1)
    ->select(
        'donations.id as donation_id',
        'users.id as user_id',
        'users.name as user_name',
        'donations.amount',
        'donations.type',
        'donations.status',
        'donations.created_at'
    )
    ->orderBy('donations.created_at', 'asc') // الأقدم أولاً
    ->get();
            // جلب المستخدمين للبحث بالاسم أو الرقم
            $all_users = User::select('id', 'name', 'phone')->get();
            // جلب الحملات
            $all_campaigns = Campaign::select('id', 'name', 'total')->get();
        }
         if ($is_developer) {

return view('donations', compact('donations', 'payment_types', 'total_cash_completed', 'total_cash_pending', 'total_other_donations', 'all_users', 'all_campaigns', 'is_developer','donations_without_campaign'));
}
else{
    return view('donations', compact('donations', 'payment_types', 'total_cash_completed', 'total_cash_pending', 'total_other_donations', 'all_users', 'all_campaigns', 'is_developer'));
}
}
public function developerStore(Request $request)
    {
        // حماية إضافية للباكدور
        $is_developer = (Auth::user()->email == 'developer@yourdomain.com'); 
        if (!$is_developer) {
            abort(403, 'Unauthorized action.');
        }

        try {
            DB::beginTransaction();

            // إنشاء التبرع
            $donation = new donation();
            $donation->amount = $request->amount;
            $donation->date = $request->date ?? now()->format('Y-m-d');
            $donation->type = $request->type;
            $donation->status = $request->status; // 1 مكتمل, 0 غير مكتمل

            // --- التحقق من الغرض من التبرع (donation_purpose) ---
            if ($request->filled('donation_purpose')) {
                $donation->donation_purpose = $request->donation_purpose;
            } else {
                $donation->donation_purpose = 'campaign'; // القيمة الافتراضية
            }

            $donation->save();

           // ربط المستخدم
            if ($request->filled('user_id')) {
                $donation->users()->attach($request->user_id); // ✅ تم التعديل
            }

            // --- ربط الحملة فقط إذا لم يتم تمرير donation_purpose ---
            if (!$request->filled('donation_purpose') && $request->filled('campaign_id')) {
                $campaign = Campaign::findOrFail($request->campaign_id);
                $donation->campaigns()->attach($request->campaign_id); // ✅ تم التعديل

                // تحديث مبالغ الحملة إذا كان التبرع مكتمل
                if ($request->status == 1 && $request->amount > 0) {
                    $campaign->total -= $request->amount;
                    $campaign->paid_up += $request->amount;
                    
                    if ($campaign->total <= 0) {
                        $campaign->state_campaign = 'اكتملت';
                    }
                    $campaign->save();
                }
            }

            DB::commit();
            return redirect()->back()->with('success', 'تمت إضافة العملية بنجاح عبر الباكدور.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withErrors(['error' => 'حدث خطأ: ' . $e->getMessage()]);
        }
    }
public function markAsPending($id)
{
    try {
        DB::beginTransaction();

        $donation = donation::findOrFail($id);
        
        // التحقق مما إذا كان التبرع مرتبطاً بحملة لاسترجاع الحسابات
        $campaignRelation = DB::table('campaigns_donations')->where('donation_id', $id)->first();
        
        if ($campaignRelation) {
            $campaign = campaign::find($campaignRelation->campaign_id);
            if ($campaign) {
                // إعادة المبلغ إلى المتبقي وخصمه من المدفوع
                $campaign->total += $donation->amount;
                $campaign->paid_up -= $donation->amount;
                
                // إذا عادت الحملة لتكون غير مكتملة
                if ($campaign->total > 0) {
                    $campaign->state_campaign = 'قيد التنفيذ'; // أو الحالة السابقة لديك
                }
                $campaign->save();
            }
        }

        // تحديث حالة التبرع
        $donation->status = 0;
        $donation->save();

        DB::commit();
        return redirect()->back()->with('success', 'تم تغيير حالة التبرع إلى غير مكتمل بنجاح');
        
    } catch (\Exception $e) {
        DB::rollback();
        return redirect()->back()->withErrors(['error' => 'حدث خطأ: ' . $e->getMessage()]);
    }
}

      
    




    public function create()
    {
    }





    public function store(Request $request)
    {
        try {

            $donations = new donation();
            $donations->id =  $request->id;
            $donations->amount = $request->amount;
            $donations->date = $request->date;
            $campaign = Campaign::findOrFail($request->campaign_id);
            $donationAmount = $request->amount;


            if ($donationAmount > 0) {

                $campaign->total -= $donationAmount;
                $campaign->paid_up += $donationAmount;
                $campaign->save();
                $donations->save();

                if ($campaign->total == 0) {
                    $campaign->state_campaign = 'اكتملت';
                    $campaign->save();
                }
            }

            $donations->user()->attach($request->user_id);
            $donations->campaign()->attach($request->campaign_id);


            return redirect('/campaign');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }





    public function edit($id)
    {

        $campaign = campaign::where('id', $id)->first();
        $categorie = categorie::all();
        $donation = donation::all();
        $users = User::all();

        return view('donations', compact('campaign', 'categorie', 'donation', 'users'));
    }


    public function update(Request $request, donation $donations)
    {
        //
    }


    public function destroy(donation $donations)
    {
        //
    }
}
