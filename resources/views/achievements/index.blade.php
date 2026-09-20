{{--
    عدّلوا @extends لاسم اللاي أوت الفعلي المستخدم في لوحة التحكم عندكم
    (مثال: layouts.admin أو adminlte::page). الكلاسات هنا بستايل Bootstrap
    الافتراضي، عدّلوها إن كنتم تستخدمون إطار عمل آخر.
--}}
@extends('layouts.master')

@section('title', 'إنجازات صندوق الزكاة')

@section('content')
<div class="container-fluid" dir="rtl">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">إنجازات صندوق الزكاة</h4>
        <a href="{{ route('achievements.create') }}" class="btn btn-success">
            + إضافة إنجاز جديد
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="table-responsive">
            <table class="table table-bordered align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 100px">الصورة</th>
                        <th>العنوان</th>
                        <th style="width: 90px">الترتيب</th>
                        <th style="width: 90px">مفعّل</th>
                        <th style="width: 160px">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($achievements as $achievement)
                        <tr>
                          <td>
    <img src="{{ asset('zakat/storage/' . $achievement->image_path) }}"
         alt="{{ $achievement->title }}"
         style="width: 70px; height: 70px; object-fit: cover; border-radius: 8px;">
</td>
                            <td>{{ $achievement->title ?? '—' }}</td>
                            <td>{{ $achievement->sort_order }}</td>
                            <td>
                                @if ($achievement->is_active)
                                    <span class="badge bg-success">مفعّل</span>
                                @else
                                    <span class="badge bg-secondary">معطّل</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('achievements.edit', $achievement) }}"
                                   class="btn btn-sm btn-primary">تعديل</a>
                                <form action="{{ route('achievements.destroy', $achievement) }}"
                                      method="POST" class="d-inline"
                                      onsubmit="return confirm('هل أنت متأكد من الحذف؟');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">حذف</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">لا توجد إنجازات مضافة بعد.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $achievements->links() }}
    </div>
</div>
@endsection
