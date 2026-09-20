@extends('layouts.master')
@section('title', 'إرسال الإشعارات')

@section('css')
<style>
/* ===== User Search Styles ===== */
.notif-card {
    border-radius: 12px;
    box-shadow: 0 2px 16px rgba(0,0,0,.07);
    border: none;
}
.notif-tab-btn {
    padding: 10px 28px;
    border-radius: 8px 8px 0 0;
    font-weight: 600;
    font-size: 14px;
    color: #6c757d;
    background: #f0f2f5;
    border: 1px solid #dee2e6;
    border-bottom: none;
    cursor: pointer;
    transition: all .2s;
}
.notif-tab-btn.active {
    background: #fff;
    color: #6259ca;
    border-color: #dee2e6;
    border-bottom-color: #fff;
    position: relative;
    z-index: 1;
}

/* Search Box */
.user-search-wrapper { position: relative; }
.user-search-wrapper input {
    border-radius: 8px;
    padding-right: 40px;
    border: 1px solid #dee2e6;
    transition: border-color .2s;
}
.user-search-wrapper input:focus {
    border-color: #6259ca;
    box-shadow: 0 0 0 3px rgba(98,89,202,.1);
}
.user-search-wrapper .search-icon {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #aaa;
    pointer-events: none;
}
.search-results-box {
    border: 1px solid #dee2e6;
    border-radius: 8px;
    max-height: 220px;
    overflow-y: auto;
    background: #fff;
    box-shadow: 0 4px 16px rgba(0,0,0,.1);
}
.search-result-item {
    padding: 10px 14px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 10px;
    border-bottom: 1px solid #f0f2f5;
    transition: background .15s;
}
.search-result-item:last-child { border-bottom: none; }
.search-result-item:hover { background: #f0f0fb; }
.search-result-item .user-avatar {
    width: 34px; height: 34px;
    border-radius: 50%;
    background: #6259ca;
    color: #fff;
    font-weight: 700;
    font-size: 13px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.search-result-item .user-details .user-name { font-weight: 600; font-size: 13px; }
.search-result-item .user-details .user-sub { font-size: 11px; color: #888; }

/* Selected Users */
.selected-users-area {
    min-height: 54px;
    border: 2px dashed #dee2e6;
    border-radius: 8px;
    padding: 8px;
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    align-content: flex-start;
    transition: border-color .2s;
    background: #fafbff;
}
.selected-users-area:has(.user-chip) { border-style: solid; border-color: #c5c0f0; }
.selected-users-area.empty-hint { align-items: center; justify-content: center; }

.user-chip {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #6259ca;
    color: #fff;
    border-radius: 20px;
    padding: 5px 12px 5px 8px;
    font-size: 12px;
    font-weight: 500;
    animation: chipIn .2s ease;
}
@keyframes chipIn {
    from { transform: scale(.8); opacity: 0; }
    to   { transform: scale(1);  opacity: 1; }
}
.user-chip .chip-avatar {
    width: 22px; height: 22px;
    border-radius: 50%;
    background: rgba(255,255,255,.3);
    display: flex; align-items: center; justify-content: center;
    font-size: 10px; font-weight: 700;
}
.user-chip .chip-remove {
    width: 18px; height: 18px;
    border-radius: 50%;
    background: rgba(255,255,255,.25);
    border: none;
    color: #fff;
    font-size: 12px;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer;
    padding: 0;
    line-height: 1;
    transition: background .15s;
}
.user-chip .chip-remove:hover { background: rgba(255,255,255,.45); }

.selected-count-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    background: #6259ca;
    color: #fff;
    border-radius: 20px;
    padding: 3px 12px;
    font-size: 12px;
    font-weight: 600;
}
.btn-clear-all {
    font-size: 11px;
    color: #dc3545;
    cursor: pointer;
    text-decoration: underline;
    background: none;
    border: none;
    padding: 0;
}

/* Alert warning */
.alert-warning { background: #fff8e1; border-color: #ffc107; color: #856404; }
</style>
@endsection

@section('page-header')
<div class="breadcrumb-header justify-content-between">
    <div class="left-content">
        <h2 class="main-content-title tx-24 mg-b-1">إرسال الإشعارات</h2>
        <p class="mg-b-0">إرسال إشعارات Firebase لمستخدمين محددين أو للجميع</p>
    </div>
</div>
@endsection

@section('content')
<div class="row">
    <!-- تم التعديل هنا: استخدام col-12 ليأخذ حجم الصفحة بالكامل -->
    <div class="col-12">

        {{-- Alerts --}}
        @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            <strong><i class="fa fa-exclamation-circle ml-1"></i> يوجد أخطاء:</strong>
            <ul class="mb-0 mt-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            <i class="fa fa-check-circle ml-1"></i> {{ session('success') }}
        </div>
        @endif

        @if (session('warning'))
        <div class="alert alert-warning alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            <i class="fa fa-exclamation-triangle ml-1"></i> {{ session('warning') }}
        </div>
        @endif

        @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            <i class="fa fa-times-circle ml-1"></i> {{ session('error') }}
        </div>
        @endif

        {{-- Tabs --}}
        <div class="d-flex mb-0" style="border-bottom: 1px solid #dee2e6;">
            <button class="notif-tab-btn active" onclick="switchTab('multiple', this)">
                <i class="fa fa-users ml-1"></i> مستخدمون محددون
            </button>
            <button class="notif-tab-btn mr-1" onclick="switchTab('all', this)">
                <i class="fa fa-globe ml-1"></i> إرسال للجميع
            </button>
        </div>

        {{-- ======= TAB 1: Multiple Users ======= --}}
        <div id="tab-multiple" class="card notif-card" style="border-radius: 0 12px 12px 12px;">
            <div class="card-body p-4">
                <form action="{{ route('notifications.sendMultiple') }}" method="POST" id="multiForm">
                    @csrf

                    {{-- Search --}}
                    <div class="form-group mb-3">
                        <label class="font-weight-semibold">
                            <i class="fa fa-search text-primary ml-1"></i>
                            ابحث عن مستخدم وحدده
                        </label>
                        <div class="user-search-wrapper">
                            <i class="fa fa-search search-icon"></i>
                            <input type="text" id="userSearchInput" class="form-control"
                                placeholder="ابحث بالاسم، الهاتف، أو البريد الإلكتروني..."
                                autocomplete="off">
                        </div>
                        {{-- نتائج البحث --}}
                        <div id="searchResults" class="search-results-box mt-1" style="display:none;"></div>
                    </div>

                    {{-- المستخدمون المحددون --}}
                    <div class="form-group mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="font-weight-semibold mb-0">
                                <i class="fa fa-user-check text-success ml-1"></i>
                                المستخدمون المحددون
                                <span id="selectedCount" class="selected-count-badge mr-2" style="display:none;">0</span>
                            </label>
                            <button type="button" class="btn-clear-all" id="clearAllBtn" onclick="clearAll()" style="display:none;">
                                مسح الكل
                            </button>
                        </div>

                        <div class="selected-users-area empty-hint" id="selectedUsersArea">
                            <span class="text-muted" style="font-size:13px;" id="emptyHint">
                                <i class="fa fa-arrow-up ml-1"></i> ابحث واختر المستخدمين
                            </span>
                        </div>

                        {{-- Hidden inputs for user_ids --}}
                        <div id="hiddenUserIds"></div>
                    </div>

                    <hr class="my-3">

                    {{-- العنوان --}}
                    <div class="form-group">
                        <label class="font-weight-semibold">
                            <i class="fa fa-heading text-primary ml-1"></i> عنوان الإشعار
                        </label>
                        <input type="text" name="title" class="form-control" required
                            placeholder="مثال: تم قبول تبرعك" value="{{ old('title') }}">
                    </div>

                    {{-- النص --}}
                    <div class="form-group">
                        <label class="font-weight-semibold">
                            <i class="fa fa-align-right text-primary ml-1"></i> نص الإشعار
                        </label>
                        <textarea name="body" class="form-control" rows="3" required
                            placeholder="اكتب نص الإشعار هنا...">{{ old('body') }}</textarea>
                    </div>

                    <div class="d-flex align-items-center justify-content-between mt-3">
                        <span class="text-muted" style="font-size:12px;">
                            <i class="fa fa-info-circle ml-1"></i>
                            يظهر فقط المستخدمون الذين لديهم رمز جهاز (device token)
                        </span>
                        <button type="submit" class="btn btn-primary px-4" id="submitMultiBtn">
                            <i class="fa fa-paper-plane ml-1"></i> إرسال الإشعار
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ======= TAB 2: All Users ======= --}}
        <div id="tab-all" class="card notif-card" style="display:none; border-radius: 0 12px 12px 12px;">
            <div class="card-body p-4">
                <form action="{{ route('notifications.send') }}" method="POST">
                    @csrf
                    <input type="hidden" name="topic" value="all">

                    <div class="alert alert-warning mb-4">
                        <i class="fa fa-exclamation-triangle ml-1"></i>
                        سيتم إرسال هذا الإشعار لجميع المستخدمين المشتركين في التطبيق.
                    </div>

                    <div class="form-group">
                        <label class="font-weight-semibold">
                            <i class="fa fa-heading text-primary ml-1"></i> عنوان الإشعار
                        </label>
                        <input type="text" name="title" class="form-control" required
                            placeholder="مثال: تحديث جديد في التطبيق">
                    </div>

                    <div class="form-group">
                        <label class="font-weight-semibold">
                            <i class="fa fa-align-right text-primary ml-1"></i> نص الإشعار
                        </label>
                        <textarea name="body" class="form-control" rows="3" required
                            placeholder="اكتب نص الإشعار هنا..."></textarea>
                    </div>

                    <div class="text-left mt-3">
                        <button type="submit" class="btn btn-success px-4"
                            onclick="return confirm('هل تريد إرسال الإشعار لجميع المستخدمين؟')">
                            <i class="fa fa-globe ml-1"></i> إرسال للجميع
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- إحصائية المستخدمين --}}
        <div class="card mt-3 notif-card">
            <div class="card-body py-3 px-4">
                <div class="d-flex align-items-center gap-3">
                    <i class="fa fa-mobile-alt text-primary fa-lg"></i>
                    <div>
                        <span class="font-weight-semibold">{{ $users->count() }}</span>
                        <span class="text-muted mr-1" style="font-size:13px;">مستخدم لديهم رمز جهاز ويمكن إرسال إشعارات لهم</span>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@section('js')
