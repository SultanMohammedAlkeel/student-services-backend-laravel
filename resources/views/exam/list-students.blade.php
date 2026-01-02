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
                    <div class=" d-flex justify-content-between align-items-center">
                        <div class="row">
                            <div class="col text-center">
                                <table class="table shadow-inset">
                                    <tr class="d-flex align-items-center border-0">
                                        <td style="border-top: none;"><h1>#</h1></td>
                                        <td style="border-top: none;" class="h2 font-weight-bold text-center custom-font">قائمة بجميع من اخذ اختبار: </td>
                                        <td style="border-top: none;" class="font-weight-bold text-center custom-font">{{ $exam->name }}</td>
                                        <td style="border-top: none;" class="font-weight-bold text-center custom-font">{{ $departments->where('id', $exam->department_id)->first()->name }}</td>
                                        <td style="border-top: none;" class="font-weight-bold text-center custom-font">{{ $exam->level }}</td>
                                    </tr>
                                </table>
                                <div class="form-group ltr-layout" style="width: 70%;">
                                    <div class="input-group">
                                        <input class="form-control" id="search" placeholder="بحث" aria-label="Input group" type="text">
                                        <div class="input-group-append">
                                            <span class="input-group-text"><span class="fas fa-search"></span></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-5">
                        <table class="table table-hover shadow-inset rounded">
                            <tr>
                                <th class="border-0 text-center" scope="col" id="id">#</th>
                                <th class="border-0 text-center" scope="col" id="row">اسم الطالب</th>
                                <th class="border-0 text-center" scope="col" id="row">النوع</th>
                                <th class="border-0 text-center">الاجابات الصحيحة</th>
                                <th class="border-0 text-center">الاجابات الخاطئة</th>
                                <th class="border-0 text-center" scope="col" id="row">النتيجة</th>
                            </tr>
                            <tbody id="tbody">
                                @foreach ($examRecord as $i => $row)
                                @php
                                    $user_id = $users->where('id', $row->student_id)->first()->user_id;
                                    $student = $students->where('id', $user_id)->first();
                                @endphp
                                <tr class="table-row text-right" data-name="{{ $student->name}}">
                                    <th class="text-center">{{ $i+1 }}</th>
                                    <th>{{ $student->name }}</th>
                                    <td class="text-center">{{ $student->gender }}</td>
                                    <td class="text-center">{{ $row->correct }}</td>
                                    <td class="text-center">{{ $row->wrong }}</td>
                                    <th class="text-center custom-font {{  $row->score < 50 ? 'text-danger' : 'text-behance' }}">{{ $row->score }}%</th>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
        });
    </script>
</body>
</html>

