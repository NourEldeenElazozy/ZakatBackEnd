@extends('layouts.master')

@section('content')
<br>
<div class="container">
    <h2>كروت الشحن</h2>
    <a href="{{ route('recharge-cards.create') }}" class="btn btn-success mb-3">إنشاء كروت جديدة</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>رقم الكارت</th>
                <th>القيمة</th>
                <th>الحالة</th>
                <th>تاريخ الإنشاء</th>
            </tr>
        </thead>
        <tbody>
            @foreach($cards as $card)
                <tr>
                    <td>{{ $card->code }}</td>
                    <td>{{ $card->value }} $</td>
                    <td>{{ $card->used ? 'مستخدم' : 'متاح' }}</td>
                    <td>{{ $card->created_at->format('Y-m-d') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

   {{ $cards->links('pagination::bootstrap-5') }}

</div>
<form action="{{ route('recharge-cards.export') }}" method="GET" class="mb-3">
    <label>تصدير حسب القيمة:</label>
    <select name="value" class="form-control" style="width:200px; display:inline-block;">
        <option value="">الكل</option>
        <option value="5">5 د.ل</option>
        <option value="10">10 د.ل</option>
        <option value="20">20 د.ل</option>
        <option value="50">50 د.ل</option>
        <option value="100">100 د.ل</option>
        <option value="200">200 د.ل</option>
        <option value="500">500 د.ل</option>
        <option value="1000">1000 د.ل</option>
        
    </select>
    <button class="btn btn-info">تصدير Excel</button>
</form>

@endsection
