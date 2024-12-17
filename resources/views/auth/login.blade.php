@extends('layouts.master2')

@section('title')
تسجيل الدخول
@stop


@section('css')
<!-- Sidemenu-respoansive-tabs css -->
<link href="{{URL::asset('assets/plugins/sidemenu-responsive-tabs/css/sidemenu-responsive-tabs.css')}}" rel="stylesheet">
@endsection
@section('content')
		<div class="container py-5 h-100">
			<div class="row d-flex justify-content-center align-items-center h-100">
				<div class="col col-xl-10">
					<div class="card" style="border-radius: 1rem;">
						<div class="row g-0">
							<div class="col-md-5 col-lg-5  d-md-block ">
								<img src="{{URL::asset('assets/img/media/logo-icon-dark.jpg')}} " height="100%"  />
							</div>
							<div class="col-md-6 col-lg-7 d-flex align-items-center">
								<div class="card-body p-4 p-lg-5 text-black">
									<div class="mb-5 d-flex"> <a href="{{ url('/' . $page='Home') }}"></a></div>

									<form method="POST" action="{{ route('login') }}" autocomplete="off">
										@csrf

										<div class="d-flex align-items-center mb-3 pb-1">
										  <h3 class="h3  mb-0"> دخــول </h3>
										</div>


										<div data-mdb-input-init class="form-outline mb-4">
											<label>البريد الالكتروني</label>
                                                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                                                     @error('email')
                                                     <span class="invalid-feedback" role="alert">
                                                     <strong>{{ $message }}</strong>
                                                     </span>
                                                     @enderror
													</div>
										  
										  <div data-mdb-input-init class="form-outline mb-4">
											<label>كلمة المرور</label> 
                                                
                                                  <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                                                  @error('password')
                                                  <span class="invalid-feedback" role="alert">
                                                  <strong>{{ $message }}</strong>
                                                  </span>
												  @enderror
                                                  <div class="form-group row">
                                                      <div class="col-md-6 offset-md-4">
                                                           <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                                                 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                                <label class="form-check-label" for="remember">
                                                                       {{ __('تذكرني') }}
                                                                </label>
                                                           </div>
                                                       </div>
                                                   </div>
												   <div class="pt-1 mb-4">
												   <button type="submit" class="btn btn-main-primary btn-block">
                                                    {{ __('تسجيل الدخول') }}
                                                    </button>
												</form>

										 
											

									</div>
								  </div>
								</div>
							  </div>
							</div>
						  </div>
						</div>
				<!-- The image half -->
				<!-- The content half -->
			
										
@endsection
@section('js')
@endsection