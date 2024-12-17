@extends('layouts.master')
@section('title')
الإعلانات
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
							<h4 class="content-title mb-0 my-auto"> الإعلانات </h4>
						</div>
					</div>

				</div>
				<!-- breadcrumb -->
@endsection
@section('content')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>رفع الصور</title>
    <!-- تضمين Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #f9f9f9, #e2e8f0);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .upload-container {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 30px;
            max-width: 500px;
            margin: auto;
            margin-top: 50px;
        }
        h2 {
            color: #2c3e50;
            font-weight: bold;
            text-shadow: 1px 1px 1px rgba(0, 0, 0, 0.1);
        }
        .btn-success {
            background-color: #28a745;
            border: none;
            transition: all 0.3s ease-in-out;
        }
        .btn-success:hover {
            background-color: #218838;
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
        }
        .form-label {
            font-weight: 600;
            color: #495057;
        }
        input[type="file"] {
            border: 2px solid #ced4da;
            border-radius: 8px;
        }
    </style>

<div class="upload-container">
    <h2 class="text-center mb-4">📤 رفع صورة جديدة</h2>

    <!-- رسالة النجاح -->
    @if(session('success'))
        <div class="alert alert-success text-center">
            {{ session('success') }}
        </div>
    @endif

    <!-- نموذج رفع الصورة -->
    <form action="{{ route('image.upload') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- اختيار الصورة -->
        <div class="mb-4">
            <label for="image" class="form-label">📸 اختيار الصورة</label>
            <input type="file" class="form-control" id="image" name="image" required>
        </div>

        <!-- وصف الصورة -->
        <div class="mb-4">
            <label for="description" class="form-label">📝 الوصف</label>
            <input type="text" class="form-control" id="description" name="description" placeholder="أدخل وصفًا للصورة" required>
        </div>

        <!-- زر الرفع -->
        <div class="text-center">
            <button type="submit" class="btn btn-success w-100">
                📥 رفع الصورة
            </button>
        </div>
    </form>
    <div class="container mt-5">
        <h2 class="text-center mb-4">📸 الصور المرفوعة</h2>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if($images->isEmpty())
            <div class="alert alert-info text-center">
                لا توجد صور مرفوعة حاليًا.
            </div>
        @else
            <div class="row">
                @foreach($images as $image)
                    <div class="col-md-4 mb-4">
                        <div class="card shadow-sm">
                            <img src="{{ asset('storage/' . $image->image_path) }}" class="card-img-top" alt="صورة">
                            <div class="card-body">
                                <p class="card-text">{{ $image->description }}</p>
                                <form action="{{ route('image.delete', $image->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm w-100">
                                        🗑 حذف
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
    <br><br><br><br>
</div>
<br><br>
@endsection
@section('js')
<!-- Internal Select2.min js -->
<script src="{{URL::asset('assets/plugins/select2/js/select2.min.js')}}"></script>
<script src="{{URL::asset('assets/js/select2.js')}}"></script>
<!-- Internal Nice-select js-->
<script src="{{URL::asset('assets/plugins/jquery-nice-select/js/jquery.nice-select.js')}}"></script>
<script src="{{URL::asset('assets/plugins/jquery-nice-select/js/nice-select.js')}}"></script>
@endsection


