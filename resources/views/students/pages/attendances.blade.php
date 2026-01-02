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

    @if (session('user_type') == 'طالب')
        @include('../students.layouts.header')  
    @else
        @include('../components.404')  
    @endif
    <div class="section section-lg pt-0">
        <div class="container">
            <!-- Title -->
            <div class="row">
                <div class="col text-center">
                    <h2 class="h5 mb-7 custom-font"></h2>
                </div>
            </div>
            <!-- End of title-->
            <div class="container my-5" id="attendance-form">
                <div class="row justify-content-center">
                    <div class="col-lg-5 col-md-10">
                        <!-- Form -->
                        <div class="form-group">
                            <select class="custom-select my-1 mr-sm-2" id="temp-period">
                                @foreach ($periods as $row )
                                    <option value="{{ $row }}">{{ $row }}</option>
                                @endforeach
                            </select>
                        </div>
                        <!-- End of Form -->
                        <div class="form-group">
                            <div class="input-group mb-4">
                                <input class="form-control" id="lecture_number" placeholder="رقم الماضرة" aria-label="Input group" type="number" max="12" min="1">
                                <div class="input-group-append">
                                    <span class="input-group-text"><span class="fa-solid fa-people-roof"></span></span>
                                </div>
                            </div>
                        </div>
                        <!-- End of Form -->
                         
                        <button class="btn btn-primary btn-block" onclick="ShowStudent()">عرض الطلاب</button>
                    </div>
                </div>
            </div>
            <div class="row justify-content-md-around hide" id="student-table">
                <div class="col-12 col-md-12 col-lg-7 mb-5 rtl-layout overflow-auto">
                    <div class="row">
                        <div class="col text-center">
                            <h2 class="h5 custom-font">قائمة بجميع طلاب</h2>
                            <div class="form-group ltr-layout" style="width: 100%;">
                                <div class="input-group">
                                    <input class="form-control" id="search" placeholder="بحث" aria-label="Input group" type="text">
                                    <div class="input-group-append">
                                        <span class="input-group-text"><span class="fas fa-search"></span></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-center mb-4 mt-lg-5">
                        <button onclick="AllPresent()" class="btn btn-info text-white nav-item nav-link mx-2 w-25 text-center">الكل حضور</button>
                        <button onclick="AllAbsent()" class="btn btn-danger text-white nav-item nav-link mx-2 w-25 text-center">الكل غياب</button>
                    </div>
                    <div class="col-12 col-md-12 col-lg-12">
                        <nav>
                            <div class="nav d-flex justify-content-center" id="nav-tab-ecommerce" role="tablist">
                                <a class="btn btn-primary nav-item nav-link mx-2 w-25 text-center" id="nav-student-card-tab" data-toggle="tab" href="#nav-student-card" role="tab" aria-controls="nav-student-card" aria-selected="true">جميع الطلاب</a>
                                <a class="btn btn-primary nav-item nav-link mx-2 w-25 text-center" id="nav-persent-tab" data-toggle="tab" href="#nav-persent" role="tab" aria-controls="nav-persent" aria-selected="false">الحضور</a>
                                <a class="btn btn-primary nav-item nav-link mx-2 w-25 text-center" id="nav-absent-tab" data-toggle="tab" href="#nav-absent" role="tab" aria-controls="nav-absent" aria-selected="false">الغياب</a>
                            </div>
                        </nav>
                        <div class="tab-content mt-4 mt-lg-5" id="nav-tabContent-ecommerce">
                            <div class="tab-pane fade show active" id="nav-student-card" role="tabpanel" aria-labelledby="nav-student-card-tab">
                                <div class="mb-5">
                                    <table class="table table-hover shadow-inset rounded">
                                        <tr>
                                            <th class="text-center border-0" scope="col" id="id">#</th>
                                            <th class="border-0" scope="col" id="row">اسم الطالب</th>
                                            <th class="text-left border-0" scope="col" id="row">الحالة</th>
                                        </tr>
                                        <tbody id="all-tbody"></tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="nav-persent" role="tabpanel" aria-labelledby="nav-persent-tab">
                                <div class="mb-5">
                                    <table class="table table-hover shadow-inset rounded">
                                        <tr>
                                            <th class="text-center border-0" scope="col" id="id">#</th>
                                            <th class="border-0" scope="col" id="row">اسم الطالب</th>
                                            <th class="text-left border-0" scope="col" id="row">الحالة</th>
                                        </tr>
                                        <tbody id="persent-tbody"></tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="nav-absent" role="tabpanel" aria-labelledby="nav-absent-tab">
                                <div class="mb-5">
                                    <table class="table table-hover shadow-inset rounded">
                                        <tr>
                                            <th class="text-center border-0" scope="col" id="id">#</th>
                                            <th class="border-0" scope="col" id="row">اسم الطالب</th>
                                            <th class="text-left border-0" scope="col" id="row">الحالة</th>
                                        </tr>
                                        <tbody id="absent-tbody"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <form action="{{ route('attendances.store') }}" method="post">
                            @csrf
                            <input type="hidden" name="period" id="period" value="">
                            <input type="hidden" name="lecture_number" id="lecture" value="">
                            <input type="hidden" name="data" id="data" value="">
                            <button class="btn btn-primary btn-block mb-1">ارسال</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('../../scripts.js')
    <script>
        let data = [];
        let present = []; // مصفوفة لتخزين الطلاب الحاضرين
        let absent = []; // مصفوفة لتخزين الطلاب الغائبين
        function ShowStudent() {
            if ($('#lecture_number').val() == '') {
                alert('يرجى إدخال رقم المحاضرة');
                return;
            }
            // التاكد من ان رقم الماضرة بين 1 و 12
            if ($('#lecture_number').val() < 1 || $('#lecture_number').val() > 12) {
                alert('رقم المحاضرة يجب أن يكون بين 1 و 12');
                return;   
            }

            $.ajax({
                url: '/chack-lecture',
                method: 'POST',
                data: {
                    period: $('#temp-period').val(),
                    _token: '{{ csrf_token() }}' // CSRF token
                },
                success: function(response) {
                    if (response.success != true) {
                        alert(response.message);
                        return;
                    } else {
                        $('#period').val($('#temp-period').val());
                        $('#lecture').val($('#lecture_number').val());
                        $('#student-table').removeClass('hide');
                        $('#attendance-form').addClass('hide');
                    }                    
                },
                error: function(xhr) {
                    console.error('Error recording interaction:', xhr.responseText);
                }
            });

        }

        $(document).ready(function() {
            // دالة لجلب بيانات الطلاب
            function fetchStudents() {
                $.ajax({
                    url: '/get-students',
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        // مثال لعرض البيانات في جدول
                        displayStudents(response);
                    },
                    error: function(xhr, status, error) {
                        console.error('حدث خطأ أثناء جلب البيانات:', error);
                    }
                });
            }

            // دالة لعرض الطلاب في الجدول (يمكنك تعديلها حسب احتياجاتك)
            function displayStudents(students) {
                var tableBody = $('#all-tbody');
                tableBody.empty(); // مسح المحتوى القديم إذا وجد
                
                $.each(students, function(index, student) {
                    let dataObject = {
                        id: student.id,
                        name: student.name,
                        status: ''
                    }
                    data.push(dataObject);
                    
                    tableBody.append(
                        `<tr id="row-${student.id}" class="table-row" data-name="${student.name}">
                            <td> ${(index + 1)} </td>
                            <td> ${student.name} </td>
                            <td class='text-left'> 
                                <button class="btn btn-info m-1" onclick="Present(${student.id})">حاضر</button> 
                                <button class="btn btn-danger m-1" onclick="Absent(${student.id})">غائب</button> 
                            </td>
                        </tr>`
                    );
                });
            }

            // استدعاء الدالة عند تحميل الصفحة
            fetchStudents();
        });

        // دالة لتحديث حالة الطالب إلى "حاضر"
        function Present(id)
        {
            let student = data.find(student => student.id === id);
            if (student) {
                student.status = '1';
                present.push(student);
                updateTable('persent-tbody', present);
            }
            $('#row-' + id).hide();
            // حذف الطالب من قائمة الغيب اذا تم تعديله 
            absent = absent.filter(student => student.id !== id);
            updateTable('absent-tbody', absent);
        }

        // دالة لتحديث حالة الطالب إلى "حاضر"
        function AllPresent()
        {
            // جعل جميع الطلاب في حالة الحضور
            data.forEach(student => {
                student.status = '1';
                present.push(student); 
                $('#row-' + student.id).hide();
            });
            updateTable('persent-tbody', present);
              
            // تفريغ جدوال الغياب
            absent = []; // تفريغ مصفوفة الغياب
            updateTable('absent-tbody', absent);
        }
        
        function AllAbsent()
        {
            // جعل جميع الطلاب في حالة الغياب
            data.forEach(student => {
                student.status = '0';
                absent.push(student);
                $('#row-' + student.id).hide();
            });
            updateTable('absent-tbody', absent);

            // تفريغ جدوال الحضور
            present = []; // تفريغ مصفوفة الحضور
            updateTable('persent-tbody', present);
        }

        function updateTable(tableId, students) {
            let tableBody = $('#' + tableId);
            tableBody.empty(); // مسح المحتوى القديم إذا وجد
            
            $.each(students, function(index, student) {
                tableBody.append(
                    `<tr id="row-${student.id}" class="table-row" data-name="${student.name}">
                        <td> ${(index + 1)} </td>
                        <td> ${student.name} </td>
                        <td class='text-center'> ${student.status == 0? '<button class="btn text-info m-1" onclick="Present('+student.id+')">تعديل الى حضور</button> ' : '<button class="btn text-danger m-1" onclick="Absent('+student.id+')">تعديل الى غياب</button> '} </td>
                    </tr>`
                );
            });
            $('#data').val(JSON.stringify(data)); 
        }
        // دالة لتحديث حالة الطالب إلى "غائب"
        function Absent(id) 
        {
            let student = data.find(student => student.id === id);
            if (student) {
                student.status = '0';
                absent.push(student);
                updateTable('absent-tbody', absent);
            }
            // إخفاء الصف في الجدول
            $('#row-' + id).hide(); 
            // حذف الطالب من قائمة الحضور اذا تم تعديله
            present = present.filter(student => student.id !== id);
            updateTable('persent-tbody', present);
        }

        $(document).ready(function() {
            // عند كتابة نص في حقل البحث
            $(document).ready(function() {
                $('#search').on('input', function() {
                    var searchText = $(this).val().toLowerCase(); // النص المدخل للبحث
                    $('.table-row').each(function() {
                        var itemName = $(this).data('name').toLowerCase(); // اسم العنصر
                        // إظهار أو إخفاء العنصر بناءً على البحث
                        if (itemName.includes(searchText)) {
                            $(this).removeClass('hide');
                        } else {
                            $(this).addClass('hide');
                        }
                    });
                });
            });
        });
    </script>
</body>
</html>

