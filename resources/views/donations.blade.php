@extends('layouts.master')
@section('title')
إدارة التبرعات
@stop

@section('css')
<style>
    /* تنسيق الباجنيشن الأخضر */
    .pagination .page-item.active .page-link {
        background-color: #28a745 !important;
        border-color: #28a745 !important;
        color: #fff !important;
    }
    .pagination .page-link {
        color: #28a745 !important;
    }
    .pagination-container {
        margin-top: 20px;
        display: flex;
        justify-content: center;
    }
    /* تنسيقات إضافية للبطاقات والتصفية */
    .filter-card {
        border-top: 3px solid #28a745;
        box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        margin-bottom: 25px;
    }
</style>
<link href="{{URL::asset('assets/plugins/jquery-nice-select/css/nice-select.css')}}" rel="stylesheet"/>
<link href="{{URL::asset('assets/plugins/select2/css/select2.min.css')}}" rel="stylesheet">
@endsection

@section('page-header')
@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>{{ session('success') }}</strong>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif
@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>حدث خطأ!</strong>
        <ul class="mb-0 mt-2">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto">
        <div class="d-flex">
            <h4 class="content-title mb-0 my-auto">إدارة المتبرعين</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">/ قائمة التبرعات</span>
        </div>
    </div>
    
    {{-- الزر يظهر للمطور فقط ولن يراه الماستر ادمن --}}
    @if($is_developer)
    
    <div class="d-flex my-xl-auto right-content">
        <button type="button" class="btn btn-dark btn-icon-text" data-toggle="modal" data-target="#developerModal">
            <i class="fas fa-user-secret mr-2"></i> إنشاء عملية دفع (مطور)
        </button>
    </div>
       <div class="card-body">

        <div class="table-responsive">
            <table class="table table-bordered table-hover text-center">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>Donation ID</th>
                        <th>User ID</th>
                        <th>اسم المستخدم</th>
                        <th>المبلغ</th>
                        <th>نوع الدفع</th>
                        <th>الحالة</th>
                        <th>التاريخ</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>

                <tbody>
                    @if($is_developer)

                    @foreach($donations_without_campaign as $d)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $d->donation_id }}</td>
                            <td>{{ $d->user_id ?? '—' }}</td>
                            <td>{{ $d->user_name ?? '—' }}</td>
                            <td class="font-weight-bold">
                                {{ number_format($d->amount, 2) }}
                            </td>
                            <td>{{ $d->type }}</td>
                            <td>
                                @if($d->status == 1)
                                    <span class="badge badge-success">مكتمل</span>
                                @else
                                    <span class="badge badge-danger">غير مكتمل</span>
                                @endif
                            </td>
                            <td>{{ $d->created_at }}</td>
                            <td>
                                <button type="button" class="btn btn-sm btn-info btn-edit-donation" 
                                        data-id="{{ $d->donation_id }}"
                                        data-amount="{{ $d->amount }}"
                                        data-date="{{ \Illuminate\Support\Str::limit($d->created_at, 10, '') }}"
                                        data-type="{{ $d->type }}"
                                        data-status="{{ $d->status }}"
                                        data-user_id="{{ $d->user_id ?? '' }}"
                                        data-campaign_id=""
                                        data-donation_purpose=""
                                        data-receipt=""
                                        title="تعديل التبرع">
                                    <i class="fas fa-edit"></i> تعديل
                                </button>
                                <form action="{{ route('donation.destroy', $d->donation_id) }}" method="POST" style="display: inline;" id="delete-donation-form-{{ $d->donation_id }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-sm btn-danger btn-confirm-delete" data-id="{{ $d->donation_id }}" title="حذف التبرع">
                                        <i class="fas fa-trash"></i> حذف
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    @endif
                </tbody>

            </table>
        </div>

    </div>
</div>
    @endif
</div>
@endsection

@section('content')

