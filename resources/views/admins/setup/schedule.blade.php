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
                                <th class="border-0" scope="col" id="building">الكلية</th>
                                <th class="border-0" scope="col" id="contact">القسم</th>
                                <th class="border-0" scope="col" id="schedule">المستوى</th>
                                <th class="border-0" scope="col" id="schedule">الفصل الدارسي</th>
                                <th class="border-0" scope="col" id="schedule">السنة الدارسية</th>
                                <th class="border-0" scope="col" id="contact">الوظيفة</th>
                            </tr>
                            <tbody id="tbody">
                                @foreach ($schedules as $schedule)
                                <tr class="table-row">
                                    <th scope="row">{{ $schedule->id }}</th>
                                    <td headers="building">{{ $colleges->where('id', $departments->where('id', $schedule->department_id)->first()->college_id)->first()->name }}</td>
                                    <td headers="building">{{ $departments->where('id', $schedule->department_id)->first()->name }}</td>
                                    <td headers="contact">{{ $schedule->level }}</td>
                                    <td headers="contact">{{ $schedule->term }}</td>
                                    <th scope="row" headers="schedule">{{ $academic_years->where('id', $schedule->academic_year_id)->first()->start_date }} - {{ $academic_years->where('id', $schedule->academic_year_id)->first()->end_date }}</th>
                                    <th headers="teacher"><a href="{{ route('schedule.show', ['schedule' => $schedule->id]) }}" class="btn btn-primary text-success" type="button" >عرض</a></th>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-4 mb-5 mb-lg-0">
                    <div class="card bg-primary shadow-soft border-light p-4">
                        <div class="card-header text-center pb-0">
                            <h2 class="h4 custom-font">اضافة جدول دراسي جديد</h2>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="put-schedule" enctype="multipart/form-data">
                                @csrf
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
                                        @foreach ($departments->where('college_id', 1) as $department )
                                            <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <!-- End of Form -->
                                <!-- Form -->
                                <div class="form-group text-right">
                                    <select class="custom-select my-1 mr-sm-2" name="academic_year" id="academic_year" data-toggle="tooltip" data-placement="top" title="السنة الدارسية">
                                        @foreach ($academic_years as $academic_year )
                                            <option value="{{ $academic_year->id }}" {{ old('academic_year') == $academic_year->id ? 'selected' : '' }}>{{ $academic_year->start_date }} - {{ $academic_year->end_date }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <!-- End of Form -->
                                <!-- Form -->
                                <div class="form-group text-right">
                                    <select class="custom-select my-1 mr-sm-2" name="term" id="term" data-toggle="tooltip" data-placement="top" title="فصل دراسي">
                                        @foreach ($terms as $term )
                                            <option value="{{ $term }}" {{ old('term') == $term  ? 'selected' : '' }}>{{ $term }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <!-- End of Form -->
                                <div class="form-group">
                                    <select class="custom-select my-1 mr-sm-2" name="level" id="level">
                                        @foreach ($levels as $level )
                                            <option value="{{ $level }}" {{ old('level') == $level ? 'selected' : '' }}>{{ $level }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <!-- End of Form -->
                                <div class="form-group checkbox-group shadow-inset fade show alert alert-success">
                                    <!-- Checkboxes -->
                                    <div class="mb-3">
                                        <span class="h6 font-weight-bold custom-font">نوع المقررات</span>
                                    </div>
                                    <input type="hidden" name="type" id="terms">
                                        @foreach ($types as $type)
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" onclick="selected()" value="{{ $type }}" id="check-{{ $type }}">
                                            <label class="form-check-label" for="check-{{ $type }}" >
                                                {{ $type }}
                                            </label> 
                                        </div>
                                        @endforeach
                                    </div>
                                <!-- End of Form -->
                                <button type="submit" class="btn btn-block btn-primary">اضافة جدول دراسي</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('../../scripts.js')
    <script>
        function selected() {
            let select = [];
            let checkbox = document.querySelectorAll('.checkbox-group input[type=checkbox]');
            for (let i = 0; i < checkbox.length; i++) {
                if (checkbox[i].checked) {
                    select.push(checkbox[i].value)
                }
            }
            document.querySelector("#terms").value = JSON.stringify(select);
        }
        $(document).ready(function() {
            // عند كتابة نص في حقل البحث
            $('#search').on('keyup', function() {
                var keyword = $(this).val(); // الحصول على الكلمة المفتاحية
    
                // إرسال طلب AJAX
                $.ajax({
                    url: "{{ route('schedule-search') }}",
                    type: "GET",
                    data: {'keyword': keyword},
                    success: function(response) {
                        // تفريغ الجدول قبل إضافة النتائج الجديدة
                        $('#tbody').empty();
                        
                        // إضافة النتائج إلى الجدول
                        $.each(response, function(index, schedule) {
                            $('#tbody').append(
                                `
                                <tr class="table-row">
                                    <th scope="row">${schedule.id}</th>
                                    <th scope="row" headers="schedule">${schedule.schedule_name}</th>
                                    <td headers="building">${schedule.college_name}</td>
                                    <td headers="contact">${ schedule.department_name }</td>
                                    <td headers="contact">${ schedule.academic_degree }</td>
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

