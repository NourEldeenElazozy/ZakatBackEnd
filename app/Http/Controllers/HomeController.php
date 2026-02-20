<?php

namespace App\Http\Controllers;

use App\Models\campaign;
use App\Models\categorie;
use App\Models\donation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // 1. البطاقات الرئيسية
        $countcampaign = campaign::count();
        $countUser = User::count();
        $countdonation = donation::count();
        
        // إجمالي الأموال (المكتملة فقط) - أهم رقم
        $total_money = donation::where('status', 1)->sum('amount');

        // 2. بيانات الرسم البياني (حالة الحملات)
        // مثال: كم حملة مستمرة وكم مكتملة
        $campaign_stats = campaign::select('state_campaign', DB::raw('count(*) as total'))
            ->groupBy('state_campaign')
            ->pluck('total', 'state_campaign')->all();
            
        // تحويل البيانات لتناسب ChartJS
        $chart_labels = array_keys($campaign_stats);
        $chart_data = array_values($campaign_stats);

        // 3. آخر 5 تبرعات (لعرضها في الجدول)
        // تأكد من وجود العلاقات في الموديل (campaign, users)
        $latest_donations = donation::with(['campaigns', 'users'])
                            ->where('status', 1)
                            ->latest()
                            ->take(6)
                            ->get();

        return view('home', compact(
            'countcampaign',
            'countUser',
            'countdonation',
            'total_money',
            'chart_labels',
            'chart_data',
            'latest_donations'
        ));
    }
}