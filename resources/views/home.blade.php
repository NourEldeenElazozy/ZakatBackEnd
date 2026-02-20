@extends('layouts.master')
@section('title', 'لوحة التحكم - صندوق الزكاة')
@section('css')
    <link href="{{URL::asset('zakat/assets/plugins/owl-carousel/owl.carousel.css')}}" rel="stylesheet" />
    <link href="{{URL::asset('zakat/assets/plugins/jqvmap/jqvmap.min.css')}}" rel="stylesheet">
    <style>
        .card-icon { font-size: 30px; position: absolute; left: 20px; top: 20px; opacity: 0.4; }
    </style>
@endsection

@section('page-header')
    <div class="breadcrumb-header justify-content-between">
        <div class="left-content">
            <div>
                <h2 class="main-content-title tx-24 mg-b-1 mg-b-lg-1">مرحباً، {{ Auth::user()->name }}</h2>
                <p class="mg-b-0">أهلاً بك في لوحة تحكم نظام الزكاة.</p>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="row row-sm">
        <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
            <div class="card overflow-hidden sales-card bg-success-gradient">
                <div class="pl-3 pt-3 pr-3 pb-2 pt-0">
                    <div class="">
                        <h6 class="mb-3 tx-12 text-white">إجمالي التبرعات المحصلة</h6>
                    </div>
                    <div class="pb-0 mt-0">
                        <div class="d-flex">
                            <h4 class="tx-20 font-weight-bold mb-1 text-white">{{ number_format($total_money, 0) }} د.ل</h4>
                            <span class="float-right my-auto mr-auto">
                                <i class="fas fa-coins text-white card-icon"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
            <div class="card overflow-hidden sales-card bg-primary-gradient">
                <div class="pl-3 pt-3 pr-3 pb-2 pt-0">
                    <div class="">
                        <h6 class="mb-3 tx-12 text-white">إجمالي الحملات</h6>
                    </div>
                    <div class="pb-0 mt-0">
                        <div class="d-flex">
                            <h4 class="tx-20 font-weight-bold mb-1 text-white">{{ $countcampaign }}</h4>
                            <span class="float-right my-auto mr-auto">
                                <i class="fas fa-bullhorn text-white card-icon"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
            <div class="card overflow-hidden sales-card bg-danger-gradient">
                <div class="pl-3 pt-3 pr-3 pb-2 pt-0">
                    <div class="">
                        <h6 class="mb-3 tx-12 text-white">عدد المستخدمين</h6>
                    </div>
                    <div class="pb-0 mt-0">
                        <div class="d-flex">
                            <h4 class="tx-20 font-weight-bold mb-1 text-white">{{ $countUser }}</h4>
                            <span class="float-right my-auto mr-auto">
                                <i class="fas fa-users text-white card-icon"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
            <div class="card overflow-hidden sales-card bg-warning-gradient">
                <div class="pl-3 pt-3 pr-3 pb-2 pt-0">
                    <div class="">
                        <h6 class="mb-3 tx-12 text-white">عدد عمليات التبرع</h6>
                    </div>
                    <div class="pb-0 mt-0">
                        <div class="d-flex">
                            <h4 class="tx-20 font-weight-bold mb-1 text-white">{{ $countdonation }}</h4>
                            <span class="float-right my-auto mr-auto">
                                <i class="fas fa-hand-holding-heart text-white card-icon"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row row-sm">
        
        <div class="col-md-12 col-lg-12 col-xl-7">
            <div class="card">
                <div class="card-header bg-transparent pd-b-0 pd-t-20 bd-b-0">
                    <div class="d-flex justify-content-between">
                        <h4 class="card-title mb-0">إحصائيات الحملات</h4>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="campaignChart" height="130"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-12 col-xl-5">
            <div class="card card-dashboard-map-one">
                <label class="main-content-label">آخر التبرعات المستلمة</label>
                <span class="d-block mg-b-20 text-muted tx-12">قائمة بأحدث العمليات المالية التي دخلت النظام.</span>
                <div class="table-responsive">
                    <table class="table table-hover text-md-nowrap mb-0">
                        <thead>
                            <tr>
                                <th>المتبرع</th>
                                <th>الحملة</th>
                                <th>المبلغ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($latest_donations as $donation)
                            <tr>
                                <td>
                                    {{ $donation->users->first()->name ?? 'فاعل خير' }}
                                </td>
                                <td>
                                    {{ $donation->campaigns->first()->name ?? 'عام' }}
                                </td>
                                <td class="tx-success font-weight-bold">
                                    {{ number_format($donation->amount) }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted">لا توجد تبرعات حديثة</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endsection

@section('js')
    <script src="{{URL::asset('zakat/assets/plugins/chart.js/Chart.bundle.min.js')}}"></script>
    
    <script>
        // إعداد الرسم البياني لحالة الحملات
        var ctx = document.getElementById('campaignChart').getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'bar', // أو 'doughnut' أو 'pie'
            data: {
                labels: @json($chart_labels), // الأسماء (مستمرة، مكتملة، إلخ)
                datasets: [{
                    label: 'عدد الحملات',
                    data: @json($chart_data), // الأرقام
                    backgroundColor: [
                        '#28a745', // أخضر
                        '#007bff', // أزرق
                        '#f74f75', // أحمر
                        '#fd7e14'  // برتقالي
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true,
                            stepSize: 1
                        }
                    }]
                },
                legend: {
                    display: false // إخفاء العنوان العلوي في حالة الـ bar
                }
            }
        });
    </script>
    
    <script src="{{URL::asset('zakat/assets/js/index.js')}}"></script>
@endsection