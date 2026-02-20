@extends('layouts.master')
@section('title', 'الملخص العام')
@section('css')
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
<style>
    body { font-family: 'Cairo', sans-serif; background-color: #f4f6f9; }
    .card-box {
        background: #fff; padding: 25px; border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05); text-align: center;
        border-bottom: 4px solid transparent; transition: 0.3s;
    }
    .card-box:hover { transform: translateY(-5px); }
    .card-green { border-color: #28a745; }
    .card-blue { border-color: #007bff; }
    .card-red { border-color: #dc3545; }
    .card-orange { border-color: #fd7e14; }
    
    .icon-box { font-size: 40px; margin-bottom: 15px; }
    .stat-value { font-size: 28px; font-weight: bold; color: #333; }
    .stat-label { color: #666; font-size: 16px; }

    @media print {
        .no-print { display: none; }
        .card-box { border: 1px solid #ddd; box-shadow: none; }
    }
</style>
@endsection

@section('page-header')
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto">
        <h4 class="content-title mb-0 my-auto">لوحة المعلومات والملخص العام</h4>
    </div>
    <div class="d-flex my-xl-auto right-content">
        <button class="btn btn-primary no-print" onclick="window.print()"><i class="fas fa-print"></i> طباعة الملخص</button>
    </div>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-md-3">
        <div class="card-box card-green">
            <div class="icon-box text-success"><i class="fas fa-hand-holding-usd"></i></div>
            <div class="stat-value">{{ number_format($total_collected, 2) }}</div>
            <div class="stat-label">إجمالي التبرعات المحصلة</div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card-box card-orange">
            <div class="icon-box text-warning"><i class="fas fa-clock"></i></div>
            <div class="stat-value">{{ number_format($total_pending, 2) }}</div>
            <div class="stat-label">تبرعات قيد الانتظار</div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card-box card-blue">
            <div class="icon-box text-primary"><i class="fas fa-users"></i></div>
            <div class="stat-value">{{ $total_donors }}</div>
            <div class="stat-label">عدد المتبرعين</div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card-box card-red">
            <div class="icon-box text-danger"><i class="fas fa-check-circle"></i></div>
            <div class="stat-value">{{ $completed_campaigns }}</div>
            <div class="stat-label">حملات مكتملة</div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header pb-0"><h3 class="card-title mb-2">رسم بياني سريع</h3></div>
            <div class="card-body">
              <div class="row mt-4">
    
    <div class="col-lg-8 col-md-12">
        <div class="card">
            <div class="card-header pb-0">
                <h3 class="card-title mb-2">حركة التبرعات خلال الأشهر الماضية</h3>
            </div>
            <div class="card-body">
                <canvas id="barChart" height="120"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-4 col-md-12">
        <div class="card">
            <div class="card-header pb-0">
                <h3 class="card-title mb-2">نسبة التحصيل</h3>
            </div>
            <div class="card-body">
                <canvas id="pieChart" height="200"></canvas>
            </div>
        </div>
    </div>
</div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // إعدادات الخط (لتناسق الخط العربي)
    Chart.defaults.font.family = "'Cairo', sans-serif";

    // 1. إعداد Bar Chart (التبرعات الشهرية)
    var ctxBar = document.getElementById('barChart').getContext('2d');
    var barChart = new Chart(ctxBar, {
        type: 'bar',
        data: {
            // نستخدم json_encode لتحويل بيانات PHP إلى JavaScript
            labels: @json($labels), 
            datasets: [{
                label: 'إجمالي التبرعات (د.ل)',
                data: @json($data),
                backgroundColor: '#28a745', // لون أخضر
                borderColor: '#1e7e34',
                borderWidth: 1,
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: '#f0f0f0' }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });

    // 2. إعداد Doughnut Chart (المحصل vs المعلق)
    var ctxPie = document.getElementById('pieChart').getContext('2d');
    var pieChart = new Chart(ctxPie, {
        type: 'doughnut',
        data: {
            labels: ['تبرعات محصلة', 'قيد الانتظار'],
            datasets: [{
                data: [{{ $total_collected }}, {{ $total_pending }}],
                backgroundColor: [
                    '#28a745', // أخضر للمحصل
                    '#fd7e14'  // برتقالي للمعلق
                ],
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            cutout: '70%', // جعل الدائرة مفرغة من المنتصف
            plugins: {
                legend: {
                    position: 'bottom',
                }
            }
        }
    });
</script>
@endsection 