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
        @if (session('mode') == 'admin')
            @include('../admins.layouts.header')  
        @else
            @include('../../layouts.header')
        @endif
    @else
        @include('../../components.404')
        @php
            return;
        @endphp
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
                                        <td style="border-top: none;" class="h2 font-weight-bold text-center custom-font">قائمة بجميع طلاب: </td>
                                        <td style="border-top: none;" class="font-weight-bold text-center custom-font">{{ $college }}</td>
                                        <td style="border-top: none;" class="font-weight-bold text-center custom-font">{{ $department }}</td>
                                        <td style="border-top: none;" class="font-weight-bold text-center custom-font">{{ $level }}</td>
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
                                <th class="border-0" scope="col" id="id">#</th>
                                <th class="border-0" scope="col" id="row">اسم الطالب</th>
                                <th class="border-0" scope="col" id="row">النوع</th>
                                <th class="border-0" scope="col" id="contact">نوع المؤهل</th>
                                <th class="border-0" scope="col" id="contact">سنة الالتحاق</th>
                                <th class="border-0" scope="col" id="row">رقم البطاقة</th>
                                <th class="border-0" scope="col" id="row">التسجيل</th>
                            </tr>
                            <tbody id="tbody">
                                @foreach ($students as $i => $row)
                                <tr class="table-row text-right" data-name="{{ $row->name }}">
                                    <th>{{ $i+1 }}</th>
                                    <th>{{ $row->name }}</th>
                                    <th>{{ $row->gender }}</th>
                                    <th>{{ $row->qualification }}</th>
                                    <th>{{ $row->enrollment_year }}</th>
                                    <th>{{ $row->card }}</th>
                                    @if ($row->is_login == 1)
                                        <td class="contact text-info">مسجل</td>
                                    @else
                                        <td class="contact text-danger">غير مسجل</td>
                                    @endif
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

