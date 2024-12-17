@extends('layouts.master')
@section('title')
التبرعات 
@stop
@section('css')
<!--Internal  Nice-select css  -->
<link href="{{URL::asset('assets/plugins/jquery-nice-select/css/nice-select.css')}}" rel="stylesheet"/>
<!-- Internal Select2 css -->
<link href="{{URL::asset('assets/plugins/select2/css/select2.min.css')}}" rel="stylesheet">
@endsection
@section('page-header')
				<!-- breadcrumb -->
				<div class="breadcrumb-header justify-content-between">
					<div class="my-auto">
						<div class="d-flex">
							<h4 class="content-title mb-0 my-auto"> المتبرعين </h4>
						</div>
					</div>
					
				</div>
				<!-- breadcrumb -->
@endsection
@section('content')
				<!-- row opened -->
				 <div class="row">
					<div class="col-xl-12 col-md-12">
						<div class="card">
							<div class="card-body">
								<!-- Shopping Cart-->
								<div class="card-body">
									<div class="table-responsive">
										<table class="table text-md-nowrap" id="example1">
											<thead>
												<tr>
													<th >ت</th>
													<th >الحمله</th>
													<th >المتبرع</th>
													<th >قيمة التبرع   </th>
													<th >تاريخ التبرع   </th>


												</tr>
											</thead>
											<tbody>
												<tr>
													<?php $i =0?>
													@foreach($donations as $d)
													<?php $i++?>
													<td>{{$i}}</td>
													<td>{{$d->campaign_name}}</td>
													<td>{{$d->username}}</td>
													<td>{{$d->amount}}</td> 
													<td>{{$d->date}}</td> 

											</tr>
											@endforeach
											</tbody>
										</table>
			
									</div>
			
								</div>  	
													
					
					</div>
				</div>
				<!-- row closed -->
			</div>
			<!-- Container closed -->
		</div>
		<!-- main-content closed -->
@endsection
@section('js')
<!-- Internal Select2.min js -->
<script src="{{URL::asset('assets/plugins/select2/js/select2.min.js')}}"></script>
<script src="{{URL::asset('assets/js/select2.js')}}"></script>
<!-- Internal Nice-select js-->
<script src="{{URL::asset('assets/plugins/jquery-nice-select/js/jquery.nice-select.js')}}"></script>
<script src="{{URL::asset('assets/plugins/jquery-nice-select/js/nice-select.js')}}"></script>
@endsection