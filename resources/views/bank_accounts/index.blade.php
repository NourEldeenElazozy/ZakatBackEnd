@extends('layouts.master')
@section('title')
الحسابات المصرفية
@stop

@section('css')
<!-- Internal Data table css -->
<link href="{{URL::asset('assets/plugins/datatable/css/dataTables.bootstrap4.min.css')}}" rel="stylesheet" />
<link href="{{URL::asset('assets/plugins/datatable/css/buttons.bootstrap4.min.css')}}" rel="stylesheet">
<link href="{{URL::asset('assets/plugins/datatable/css/responsive.bootstrap4.min.css')}}" rel="stylesheet" />
<link href="{{URL::asset('assets/plugins/datatable/css/jquery.dataTables.min.css')}}" rel="stylesheet">
<link href="{{URL::asset('assets/plugins/datatable/css/responsive.dataTables.min.css')}}" rel="stylesheet">
<link href="{{URL::asset('assets/plugins/select2/css/select2.min.css')}}" rel="stylesheet">
@endsection
@section('page-header')
<!-- breadcrumb -->
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto">
        <div class="d-flex">
            <h4 class="content-title mb-0 my-auto"> الحسابات المصرفية </h4>
        </div>
    </div>
</div>
<!-- breadcrumb -->
@endsection
@section('content')

@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if (session()->has('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>{{ session()->get('success') }}</strong>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

<!-- row opened -->
<div class="row row-sm">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header pb-0">
                <a class="modal-effect btn btn-outline-primary" data-effect="effect-scale" data-toggle="modal" href="#modal-add">إضافة حساب بنكي +</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table text-md-nowrap" id="example1">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>المصرف والفرع</th>
                                <th>رقم الحساب</th>
                                <th>النوع</th>
                                <th>العمليات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($accounts as $account)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $account->bank_name }}</td>
                                <td>{{ $account->account_number }}</td>
                                <td>{{ $account->type == 'zakat' ? 'زكاة' : 'صدقة' }}</td>
                                <td>
                                    <a class="modal-effect btn btn-sm btn-info" data-effect="effect-scale"
                                       data-id="{{ $account->id }}" 
                                       data-bank_name="{{ $account->bank_name }}"
                                       data-account_number="{{ $account->account_number }}"
                                       data-type="{{ $account->type }}"
                                       data-toggle="modal" href="#modal-edit" title="تعديل"><i class="las la-pen"></i></a>
                                    
                                    <a class="modal-effect btn btn-sm btn-danger" data-effect="effect-scale"
                                       data-id="{{ $account->id }}" 
                                       data-bank_name="{{ $account->bank_name }}"
                                       data-toggle="modal" href="#modal-delete" title="حذف"><i class="las la-trash"></i></a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- row closed -->

<!-- Add Modal -->
<div class="modal fade" id="modal-add">
    <div class="modal-dialog" role="document">
        <div class="modal-content modal-content-demo">
            <div class="modal-header">
                <h6 class="modal-title">إضافة حساب بنكي</h6>
                <button aria-label="Close" class="close" data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
            </div>
            <form action="{{ route('bank_accounts.store') }}" method="post">
                {{ csrf_field() }}
                <div class="modal-body">
                    <div class="form-group">
                        <label>المصرف والفرع</label>
                        <input type="text" class="form-control" name="bank_name" required>
                    </div>
                    <div class="form-group">
                        <label>رقم الحساب</label>
                        <input type="text" class="form-control" name="account_number" required>
                    </div>
                    <div class="form-group">
                        <label>نوع الحساب</label>
                        <select name="type" class="form-control" required>
                            <option value="zakat">زكاة</option>
                            <option value="sadaqah">صدقة</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">تأكيد</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">إغلاق</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="modal-edit">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تعديل حساب بنكي</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('bank_accounts.update', 0) }}" method="post" id="edit-form">
                {{ method_field('patch') }}
                {{ csrf_field() }}
                <div class="modal-body">
                    <input type="hidden" name="id" id="edit_id">
                    <div class="form-group">
                        <label>المصرف والفرع</label>
                        <input type="text" class="form-control" name="bank_name" id="edit_bank_name" required>
                    </div>
                    <div class="form-group">
                        <label>رقم الحساب</label>
                        <input type="text" class="form-control" name="account_number" id="edit_account_number" required>
                    </div>
                    <div class="form-group">
                        <label>نوع الحساب</label>
                        <select name="type" id="edit_type" class="form-control" required>
                            <option value="zakat">زكاة</option>
                            <option value="sadaqah">صدقة</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">تأكيد</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">إغلاق</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="modal-delete">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content modal-content-demo">
            <div class="modal-header">
                <h6 class="modal-title">حذف حساب بنكي</h6>
                <button aria-label="Close" class="close" data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
            </div>
            <form action="{{ route('bank_accounts.destroy', 0) }}" method="post" id="delete-form">
                {{ method_field('delete') }}
                {{ csrf_field() }}
                <div class="modal-body">
                    <p>هل أنت متأكد من عملية الحذف؟</p>
                    <input type="text" class="form-control" id="delete_bank_name" readonly>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-danger">تأكيد</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
