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
            <div class="row justify-content-md-around contact-section">
                <div class="col-12 col-md-6 col-lg-6 mb-5 mb-lg-0">
                    <div class="form-group ltr-layout">
                        <div class="input-group mb-4">
                            <a href="{{ route('chat.index') }}" class="btn btn-icon-only btn-facebook" type="button" aria-label="add button" title="add button" onclick="showAddRow()"> 
                                <span aria-hidden="true" class="fa-solid fa-chevron-right"></span>
                            </a>
                            <input class="form-control" id="search" placeholder="بحث" aria-label="Input group" type="text">
                            <div class="input-group-append">
                                <span class="input-group-text"><span class="fas fa-search"></span></span>
                            </div>
                        </div>
                    </div>
                    <div id="user-secion">
                        @foreach ($users as $user)
                            <div class="btn d-flex justify-content-between align-items-center py-1 my-1 list-contant rtl-layout">
                                <span class="font-small d-flex align-items-center">
                                    <img class="avatar-lg img-fluid rounded-circle ml-3 " src="{{ $user->image_url }}"
                                        alt="avatar">
                                    <div class="">
                                        <p class="mb-0 custom-font">{{ $user->name }}</p>
                                        <small
                                            class=" custom-font">{{ $roles->where('id', $user->role_id)->first()->name }}</small>
                                    </div>
                                </span>
                                <div>
                                    <form action="{{ route('contact.store') }}" method="post">
                                        @csrf
                                        <input type="hidden" name="friend_id" value="{{ $user->id }}">
                                        <input type="hidden" name="friend_type" value="{{ $user->user }}">
                                        <button class="btn ml-3 bg-success text-white">اضافة</button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-4 mb-5 mb-lg-0 rtl-layout">
                    <!--Accordion-->
                    <div class="accordion shadow-soft rounded" id="accordionExample1">
                        <div class="card card-sm card-body bg-primary border-light mb-0">
                            <a href="#panel-1" data-target="#panel-1" class="accordion-panel-header"
                                data-toggle="collapse" role="button" aria-expanded="false" aria-controls="panel-1">
                                <span class="h6 mb-0 font-weight-bold custom-font"># تحديد المستخدم</span>
                                <span class="icon"><span class="fas fa-plus"></span></span>
                            </a>
                            <div class="collapse" id="panel-1">
                                <div class="pt-3">
                                    <p class="mb-0">
                                        <div class="card-body">
                                            <form method="post" action="{{ route('contact.select-user') }}"
                                                enctype="multipart/form-data">
                                                @csrf
                                                <!-- Form -->
                                                <div class="form-group">
                                                    <select class="custom-select my-1 mr-sm-2" name="type"
                                                        id="type" data-toggle="tooltip" data-placement="top"
                                                        title="نوع المستخدم" onchange="typeOfUser(this.value)">
                                                        @foreach ($types as $type)
                                                            <option value="{{ $type }}" {{ old('type') == $type ? 'selected' : '' }}> {{ $type }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <!-- End of Form -->
                                                <!-- Form -->
                                                <div class="form-group hide" id="college">
                                                    <select class="custom-select my-1 mr-sm-2" id="college_id" name="college_id">
                                                        @foreach ($colleges as $college)
                                                            <option value="{{ $college->id }}"
                                                                {{ old('college_id') == $college->id ? 'selected' : '' }}>
                                                                {{ $college->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <!-- End of Form -->
                                                <!-- Form -->
                                                <div class="invalid-feedback show">
                                                    {{ $errors->first('department_id') }}</div>
                                                <div class="form-group hide" id="department"> 
                                                    <select class="custom-select my-1 mr-sm-2" name="department_id"
                                                        id="department_id">
                                                        @foreach ($departments->where('college_id', 1) as $department)
                                                            <option value="{{ $department->id }}"
                                                                {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                                                {{ $department->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <!-- End of Form -->
                                                <!-- Form -->
                                                <div class="form-group hide" id="level">
                                                    <select class="custom-select my-1 mr-sm-2" name="level"
                                                        id="level">
                                                        @foreach ($levels as $level)
                                                            <option value="{{ $level }}"
                                                                {{ old('level') == $level ? 'selected' : '' }}>
                                                                {{ $level }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <!-- End of Form -->
                                                <button type="submit" class="btn btn-block btn-primary">تحديد</button>
                                            </form>
                                        </div>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--End of Accordion-->
                </div>
            </div>
        </div>
    </div>

    @include('../../scripts.js')
    <script>
        function typeOfUser(val) {
            if (val == 'مشرف') {
                $('#college').addClass('hide');
                $('#department').addClass('hide');
                $('#level').addClass('hide');
            } else if (val == 'معلم') {
                $('#college').removeClass('hide');
                $('#department').removeClass('hide');
                $('#level').addClass('hide');
            } else {
                $('#college').removeClass('hide');
                $('#department').removeClass('hide');
                $('#level').removeClass('hide');
            }
        }
        $(document).ready(function() {

            // عند كتابة نص في حقل البحث
            $('#search').on('keyup', function() {
                var keyword = $(this).val(); // الحصول على الكلمة المفتاحية

                // إرسال طلب AJAX
                $.ajax({
                    url: "{{ route('contact-search') }}",
                    type: "GET",
                    data: {
                        'keyword': keyword
                    },
                    success: function(response) {
                        $('#user-secion').empty();
                        $.each(response, function(index, user) {
                            $('#user-secion').append(
                                `
                                 <div class="btn d-flex justify-content-between align-items-center py-1 my-1 list-contant rtl-layout">
                                    <span class="font-small d-flex align-items-center">
                                        <img class="avatar-lg img-fluid rounded-circle ml-3 " src="${ user.image_url }"
                                            alt="avatar">
                                        <div class="">
                                            <p class="mb-0 custom-font">${user.name}</p>
                                            <small class=" custom-font">${ user.role_name }</small>
                                        </div>
                                    </span>
                                    <div>
                                        <form action="{{ route('contact.store') }}" method="post">
                                            @csrf
                                            <input type="hidden" name="friend_id" value="${ user.id }">
                                            <input type="hidden" name="friend_type" value="${user.user}">
                                            <button class="btn ml-3 bg-success text-white">اضافة</button>
                                        </form>
                                    </div>
                                </div>
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
