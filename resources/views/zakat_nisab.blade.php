


@extends('layouts.master')
@section('title')
نصاب الزكاة
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
                            <h4 class="content-title mb-0 my-auto"> نصاب الزكاة </h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">
                            </span>
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

@if (session()->has('Add'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>{{ session()->get('Add') }}</strong>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if (session()->has('delete'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>{{ session()->get('delete') }}</strong>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if (session()->has('edit'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>{{ session()->get('edit') }}</strong>
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
								<div class="d-flex justify-content-between">
								</div>
                                <a class="modal-effect btn btn-outline-primary " data-effect="effect-scale" data-toggle="modal" href="#modaldemo8">  تحديث النصاب +</a>
							</div>
							<div class="card-body">
								<div class="table-responsive">
									<table class="table text-md-nowrap" id="example1">
								<thead>
    <tr>
        <th>ت</th>
        <th>المبلغ النصاب</th>
        <th>نصاب 24</th>
        <th>نصاب 21</th>
        <th>نصاب 18</th>
        <th>سعر 24</th>
        <th>سعر 21</th>
        <th>سعر 18</th>
        <th class="text-danger">كفارة اليمين</th>
        <th class="text-warning">فدية الصيام</th>
        
        <th>آخر تاريخ تحديث</th>
        <th>العمليات</th>
    </tr>
</thead>

<tbody>
    <?php $i =0?>
    @foreach($nisabs as $nisab)
    <?php $i++?>
    <tr>
        <td>{{ $i }}</td>
        <td>{{ $nisab->nisab_amount }}</td>
        <td>{{ $nisab->nisab_24 }}</td>
        <td>{{ $nisab->nisab_21 }}</td>
        <td>{{ $nisab->nisab_18 }}</td>
        <td>{{ $nisab->price_24 }}</td>
        <td>{{ $nisab->price_21 }}</td>
        <td>{{ $nisab->price_18 }}</td>
        
        <td class="font-weight-bold text-danger">{{ $nisab->kaffarat_yameen }}</td>
        <td class="font-weight-bold text-warning">{{ $nisab->fidyah_siyam }}</td>

        <td>{{ $nisab->last_updated }}</td>
        <td>
             <a class="modal-effect btn btn-sm btn-danger" data-effect="effect-scale"
                data-id="{{ $nisab->id }}" data-name_category="{{ $nisab->nisab_amount }}"
                data-toggle="modal" href="#modaldemo9" title="حذف"><i class="las la-trash"></i></a>
        </td>
    </tr>
    @endforeach
</tbody>
                                    </table>
                                    </div>

                                </div>
                                    <!-- bd -->

                                    <div class="modal" id="modaldemo8">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content modal-content-demo">
                                                <div class="modal-header">
                                                    <h6 class="modal-title">تحديث النصاب </h6><button aria-label="Close" class="close" data-dismiss="modal"
                                                        type="button"><span aria-hidden="true">&times;</span></button>
                                                </div>
                                                <div class="modal-body">
                                  <form action="{{route('zakat_nisab.store')}}" method="post" autocomplete="off">
    {{ csrf_field() }}

    <div class="form-group">
        <label>قيمة النصاب لتاريخ اليوم</label>
        <input type="text" class="form-control" name="nisab_amount" value="{{ $latest->nisab_amount ?? '' }}">
    </div>

    <div class="row">
        <div class="col-md-4">
            <label>نصاب عيار 24</label>
            <input type="number" step="0.01" name="nisab_24" class="form-control" value="{{ $latest->nisab_24 ?? '' }}">
        </div>
        <div class="col-md-4">
            <label>نصاب عيار 21</label>
            <input type="number" step="0.01" name="nisab_21" class="form-control" value="{{ $latest->nisab_21 ?? '' }}">
        </div>
        <div class="col-md-4">
            <label>نصاب عيار 18</label>
            <input type="number" step="0.01" name="nisab_18" class="form-control" value="{{ $latest->nisab_18 ?? '' }}">
        </div>
    </div>

    <hr>

    <div class="row">
        <div class="col-md-4">
            <label>سعر عيار 24</label>
            <input type="number" step="0.01" name="price_24" class="form-control" value="{{ $latest->price_24 ?? '' }}">
        </div>
        <div class="col-md-4">
            <label>سعر عيار 21</label>
            <input type="number" step="0.01" name="price_21" class="form-control" value="{{ $latest->price_21 ?? '' }}">
        </div>
        <div class="col-md-4">
            <label>سعر عيار 18</label>
            <input type="number" step="0.01" name="price_18" class="form-control" value="{{ $latest->price_18 ?? '' }}">
        </div>
    </div>

    <hr>

    <h6 class="text-primary font-weight-bold">القيم الشرعية الأخرى</h6>
    <div class="row">
        <div class="col-md-6">
            <label class="text-danger">كفارة اليمين (دينار)</label>
            <input type="number" step="0.01" name="kaffarat_yameen" class="form-control" 
                   placeholder="مثال: 150" value="{{ $latest->kaffarat_yameen ?? '' }}">
        </div>

        <div class="col-md-6">
            <label class="text-warning">فدية الصيام (لليوم الواحد)</label>
            <input type="number" step="0.01" name="fidyah_siyam" class="form-control" 
                   placeholder="مثال: 10" value="{{ $latest->fidyah_siyam ?? '' }}">
        </div>
    </div>
    <div class="modal-footer mt-3">
        <button type="submit" class="btn btn-primary">تاكيد</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">اغلاق</button>
    </div>

</form>

                                                </div>
                                            </div>
                                        </div>
                                    
                                        </div>
                                    


                                    <div class="modal fade" id="exampleModal2" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                                    aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLabel">تعديل النصاب</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                            
                                                <form action="categories/update" method="post" autocomplete="off">
                                                    {{ method_field('patch') }}
                                                    {{ csrf_field() }}
                                                    <div class="form-group">
                                                        <input type="hidden" name="id" id="id" value="">
                                                        <label for="recipient-name" class="col-form-label"> التصنيف:</label>
                                                        <input class="form-control" name="name_category" id="name_category" type="text">
                                                    </div>
                                                   
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-primary">تاكيد</button>
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">اغلاق</button>
                                            </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            
                                
                            </div><!-- bd -->
                        </div>
    
    
                </div>
                
               
        </div>
                      <div class="modal" id="modaldemo9">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content modal-content-demo">
                <div class="modal-header">
                    <h6 class="modal-title">حذف قيمة النصاب</h6><button aria-label="Close" class="close" data-dismiss="modal"
                        type="button"><span aria-hidden="true">&times;</span></button>
                </div>
              
                <form action="zakat_nisab/destroy" method="post">
                    {{ method_field('delete') }}
                    {{ csrf_field() }}
                    <div class="modal-body">
                        <p>هل انت متاكد من عملية الحذف ؟</p><br>
                        <input type="hidden" name="id" id="id" value="">
                        <input class="form-control" name="nisab_amount" id="nisab_amount" type="text" readonly>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">الغاء</button>
                        <button type="submit" class="btn btn-danger">تاكيد</button>
                    </div>
            </div>
            </form>
        </div>
    </div>
   <!--تعديل -->
          
                    </div>
    
                    <!-- row closed -->
                </div>
            
                    
                <!-- Container closed -->
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
    $('#exampleModal2').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget)
        var id = button.data('id')
        var name_category = button.data('name_category')
        var modal = $(this)
        modal.find('.modal-body #id').val(id);
        modal.find('.modal-body #name_category').val(name_category);
    })

</script>

<script>
    $('#modaldemo9').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget)
        var id = button.data('id')
        var name_category = button.data('name_category')
        var modal = $(this)
        modal.find('.modal-body #id').val(id);
        modal.find('.modal-body #name_category').val(name_category);
    })

</script>



@endsection