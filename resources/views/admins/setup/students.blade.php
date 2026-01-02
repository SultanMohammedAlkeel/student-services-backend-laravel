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
                <div class="col-12 col-md-12 col-lg-12 mb-5 mb-lg-0 rtl-layout overflow-auto">
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
                                <th class="border-0" scope="col" id="row">اسم الطالب</th>
                                <th class="border-0" scope="col" id="row">النوع</th>
                                <th class="border-0" scope="col" id="row">المستوى</th>
                                <th class="border-0" scope="col" id="building">الكلية</th>
                                <th class="border-0" scope="col" id="contact">القسم</th>
                                <th class="border-0" scope="col" id="contact">نوع المؤهل</th>
                                <th class="border-0" scope="col" id="contact">سنة الالتحاق</th>
                            </tr>
                            <tbody id="tbody">
                                @foreach ($data as $i => $row)
                                <tr class="table-row" data-name="{{ $row['name'] }}">
                                    <th scope="row">{{ $i + 1 }}</th>
                                    <th scope="row" headers="row" class="data-item">{{ $row['name'] }}</th>
                                    <th scope="row">{{ $row['gender'] }}</th>
                                    <th scope="row">{{ $level[$row['level'] -1] }}</th>
                                    <td headers="building">{{ $colleges->where('id', $row['college'])->first()->name }}</td>
                                    <td headers="contact">{{ $departments->where('id', $row['department'])->first()->name }}</td>
                                    <td headers="contact">{{ $row['qualification'] }}</td>
                                    <td headers="contact">{{ $row['enrollment_year'] }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <a href="{{ route('student.upload-students', '') }}/{{ $id }}" class="btn btn-icon-only btn-add"> 
        <span aria-hidden="true" class="fab fa-add"></span>
    </a>


    @include('../../scripts.js')

    <script>
        $(document).ready(function() {
            // عند كتابة نص في حقل البحث
            $(document).ready(function() {
                $('#search').on('input', function() {
                    var searchText = $(this).val().toLowerCase(); // النص المدخل للبحث
                    $('.table-row').each(function() {
                        var itemName = $(this).data('name').toLowerCase(); // اسم العنصر
                        console.log(itemName);

                        // إظهار أو إخفاء العنصر بناءً على البحث
                        if (itemName.includes(searchText)) {
                            $(this).removeClass('hide');
                        } else {
                            $(this).addClass('hide');
                        }
                    });
                });
            });
            $('#college_id').change(function () {
                var college_id = $(this).val(); 

                if (college_id) {
                    $.ajax({
                        url: '/get-department-s/' + college_id,
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
           
            $('#department_id').change(function () {
                var search = $('#search').val();
                $.ajax({
                    url: '/get-student-data',
                    type: 'GET',
                    data: { search: search },
                    success: function(response) {
                        $('#results').empty();
                        console.log(response);
                        
                        // response.forEach(function(item) {
                        //     $('#results').append('<li>' + item.name + ' - ' + item.email + '</li>');
                        // });
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText);
                    }
                });
            });
        });
    </script>
</body>
</html>

