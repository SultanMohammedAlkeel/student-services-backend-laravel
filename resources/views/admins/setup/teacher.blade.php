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
                                <th class="border-0" scope="col" id="teacher">اسم المعلم</th>
                                <th class="border-0" scope="col" id="building">الكلية</th>
                                <th class="border-0" scope="col" id="contact">القسم</th>
                                <th class="border-0" scope="col" id="contact">الدرجة العلمية</th>
                            </tr>
                            <tbody id="tbody">
                                @foreach ($teachers as $teacher)
                                <tr class="table-row">
                                    <th scope="row">{{ $teacher->id }}</th>
                                    <th scope="row" headers="teacher">{{ $teacher->name }}</th>
                                    <td headers="building">{{ $colleges->where('id', $teacher->college_id)->first()->name }}</td>
                                    <td headers="building">{{ $departments->where('id', $teacher->department_id)->first()->name }}</td>
                                    <td headers="contact">{{ $teacher->academic_degree }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-4 mb-5 mb-lg-0">
                    <div class="card bg-primary shadow-soft border-light p-4">
                        <div class="card-header text-center pb-0">
                            <h2 class="h4 custom-font">اضافة معلم جديد</h2>
                        </div>
                        <div class="card-body">
                            <form method="post" action="{{ route('teacher.store') }}" enctype="multipart/form-data">
                                @csrf
                                <!-- Form -->
                                <div class="invalid-feedback show">{{ $errors->first('name') }}</div>
                                <div class="form-group">
                                    <div class="input-group mb-4">
                                        <input class="form-control" name="name" placeholder="اسم المعلم"
                                            aria-label="Input group" type="text" value="{{ old('name') }}">
                                        <div class="input-group-append">
                                            <span class="input-group-text"><span class="fa-solid fa-chalkboard-user"></span></span>
                                        </div>
                                    </div>
                                </div>
                                <!-- End of Form -->
                                <!-- Form -->
                                <div class="form-group">
                                    <select class="custom-select my-1 mr-sm-2" name="gender">
                                        <option value="ذكر" {{ old('gender') == 'ذكر' ? 'selected' : '' }}>ذكر</option>
                                        <option value="انثى" {{ old('gender') == 'انثى' ? 'selected' : '' }}>انثى</option>
                                    </select>
                                </div>
                                <!-- End of Form -->
                                <!-- Form -->
                                <div class="form-group">
                                    <select class="custom-select my-1 mr-sm-2" name="college_id" id="college_id">
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
                                <!-- Form -->
                                <div class="form-group">
                                    <select class="custom-select my-1 mr-sm-2" name="academic_degree">
                                        <option value="أستاذ دكتور" {{ old('type') == 'أستاذ دكتور' ? 'selected' : '' }}>أستاذ دكتور</option>
                                        <option value="أستاذ مشارك" {{ old('type') == 'أستاذ مشارك' ? 'selected' : '' }}>أستاذ مشارك</option>
                                        <option value="أستاذ مساعد" {{ old('type') == 'أستاذ مساعد' ? 'selected' : '' }}>أستاذ مساعد</option>
                                        <option value="مدرس" {{ old('type') == 'مدرس' ? 'selected' : '' }}>مدرس</option>
                                        <option value="معيد" {{ old('type') == 'معيد' ? 'selected' : '' }}>معيد</option>
                                    </select>
                                </div>
                                <!-- End of Form -->
                                <!-- Form -->
                                <div class="invalid-feedback show">{{ $errors->first('specialization') }}</div>
                                <div class="form-group">
                                    <div class="input-group mb-4">
                                        <input class="form-control" name="specialization" placeholder="التخصص"
                                            aria-label="Input group" type="text" value="{{ old('specialization') }}">
                                        <div class="input-group-append">
                                            <span class="input-group-text"><span class="fa-solid fa-graduation-cap"></span></span>
                                        </div>
                                    </div>
                                </div>
                                <!-- End of Form -->
                                <button type="submit" class="btn btn-block btn-primary">اضافة معلم</button>
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
                    url: "{{ route('teacher-search') }}",
                    type: "GET",
                    data: {'keyword': keyword},
                    success: function(response) {
                        // تفريغ الجدول قبل إضافة النتائج الجديدة
                        $('#tbody').empty();
                        
                        // إضافة النتائج إلى الجدول
                        $.each(response, function(index, teacher) {
                            $('#tbody').append(
                                `
                                <tr class="table-row">
                                    <th scope="row">${teacher.id}</th>
                                    <th scope="row" headers="teacher">${teacher.teacher_name}</th>
                                    <td headers="building">${teacher.college_name}</td>
                                    <td headers="contact">${ teacher.department_name }</td>
                                    <td headers="contact">${ teacher.academic_degree }</td>
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

