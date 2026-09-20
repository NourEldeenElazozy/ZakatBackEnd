@extends('layouts.master')

@section('title', 'إضافة إنجاز جديد')

@section('content')
<div class="container-fluid" dir="rtl">
    <h4 class="mb-3">إضافة إنجاز جديد</h4>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form action="{{ route('achievements.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label class="form-label">العنوان (اختياري)</label>
                    <input type="text" name="title" class="form-control" value="{{ old('title') }}">
                </div>

                <div class="mb-3">
                    <label class="form-label">الوصف (اختياري)</label>
                    <textarea name="description" rows="4" class="form-control">{{ old('description') }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">الصورة</label>
                    <input type="file" name="image" accept="image/*" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">الترتيب</label>
                    <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', 0) }}">
                </div>

                <div class="form-check mb-3">
                    <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active" checked>
                    <label class="form-check-label" for="is_active">مفعّل ويظهر في التطبيق</label>
                </div>

                <button type="submit" class="btn btn-success">حفظ</button>
                <a href="{{ route('achievements.index') }}" class="btn btn-secondary">إلغاء</a>
            </form>
        </div>
    </div>
</div>
@endsection
