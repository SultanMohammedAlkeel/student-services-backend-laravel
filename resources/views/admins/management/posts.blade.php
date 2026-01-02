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
                        <p class="custom-font mx-3" style="width: fit-content;">قائمة بجميع المشاركين</p>
                        <div class="form-group ltr-layout" style="width: 70%;">
                            <div class="input-group">
                                <input class="form-control" id="search" placeholder="بحث" aria-label="Input group" type="text">
                                <div class="input-group-append">
                                    <span class="input-group-text"><span class="fas fa-search"></span></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-5">
                        <table class="table table-hover shadow-inset rounded">
                            <tr>
                                <th class="border-0 text-center" scope="col" rowspan="2">#</th>
                                <th class="border-0 text-center" scope="col" rowspan="2">صورة</th>
                                <th class="border-0" scope="col" rowspan="2">اسم المستخدم</th>
                                <th class="border-0 text-center" scope="col" rowspan="2">نوع الحساب</th>
                                <th class="border-0 text-center" scope="col" rowspan="2">عدد النشورات</th>
                                <th class="border-0 text-center" scope="col" rowspan="2">منشورات محذوفة</th>
                                <th class="border-0 text-center" scope="col" rowspan="2">عدد الوسائط</th>
                                <th class="border-0 text-center" scope="col" rowspan="2">عرض البيانات</th>
                            </tr>
                            <tbody id="tbody">
                                @foreach ($sendersWithCount as $i => $row)
                                <tr class="table-row text-right" data-name="{{ $users->where('id', $row->id)->first()->name }} {{ $users->where('id', $row->id)->first()->user }}">
                                    <th class="text-center">{{ $i+1 }}</th>
                                    <td class="text-center"><img src="{{ asset($users->where('id', $row->id)->first()->image_url) }}" alt="user image" class="rounded-circle" style="width: 50px; height: 50px;"></td>
                                    <td> {{ $users->where('id', $row->id)->first()->name }}</td>
                                    <td class="text-center">{{ $users->where('id', $row->id)->first()->user }}</td>
                                    <th class="text-center">{{ $row->count }}</th>
                                    <th class="text-center">{{ $posts->where('sender_id', $row->id )->where('deleted', 1)->count() }}</th>
                                    <th class="text-center">{{ $posts->where('sender_id', $row->id )->where('file_url', '!=' , null)->count() }}</th>
                                    <td class="text-center"><a href="{{ route('user.user-posts', '') }}/{{ $users->where('id', $row->id)->first()->code }}" class="btn btn-primary" type="button">عرض</a></td>
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