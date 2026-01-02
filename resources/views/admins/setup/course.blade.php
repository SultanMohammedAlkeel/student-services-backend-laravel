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

@include('../../layouts.header')
    
    <div class="section section-lg pt-0">
        <div class="container">
            <!-- Title -->
            
            <div class="row">
                <div class="col text-center">
                    <h2 class="h5 mb-7 custom-font"></h2>
                </div>
            </div>
            <!-- End of title-->
            <div class="row justify-content-md-around">
                <div class="col-12 col-md-6 col-lg-6 mb-5 mb-lg-0 rtl-layout overflow-auto">
                    <div class="mb-5">
                        <div class="form-group ltr-layout">
                            <div class="input-group mb-4">
                                <input class="form-control" id="search" placeholder="بحث" aria-label="Input group" type="text">
                                <div class="input-group-append">
                                    <span class="input-group-text"><span class="fas fa-search"></span></span>
                                </div>
                            </div>
                        </div>
                        <table class="table table-hover shadow-inset rounded">
                            <tr>
                                <th class="border-0" scope="col" id="id">#</th>
                                <th class="border-0" scope="col" id="course">اسم المقرر</th>
                                <th class="border-0" scope="col" id="college">النوع</th>
                                <th class="border-0" scope="col" id="college">القسم</th>
                                <th class="border-0" scope="col" id="college">المستوى</th>
                                <th class="border-0" scope="col" id="college">الفصل</th>
                            </tr>
                            <tbody id="tbody">
                                @foreach ($courses as $course)
                                <tr class="table-row">
                                    <th scope="row">{{ $course->id }}</th>
                                    <th scope="row" headers="course">{{ $course->name }}</th>
                                    <th scope="row" headers="course">{{ $course->type }}</th>
                                    <td headers="contact">{{ $departments->where('id', $course->department_id)->first()->name }}</td>
                                    <td headers="contact">{{ $course->level }}</td>
                                    <td headers="contact">{{ $course->term }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-4 mb-5 mb-lg-0">
                    <div class="card bg-primary shadow-soft border-light p-4">
                        <div class="card-header text-center pb-0">
                            <h2 class="h4 custom-font">اضافة مقرر جديد</h2>
                        </div>
                        <div class="card-body">
                            <form method="post" action="{{ route('course.store') }}" enctype="multipart/form-data">
                                @csrf
                                <!-- Form -->
                                <div class="invalid-feedback show">{{ $errors->first('name') }}</div>
                                <div class="form-group">
                                    <div class="input-group mb-4">
                                        <input class="form-control" name="name" placeholder="اسم المقرر"
                                            aria-label="Input group" type="text" value="{{ old('name') }}">
                                        <div class="input-group-append">
                                            <span class="input-group-text"><span class="fa-solid fa-book"></span></span>
                                        </div>
                                    </div>
                                </div>
                                <!-- End of Form -->
                                <!-- Form -->
                                <div class="form-group">
                                    <select class="custom-select my-1 mr-sm-2" name="type" id="type" data-toggle="tooltip" data-placement="top" title="نوع المقرر">
                                        @foreach ($types as $type )
                                            <option value="{{ $type }}" {{ old('type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <!-- End of Form -->
                                <!-- Form -->
                                <div class="form-group">
                                    <select class="custom-select my-1 mr-sm-2" name="level" id="level">
                                        @foreach ($levels as $level )
                                            <option value="{{ $level }}" {{ old('level') == $level ? 'selected' : '' }}>{{ $level }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <!-- End of Form -->
                                <!-- Form -->
                                <div class="form-group">
                                    <select class="custom-select my-1 mr-sm-2" name="term" id="term"  data-toggle="tooltip" data-placement="top" title="فصل دراسي">">
                                        @foreach ($terms as $term )
                                            <option value="{{ $term }}" {{ old('term') == $term ? 'selected' : '' }}>{{ $term }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <!-- End of Form -->
                                <!-- Form -->
                                <div class="form-group">
                                    <select class="custom-select my-1 mr-sm-2" id="college_id">
                                        @foreach ($colleges as $college )
                                            <option value="{{ $college->id }}" {{ old('college_id') == $college->id ? 'selected' : '' }}>{{ $college->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <!-- End of Form -->
                                <!-- Form -->
                                <div class="invalid-feedback show">{{ $errors->first('department_id') }}</div>
                                <div class="form-group">
                                    <select class="custom-select my-1 mr-sm-2" name="department_id" id="department_id">
                                        <option value="">اختر القسم</option>
                                        @foreach ($departments->where('college_id', 1) as $department )
                                            <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <!-- End of Form -->
                                <div class="form-group">
                                    <textarea class="form-control right-text rtl-layout" placeholder="اكتب وصف عن الصلاحية..." name="description" rows="4" value="{{ old('description') }}"></textarea>
                                </div>
                                <!-- End of Form -->
                                <button type="submit" class="btn btn-block btn-primary">اضافة مقرر</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('../../scripts.js')
    <script>
        $(document).ready(function() {
            // عند كتابة نص في حقل البحث
            $('#search').on('keyup', function() {
                var keyword = $(this).val(); // الحصول على الكلمة المفتاحية
    
                // إرسال طلب AJAX
                $.ajax({
                    url: "{{ route('course-search') }}",
                    type: "GET",
                    data: {'keyword': keyword},
                    success: function(response) {
                        // تفريغ الجدول قبل إضافة النتائج الجديدة
                        $('#tbody').empty();
                        
                        
                        
                        // إضافة النتائج إلى الجدول
                        $.each(response, function(index, course) {
                            $('#tbody').append(
                                `
                                <tr  class="table-row">
                                    <th scope="row">${course.id}</th>
                                    <th scope="row" headers="course">${course.course_name}</th>
                                    <td headers="college">${course.type}</td>
                                    <td headers="contact">${ course.department_name }</td>
                                    <td headers="college">${course.level}</td>
                                </tr>
                                `
                            );
                        });
                    }
                });
            });
            
            $('#college_id').change(function () {
                var college_id = $(this).val(); 

                if (college_id) {
                    $.ajax({
                        url: '/get-departments-course/' + college_id,
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

