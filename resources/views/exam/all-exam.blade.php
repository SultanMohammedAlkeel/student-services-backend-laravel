<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Yotta Uni App</title>
    @include('scripts.css')
</head>

<body>

    
    @if (session('user_type') == 'مشرف')
        @include('../admins.layouts.header')  
    @elseif ((session('user_type') == 'معلم'))
        @include('../teacher.layouts.header')  
    @else
        @include('../students.layouts.header')  
    @endif
    <br>
    <br>
    <br>

    <div class="section section-md bg-primary text-black pt-0 line-bottom-light">
        <div class="container">
            <div class="row justify-content-center">
                <div class="form-group ltr-layout book-search">
                    <form action="{{ route('library.search') }}" method="post">
                    @csrf
                        <div class="input-group my-4">
                            <button class="btn btn-primary btn-sm">بحث</button>
                            <input class="form-control text-center" name="search" id="search" placeholder="بحث" aria-label="Input group" type="text" value="{{ old('search') }}">
                            <div class="input-group-append">
                                <span class="input-group-text"><span class="fas fa-search"></span></span>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="mt-5 px-5 library-container d-flex align-items-center" id="library-container">
                    @foreach ($exams as $data)
                        @include('exam.components.exam-card', ['exam_data' => $data, 'users' => $users, 'records' => $records])
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <button class="btn btn-icon-only btn-up library-btn-up" type="button" onclick="scrollToTop()"><span class="fa-solid fa-chevron-up"></span></button>
    <button class="btn btn-icon-only btn-filtter" type="button" aria-label="add button" title="add button" data-toggle="modal" data-target="#modal-form"><span class="fa-solid fa-filter"></span></button>
    @if (session('role_id') != 3)
        <a class="btn btn-icon-only btn-add" href="{{ url('/add-exam') }}"><span aria-hidden="true" class="fab fa-add"></span></a>
    @endif
    
    <div class="modal fade" id="modal-form" tabindex="-1" role="dialog" aria-labelledby="modal-form"
        aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body p-0">
                    <div class="card bg-primary shadow-soft border-light p-4">
                        <button type="button" class="close ml-auto" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                        <div class="card-header text-center pb-0">
                            <h2 class="h4 custom-font">بحث و التصنيف في المكتبة</h2>
                        </div>
                        <div class="card-body">
                        <form method="post" action="{{ route('library.filter') }}" enctype="multipart/form-data">
                                @csrf
                                <!-- Form -->
                                <div class="invalid-feedback show">{{ $errors->first('type') }}</div>
                                <div class="form-group" data-toggle="tooltip" data-placement="top" title="صيغة الملف">
                                    <select class="custom-select my-1 mr-sm-2" name="type" id="type">
                                        <option value="">اختر الصيغة</option>    
                                        @foreach ($types as $type )
                                            <option value="{{ $type }}" {{ old('type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <!-- End of Form -->
                                <!-- Form -->
                                <div class="invalid-feedback show">{{ $errors->first('type') }}</div>
                                <div class="form-group" data-toggle="tooltip" data-placement="top" title="الجهة المستفيدة من الملف">
                                    <select class="custom-select my-1 mr-sm-2" name="type" id="type" onchange="getTypeOfBook(this.value)">
                                        <option value="">اختر النوع</option>
                                        @foreach ($types as $type )
                                            <option value="{{ $type }}" {{ old('type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <!-- End of Form -->
                                 <div class="alert hide bg-white" id="select-college">
                                    <!-- Form -->
                                    <div class="form-group" id="college">
                                        <select class="custom-select my-1 mr-sm-2" name="college_id" id="college_id">
                                            @foreach ($colleges as $college )
                                                <option value="{{ $college->id }}" {{ old('college_id') == $college->id ? 'selected' : '' }}>{{ $college->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <!-- End of Form -->
                                    <!-- Form -->
                                    <div class="invalid-feedback show">{{ $errors->first('department_id') }}</div>
                                    <div class="form-group" id="department">
                                        <select class="custom-select my-1 mr-sm-2" name="department_id" id="department_id">
                                            <option value="">اختر القسم</option>
                                            @foreach ($departments->where('college_id', 1) as $department )
                                                <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <!-- End of Form -->
                                    <!-- Form -->
                                    <div class="invalid-feedback show">{{ $errors->first('level') }}</div>
                                    <div class="form-group hide" id="level">
                                        <select class="custom-select my-1 mr-sm-2" name="level" >
                                            @foreach ($levels as $level )
                                                <option value="{{ $level }}" {{ old('type') == $level ? 'selected' : '' }}>{{ $level }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <!-- End of Form -->
                                 </div> 
                                <button type="submit" class="btn btn-block btn-primary">اضافة الكتاب</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End of Modal Content -->

    @include('scripts.js')
</body>

</html>