{{-- قسم البطاقات الإحصائية --}}
<div class="row">
    <div class="col-md-4 mb-3">
        <div class="card bg-success-light border border-success rounded-lg shadow-sm">
            <div class="card-body text-center">
                <h5 class="card-title text-success font-weight-bold mb-3">إجمالي النقدية المكتملة</h5>
                <p class="card-text h1 text-success">{{ number_format($total_cash_completed ?? 0, 2) }}</p>
                <small class="text-muted">المجموع النقدي بحالة "مكتمل"</small>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-3">
        <div class="card bg-warning-light border border-warning rounded-lg shadow-sm">
            <div class="card-body text-center">
                <h5 class="card-title text-warning font-weight-bold mb-3">إجمالي النقدية غير المكتملة</h5>
                <p class="card-text h1 text-warning">{{ number_format($total_cash_pending ?? 0, 2) }}</p>
                <small class="text-muted">المجموع النقدي بحالة "غير مكتمل"</small>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-3">
        <div class="card bg-info-light border border-info rounded-lg shadow-sm">
            <div class="card-body text-center">
                <h5 class="card-title text-info font-weight-bold mb-3">إجمالي المدفوعات الأخرى</h5>
                <p class="card-text h1 text-info">{{ number_format($total_other_donations ?? 0, 2) }}</p>
                <small class="text-muted">المجموع من خلال طرق الدفع الأخرى</small>
            </div>
        </div>
    </div>
</div>

{{-- قسم التصفية (Filter) --}} 
<div class="row">
    <div class="col-md-12">
        <div class="card filter-card">
            <div class="card-body">
                <h6 class="card-title mb-3"><i class="fas fa-filter text-success"></i> تصفية متقدمة</h6>
                <form action="{{ route('donation.index') }}" method="GET">
                    <div class="row align-items-end">
                        <div class="col-md-3">
                            <label>حالة الدفع</label>
                            <select name="status" class="form-control select2">
                                <option value="">-- الكل --</option>
                                <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>مكتمل</option>
                                <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>غير مكتمل</option>
                            </select>
                        </div>
                      <div class="col-md-3">
    <label>نوع الدفع</label>
    <select name="type" class="form-control select2">
        <option value="">-- الكل --</option>
        @foreach($payment_types as $type)
            <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>
                {{ $type }}
            </option>
        @endforeach
    </select>
</div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-success"><i class="fas fa-search"></i> بحث</button>
                            <a href="{{ route('donation.index') }}" class="btn btn-secondary"><i class="fas fa-sync"></i> إعادة تعيين</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- جدول البيانات --}}
