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
                <div class="col-12 col-md-6 col-lg-6 mb-5 mb-lg-0 rtl-layout overflow-auto">
                    <div class="mb-5">
                        <div class="form-group ltr-layout">
                            <div class="input-group mb-4">
                                <button class="btn btn-icon-only btn-facebook" type="button" aria-label="add button" title="add button" onclick="showAddRow()"> 
                                    <span aria-hidden="true" class="fab fa-add"></span>
                                </button>
                                <input class="form-control" id="search" placeholder="بحث" aria-label="Input group" type="text">
                                <div class="input-group-append">
                                    <span class="input-group-text"><span class="fas fa-search"></span></span>
                                </div>
                            </div>
                        </div>
                        <table class="table table-hover shadow-inset rounded">
                            <tr>
                                <th class="border-0" scope="col" id="id">#</th>
                                <th class="border-0" scope="col" id="role">المنصه </th>
                                <th class="border-0" scope="col" id="college">البيان</th>
                                <th class="border-0" scope="col" id="id">ايقونة</th>
                            </tr>
                            <tbody id="tbody">
                                @foreach ($contactInfos as $i => $contactInfo)
                                <tr class="table-row">
                                    <th scope="row">{{ $i+1 }}</th>
                                    <th scope="row" headers="role">{{ $contactInfo->platform }}</th>
                                    <th scope="row" headers="role"><a href="{{ $contactInfo->url }}" target="_blank">{{ $contactInfo->url }}</a></th>
                                    <th scope="row"><?php echo $icons[$contactInfo->platform];?></th>
                                </tr>
                                @endforeach
                                <tr class="add-row hide">
                                    <form action="{{ route('contactInfo.store') }}" method="post">
                                        @csrf
                                        <th scope="row" id="row-id"></th>
                                        <th scope="row" headers="role">
                                            <div class="form-group">
                                                <select class="custom-select my-1 mr-sm-2" name="platform">
                                                    @foreach ($platforms as $platform )
                                                    <option value="{{ $platform }}">{{ $platform }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </th>
                                        <th scope="row" headers="role">
                                            <div class="form-group">
                                                <div class="input-group mb-4">
                                                    <input class="form-control" name="url" placeholder="الرابط"
                                                    aria-label="Input group" type="text" value="{{ old('url') }}">
                                                </div>
                                            </div>
                                        </th>
                                        <th><button type="submit" class="btn btn-block btn-primary">حفظ</button></th>
                                    </form>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-4 mb-5 mb-lg-0">
                    <div class="card bg-primary shadow-soft border-light p-4">
                        <div class="card-header text-center pb-0">
                            <h2 class="h4 custom-font">حسابي</h2>
                        </div>
                        <div class="card-body">
                            <form method="post" action="{{ route('user.update-account') }}" enctype="multipart/form-data" >
                                @csrf
                                <div class="center-image">
                                    <img src="{{ asset(session('image_url')) }}" id="preview" class="rounded-circle img-thumbnail image-lg border-light shadow-inset p-3 profile-img" alt="{{ session('username') }}">
                                </div>
                                <!-- Form -->
                                <input type="hidden" name="id" value="{{ session('user_id') }}">
                                <input type="hidden" name="image" value="{{ session('image_url') }}">
                                <div class="invalid-feedback show">{{ $errors->first('name') }}</div>
                                <div class="form-group">
                                    <div class="input-group mb-4">
                                        <input class="form-control" name="name" placeholder="اسم المستخدم"
                                            aria-label="Input group" type="text" value="{{ session('username') }}">
                                        <div class="input-group-append">
                                            <span class="input-group-text"><span class="fas fa-user"></span></span>
                                        </div>
                                    </div>
                                </div>
                                <!-- End of Form -->
                                <!-- Form -->
                                <div class="invalid-feedback show">{{ $errors->first('image_url') }}</div>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" name="image_url" aria-label="File upload" id="imageUpload" accept="image/*" value="{{ session('image_url') }}">
                                    <label class="custom-file-label" for="customFile">Choose Profile Image</label>
                                </div>
                                <br> <br>
                                <button type="submit" class="btn btn-block btn-primary">تعديل الحساب</button>
                            </form>
                            <br>
                            <button class="btn btn-pill btn-primary" type="button" aria-label="add button" title="add button" data-toggle="modal" data-target="#modal-form">
                                تغير كلمة السر
                                <span class="ml-1"><span aria-hidden="true" class="fa-solid fa-unlock-keyhole"></span></span>
                            </button>
                            <button class="btn btn-pill btn-primary" type="button" aria-label="add button" title="add button" data-toggle="modal" data-target="#modal-form-student-data">
                                تعديل بياناتي
                                <span class="ml-1"><span aria-hidden="true" class="fas fa-cog"></span></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-form" tabindex="-1" role="dialog" aria-labelledby="modal-form" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body p-0">
                    <div class="card bg-primary shadow-soft border-light p-4">
                        <button type="button" class="close ml-auto" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                        <div class="card-header text-center pb-0">
                            <h2 class="h4 custom-font">تغير كلمة السر</h2>
                            <div class="py-3 text-center">
                                <span class="modal-icon display-1-lg">
                                    <span class="fa-solid fa-unlock-keyhole"></span>
                                </span>
                            </div>
                        </div> <!-- إغلاق div.card-header -->
                        <form method="post" action="/change-password" enctype="multipart/form-data" class="mt-4">
                            @csrf
                            <div class="invalid-feedback show">{{ $errors->first('lastPassword') }}</div>
                            <div class="form-group">
                                <div class="input-group mb-4">
                                    <input class="form-control" name="lastPassword" placeholder="كلمة المرور القديمة" aria-label="Input group" type="password">
                                    <div class="input-group-append">
                                        <span class="input-group-text"><span class="fa-solid fa-key"></span></span>
                                    </div>
                                </div>
                            </div>
                            <div class="invalid-feedback show">{{ $errors->first('newPassword') }}</div>
                            <div class="invalid-feedback show">{{ $errors->first('password') }}</div>
                            <div class="form-group">
                                <div class="input-group mb-4">
                                    <input class="form-control" name="newPassword" placeholder="كلمة المرور الجديدة" aria-label="Input group" type="password">
                                    <div class="input-group-append">
                                        <span class="input-group-text"><span class="fa-solid fa-lock"></span></span>
                                    </div>
                                </div>
                            </div>
                            <div class="invalid-feedback show">{{ $errors->first('againPassword') }}</div>
                            <div class="invalid-feedback show">{{ $errors->first('password') }}</div>
                            <div class="form-group">
                                <div class="input-group mb-4">
                                    <input class="form-control" name="againPassword" placeholder="اعادة كلمة المرور" aria-label="Input group" type="password">
                                    <div class="input-group-append">
                                        <span class="input-group-text"><span class="fa-solid fa-lock"></span></span>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <button type="submit" class="btn btn-block btn-primary">حفظ</button>
                        </form>
                    </div> <!-- إغلاق div.card -->
                </div> <!-- إغلاق div.modal-body -->
            </div> <!-- إغلاق div.modal-content -->
        </div> <!-- إغلاق div.modal-dialog -->
    </div> 

    @if (session('user_type') == 'طالب')
        <div class="modal fade" id="modal-form-student-data" tabindex="-1" role="dialog" aria-labelledby="modal-form-student-data" aria-hidden="true" style="display: none;">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-body p-0">
                        <div class="card bg-primary shadow-soft border-light p-4">
                            <div class="modal-header">
                                <p class="modal-title custom-font" id="modal-title-delete">عرض و تعديل</p>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                            </div>
                            <div class="card-header text-center pb-0">
                                <h2 class="h4 custom-font">بياناتي الاكاديمية</h2>
                            </div> <!-- إغلاق div.card-header -->
                            <form method="post" action="{{ route('students.update-data') }}" enctype="multipart/form-data" class="mt-4">
                                @csrf
                                <div class="card card-sm card-body bg-primary border-light mb-0">
                                    <a href="#panel-4" data-target="#panel-4" class="accordion-panel-header" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="panel-1">
                                        <span class="icon-title h6 mb-0 font-weight-bold custom-font"><span class="fa-solid fa-id-card"></span>بياناتي الاساسي</span>
                                        <span class="icon"><span class="fas fa-plus"></span></span>
                                    </a>
                                    <div class="collapse" id="panel-4">
                                        <div class="pt-3">
                                            <p class="mb-0">
                                                <div class="form-group disabled">
                                                    <div class="input-group mb-4">
                                                        <input class="form-control" placeholder="اسم المستخدم" aria-label="Input group" type="text" value="{{ session('sname') }}">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text"><span class="fas fa-user"></span></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group disabled">
                                                    <div class="input-group mb-4">
                                                        <input class="form-control" placeholder="رقم البطاقة" aria-label="Input group" type="text" value="{{ session('card') }}">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text"><span class="fa-solid fa-address-card"></span></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group disabled">
                                                    <div class="input-group mb-4">
                                                        <input class="form-control" placeholder="النوع" aria-label="Input group" type="text" value="{{ session('gender') }}">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text"><span class="fa-solid fa-person"></span></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group disabled">
                                                    <div class="input-group mb-4">
                                                        <input class="form-control" aria-label="Input group" type="text" value="{{ $college->where('id', session('college'))->first()->name }}">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text"><span class="fa-solid fa-building-columns"></span></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group disabled">
                                                    <div class="input-group mb-4">
                                                        <input class="form-control" aria-label="Input group" type="text" value="{{ $department->where('id', session('department_id'))->first()->name }}">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text"><span class="fa-solid fa-building-user"></span></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group disabled">
                                                    <div class="input-group mb-4">
                                                        <input class="form-control" aria-label="Input group" type="text" value="{{ session('level')}}">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text"><span class="fa-solid fa-layer-group"></span></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group disabled" data-toggle="tooltip" data-placement="top" title="تاريخ تسجيل الدخول">
                                                    <div class="input-group mb-4">
                                                        <input class="form-control" placeholder="القسم" aria-label="Input group" type="text" value="{{ session('login')}}">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text"><span class="far fa-calendar-alt"></span></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <input type="hidden" name="id" value="{{ session('user_ref_id') }}">
                                <div class="invalid-feedback show">{{ $errors->first('address') }}</div>
                                <div class="form-group">
                                    <div class="input-group mb-4">
                                        <input class="form-control" name="address" placeholder="المحافظة - العنوان"
                                            aria-label="Input group" type="text" value="{{ session('address') }}">
                                        <div class="input-group-append">
                                            <span class="input-group-text"><span class="fa-solid fa-map-location-dot"></span></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="invalid-feedback show">{{ $errors->first('birth_date') }}</div>
                                <div class="form-group">
                                    <label class="h6 text-right custom-font" style="width: 100%;" for="exampleInputDate3">تاريخ الميلاد</label>
                                    <div class="input-group input-group-border">
                                        <div class="input-group-prepend"><span class="input-group-text"><span class="far fa-calendar-alt"></span></span></div>
                                        <input class="form-control datepicker" name="birth_date" placeholder="Birth Date" type="text" value="{{ session('birth_date') }}">
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-block btn-primary">حفظ</button>
                            </form>
                        </div> 
                    </div> 
                </div> 
            </div> 
        </div> 
    @endif

    @include('../../scripts.js')
    <script>
        function showAddRow() {
            let table = document.querySelector(".add-row");
            table.classList.toggle('hide');
            let rowId = document.querySelector("#row-id");
            rowId.innerHTML = document.querySelectorAll(".table-row").length + 1;
        }
        document.getElementById('imageUpload').addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                const preview = document.getElementById('preview');
                preview.src = e.target.result;
                preview.style.display = 'block';
                }
                
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>

