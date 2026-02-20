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

<div class="breadcrumb-header justify-content-between">
    <div class="my-auto">
        <div class="d-flex">
            <h4 class="content-title mb-0 my-auto">إدارة المتبرعين</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">/ قائمة التبرعات</span>
        </div>
    </div>
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
                                    @if($d->status == 0)
                                        <form action="{{ route('donations.updateStatus', $d->id) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-primary">تغيير إلى مكتمل</button>
                                        </form>
                                    @else
                                        <form action="{{ route('donations.markAsPending', $d->id) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('PATCH')
                                            <button type="button" class="btn btn-sm btn-danger btn-confirm-pending">
                                                تغيير لغير مكتمل
                                            </button>
                                        </form>
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
</script>

<script src="{{URL::asset('assets/plugins/select2/js/select2.min.js')}}"></script>
<script src="{{URL::asset('assets/js/select2.js')}}"></script>
<script src="{{URL::asset('assets/plugins/jquery-nice-select/js/jquery.nice-select.js')}}"></script>
<script src="{{URL::asset('assets/plugins/jquery-nice-select/js/nice-select.js')}}"></script>
@endsection