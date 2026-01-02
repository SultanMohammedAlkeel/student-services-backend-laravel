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

@include('../teacher.layouts.header')
    <div class="section section-lg pt-0">
        <div class="container">
            <!-- Title -->
            <div class="row">
                <div class="col text-center">
                    <h2 class="h5 mb-7 custom-font"></h2>
                </div>
            </div>
            <!-- End of title-->
            <div class="row justify-content-md-around" id="student-table">
                <div class="col-12 col-md-12 col-lg-7 mb-5 rtl-layout overflow-auto">
                    <div class="row">
                        <div class="col text-center">
                            <h2 class="h5 custom-font">قائمة بجميع طلاب</h2>
                            <table class="table table-hover shadow-inset rounded">
                                <tr>
                                    <th>{{ $level }}</th>
                                    <th>{{ $courses }}</th>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <nav>
                        <div class="nav nav-pills nav-fill flex-column flex-sm-row" id="nav-tab-ecommerce" role="tablist">
                            @foreach ($attendances as $i => $row)
                                        <a class="nav-item nav-link m-1" id="lecture-card-{{ $i }}-tab" data-toggle="tab" href="#lecture-card-{{ $i }}" role="tab" aria-controls="lecture-card-{{ $i }}" aria-selected="false">المحاضرة رقم ({{ $row->lecture_number }})</a>
                            @endforeach
                        </div>
                    </nav>
                    <div class="tab-content mt-4 mt-lg-5" id="nav-tabContent-ecommerce">
                        <div class="form-group ltr-layout" style="width: 100%;">
                            <div class="input-group">
                                <input class="form-control" id="search" placeholder="بحث" aria-label="Input group" type="text">
                                <div class="input-group-append">
                                    <span class="input-group-text"><span class="fas fa-search"></span></span>
                                </div>
                            </div>
                        </div>
                        @foreach ($attendances as $i => $row)
                            <div class="tab-pane fade" id="lecture-card-{{ $i }}" role="tabpanel" aria-labelledby="lecture-card-{{ $i }}-tab">
                                <div class="mb-5">
                                    <table class="table table-hover shadow-inset rounded">
                                        <tr>
                                            <th class="text-center border-0" scope="col" id="id">#</th>
                                            <th class="border-0" scope="col" id="row">اسم الطالب</th>
                                            <th class="text-left border-0" scope="col" id="row">الحالة</th>
                                        </tr>
                                        <tbody id="all-tbody">
                                        @php
                                            $data = json_decode($attendances->where('id', $row->id)->first()->data ?? '[]', true);
                                        @endphp

                                        @foreach ($data as $i => $item)
                                            <tr class="table-row" data-name="{{ $item['name'] }}" id="row-{{ $i }}">
                                                <th>{{ $i }}</th>
                                                <td>{{ $item['name'] }}</td>
                                                <td class="text-left">
                                                    @if ($item['status'] == 1)
                                                        <span class="badge badge-success">حاضر</span>
                                                    @else
                                                        <span class="badge badge-danger">غائب</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                    <table class="table table-hover shadow-inset rounded">
                                        <tr>
                                            <th class="text-center text-info">الحضور</th>
                                            <th class="text-center text-danger">الغياب</th>
                                        </tr>
                                        <tr>
                                            <th class="text-center text-info" id="persent-count">0</th>
                                            <th class="text-center text-danger" id="absent-count">0</th>
                                        </tr>
                                        <tr>
                                            <th class="text-center text-info" id="persent">0</th>
                                            <th class="text-center text-danger" id="absent">0</th>
                                        </tr>
                                    </table>
                                </div>
                            </div>              
                        @endforeach
                    </div>
                </div> 
            </div>
        </div>
    </div>

    @include('../../scripts.js')
    <script>
        $(document).ready(function() {
            // حساب نسبة الحضور والغياب
            $('.tab-pane').each(function() {
                var presentCount = $(this).find('.badge-success').length; // عدد الحضور
                var absentCount = $(this).find('.badge-danger').length; // عدد الغياب
                var totalCount = presentCount + absentCount; // العدد الكلي

                if (totalCount > 0) {
                    var presentPercentage = Math.round((presentCount / totalCount) * 100); // نسبة الحضور
                    var absentPercentage = Math.round((absentCount / totalCount) * 100); // نسبة الغياب
                } else {
                    var presentPercentage = 0;
                    var absentPercentage = 0;
                }

                $(this).find('#persent').text(presentPercentage + '%'); // عرض نسبة الحضور
                $(this).find('#absent').text(absentPercentage   + '%'); // عرض نسبة الغياب

                $(this).find('#persent-count').text(presentCount); // عرض نسبة الحضور
                $(this).find('#absent-count').text(absentCount); // عرض نسبة الغياب
            });
        });
        $(document).ready(function() {
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

