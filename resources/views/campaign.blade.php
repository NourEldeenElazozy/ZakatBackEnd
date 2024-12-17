@extends('layouts.master')
@section('title')
الحملات 
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
                            <h4 class="content-title mb-0 my-auto"> الحملات </h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">
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
                                <a class="modal-effect btn btn-outline-primary " data-effect="effect-scale" data-toggle="modal" href="#modaldemo8"> إضافة حمله +</a>
							</div>
							<div class="card-body">
								<div class="table-responsive">
									<table class="table text-md-nowrap" id="example1">
										<thead>
                                            <tr>
                                                <th >ت</th>
                                                <th >الصورة</th>

                                                <th >عنوان الحمله  </th>
                                                <th >الوصف</th>
                                                <th >التصنيف</th>
                                                <th >القيمة</th>
                                                <th >المدفوع</th>
                                                <th >نوع المستفيد</th>
                                                <th >حالة الحملة </th>
                                               <th >العمليات</th>


                                            </tr>
                						</thead>
										<tbody>
                                            <tr>
                                                <?php $i =0?>
                                                @foreach($campaign as $x)
                                                <?php $i++?>
                                           
                                                <td>{{$i}}</td>
                                                <td>
                                                    <img class="img-sm rounded-circle bg-warning d-flex align-items-center justify-content-center text-white" src="public/img/{{$x->image}}" >
                                                    </td>
                                                <td>{{$x->name}}</td>
                                                <td>{{$x->description}}</td>
                                                <td>{{$x->categorie->name_category}}</td>
                                               
                                                <td>{{$x->total}}</td>
                                                <td>{{$x->paid_up}}</td>
                                                <td>{{$x->recipient}}</td>
                                                <td>{{$x->state_campaign}}</td>

                                            <td>
                                               
                                                <a type="button" class="btn btn-sm btn-info" href=" {{ url('donations') }}/{{ $x->id }} " ><i class=" las la-phone-volume"></i></a>
                                                
                                                    <a class="modal-effect btn btn-sm btn-info" data-effect="effect-scale"
                                                        data-id="{{ $x->id }}" data-name="{{ $x->name }}" data-description="{{ $x->description }}"
                                                         data-name_category="{{$x->categorie->name_category}}" data-image="{{ $x->image }}"  data-total="{{ $x->total }}"
                                                         data-paid_up="{{ $x->paid_up }}" data-recipient="{{ $x->recipient }}" data-state_campaign="{{ $x->state_campaign }}"
                                                        data-toggle="modal"
                                                        href="#exampleModal2" title="تعديل"><i class="las la-pen"></i></a>
                                              
                                              
                                                    <a class="modal-effect btn btn-sm btn-danger" data-effect="effect-scale"
                                                        data-id="{{ $x->id }}" data-name="{{ $x->name }}"
                                                        data-toggle="modal" href="#modaldemo9" title="حذف"><i
                                                            class="las la-trash"></i></a>
                                              
                                            </td>
                                        </tr>
                                        @endforeach
        
        
										</tbody>
                                    </table>

                                    </div>
                                  
                                    
                                </div>
                        </div>
                                    <!-- bd -->
                                   
                                    <div class="modal" id="modaldemo8">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content modal-content-demo">
                                                <div class="modal-header">
                                                    <h6 class="modal-title">اضافة حمله</h6><button aria-label="Close" class="close" data-dismiss="modal"
                                                        type="button"><span aria-hidden="true">&times;</span></button>
                                                </div>
                                                <div class="modal-body">
                                                    <form action="{{route('campaign.store')}}" enctype="multipart/form-data" method="post" autocomplete="off">
                                                        {{ csrf_field() }}
                                
                                                        <div class="form-group">
                                                            <label for="exampleInputEmail1">عنوان الحمله </label>
                                                            <input type="hidden" class="form-control" id="id" name="id">

                                                            <input type="text" class="form-control" id="name" name="name">
                                                        </div>

                                                        <div class="form-group">
                                                            <label for="exampleInputEmail1">الوصف </label>
                                                            <input type="text" class="form-control" id="description" name="description">
                                                        </div>

                                                        <div class="form-group">
                                                            <label for="exampleInputEmail1">التصنيف </label>
                                                            <select class='form-control' name='categorie_id'>
                                                                <option value="" selected disabled>حدد التصنيف</option>

                                                                @foreach ($categorie as $categories)
                                                                    <option value="{{ $categories->id }}"> {{ $categories->name_category }}</option>
                                                                @endforeach
                                                                </select>
                                                        </div>
                                                        
                                                        <div class="form-group">
                                                            <label for="exampleInputEmail1">الصوره </label>
                                                            <input type="file" class="form-control" id="image" name="image">
                                                        </div>

                                                        <div class="form-group">
                                                            <label for="exampleInputEmail1">القيمة </label>
                                                            <input type="text" class="form-control" id="total" name="total">
                                                        </div>

                                                        <div class="form-group">
                                                            <label for="exampleInputEmail1">المدفوع </label>
                                                            <input type="text" class="form-control" id="paid_up" name="paid_up">
                                                        </div>

                                                        <div class="form-group">
                                                            <label for="exampleInputEmail1">نوع المستفيد </label>
                                                            <input type="text" class="form-control" id="recipient" name="recipient">
                                                        </div>

                                                        <div class="form-group">
                                                            <label for="exampleInputEmail1">حالة الحمله </label>
                                                            <input type="text" class="form-control" id="state_campaign" name="state_campaign" value="مستمره" readonly>
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
                                   