@section('js')
<!-- Internal Data tables -->
<script src="{{URL::asset('assets/plugins/datatable/js/jquery.dataTables.min.js')}}"></script>
<script src="{{URL::asset('assets/plugins/datatable/js/dataTables.dataTables.min.js')}}"></script>
<script src="{{URL::asset('assets/plugins/datatable/js/dataTables.responsive.min.js')}}"></script>
<script src="{{URL::asset('assets/plugins/datatable/js/responsive.dataTables.min.js')}}"></script>
<script src="{{URL::asset('assets/plugins/datatable/js/jquery.dataTables.js')}}"></script>
<script src="{{URL::asset('assets/plugins/datatable/js/dataTables.bootstrap4.js')}}"></script>
<script src="{{URL::asset('assets/plugins/datatable/js/dataTables.buttons.min.js')}}"></script>
<script src="{{URL::asset('assets/plugins/datatable/js/buttons.bootstrap4.min.js')}}"></script>
<script src="{{URL::asset('assets/plugins/datatable/js/jszip.min.js')}}"></script>
<script src="{{URL::asset('assets/plugins/datatable/js/pdfmake.min.js')}}"></script>
<script src="{{URL::asset('assets/plugins/datatable/js/vfs_fonts.js')}}"></script>
<script src="{{URL::asset('assets/plugins/datatable/js/buttons.html5.min.js')}}"></script>
<script src="{{URL::asset('assets/plugins/datatable/js/buttons.print.min.js')}}"></script>
<script src="{{URL::asset('assets/plugins/datatable/js/buttons.colVis.min.js')}}"></script>
<script src="{{URL::asset('assets/plugins/datatable/js/dataTables.responsive.min.js')}}"></script>
<script src="{{URL::asset('assets/plugins/datatable/js/responsive.bootstrap4.min.js')}}"></script>
<!--Internal  Datatable js -->
<script src="{{URL::asset('assets/js/table-data.js')}}"></script>

<script>
    $('#modal-edit').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var id = button.data('id');
        var bank_name = button.data('bank_name');
        var account_number = button.data('account_number');
        var type = button.data('type');
        
        var modal = $(this);
        modal.find('#edit_id').val(id);
        modal.find('#edit_bank_name').val(bank_name);
        modal.find('#edit_account_number').val(account_number);
        modal.find('#edit_type').val(type);

        var action = "{{ route('bank_accounts.update', ':id') }}";
        action = action.replace(':id', id);
        modal.find('#edit-form').attr('action', action);
    });

    $('#modal-delete').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var id = button.data('id');
        var bank_name = button.data('bank_name');
        
        var modal = $(this);
        modal.find('#delete_bank_name').val(bank_name);

        var action = "{{ route('bank_accounts.destroy', ':id') }}";
        action = action.replace(':id', id);
        modal.find('#delete-form').attr('action', action);
    });
</script>
@endsection