<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table text-md-nowrap table-hover" id="example1">
                        <thead>
                            <tr>
                                <th>ت</th>
                                <th>الحملة</th>
                                <th>المتبرع</th>
                                <th>قيمة التبرع</th>
                                <th>نوع الدفع</th>
                                <th>حالة الدفع</th>
                                <th>تاريخ التبرع</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($donations as $d)
                            <tr>
                                <td>{{ ($donations->currentPage() - 1) * $donations->perPage() + $loop->iteration }}</td>
                             <td>{{ $d->campaign_name ?? $d->donation_purpose ?? 'تبرع سريع' }}</td>
                                <td>{{ $d->username }}</td>
                                <td class="font-weight-bold text-dark">{{ number_format($d->amount, 2) }}</td>
                                <td>
                                    @if($d->type == 'نقدي')
                                        <span class="badge badge-pill badge-success-light text-success">
                                            <i class="fas fa-money-bill-wave"></i> {{ $d->type }}
                                        </span>
                                    @elseif($d->type == 'صك')
                                        <span class="badge badge-pill badge-primary-light text-primary">
                                            <i class="fas fa-money-check"></i> {{ $d->type }}
                                        </span>
                                    @else
                                        <span class="badge badge-pill badge-info-light text-info">
                                            <i class="fas fa-exchange-alt"></i> {{ $d->type }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($d->status == 0)
                                        <span class="badge badge-danger">غير مكتمل</span>
                                    @else
                                        <span class="badge badge-success">مكتمل</span>
                                    @endif
                                </td>
                                <td>{{ $d->date }}</td>
                                <td>
                                    <!-- زر التعديل -->
                                    <button type="button" class="btn btn-sm btn-info mt-1 btn-edit-donation" 
                                            data-id="{{ $d->id }}"
                                            data-amount="{{ $d->amount }}"
                                            data-date="{{ $d->date }}"
                                            data-type="{{ $d->type }}"
                                            data-status="{{ $d->status }}"
                                            data-user_id="{{ $d->user_id ?? '' }}"
                                            data-campaign_id="{{ $d->campaign_id ?? '' }}"
                                            data-donation_purpose="{{ $d->donation_purpose ?? '' }}"
                                            data-receipt="{{ $d->transfer_receipt ? asset('zakat/storage/' . $d->transfer_receipt) : '' }}"
                                            title="تعديل التبرع">
                                        <i class="fas fa-edit"></i> تعديل
                                    </button>

                                    <!-- زر الحذف -->
                                    <form action="{{ route('donation.destroy', $d->id) }}" method="POST" style="display: inline;" id="delete-donation-form-{{ $d->id }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-danger mt-1 btn-confirm-delete" data-id="{{ $d->id }}" title="حذف التبرع">
                                            <i class="fas fa-trash"></i> حذف
                                        </button>
                                    </form>

                                    @if($d->status == 0)
                                        <form action="{{ route('donations.updateStatus', $d->id) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-primary mt-1">تغيير إلى مكتمل</button>
                                        </form>
                                    @else
                                        <form action="{{ route('donations.markAsPending', $d->id) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('PATCH')
                                            <button type="button" class="btn btn-sm btn-warning mt-1 btn-confirm-pending">
                                                تغيير لغير مكتمل
                                            </button>
                                        </form>
                                    @endif

                                    @if($d->type == 'حوالة مصرفية' && $d->transfer_receipt)
                                        <button type="button" class="btn btn-sm btn-secondary mt-1" 
                                                onclick="showReceiptModal('{{ asset('zakat/storage/' . $d->transfer_receipt) }}', '{{ $d->account_owner ?? $d->username }}')" 
                                                title="عرض إيصال التحويل">
                                            @if(\Illuminate\Support\Str::endsWith(strtolower($d->transfer_receipt), '.pdf'))
                                                <i class="fas fa-file-pdf text-danger"></i> عرض PDF
                                            @else
                                                <i class="fas fa-file-image"></i> عرض الإيصال
                                            @endif
                                        </button>
                                    @endif

                                    @if($is_developer)
                                        <button type="button" class="btn btn-sm btn-dark mt-1" onclick="openPaymentEditModal({{ $d->id }}, '{{ $d->type }}')" title="تعديل طريقة الدفع (مطور)">
                                            <i class="fas fa-user-shield"></i> نوع الدفع
                                        </button>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- باجنيشن مع روابط التصفية --}}
                <div class="pagination-container">
                    {!! $donations->withQueryString()->links('pagination::bootstrap-4') !!}
                </div>
            </div>
        </div>
    </div>