<!--report donation-->
   



    </div>

    </div>





                                <div class="modal fade" id="exampleModal2" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                                    aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLabel">تعديل الحمله</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                            
                                                <form action="campaign/update" method="post" enctype="multipart/form-data" autocomplete="off">
                                                    {{ method_field('patch') }}
                                                    {{ csrf_field() }}
                                                   
                                                    <div class="form-group">
                                                        <input type="hidden" name="id" id="id" value="">
                                                        <label for="recipient-name" class="col-form-label"> الحمله</label>
                                                        <input class="form-control" name="name" id="name" type="text">
                                                    </div>
                                                    <div class="form-group">
                                                       
                                                        <label for="recipient-name" class="col-form-label"> الوصف</label>
                                                        <input class="form-control" name="description" id="description" type="text">
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="exampleInputEmail1" class="control-label">التصنيف </label>
                                                        <select class='form-control' name='name_category' id="name_category" >
                                                            <!--placeholder-->
                                                            @foreach ($categorie as $categorie)

                                                            <option > {{$categorie->name_category}}</option>
                                                            @endforeach
                                                            </select>
                                                    </div>

                                                     <div class="form-group">
                                                        <label for="exampleInputEmail1">الصوره </label>
                                                        <input type="text" class="form-control" id="image" name="image">

                                                        <input type="file" class="form-control"  name="image" >

                                                    </div>
                                                   <div class="form-group">
                                                       
                                                        <label for="recipient-name" class="col-form-label"> القيمة</label>
                                                        <input class="form-control" name="total" id="total" type="text">
                                                    </div>

                                                    
                                                    <div class="form-group">
                                                       
                                                        <label for="recipient-name" class="col-form-label"> المدفوع</label>
                                                        <input class="form-control" name="paid_up" id="paid_up" type="text">
                                                    </div>

                                                    <div class="form-group">
                                                       
                                                        <label for="recipient-name" class="col-form-label"> المستفيد</label>
                                                        <input class="form-control" name="recipient" id="recipient" type="text">
                                                    </div>

                                                    <div class="form-group">
                                                       
                                                        <label for="recipient-name" class="col-form-label"> الحاله</label>
                                                        <input class="form-control" name="state_campaign" id="state_campaign" type="text">
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
                            
                                
                            </div>
                                                  
                      </div>
                                           
             <!-- row closed -->
                 </div>
                                      
              </div>
                                    
         </div>
                 </div>                       
             </div>
         </div>
       
            <div class="modal" id="modaldemo9">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content modal-content-demo">
                        <div class="modal-header">
                            <h6 class="modal-title">حذف الحمله</h6><button aria-label="Close" class="close" data-dismiss="modal"
                                type="button"><span aria-hidden="true">&times;</span></button>
                        </div>
                        <form action="campaign/destroy" method="post">
                            {{ method_field('delete') }}
                            {{ csrf_field() }}
                            <div class="modal-body">
                                <p>هل انت متاكد من عملية الحذف ؟</p><br>
                                <input type="hidden" name="id" id="id" value="">
                                <input class="form-control" name="name" id="name" type="text" readonly>
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
        var name = button.data('name')
        var description = button.data('description')
        var name_category = button.data('name_category')
        var image = button.data('image')
        var total = button.data('total')
        var paid_up = button.data('paid_up')
        var recipient = button.data('recipient')
        var state_campaign = button.data('state_campaign')

        var modal = $(this)
        modal.find('.modal-body #id').val(id);
        modal.find('.modal-body #name').val(name);
        modal.find('.modal-body #description').val(description);
        modal.find('.modal-body #name_category').val(name_category);
        modal.find('.modal-body #image').val(image);
        modal.find('.modal-body #total').val(total);
        modal.find('.modal-body #paid_up').val(paid_up);
        modal.find('.modal-body #recipient').val(recipient);
        modal.find('.modal-body #state_campaign').val(state_campaign);

    })

</script>

<script>
    $('#exampleModal22').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget)
        var id = button.data('id')
        var amount = button.data('amount')
      

        var modal = $(this)
        modal.find('.modal-body #id').val(id);
        modal.find('.modal-body #amount').val(amount);

    })

</script>

>

<script>
    $('#modaldemo9').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget)
        var id = button.data('id')
        var name = button.data('name')
        var modal = $(this)
        modal.find('.modal-body #id').val(id);
        modal.find('.modal-body #name').val(name);
    })

</script>


@endsection