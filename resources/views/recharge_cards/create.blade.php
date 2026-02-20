<!-- resources/views/recharge_cards/create.blade.php -->
@extends('layouts.master')

@section('content')
<div class="container">
    <h2>إنشاء كروت شحن</h2>
    <form action="{{ route('recharge-cards.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label>قيمة الكارت</label>
            <input type="number" name="value" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>عدد الكروت</label>
            <input type="number" name="quantity" class="form-control" value="1" min="1" max="100" required>
        </div>
        <button class="btn btn-primary">إنشاء كروت</button>
    </form>
</div>
@endsection