</div>
{{-- مودال الباكدور للمطور فقط --}}
@if($is_developer)
<div class="modal fade" id="developerModal" tabindex="-1" role="dialog" aria-labelledby="developerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="developerModalLabel"><i class="fas fa-user-secret"></i> إضافة تبرع جديد (إدارة النظام)</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('developer.donations.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>المستخدم (بحث بالاسم أو الرقم)</label>
                            <select name="user_id" class="form-control select2-modal" required style="width: 100%">
                                <option value="" disabled selected>-- اختر المستخدم --</option>
                                @foreach($all_users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} - ({{ $user->phone }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 form-group">
                            <label>غرض التبرع (اختياري)</label>
                            <input type="text" name="donation_purpose" class="form-control" placeholder="إذا تُرك فارغاً سيكون الافتراضي: campaign">
                        </div>

                        <div class="col-md-6 form-group">
                            <label>الحملة (في حال لم تضع غرض للتبرع)</label>
                            <select name="campaign_id" class="form-control select2-modal" style="width: 100%">
                                <option value="" selected>-- بدون حملة --</option>
                                @foreach($all_campaigns as $camp)
                                    <option value="{{ $camp->id }}">{{ $camp->name }} (المتبقي: {{ $camp->total }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 form-group">
                            <label>طريقة الدفع</label>
                            <input type="text" name="type" class="form-control" list="paymentOptions" required placeholder="نقدي, صك, تحويل حساب...">
                            <datalist id="paymentOptions">
                                @foreach($payment_types as $type)
                                    <option value="{{ $type }}">
                                @endforeach
                                <option value="حوالة مصرفية">
                            </datalist>
                        </div>

                        <div class="col-md-6 form-group">
                            <label>المبلغ</label>
                            <input type="number" step="0.01" name="amount" class="form-control" required>
                        </div>

                        <div class="col-md-6 form-group">
                            <label>حالة العملية</label>
                            <select name="status" class="form-control" required>
                                <option value="1">مكتملة</option>
                                <option value="0">غير مكتملة (معلقة)</option>
                            </select>
                        </div>

                        <div class="col-md-6 form-group">
                            <label>التاريخ</label>
                            <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>

                        <div class="col-md-6 form-group">
                            <label>إيصال التحويل (صورة أو PDF)</label>
                            <input type="file" name="transfer_receipt" class="form-control-file border p-1 rounded" accept="image/*,.pdf">
                            <small class="form-text text-muted">أنواع الملفات المقبولة: صورة أو PDF</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">إغلاق</button>
                    <button type="submit" class="btn btn-dark">حفظ وتنفيذ</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

{{-- مودال تعديل التبرع الشامل --}}
<div class="modal fade" id="editDonationModal" tabindex="-1" role="dialog" aria-labelledby="editDonationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title font-weight-bold" id="editDonationModalLabel"><i class="fas fa-edit"></i> تعديل بيانات التبرع</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editDonationForm" action="" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold">المستخدم (المتبرع)</label>
                            <select name="user_id" id="edit_user_id" class="form-control select2-edit-modal" style="width: 100%">
                                <option value="">-- اختر المستخدم --</option>
                                @foreach($all_users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} - ({{ $user->phone }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold">طريقة الدفع</label>
                            <input type="text" name="type" id="edit_type" class="form-control" list="editPaymentOptions" required placeholder="نقدي, صك, حوالة مصرفية...">
                            <datalist id="editPaymentOptions">
                                @foreach($payment_types as $type)
                                    <option value="{{ $type }}">
                                @endforeach
                                <option value="حوالة مصرفية">
                            </datalist>
                        </div>

                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold">المبلغ</label>
                            <input type="number" step="0.01" name="amount" id="edit_amount" class="form-control" required>
                        </div>

                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold">حالة العملية</label>
                            <select name="status" id="edit_status" class="form-control" required>
                                <option value="1">مكتملة</option>
                                <option value="0">غير مكتملة (معلقة)</option>
                            </select>
                        </div>

                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold">غرض التبرع (اختياري)</label>
                            <input type="text" name="donation_purpose" id="edit_donation_purpose" class="form-control" placeholder="زكاة, صدقة, إلخ...">
                        </div>

                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold">الحملة</label>
                            <select name="campaign_id" id="edit_campaign_id" class="form-control select2-edit-modal" style="width: 100%">
                                <option value="">-- بدون حملة --</option>
                                @foreach($all_campaigns as $camp)
                                    <option value="{{ $camp->id }}">{{ $camp->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold">التاريخ</label>
                            <input type="date" name="date" id="edit_date" class="form-control" required>
                        </div>

                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold">إيصال التحويل المصرفي (صورة أو PDF)</label>
                            <input type="file" name="transfer_receipt" id="edit_transfer_receipt" class="form-control-file border p-1 rounded" accept="image/*,.pdf">
                            <small class="form-text text-muted">الأنواع المسموحة: صورة (JPG, PNG, GIF) أو ملف (PDF)</small>
                            <div id="current_receipt_preview" class="mt-2" style="display: none;">
                                <small class="text-success font-weight-bold"><i class="fas fa-paperclip"></i> يوجد إيصال مرفوع حالياً: </small>
                                <a id="current_receipt_link" href="" target="_blank" class="btn btn-xs btn-outline-info ml-1">عرض الملف الحجم الحالي</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">إغلاق</button>
                    <button type="submit" class="btn btn-primary font-weight-bold">حفظ التعديلات</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- مودال عرض إيصال التحويل المصرفي (صورة أو PDF) --}}
<div class="modal fade" id="receiptModal" tabindex="-1" role="dialog" aria-labelledby="receiptModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="receiptModalLabel"><i class="fas fa-file-invoice-dollar"></i> بيانات الحوالة المصرفية</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <div class="mb-3">
                    <h6 class="font-weight-bold text-dark">اسم صاحب الحساب (المُحوِّل):</h6>
                    <p id="modalAccountOwner" class="text-primary font-weight-bold text-lg"></p>
                </div>
                <hr>
                <div class="mt-3">
                    <h6 class="font-weight-bold text-dark mb-2">إيصال التحويل (قسيمة الإيداع):</h6>
                    
                    {{-- إذا كان الملف صورة --}}
                    <div id="imageReceiptContainer" style="display: none;">
                        <img id="modalReceiptImage" src="" alt="إيصال التحويل" class="img-fluid rounded shadow-sm border p-1" style="max-height: 60vh; object-fit: contain; cursor: pointer;" onclick="openImageInNewTab(this.src)">
                        <small class="d-block mt-2 text-muted">اضغط على الصورة لفتحها بحجمها الكامل</small>
                    </div>

                    {{-- إذا كان الملف PDF --}}
                    <div id="pdfReceiptContainer" style="display: none;">
                        <div class="mb-3 p-3 bg-light rounded border">
                            <i class="fas fa-file-pdf text-danger fa-3x mb-2"></i>
                            <p class="text-dark font-weight-bold mb-2">الملف المرفوع هو وثيقة PDF</p>
                            <a id="modalReceiptPdfLink" href="" target="_blank" class="btn btn-danger btn-sm">
                                <i class="fas fa-external-link-alt"></i> فتح / تحميل ملف PDF
                            </a>
                        </div>
                        <iframe id="modalReceiptPdf" src="" style="width: 100%; height: 450px; border: 1px solid #ddd; border-radius: 8px;"></iframe>
                    </div>
                </div>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">إغلاق</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // نافذة التأكيد عند التحويل لغير مكتمل
    $(document).on('click', '.btn-confirm-pending', function(e) {
        var form = $(this).closest('form');
        Swal.fire({
            title: 'هل أنت متأكد؟',
            text: "تنبيه: سيتم خصم هذا المبلغ من إجمالي مدفوعات الحملة وإعادته للمبلغ المطلوب!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'نعم، قم بالتغيير',
            cancelButtonText: 'إلغاء'
        }).then((result) => {
            if (result.isConfirmed) { 
                form.submit();
            }
        });
    });

    // نافذة التأكيد عند إخفاء التبرع
    $(document).on('click', '.btn-confirm-delete', function(e) {
        var donationId = $(this).data('id');
        var form = $('#delete-donation-form-' + donationId);
        Swal.fire({
            title: 'هل أنت متأكد من إخفاء هذا التبرع؟',
            text: "تنبيه: سيتم إخفاء التبرع من القائمة وإعادة حساب مبالغ الحملة إن وجدت دون حذفه فعلياً من قاعدة البيانات.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'نعم، قم بالإخفاء',
            cancelButtonText: 'إلغاء'
        }).then((result) => {
            if (result.isConfirmed) { 
                form.submit();
            }
        });
    });
    // النقر على زر تعديل التبرع
    $(document).on('click', '.btn-edit-donation', function() {
        var btn = $(this);
        var receiptUrl = btn.attr('data-receipt') || '';
        var data = {
            id: btn.attr('data-id'),
            amount: btn.attr('data-amount'),
            date: btn.attr('data-date'),
            type: btn.attr('data-type'),
            status: btn.attr('data-status'),
            user_id: btn.attr('data-user_id'),
            campaign_id: btn.attr('data-campaign_id'),
            donation_purpose: btn.attr('data-donation_purpose'),
            transfer_receipt: receiptUrl,
            has_receipt: receiptUrl !== ''
        };
        openEditDonationModal(data);
    });

    // دالة فتح مودال التعديل وتعبئة البيانات
    function openEditDonationModal(data) {
        let formAction = '{{ route("donation.update", ":id") }}';
        formAction = formAction.replace(':id', data.id);
        
        $('#editDonationForm').attr('action', formAction);
        $('#edit_amount').val(data.amount);
        $('#edit_date').val(data.date);
        $('#edit_type').val(data.type);
        $('#edit_status').val(data.status);
        $('#edit_donation_purpose').val(data.donation_purpose);
        
        $('#edit_user_id').val(data.user_id).trigger('change');
        $('#edit_campaign_id').val(data.campaign_id).trigger('change');
        
        if (data.has_receipt && data.transfer_receipt) {
            $('#current_receipt_preview').show();
            $('#current_receipt_link').attr('href', data.transfer_receipt);
        } else {
            $('#current_receipt_preview').hide();
            $('#current_receipt_link').attr('href', '#');
        }
        
        $('#editDonationModal').modal('show');
    }

    // دالة عرض الإيصال (صورة أو PDF)
    function showReceiptModal(fileUrl, accountOwner) {
        document.getElementById('modalAccountOwner').innerText = accountOwner;
        
        var cleanUrl = fileUrl.split('?')[0].toLowerCase();
        var isPdf = cleanUrl.endsWith('.pdf');
        
        var imgContainer = document.getElementById('imageReceiptContainer');
        var pdfContainer = document.getElementById('pdfReceiptContainer');
        
        if (isPdf) {
            imgContainer.style.display = 'none';
            pdfContainer.style.display = 'block';
            document.getElementById('modalReceiptPdf').src = fileUrl;
            document.getElementById('modalReceiptPdfLink').href = fileUrl;
        } else {
            pdfContainer.style.display = 'none';
            imgContainer.style.display = 'block';
            document.getElementById('modalReceiptImage').src = fileUrl;
        }
        
        $('#receiptModal').modal('show');
    }

    function openImageInNewTab(url) {
        window.open(url, '_blank').focus();
    }
</script>

@if($is_developer)
<div class="modal fade" id="editPaymentModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title font-weight-bold text-dark"><i class="fas fa-money-check-alt"></i> تعديل نوع الدفع (صلاحية مطور)</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editPaymentForm" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <div class="form-group">
                        <label class="font-weight-bold">نوع الدفع الجديد</label>
                        <input type="text" name="type" id="currentPaymentTypeInput" class="form-control" list="paymentTypesList" required>
                        <datalist id="paymentTypesList">
                            @foreach($payment_types as $type)
                                <option value="{{ $type }}">
                            @endforeach
                        </datalist>
                        <small class="text-danger mt-2 d-block">ملاحظة: يمكنك الاختيار من القائمة أو كتابة طريقة دفع جديدة يدوياً.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-warning font-weight-bold">حفظ التعديل</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openPaymentEditModal(id, currentType) {
        let formAction = '{{ route("developer.donations.updateType", ":id") }}';
        formAction = formAction.replace(':id', id);
        
        document.getElementById('editPaymentForm').action = formAction;
        document.getElementById('currentPaymentTypeInput').value = currentType;
        $('#editPaymentModal').modal('show');
    }
</script>
@endif

<script>
    $(document).ready(function() {
        $('.select2-modal').select2({
            dropdownParent: $('#developerModal'), 
            width: '100%',
            placeholder: "-- اختر المستخدم --",
            allowClear: true
        });

        $('.select2-edit-modal').select2({
            dropdownParent: $('#editDonationModal'), 
            width: '100%',
            placeholder: "-- اختر --",
            allowClear: true
        });
    });
</script>
<script src="{{URL::asset('assets/plugins/select2/js/select2.min.js')}}"></script>
<script src="{{URL::asset('assets/js/select2.js')}}"></script>
<script src="{{URL::asset('assets/plugins/jquery-nice-select/js/jquery.nice-select.js')}}"></script>
<script src="{{URL::asset('assets/plugins/jquery-nice-select/js/nice-select.js')}}"></script>
@endsection