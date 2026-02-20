<?php

namespace App\Http\Controllers;

use App\Models\campaign;
use App\Models\categorie;
use App\Models\donation;
use App\Models\User;
use App\Models\user_donation;
use App\Models\campaign_donation;
use App\Http\Requests\StoreCampaignRequest;
use App\Http\Requests\UpdateCampaignRequest;


use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

use function Laravel\Prompts\select;

class CampaignController extends Controller
{


   

public function index()
{
    $categorie = categorie::latest()->get();
    
    // جلب الحملات مع التبرعات
    $campaigns = Campaign::with('donations')->latest()->get();

    foreach ($campaigns as $c) {
        // التعديل هنا: نستخدم where لفلترة التبرعات المكتملة فقط قبل الجمع
        // بافتراض أن رقم 1 يعني "مكتمل"
        $c->total_paid = $c->donations->where('status', 1)->sum('amount'); 
        
        // حساب المتبقي بناءً على المدفوع المكتمل فقط
        $c->remaining  = $c->total - $c->total_paid; 
        
        // (اختياري) لضمان عدم ظهور قيم سالبة إذا تجاوز التبرع المبلغ المطلوب
        if($c->remaining < 0) {
            $c->remaining = 0;
        }
    }

    return view('campaign', compact('campaigns', 'categorie'));
}
    public function create()
    {
        //
    }

   public function index2($id)
{
    // 1. جلب قائمة التبرعات
    $donations = DB::table('donations')
        ->join('campaigns_donations', 'donations.id', '=', 'campaigns_donations.donation_id')
        ->join('campaigns', 'campaigns_donations.campaign_id', '=', 'campaigns.id')
        ->join('users_donations', 'donations.id', '=', 'users_donations.donation_id')
        ->join('users', 'users_donations.user_id', '=', 'users.id')
        ->where('campaigns.id', $id)
        ->select('donations.*', 'users.name as username', 'campaigns.name as campaign_name', 'users.phone as phone')
        ->orderBy('donations.created_at', 'desc')
        ->get();

    // 2. حساب إجمالي النقدية "المكتملة" (Status = 1)
    $total_cash_completed = DB::table('donations')
        ->join('campaigns_donations', 'donations.id', '=', 'campaigns_donations.donation_id')
        ->where('campaigns_donations.campaign_id', $id)
        ->where('donations.type', 'نقدي')
        ->where('donations.status', 1) // شرط الاكتتمال
        ->sum('donations.amount');

    // 3. حساب إجمالي النقدية "غير المكتملة" (Status = 0)
    $total_cash_pending = DB::table('donations')
        ->join('campaigns_donations', 'donations.id', '=', 'campaigns_donations.donation_id')
        ->where('campaigns_donations.campaign_id', $id)
        ->where('donations.type', 'نقدي')
        ->where('donations.status', 0) // شرط عدم الاكتتمال
        ->sum('donations.amount');

    // 4. حساب إجمالي باقي القيم (المدفوعات غير النقدية - إلكتروني/شيك/إلخ)
    $total_other_donations = DB::table('donations')
        ->join('campaigns_donations', 'donations.id', '=', 'campaigns_donations.donation_id')
        ->where('campaigns_donations.campaign_id', $id)
        ->where('donations.type', '!=', 'نقدي')
        ->sum('donations.amount');

    // 5. إرسال البيانات إلى العرض
    return view('donations', compact('donations', 'total_cash_completed', 'total_cash_pending', 'total_other_donations'));
}



    public function store(StoreCampaignRequest $request)
    {


        $input = $request->all();

        // تحقق مما إذا كان total فارغًا
        if (empty($input['total'])) {
            $input['total'] = 0; // تعيين القيمة إلى 0
        }
    
        if ($image = $request->File('image')) {


            $destinationPath = 'public/img/';
            $profileimage = date('YmdHis') . "." . $image->getClientOriginalExtension();
            $image->move($destinationPath, $profileimage);
            $input['image'] = "$profileimage";
        }

        campaign::create($input);

        session()->flash('Add', 'تم اضافة البيانات بنجاح ');
        return redirect('/campaign');
    }



    public function show($id)
    {
    }



    public function edit(campaign $campaigns)
    {
        //
    }


    public function update(UpdateCampaignRequest $request, campaign $campaigns)
    {


        $id = categorie::where('name_category', $request->name_category)->first()->id;

        $input = campaign::findOrFail($request->id);

        $input->name = $request->name;
        $input->description = $request->description;
        $input->categorie_id = $id;
        $input->image = $request->image;
        $input->total = $request->total;
        $input->paid_up = $request->paid_up;
        $input->recipient = $request->recipient;
        $input->state_campaign = $request->state_campaign;




        if ($image = $request->File('image')) {


            $destinationPath = 'public/img/';
            $profileimage = date('YmdHis') . "." . $image->getClientOriginalExtension();
            $image->move($destinationPath, $profileimage);
            $input['image'] = "$profileimage";
        } else {
            unset($input['image']);
        }


        $input->save();

        session()->flash('edit', 'تم تعديل البيانات بنجاج');
        return redirect('/campaign');
    }


    public function destroy(Request $request, campaign $campaigns)
    {

        campaign::findOrFail($request->id)->delete();

        $campaigns->delete();
        session()->flash('delete', 'تم حذف البيانات بنجاج');
        return redirect('/campaign');
    }
}
