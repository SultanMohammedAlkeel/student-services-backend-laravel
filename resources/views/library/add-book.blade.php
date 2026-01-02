<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Yotta Uni App</title>
    @include('../../scripts.css')
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
    @if (session('error'))
        <script>alert('المكتبة لا تدعم هذا النوع من الملفات')</script>
    @endif

    <section class="min-vh-100 d-flex bg-primary align-items-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-md-8 col-lg-6 justify-content-center">
                    <div class="card bg-primary shadow-soft border-light p-4">
                        <div class="card-header text-center pb-0">
                            <h2 class="mb-0 h5 custom-font">ادخال بيانات الكتاب</h2>
                        </div>
                        <div class="card-body">
                            <form method="post" action="{{ route('library.store') }}" enctype="multipart/form-data">
                                @csrf
                                <!-- Form -->
                                <div class="invalid-feedback show">{{ $errors->first('title') }}</div>
                                <div class="form-group">
                                    <div class="input-group mb-4">
                                        <input class="form-control" name="title" placeholder="اسم الكتاب" aria-label="Input group" type="text" value="{{ old('title') }}">
                                        <div class="input-group-append">
                                            <span class="input-group-text"><span class="fas fa-book"></span></span>
                                        </div>
                                    </div>
                                </div>
                                <!-- End of Form -->
                                <!-- Form -->
                                <div class="invalid-feedback show">{{ $errors->first('author') }}</div>
                                <div class="form-group">
                                    <div class="input-group mb-4">
                                        <input class="form-control" name="author" placeholder="مؤلف الكتاب" aria-label="Input group" type="text" value="{{ old('author') }}">
                                        <div class="input-group-append">
                                            <span class="input-group-text"><span class="fas fa-user"></span></span>
                                        </div>
                                    </div>
                                </div>
                                <!-- End of Form -->
                                <!-- Form -->
                                <div class="invalid-feedback show">{{ $errors->first('type') }}</div>
                                <div class="form-group {{ session('user_type') == 'طالب'? 'hide': '' }}">
                                    <select class="custom-select my-1 mr-sm-2" name="type" id="type" onchange="getTypeOfBook(this.value)">
                                        @if (session('user_type') == 'طالب')
                                            <option value="محدد">محدد</option>
                                        @else
                                            @foreach ($types as $type )
                                                <option value="{{ $type }}" {{ old('type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <!-- End of Form -->
                                 <div class="alert hide bg-white" id="select-college">
                                    <!-- Form -->
                                    <div class="form-group" id="college">
                                        <select class="custom-select my-1 mr-sm-2" name="college_id" id="college_id">
                                            @if (session('user_type') == 'طالب')
                                                <option value="{{ session('college') }}">{{ session('college') }}</option>
                                            @endif
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
                                            @if (session('user_type') == 'طالب')
                                                <option value="{{ session('department_id') }}">{{ session('department_id') }}</option>
                                            @endif
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
                                            @if (session('user_type') == 'طالب')
                                                <option value="{{ session('level') }}">{{ session('level') }}</option>
                                            @endif
                                            @foreach ($levels as $level )
                                                <option value="{{ $level }}" {{ old('type') == $level ? 'selected' : '' }}>{{ $level }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <!-- End of Form -->
                                 </div>
                                <!-- Form -->
                                <div class="invalid-feedback show">{{ $errors->first('category_id') }}</div>
                                <div class="form-group">
                                    <select class="custom-select my-1 mr-sm-2" name="category_id" id="type">
                                        @foreach ($categories as $category )
                                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <!-- End of Form -->
                                <!-- Form -->
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" name="file_url" aria-label="File upload">
                                    <label class="custom-file-label" for="customFile">Choose Book</label>
                                </div>
                                <br> <br>
                                <!-- Form -->
                                <div class="form-group">
                                    <textarea class="form-control right-text rtl-layout" placeholder="اكتب وصف عن الكتاب..." name="description" rows="4" value="{{ old('description') }}"></textarea>
                                </div>
                                <!-- End of Form -->
                                <button type="submit" class="btn btn-block btn-primary">اضافة الكتاب</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @include('../../scripts.js')
    <script>

        function getTypeOfBook(val) {
            let selectCollege = document.getElementById('select-college');
            let college = document.getElementById('college');
            let department = document.getElementById('department');
            let level = document.getElementById('level');

            console.log(val);
            
            if (val != 'عام') {
                selectCollege.classList.remove('hide');
            } else {
                selectCollege.classList.add('hide');
            }

            if (val == 'خاص') {
                department.classList.remove('hide');
                level.classList.add('hide');
            }

            if (val == 'مشترك') {
                department.classList.add('hide');
                level.classList.remove('hide');
            }

            if (val == 'محدد') {
                department.classList.remove('hide');
                level.classList.remove('hide');
            }
        }
        $(document).ready(function() {
            $('#college_id').change(function () {
                var college_id = $(this).val(); 
                if (college_id) {
                    $.ajax({
                        url: '/get-departments/' + college_id,
                        type: 'GET',
                        success: function (data) {
                            $('#department_id').empty(); // مسح الأقسام الحالية
                            $('#department_id').append('<option value="">اختر القسم</option>');

                            // إضافة الأقسام الجديدة
                            $.each(data, function (key, value) {
                                $('#department_id').append(`<option value="${value.id}">${ value.name }</option>`);
                            });
                        }
                    });
                } else {
                    $('#department_id').empty(); // إذا لم يتم اختيار كلية
                    $('#department_id').append('<option value="">اختر القسم</option>');
                }
            });
        });
    </script>
</body>

</html>