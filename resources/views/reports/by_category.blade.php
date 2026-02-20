@extends('layouts.master')

@section('title')
تقرير إيرادات الزكاة
@stop

@section('css')
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">

<style>
    /* 1. تنسيقات عامة للصفحة */
    body {
        font-family: 'Cairo', sans-serif;
        background-color: #f4f6f9;
    }

    .report-container {
        background: #fff;
        padding: 40px;
        border-radius: 8px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        margin: 30px auto;
        max-width: 210mm; /* عرض ورقة A4 */
        min-height: 297mm; /* طول ورقة A4 */
    }

    /* 2. تنسيق رأس التقرير */
    .report-header {
        border-bottom: 2px solid #28a745;
        margin-bottom: 30px;
        padding-bottom: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .report-title h2 {
        font-weight: 700;
        color: #333;
        margin: 0;
    }

    .report-meta {
        text-align: left;
        font-size: 14px;
        color: #666;
    }

    /* 3. تنسيق الجدول */
    .custom-table thead {
        background-color: #28a745;
        color: #fff;
    }
    
    .custom-table th, .custom-table td {
        vertical-align: middle;
        text-align: center;
        padding: 12px;
        border-color: #dee2e6;
    }

    .custom-table tbody tr:nth-of-type(odd) {
        background-color: rgba(40, 167, 69, 0.05);
    }

    /* 4. تنسيق صف الإجمالي (للشاشة) */
    .total-row td {
        background-color: #0b2994 !important; /* لون برتقالي */
        color: #ffffff !important;
        font-weight: 800;
        font-size: 1.2em;
        border-top: 3px solid #333;
    }

    /* 5. منطقة التوقيعات */
    .signatures {
        margin-top: 80px;
        display: flex;
        justify-content: space-between;
        padding: 0 50px;
    }
    .signature-box {
        text-align: center;
        width: 200px;
    }
    .signature-line {
        margin-top: 50px;
        border-top: 1px solid #333;
    }

    /* --- 6. إعدادات الطباعة (A4) --- */
    @media print {
        /* إخفاء عناصر الموقع */
        body * { visibility: hidden; }
        .no-print, header, footer, .sidebar, .navbar { display: none !important; }
        
        /* إظهار التقرير */
        .report-container, .report-container * { visibility: visible; }

        /* ضبط الموضع */
        .report-container {
            position: absolute; left: 0; top: 0; margin: 0; padding: 20px;
            box-shadow: none; width: 100%; border: none;
        }

        /* ضمان طباعة الألوان */
        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        /* ========================================== */
        /* الحل النهائي لظهور لون الإجمالي في الطباعة */
        /* ========================================== */
        .total-row td {
            background-color: #0b19b3 !important;
            color: #ffffff !important;
            /* الحيلة: استخدام الظل الداخلي ليحل محل الخلفية */
            box-shadow: inset 0 0 0 1000px #0b19b3 !important;
            border-top: 2px solid #000 !important;
        }
    }
</style>
@endsection

@section('content')

<div class="container mt-4 mb-2 no-print">
    <div class="d-flex justify-content-end">
        <button onclick="window.print()" class="btn btn-outline-primary btn-lg">
            <i class="fas fa-print"></i> طباعة التقرير
        </button>
    </div>
</div>

<div class="report-container">
    
    <div class="report-header">
        <div class="logo">
            <h3>نظام الزكاة</h3> 
        
        </div>
        
        <div class="report-title text-center">
            <h2>تقرير إيرادات الزكاة حسب التصنيف</h2>
            <p class="mb-0 text-muted">ملخص الأداء المالي للحملات</p>
        </div>

        <div class="report-meta">
            <p><strong>تاريخ التقرير:</strong> {{ date('Y-m-d') }}</p>
            <p><strong>الوقت:</strong> {{ date('H:i A') }}</p>
            <p><strong>المستخدم:</strong> {{ auth()->user()->name ?? 'مسؤول النظام' }}</p>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered custom-table">
            <thead>
                <tr>
                    <th width="40%">وجه الصرف (التصنيف)</th>
                    <th width="20%">عدد الحملات</th>
                    <th width="40%">إجمالي التبرعات المحصلة</th>
                </tr>
            </thead>
            <tbody>
                @forelse($report_data as $row)
                <tr>
                    <td class="text-right font-weight-bold">{{ $row['category_name'] }}</td>
                    <td>
                        <span class="badge badge-light" style="font-size: 14px; border: 1px solid #ddd;">
                            {{ $row['total_campaigns'] }}
                        </span>
                    </td>
                    <td class="font-weight-bold text-success">
                        {{ number_format($row['total_amount'], 2) }} <small>د.ل</small>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="text-center py-4 text-muted">لا توجد بيانات متاحة لعرضها</td>
                </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="2" class="text-left pl-4">الإجمالي الكلي للإيرادات</td>
                    <td>{{ number_format($report_data->sum('total_amount'), 2) }} د.ل</td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="mt-4 p-3" style="background: #f9f9f9; border: 1px dashed #ccc;">
        <h6 class="font-weight-bold">ملاحظات:</h6>
        <ul class="mb-0 text-muted small">
            <li>هذا التقرير يشمل المبالغ التي تم تحصيلها وتأكيدها فقط .</li>
            <li>الأرقام بالدينار الليبي (د.ل).</li>
        </ul>
    </div>

    <div class="signatures">
        
        <div class="signature-box">
            <p><strong>مدير الصندوق</strong></p>
            <div class="signature-line"></div>
        </div>
    </div>

</div>
@endsection

@section('js')
<script>
    // يمكنك إضافة أي أكواد تفاعلية هنا إذا لزم الأمر
</script>
@endsection