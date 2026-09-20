@extends('layouts.master')

@section('title', 'تعديل إنجاز')

@section('content')
<div class="container-fluid" dir="rtl">
    <h4 class="mb-3">تعديل الإنجاز</h4>

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
            <img src="{{ Storage::disk('public')->url($achievement->image_path) }}"
                 alt="{{ $achievement->title }}"
                 style="width: 160px; height: 160px; object-fit: cover; border-radius: 8px;"
                 class="mb-3">

            <form action="{{ route('achievements.update', $achievement) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">العنوان (اختياري)</label>
                    <input type="text" name="title" class="form-control" value="{{ old('title', $achievement->title) }}">
                </div>

                <div class="mb-3">
                    <label class="form-label">الوصف (اختياري)</label>
                    <textarea name="description" rows="4" class="form-control">{{ old('description', $achievement->description) }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">استبدال الصورة (اختياري)</label>
                    <input type="file" name="image" accept="image/*" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">الترتيب</label>
                    <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $achievement->sort_order) }}">
                </div>

                <div class="form-check mb-3">
                    <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active"
                           {{ old('is_active', $achievement->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">مفعّل ويظهر في التطبيق</label>
                </div>

                <button type="submit" class="btn btn-success">حفظ التعديلات</button>
                <a href="{{ route('achievements.index') }}" class="btn btn-secondary">إلغاء</a>
            </form>
        </div>
    </div>
</div>
@endsection
