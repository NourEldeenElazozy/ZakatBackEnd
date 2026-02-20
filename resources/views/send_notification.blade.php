@extends('layouts.master')
@section('title')
الإشعارات
@stop

@section('content')
<div class="container py-5">
    <h1 class="text-center mb-4">إرسال إشعار عبر Firebase</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('notifications.send') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="notification_target_type">الوجهة:</label>
            <select name="notification_target_type" id="notification_target_type" class="form-control">
                <option value="all">الجميع (موضوع: all)</option>
                <option value="specific_user">مستخدم محدد</option>
                {{-- إذا كان لديك مواضيع أخرى غير 'all' وتريد إظهارها، أضفها هنا: --}}
                {{-- <option value="news_topic">أخبار</option> --}}
            </select>
        </div>

        {{-- حقل اختيار المستخدم (يظهر فقط عند اختيار "مستخدم محدد") --}}
        <div class="form-group" id="specific_user_select_group" style="display: none;">
            <label for="user_id">اختر المستخدم:</label>
            <select name="user_id" id="user_id" class="form-control">
                <option value="">-- اختر مستخدمًا --</option>
                @foreach($users as $user)
                    {{-- تأكد أن المستخدم لديه device_token قبل إظهاره كخيار --}}
                    @if($user->device_token)
                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                    @endif
                @endforeach
            </select>
            <small class="form-text text-muted">سيتم إرسال الإشعار إلى رمز الجهاز الخاص بالمستخدم المحدد.</small>
        </div>

        {{-- حقل مخفي لتمرير قيمة التوبيك (إذا لم يتم اختيار مستخدم) --}}
        {{-- سنرسل 'all' هنا عندما لا يتم اختيار مستخدم، أو اسم توبيك آخر إذا أضفته كخيار في الكومبوبوكس --}}
        <input type="hidden" name="topic" id="hidden_topic" value="all">


        <div class="form-group">
            <label for="title">العنوان</label>
            <input type="text" name="title" id="title" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="body">النص</label>
            <textarea name="body" id="body" class="form-control" rows="4" required></textarea>
        </div>

        <button type="submit" class="btn btn-primary mt-3">إرسال الإشعار</button>
    </form>
</div>
@endsection

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const notificationTargetType = document.getElementById('notification_target_type');
        const specificUserSelectGroup = document.getElementById('specific_user_select_group');
        const userIdSelect = document.getElementById('user_id'); // عنصر الكومبوبوكس للمستخدمين
        const hiddenTopic = document.getElementById('hidden_topic'); // الحقل المخفي للتوبيك

        // دالة لتحديث عرض الحقول
        function updateVisibility() {
            if (notificationTargetType.value === 'specific_user') {
                specificUserSelectGroup.style.display = 'block'; // إظهار كومبوبوكس المستخدمين
                userIdSelect.setAttribute('required', 'required'); // اجعل اختيار المستخدم مطلوبًا
                hiddenTopic.value = ''; // لا يوجد توبيك عند اختيار مستخدم محدد
            } else {
                specificUserSelectGroup.style.display = 'none'; // إخفاء كومبوبوكس المستخدمين
                userIdSelect.removeAttribute('required'); // ليس مطلوبا
                userIdSelect.value = ''; // مسح اختيار المستخدم
                hiddenTopic.value = notificationTargetType.value; // تعيين التوبيك (سيكون 'all_users' أو أي توبيك آخر)
            }
        }

        // استدعاء الدالة عند تحميل الصفحة للتأكد من الحالة الأولية
        updateVisibility();

        // إضافة مستمع للتغييرات في الكومبوبوكس الرئيسي (الوجهة)
        notificationTargetType.addEventListener('change', updateVisibility);
    });
</script>
@stop