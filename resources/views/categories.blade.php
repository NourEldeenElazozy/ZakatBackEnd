@extends('layouts.master')
@section('title')
التصنيفات 
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
                            <h4 class="content-title mb-0 my-auto"> التصنيفات </h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">
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
                                <a class="modal-effect btn btn-outline-primary " data-effect="effect-scale" data-toggle="modal" href="#modaldemo8"> إضافة تصنيف +</a>
							</div>
							<div class="card-body">
								<div class="table-responsive">
									<table class="table text-md-nowrap" id="example1">
										<thead>
                                            <tr>
                                                <th >ت</th>
                                                <th >التصنيف </th>
                                               <th >العمليات</th>

                                            </tr>
                						</thead>
										<tbody>
                                            
											<tr>
                                                <?php $i =0?>
                                                @foreach($categorie as $x)
                                                <?php $i++?>
                                           
                                                <td>{{$i}}</td>
                                                <td>{{$x->name_category}}</td>
                                            <td>
                                             
                                                    <a class="modal-effect btn btn-sm btn-info" data-effect="effect-scale"
                                                        data-id="{{ $x->id }}" data-name_category="{{$x->name_category}}"data-image="{{$x->image}}"
                                                        data-toggle="modal"
                                                        href="#exampleModal2" title="تعديل"><i class="las la-pen"></i></a>
                                              
                                              
                                                    <a class="modal-effect btn btn-sm btn-danger" data-effect="effect-scale"
                                                        data-id="{{ $x->id }}" data-name_category="{{ $x->name_category }}"
                                                        data-toggle="modal" href="#modaldemo9" title="حذف"><i
                                                            class="las la-trash"></i></a>
                                              
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
                                                    <h6 class="modal-title">اضافة تصنيف</h6><button aria-label="Close" class="close" data-dismiss="modal"
                                                        type="button"><span aria-hidden="true">&times;</span></button>
                                                </div>
                                                <div class="modal-body">
                                                    <form action="{{route('categories.store')}}" method="post" enctype="multipart/form-data" autocomplete="off">
                                                        {{ csrf_field() }}
                                
                                                        <div class="form-group">
                                                            <label for="exampleInputEmail1">التصنيف </label>
                                                            <input type="text" class="form-control" id="name_category" name="name_category">
                                                        </div>
                                                        <div class="form-group">
        <label for="image" class="col-form-label">الصورة:</label>
        <input type="file" class="form-control" name="image" id="image" accept="image/*">
    </div>
                                                        <div class="modal-footer">
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
                                                <h5 class="modal-title" id="exampleModalLabel">تعديل التصنيف</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                            
                                            <form action="categories/update" method="post" enctype="multipart/form-data" autocomplete="off">
    {{ method_field('patch') }}
    {{ csrf_field() }}
    <div class="form-group">
        <input type="hidden" name="id" id="id" value="">
        <label for="recipient-name" class="col-form-label">التصنيف:</label>
        <input class="form-control" name="name_category" id="name_category" type="text">
    </div>
    <div class="form-group">
                                                        <label for="exampleInputEmail1">الصوره </label>
                                                        <input type="text" class="form-control" id="image" name="image">

                                                        <input type="file" class="form-control"  name="image" accept="image/*">

                                                    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">تأكيد</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">إغلاق</button>
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
                    <h6 class="modal-title">حذف التصنيف</h6><button aria-label="Close" class="close" data-dismiss="modal"
                        type="button"><span aria-hidden="true">&times;</span></button>
                </div>
                <form action="categories/destroy" method="post">
                    {{ method_field('delete') }}
                    {{ csrf_field() }}
                    <div class="modal-body">
                        <p>هل انت متاكد من عملية الحذف ؟</p><br>
                        <input type="hidden" name="id" id="id" value="">
                        <input class="form-control" name="name_category" id="name_category" type="text" readonly>
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
        var image = button.data('image')
        var modal = $(this)
        modal.find('.modal-body #id').val(id);
        
        modal.find('.modal-body #name_category').val(name_category);
        modal.find('.modal-body #image').val(image);
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