<script>
// ========================
// Tab Switching
// ========================
function switchTab(tab, btn) {
    document.getElementById('tab-multiple').style.display = tab === 'multiple' ? '' : 'none';
    document.getElementById('tab-all').style.display      = tab === 'all'      ? '' : 'none';
    document.querySelectorAll('.notif-tab-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
}

// ========================
// Selected Users State
// ========================
const selectedUsers = new Map(); // id → { id, name, phone }

function addUser(user) {
    if (selectedUsers.has(user.id)) return; // لا تضف مرتين
    selectedUsers.set(user.id, user);
    renderChips();
    renderHiddenInputs();
}

function removeUser(id) {
    selectedUsers.delete(id);
    renderChips();
    renderHiddenInputs();
}

function clearAll() {
    selectedUsers.clear();
    renderChips();
    renderHiddenInputs();
}

function getInitial(name) {
    return name ? name.trim().charAt(0).toUpperCase() : '?';
}

function renderChips() {
    const area      = document.getElementById('selectedUsersArea');
    const countBadge = document.getElementById('selectedCount');
    const clearBtn  = document.getElementById('clearAllBtn');
    const emptyHint = document.getElementById('emptyHint');
    const count     = selectedUsers.size;

    // إزالة الـ chips القديمة
    area.querySelectorAll('.user-chip').forEach(c => c.remove());

    if (count === 0) {
        area.classList.add('empty-hint');
        if (emptyHint) emptyHint.style.display = '';
        countBadge.style.display = 'none';
        clearBtn.style.display   = 'none';
        return;
    }

    area.classList.remove('empty-hint');
    if (emptyHint) emptyHint.style.display = 'none';
    countBadge.textContent  = count;
    countBadge.style.display = 'inline-flex';
    clearBtn.style.display   = '';

    selectedUsers.forEach((user, id) => {
        const chip = document.createElement('div');
        chip.className = 'user-chip';
        chip.innerHTML = `
            <div class="chip-avatar">${getInitial(user.name)}</div>
            <span>${user.name}</span>
            ${user.phone ? `<small style="opacity:.75">${user.phone}</small>` : ''}
            <button type="button" class="chip-remove" onclick="removeUser(${id})" title="إزالة">×</button>
        `;
        area.appendChild(chip);
    });
}

function renderHiddenInputs() {
    const container = document.getElementById('hiddenUserIds');
    container.innerHTML = '';
    selectedUsers.forEach((_, id) => {
        const input = document.createElement('input');
        input.type  = 'hidden';
        input.name  = 'user_ids[]';
        input.value = id;
        container.appendChild(input);
    });
}

// ========================
// Search (AJAX)
// ========================
let searchTimeout = null;
const searchInput   = document.getElementById('userSearchInput');
const resultsBox    = document.getElementById('searchResults');
const searchUrl     = '{{ route("notifications.searchUsers") }}';

searchInput.addEventListener('input', function () {
    clearTimeout(searchTimeout);
    const q = this.value.trim();

    if (q.length < 1) {
        resultsBox.style.display = 'none';
        resultsBox.innerHTML     = '';
        return;
    }

    searchTimeout = setTimeout(() => {
        fetch(`${searchUrl}?q=${encodeURIComponent(q)}`)
            .then(r => r.json())
            .then(users => renderSearchResults(users, q))
            .catch(() => {
                resultsBox.innerHTML     = '<div class="p-3 text-danger text-center">حدث خطأ في البحث</div>';
                resultsBox.style.display = '';
            });
    }, 280);
});

// أغلق نتائج البحث عند النقر خارجها
document.addEventListener('click', function (e) {
    if (!searchInput.contains(e.target) && !resultsBox.contains(e.target)) {
        resultsBox.style.display = 'none';
    }
});

function renderSearchResults(users, q) {
    resultsBox.innerHTML = '';

    if (users.length === 0) {
        resultsBox.innerHTML = `<div class="p-3 text-center text-muted" style="font-size:13px;">
            <i class="fa fa-search ml-1"></i> لا توجد نتائج لـ "<strong>${q}</strong>"
        </div>`;
        resultsBox.style.display = '';
        return;
    }

    users.forEach(user => {
        const isSelected = selectedUsers.has(user.id);
        const item = document.createElement('div');
        item.className = 'search-result-item' + (isSelected ? ' bg-light' : '');
        item.innerHTML = `
            <div class="user-avatar">${getInitial(user.name)}</div>
            <div class="user-details flex-grow-1">
                <div class="user-name">${user.name}</div>
                <div class="user-sub">${user.phone || ''} ${user.phone && user.email ? '·' : ''} ${user.email || ''}</div>
            </div>
            ${isSelected
                ? '<span class="badge badge-success"><i class="fa fa-check"></i> محدد</span>'
                : '<span class="badge badge-light text-primary border">+ إضافة</span>'
            }
        `;
        if (!isSelected) {
            item.addEventListener('click', () => {
                addUser(user);
                renderSearchResults(users, q); // تحديث القائمة لإظهار "محدد"
            });
        }
        resultsBox.appendChild(item);
    });

    resultsBox.style.display = '';
}

// ========================
// Form Validation
// ========================
document.getElementById('multiForm').addEventListener('submit', function (e) {
    if (selectedUsers.size === 0) {
        e.preventDefault();
        // أظهر رسالة خطأ
        const existing = document.getElementById('noUserAlert');
        if (!existing) {
            const alert = document.createElement('div');
            alert.id = 'noUserAlert';
            alert.className = 'alert alert-danger mt-2';
            alert.innerHTML = '<i class="fa fa-exclamation-circle ml-1"></i> يجب اختيار مستخدم واحد على الأقل من البحث.';
            document.getElementById('selectedUsersArea').parentNode.appendChild(alert);
            setTimeout(() => alert.remove(), 4000);
        }
        return false;
    }

    const btn = document.getElementById('submitMultiBtn');
    btn.disabled   = true;
    btn.innerHTML  = '<i class="fa fa-spinner fa-spin ml-1"></i> جاري الإرسال...';
});

// تهيئة أولية
renderChips();
</script>
@endsection