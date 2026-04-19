<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\campaign;
use App\Models\categorie;
use App\Models\donation;

class ReportController extends Controller
{
    /**
     * تقرير 1: لوحة المعلومات العامة (الإجماليات)
     */
    public function generalSummary()
    {
        // إجمالي التبرعات المحصلة (المكتملة فقط)
        $total_collected = donation::where('status', 1)->sum('amount');

        // إجمالي التبرعات المعلقة (غير المكتملة)
        $total_pending = donation::where('status', 0)->sum('amount');

        // عدد المتبرعين الفريدين
        $total_donors = DB::table('users_donations')->distinct('user_id')->count();

        // إجمالي الحملات المكتملة
        $completed_campaigns = campaign::where('state_campaign', 'اكتملت')->count();
$total_collected = donation::where('status', 1)->sum('amount');
    $total_pending = donation::where('status', 0)->sum('amount');
    $total_donors = DB::table('users_donations')->distinct('user_id')->count();
    $completed_campaigns = campaign::where('state_campaign', 'اكتملت')->count();

    // === إضافات للرسوم البيانية ===

    // 1. جلب التبرعات مجمعة حسب الشهور (لآخر 6 أشهر مثلاً)
    $monthly_stats = donation::select(
        DB::raw('SUM(amount) as total'),
        DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month") // تجميع حسب السنة-الشهر
    )
    ->where('status', 1) // فقط المحصل
    ->groupBy('month')
    ->orderBy('month', 'ASC')
    ->limit(6) // آخر 6 أشهر
    ->get();

    // تجهيز المصفوفات لإرسالها للرسم البياني
    $labels = $monthly_stats->pluck('month')->toArray();
    $data   = $monthly_stats->pluck('total')->toArray();
      return view('reports.summary', compact(
        'total_collected', 
        'total_pending',  
        'total_donors', 
        'completed_campaigns',
        'labels', // مصفوفة الشهور
        'data'    // مصفوفة المبالغ
    ));
    }

    /**
     * تقرير 2: تقرير الزكاة حسب التصنيف (الوجه الشرعي)
     * مثال: كم جمعنا لزكاة المال، وكم لزكاة الفطر، وكم للصدقات
     */
    public function donationsByCategory()
    {
        $categories = categorie::with(['campaign' => function($q) {
            $q->withSum(['donations' => function($d) {
                $d->where('status', 1); // فقط المدفوع
            }], 'amount');
        }])->get();

        // تجهيز البيانات للعرض
        $report_data = $categories->map(function($cat) {
            return [
                'category_name' => $cat->name_category,
                'total_campaigns' => $cat->campaign->count(),
                // نجمع تبرعات كل الحملات داخل هذا التصنيف
                'total_amount' => $cat->campaign->sum('donations_sum_amount')
            ];
        });

        return view('reports.by_category', compact('report_data'));
    }

    /**
     * تقرير 3: التقرير المالي حسب طريقة الدفع (نقدي / إلكتروني)
     */
    public function donationsByType()
    {
        $report = donation::where('status', 1)
            ->select('type', DB::raw('sum(amount) as total_amount'), DB::raw('count(*) as count'))
            ->groupBy('type')
            ->get();

        return view('reports.by_type', compact('report'));
    }

    /**
     * تقرير 4: تقرير تفصيلي خلال فترة زمنية (من - إلى)
     */
   public function dateRangeReport(Request $request)
    {
        // 1. تجهيز قوائم الفلترة (للعرض في الـ Select Box)
        $campaigns_list = \App\Models\campaign::select('id', 'name')->get();
        
        // 🌟 جلب أوجه التبرع الفريدة (تجاهل القيمة 'campaign' لأنها تعني أنه مرتبط بحملة مخصصة)
        $purposes_list = DB::table('donations')
            ->whereNotNull('donation_purpose')
            ->where('donation_purpose', '!=', 'campaign')
            ->select('donation_purpose')
            ->distinct()
            ->pluck('donation_purpose');
        
        // جلب المتبرعين الذين لديهم تبرعات فقط لتخفيف الحمل
        $donors_list = \App\Models\User::whereHas('donation')->select('id', 'name')->get();
        
        // أنواع الدفع المتاحة في النظام
        $payment_types = DB::table('donations')->select('type')->distinct()->pluck('type');

        // 2. بناء الاستعلام الأساسي
        $query = DB::table('donations')
            ->leftJoin('campaigns_donations', 'donations.id', '=', 'campaigns_donations.donation_id')
            ->leftJoin('campaigns', 'campaigns_donations.campaign_id', '=', 'campaigns.id')
            ->leftJoin('users_donations', 'donations.id', '=', 'users_donations.donation_id')
            ->leftJoin('users', 'users_donations.user_id', '=', 'users.id')
            ->where('donations.status', 1) // فقط المكتمل
            ->select(
                'donations.id',
                'donations.amount',
                'donations.created_at',
                'donations.type',
                'donations.donation_purpose',
                DB::raw('COALESCE(users.name, "فاعل خير / غير محدد") as donor_name'),
                DB::raw('COALESCE(users.phone, "غير متوفر") as donor_phone'),
                DB::raw('COALESCE(campaigns.name, donations.donation_purpose, "غير محدد") as campaign_name')
            );

        // 3. تطبيق الفلاتر (إذا تم اختيارها)
        
        // فلتر التاريخ
        if ($request->has('from_date') && $request->from_date != '') {
            $query->whereDate('donations.created_at', '>=', $request->from_date);
        }
        if ($request->has('to_date') && $request->to_date != '') {
            $query->whereDate('donations.created_at', '<=', $request->to_date);
        }

        // 🌟 فلتر الحملة أو وجه التبرع
        if ($request->has('campaign_id') && $request->campaign_id != '') {
            $val = $request->campaign_id;
            
            if ($val == 'general') {
                // تبرع عام (غير مرتبط بأي حملة)
                $query->whereNull('campaigns.id');
            } elseif (\Illuminate\Support\Str::startsWith($val, 'purpose_')) {
                // 🌟 إذا كانت القيمة تبدأ بـ purpose_، فهذا يعني أننا نبحث عن وجه تبرع (مثل: اخراج زكاة)
                $purposeName = str_replace('purpose_', '', $val);
                $query->where('donations.donation_purpose', $purposeName);
            } else {
                // بحث عادي برقم الحملة
                $query->where('campaigns.id', $val);
            }
        }

        // فلتر المتبرع
        if ($request->has('donor_id') && $request->donor_id != '') {
            $query->where('users.id', $request->donor_id);
        }

        // فلتر طريقة الدفع
        if ($request->has('payment_type') && $request->payment_type != '') {
            $query->where('donations.type', $request->payment_type);
        }

        // 4. حساب الإجمالي الكلي (قبل التقسيم)
        $total_in_period = $query->clone()->sum('donations.amount');

        // 5. التحقق من وضع الطباعة
        if ($request->has('print_mode')) {
            $donations = $query->orderBy('donations.created_at', 'desc')->get();
            $is_print_mode = true;
        } else {
            $donations = $query->orderBy('donations.created_at', 'desc')->paginate(20);
            $is_print_mode = false;
        }

        // 🌟 تمرير purposes_list للفيو
        return view('reports.date_range', compact(
            'donations', 
            'total_in_period', 
            'is_print_mode',
            'campaigns_list',
            'purposes_list',
            'donors_list',
            'payment_types'
        ));
    }
}