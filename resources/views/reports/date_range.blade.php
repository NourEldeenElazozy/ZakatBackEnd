@extends('layouts.master')

@section('title')
تقرير التبرعات الشامل
@stop

@section('css')
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
<link href="{{URL::asset('zakat/assets/plugins/select2/css/select2.min.css')}}" rel="stylesheet">

<style>
    /* تنسيقات عامة للصفحة */
    body { font-family: 'Cairo', sans-serif; background-color: #f4f6f9; }
    
    .report-container {
        background: #fff; padding: 40px; border-radius: 8px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1); margin: 20px auto;
        max-width: 210mm; min-height: 297mm;
    }

    .report-header { border-bottom: 2px solid #28a745; margin-bottom: 25px; padding-bottom: 20px; display: flex; justify-content: space-between; align-items: center; }
    
    /* تنسيق الجدول */
    .custom-table { width: 100%; font-size: 14px; }
    .custom-table thead { background-color: #28a745; color: #fff; }
    .custom-table th, .custom-table td { padding: 10px; border: 1px solid #dee2e6; text-align: center; vertical-align: middle; }
    
    /* === تعديل صف الإجمالي (اللون البرتقالي) === */
    .total-row td {
        background-color: #09467b !important; /* لون برتقالي */
        color: #fff !important;               /* نص أبيض */
        font-weight: 800;                     /* خط سميك */
        font-size: 1.2em;                     /* حجم أكبر */
        border-top: 3px solid #333;           /* خط فاصل علوي */
    }
    
    /* تنسيق الباجنيشن الأخضر */
    .page-item.active .page-link { background-color: #28a745 !important; border-color: #28a745 !important; color: #fff !important; }
    .page-link { color: #28a745 !important; }

    .signatures { margin-top: 60px; display: flex; justify-content: space-between; padding: 0 40px; }
    .signature-line { margin-top: 40px; border-top: 1px solid #333; }

    /* إعدادات الطباعة */
    @media print {
        body * { visibility: hidden; }
        .no-print, .filter-box, .pagination-container { display: none !important; }
        
        .report-container, .report-container * { visibility: visible; }
        .report-container { position: absolute; left: 0; top: 0; width: 100%; margin: 0; padding: 0; box-shadow: none; border: none; }
        
        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        /* === إجبار لون الإجمالي على الظهور في الطباعة === */
        .total-row td {
            background-color: #09467b !important;
            color: #ffffff !important;
            box-shadow: inset 0 0 0 1000px #09467b !important; /* حيلة الظل لضمان الطباعة */
            border-top: 2px solid #000 !important;
        }
    }
</style>
@endsection

@section('content')

<div class="container mt-4">

    @if(!$is_print_mode)
    <div class="filter-box no-print p-4 mb-4 bg-white rounded shadow-sm border-top border-primary">
        <h5 class="mb-3 text-primary"><i class="fas fa-filter"></i> بحث وتصفية متقدم</h5>
        
        <form action="{{ route('reports.detailed') }}" method="GET">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label class="font-weight-bold">من تاريخ:</label>
                    <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="font-weight-bold">إلى تاريخ:</label>
                    <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                </div>

              <div class="col-md-3 mb-3">
                    <label class="font-weight-bold">الحملة / وجه التبرع:</label>
                    <select name="campaign_id" class="form-control select2">
                        <option value="">-- الكل --</option>
                        
                        <option value="general" {{ request('campaign_id') == 'general' ? 'selected' : '' }}>
                            تبرع عام (كل التبرعات غير المرتبطة بحملة)
                        </option>

                        <optgroup label="الحملات المخصصة">
                            @foreach($campaigns_list as $cmp)
                                <option value="{{ $cmp->id }}" {{ request('campaign_id') == $cmp->id ? 'selected' : '' }}>
                                    {{ $cmp->name }}
                                </option>
                            @endforeach
                        </optgroup>

                        @if($purposes_list->count() > 0)
                        <optgroup label="أوجه التبرع">
                            @foreach($purposes_list as $purpose)
                                <option value="purpose_{{ $purpose }}" {{ request('campaign_id') == 'purpose_'.$purpose ? 'selected' : '' }}>
                                    {{ $purpose }}
                                </option>
                            @endforeach
                        </optgroup>
                        @endif

                    </select>
                </div>
                
                <div class="col-md-3 mb-3">
                    <label class="font-weight-bold">المتبرع:</label>
                    <select name="donor_id" class="form-control select2">
                        <option value="">-- جميع المتبرعين --</option>
                        @foreach($donors_list as $donor)
                            <option value="{{ $donor->id }}" {{ request('donor_id') == $donor->id ? 'selected' : '' }}>
                                {{ $donor->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 mb-3">
                    <label class="font-weight-bold">طريقة الدفع:</label>
                    <select name="payment_type" class="form-control">
                        <option value="">-- الكل --</option>
                        @foreach($payment_types as $type)
                            <option value="{{ $type }}" {{ request('payment_type') == $type ? 'selected' : '' }}>
                                {{ $type }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-9 mb-3 d-flex align-items-end justify-content-end">
                    <button type="submit" class="btn btn-primary mr-2">
                        <i class="fas fa-search"></i> بحث
                    </button>
                    
                    <a href="{{ route('reports.detailed') }}" class="btn btn-secondary mr-2">
                        <i class="fas fa-undo"></i> إعادة تعيين
                    </a>

                    <a href="{{ request()->fullUrlWithQuery(['print_mode' => 1]) }}" target="_blank" class="btn btn-success">
                        <i class="fas fa-print"></i> طباعة التقرير كاملاً
                    </a>
                </div>
            </div>
        </form>
    </div>
    @endif

    <div class="report-container">
        <div class="report-header">
            <div class="logo"><h3>نظام الزكاة</h3></div>
            <div class="report-title text-center">
                <h2>كشف حركة التبرعات</h2>
                
                <div class="mt-2 text-muted" style="font-size: 14px;">
                    @if(request('campaign_id'))
                        @if(request('campaign_id') == 'general')
                            <span class="badge badge-light border ml-1">التصنيف: تبرع عام</span>
                        @elseif(\Illuminate\Support\Str::startsWith(request('campaign_id'), 'purpose_'))
                            <span class="badge badge-light border ml-1">وجه التبرع: {{ str_replace('purpose_', '', request('campaign_id')) }}</span>
                        @else
                            <span class="badge badge-light border ml-1">حملة: {{ $campaigns_list->where('id', request('campaign_id'))->first()->name ?? '' }}</span>
                        @endif
                    @endif
                    
                    @if(request('donor_id'))
                        <span class="badge badge-light border ml-1">المتبرع: {{ $donors_list->where('id', request('donor_id'))->first()->name ?? '' }}</span>
                    @endif

                    @if(request('payment_type'))
                        <span class="badge badge-light border ml-1">نوع الدفع: {{ request('payment_type') }}</span>
                    @endif

                    @if(request('from_date')) 
                        <span class="badge badge-light border ml-1">من: {{ request('from_date') }}</span>
                    @endif
                    
                    @if(request('to_date')) 
                        <span class="badge badge-light border ml-1">إلى: {{ request('to_date') }}</span>
                    @endif
                </div>
            </div>

            <div class="report-meta">
                <p>تاريخ: {{ date('Y-m-d') }}</p>
                <p>العدد: {{ count($donations) }}</p>
            </div>
        </div>
<div class="table-responsive">
    <table class="table table-bordered custom-table">
        <thead>
            <tr>
                <th>#</th>
                <th>المتبرع</th>
                     <th>رقم الهاتف</th>
                <th>الحملة</th>
                <th>النوع</th>
                <th>التاريخ</th>
                <th>المبلغ</th>
            </tr>
        </thead>
        <tbody>
            @forelse($donations as $key => $d)
            <tr>
                <td>
                    @if($is_print_mode) {{ $loop->iteration }} @else {{ ($donations->currentPage() - 1) * $donations->perPage() + $loop->iteration }} @endif
                </td>
                <td>{{ $d->donor_name }}</td>
                <td>{{ $d->donor_phone }}</td>
                <td>{{ $d->campaign_name }}</td>
                <td>
                    <span class="badge badge-{{ $d->type == 'نقدي' ? 'success' : 'info' }}">
                        {{ $d->type }}
                    </span>
                </td>
                <td>{{ \Carbon\Carbon::parse($d->created_at)->format('Y-m-d') }}</td>
                <td class="font-weight-bold">{{ number_format($d->amount, 2) }}</td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-center py-4">لا توجد بيانات تطابق شروط البحث</td></tr>
            @endforelse

            <tr class="total-row">
                <td colspan="5" class="text-left pl-4">الإجمالي النهائي</td>
                <td>{{ number_format($total_in_period, 2) }} د.ل</td>
            </tr>
            
        </tbody>
        
        </table>

    @if(!$is_print_mode)
    <div class="mt-4 d-flex justify-content-center no-print pagination-container">
       {!! $donations->withQueryString()->links('pagination::bootstrap-4') !!}
    </div>
    @endif
</div>

        <div class="signatures">
            <div class="signature-box"><p><strong>رئيس قسم الجبايه والصرف</strong></p><div class="signature-line"></div></div>
            <div class="signature-box"><p><strong>مدير الصندوق</strong></p><div class="signature-line"></div></div>
        </div>
    </div>
</div>

@endsection

@section('js')
<script src="{{URL::asset('zakat/assets/plugins/select2/js/select2.min.js')}}"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2();
    });

    @if(isset($is_print_mode) && $is_print_mode)
        window.onload = function() { window.print(); }
    @endif
</script>
@endsection