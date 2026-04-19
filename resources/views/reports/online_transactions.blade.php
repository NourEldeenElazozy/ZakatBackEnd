@extends('layouts.master')
@section('title', 'سجل مدفوعات معاملات')

@section('content')
<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">سجل مدفوعات معاملات (أونلاين)</h5>
           
        </div>
        <div class="card-body">
            <table class="table table-hover table-bordered text-center">
                <thead class="bg-light">
                    <tr>
                        <th>الاسم</th>
                        <th>عنوان المعاملة (الغرض)</th>
                        <th>القيمة (د.ل)</th>
                        <th>التاريخ</th>
                        <th>المعرف المرجعي</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transactions as $t)
                    <tr>
                        <td>{{ $t['name'] }}</td>
                        <td><span class="badge badge-info">{{ $t['title'] }}</span></td>
                        <td class="font-weight-bold text-success">{{ number_format($t['amount'], 2) }}</td>
                        <td>{{ $t['date'] }}</td>
                        <td><code>{{ $t['order_ref'] }}</code></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection