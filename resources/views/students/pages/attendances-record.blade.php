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
            <div class="row justify-content-md-around" id="student-table">
                <div class="col-12 col-md-12 col-lg-7 mb-5 rtl-layout overflow-auto">
                    <div class="row">
                        <div class="col text-center">
                            <h2 class="h5 custom-font">قائمة بجميع طلاب</h2>
                            <table class="table table-hover shadow-inset rounded">
                                <tr>
                                    <th>{{ $period }}</th>
                                    <th>{{ $teacher }}</th>
                                    <th>{{ $course }}</th>
                                </tr>
                            </table>
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
                    <div class="col-12 col-md-12 col-lg-12">
                        <div class="mb-5">
                            <table class="table table-hover shadow-inset rounded">
                                <tr>
                                    <th class="text-center border-0" scope="col" id="id">#</th>
                                    <th class="border-0" scope="col" id="row">اسم الطالب</th>
                                    <th class="text-left border-0" scope="col" id="row">الحالة</th>
                                </tr>
                                <tbody id="all-tbody">
                                    @foreach ($data as $i => $row)
                                        <tr class="table-row" data-name="{{ $row['name'] }}" id="row-{{ $i }}">
                                            <th>{{ $i }}</th>
                                            <td>{{ $row['name'] }}</td>
                                            @if ($row['status'] == 1)
                                                <td class="text-left">
                                                    <span class="badge badge-success">حاضر</span>
                                                </td>
                                            @else
                                                <td class="text-left">
                                                    <span class="badge badge-danger">غائب</span>
                                                </td>
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
    </div>

    @include('../../scripts.js')
    <script>
        $(document).ready(function() {

            $(document).ready(function() {
                $('#search').on('input', function() {
                    var searchText = $(this).val().toLowerCase(); // النص المدخل للبحث
                    $('.table-row').each(function() {
                        var itemName = $(this).data('name').toLowerCase(); // اسم العنصر
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

