@extends('layouts.master')
@section('title', 'تقرير التبرعات حسب طريقة الدفع')

@section('css')
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
<style>
    body { font-family: 'Cairo', sans-serif; background-color: #f4f6f9; }
    .report-container {
        background: #fff; padding: 40px; border-radius: 8px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1); margin: 20px auto;
        max-width: 210mm; min-height: 297mm;
    }
    .report-header { border-bottom: 2px solid #007bff; margin-bottom: 25px; padding-bottom: 20px; text-align: center; }
    .custom-table th { background-color: #007bff; color: white; text-align: center; padding: 15px; }
    .custom-table td { text-align: center; padding: 12px; font-size: 16px; }
    
    @media print {
        .no-print, header, footer, .sidebar { display: none !important; }
        .report-container { position: absolute; top: 0; left: 0; width: 100%; margin: 0; box-shadow: none; }
    }
</style>
@endsection

@section('content')
<div class="container mt-4">
    <div class="text-left mb-3 no-print">
        <button onclick="window.print()" class="btn btn-outline-primary"><i class="fas fa-print"></i> طباعة</button>
    </div>

    <div class="report-container">
        <div class="report-header">
            <h3>تقرير الإيرادات حسب طريقة الدفع</h3>
            <p class="text-muted">تاريخ التقرير: {{ date('Y-m-d') }}</p>
        </div>

        <table class="table table-bordered table-striped custom-table">
            <thead>
                <tr>
                    <th>طريقة الدفع (النوع)</th>
                    <th>عدد العمليات</th>
                    <th>إجمالي المبلغ</th>
                    <th>نسبة المساهمة</th>
                </tr>
            </thead>
            <tbody>
                @php $grand_total = $report->sum('total_amount'); @endphp
                
                @foreach($report as $row)
                <tr>
                    <td class="font-weight-bold">{{ $row->type }}</td>
                    <td>{{ $row->count }}</td>
                    <td class="text-success font-weight-bold">{{ number_format($row->total_amount, 2) }}</td>
                    <td>
                        @if($grand_total > 0)
                            {{ round(($row->total_amount / $grand_total) * 100, 1) }}%
                        @else
                            0%
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="table-dark text-white">
                    <td>الإجمالي الكلي</td>
                    <td>{{ $report->sum('count') }}</td>
                    <td>{{ number_format($grand_total, 2) }}</td>
                    <td>100%</td>
                </tr>
            </tfoot>
        </table>

        <div class="mt-5">
            <h6>ملاحظات:</h6>
            <ul class="text-muted small">
                <li>المبالغ المعروضة هي للمبالغ المؤكدة فقط (Status = 1).</li>
                <li>يتم حساب النسبة المئوية بناءً على إجمالي الإيرادات.</li>
            </ul>
        </div>
    </div>
</div>
@endsection