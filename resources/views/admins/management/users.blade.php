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
                        <p class="custom-font mx-3" style="width: fit-content;">قائمة بجميع المستخدمين</p>
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
                                <th class="border-0 text-center" scope="col" rowspan="2">النوع</th>
                                <th class="border-0 text-center" scope="col" rowspan="2">الصلاحيات</th>
                                <th class="border-0 text-center" scope="col" rowspan="2">الحالة</th>
                                <th class="border-0 text-center" scope="col" rowspan="2">عرض البيانات</th>
                            </tr>
                            <tbody id="tbody">
                                @foreach ($users as $i => $row)
                                <tr class="table-row text-right" data-name="{{ $row->name }} {{ $row->user }}">
                                    <th class="text-center">{{ $i+1 }}</th>
                                    <td class="text-center"><img src="{{ asset($row->image_url) }}" alt="user image" class="rounded-circle" style="width: 50px; height: 50px;"></td>
                                    <td> {{ $row->name }}</td>
                                    <td class="text-center"> {{ $row->gender }}</td>
                                    <th class="text-center">{{ $roles->where('id', $row->role_id)->first()->name }}</th>
                                    @if ($row->is_active == 1)
                                        <td class="text-center"><span class="badge badge-success">فعال</span></td>
                                    @else
                                        <td class="text-center"><span class="badge badge-danger">موقف</span></td>
                                    @endif
                                    <td aria-hidden="true" class="text-center" data-toggle="modal" data-target="#info-icon-{{ $row->id }}">
                                        <button class="btn btn-primary" type="button" aria-label="add to cart button" title="عرض البيانات" data-toggle="modal" data-target="#info-icon-{{ $row->id }}">عرض</button>
                                    </td>
                                </tr>
                                <div class="modal fade ltr-layout" id="info-icon-{{ $row->id }}" tabindex="-1" role="dialog" aria-labelledby="info-icon-{{ $row->id }}" aria-hidden="true" style="display: none;">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content bg-primary">
                                            <div class="modal-header">
                                                <p class="modal-title custom-font" id="modal-title-delete">معلومات عن المستخدم</p>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">×</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="py-3 text-center">
                                                    @if ($row->user == 'طالب')
                                                        <div class="card card-sm card-body bg-primary border-light mb-0">
                                                            <a href="#panel-4" data-target="#panel-4" class="accordion-panel-header" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="panel-1">
                                                                <span class="icon-title h6 mb-0 font-weight-bold custom-font"><span class="fa-solid fa-id-card"></span>البيانات الاكاديمية</span>
                                                                <span class="icon"><span class="fas fa-plus"></span></span>
                                                            </a>
                                                            <div class="collapse" id="panel-4">
                                                                <div class="pt-3">
                                                                    <p class="mb-0">
                                                                        <div class="form-group" data-toggle="tooltip" data-placement="top" title="اسم الطالب">
                                                                            <div class="input-group mb-4">
                                                                                <input class="form-control" placeholder="اسم المستخدم" aria-label="Input group" type="text" value="{{ $students->where('id', $row->user_id)->first()->name }}">
                                                                                <div class="input-group-append">
                                                                                    <span class="input-group-text"><span class="fas fa-user"></span></span>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group" data-toggle="tooltip" data-placement="left" title="رقم البطاقة">
                                                                            <div class="input-group mb-4">
                                                                                <input class="form-control" placeholder="رقم البطاقة" aria-label="Input group" type="text" value="{{ $students->where('id', $row->user_id)->first()->card }}">
                                                                                <div class="input-group-append">
                                                                                    <span class="input-group-text"><span class="fa-solid fa-address-card"></span></span>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group" data-toggle="tooltip" data-placement="left" title="القسم">
                                                                            <div class="input-group mb-4">
                                                                                <input class="form-control" aria-label="Input group" type="text" value="{{ $department->where('id', $students->where('id', $row->user_id)->first()->department_id)->first()->name }}">
                                                                                <div class="input-group-append">
                                                                                    <span class="input-group-text"><span class="fa-solid fa-building-user"></span></span>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group" data-toggle="tooltip" data-placement="left" title="المستوى">
                                                                            <div class="input-group mb-4">
                                                                                <input class="form-control" aria-label="Input group" type="text" value="{{ $students->where('id', $row->user_id)->first()->level }}">
                                                                                <div class="input-group-append">
                                                                                    <span class="input-group-text"><span class="fa-solid fa-layer-group"></span></span>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group " data-toggle="tooltip" data-placement="left" title="المؤهل">
                                                                            <div class="input-group mb-4">
                                                                                <input class="form-control" placeholder="القسم" aria-label="Input group" type="text" value="{{ $students->where('id', $row->user_id)->first()->qualification }}">
                                                                                <div class="input-group-append">    
                                                                                    <span class="input-group-text"><span class="fa-solid fa-ranking-star"></span></span>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div> 
                                                    @elseif ($row->user == 'معلم')
                                                        <div class="card card-sm card-body bg-primary border-light mb-0">
                                                            <a href="#panel-4" data-target="#panel-4" class="accordion-panel-header" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="panel-1">
                                                                <span class="icon-title h6 mb-0 font-weight-bold custom-font"><span class="fa-solid fa-id-card"></span>البيانات الاكاديمية</span>
                                                                <span class="icon"><span class="fas fa-plus"></span></span>
                                                            </a>
                                                            <div class="collapse" id="panel-4">
                                                                <div class="pt-3">
                                                                    <p class="mb-0">
                                                                        <div class="form-group" data-toggle="tooltip" data-placement="top" title="اسم الطالب">
                                                                            <div class="input-group mb-4">
                                                                                <input class="form-control" placeholder="اسم المستخدم" aria-label="Input group" type="text" value="{{ $techers->where('id', $row->user_id)->first()->name }}">
                                                                                <div class="input-group-append">
                                                                                    <span class="input-group-text"><span class="fas fa-user"></span></span>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group" data-toggle="tooltip" data-placement="left" title="رقم البطاقة">
                                                                            <div class="input-group mb-4">
                                                                                <input class="form-control" placeholder="رقم البطاقة" aria-label="Input group" type="text" value="{{ $techers->where('id', $row->user_id)->first()->code }}">
                                                                                <div class="input-group-append">
                                                                                    <span class="input-group-text"><span class="fa-solid fa-address-card"></span></span>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group" data-toggle="tooltip" data-placement="left" title="الكلية">
                                                                            <div class="input-group mb-4">
                                                                                <input class="form-control" aria-label="Input group" type="text" value="{{ $college->where('id', $techers->where('id', $row->user_id)->first()->college_id)->first()->name }}">
                                                                                <div class="input-group-append">
                                                                                    <span class="input-group-text"><span class="fa-solid fa-building-columns"></span></span>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group" data-toggle="tooltip" data-placement="left" title="القسم">
                                                                            <div class="input-group mb-4">
                                                                                <input class="form-control" aria-label="Input group" type="text" value="{{ $department->where('id', $techers->where('id', $row->user_id)->first()->department_id)->first()->name }}">
                                                                                <div class="input-group-append">
                                                                                    <span class="input-group-text"><span class="fa-solid fa-building-user"></span></span>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group " data-toggle="tooltip" data-placement="left" title="المؤهل">
                                                                            <div class="input-group mb-4">
                                                                                <input class="form-control" placeholder="القسم" aria-label="Input group" type="text" value="{{ $techers->where('id', $row->user_id)->first()->academic_degree }}">
                                                                                <div class="input-group-append">    
                                                                                    <span class="input-group-text"><span class="fa-solid fa-ranking-star"></span></span>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div> 
                                                    @endif
                                                    <br>
                                                    <p class="h4 custom-font">تغير الصلاحية</p>
                                                    <form action="{{ route('user.update-role') }}" method="post">
                                                        @csrf
                                                        <input type="hidden" name="id" value="{{ $row->id }}">
                                                        <div class="invalid-feedback show">{{ $errors->first('role_id') }}</div>
                                                        <div class="form-group">
                                                            <select class="custom-select my-1 mr-sm-2" name="role_id">
                                                                @foreach ($roles->where('type', $row->user) as $role)
                                                                @if ($role->id == 1)
                                                                    @continue
                                                                @endif
                                                                    <option value="{{ $role->id }}" {{ $role->id == old('role_id') ? 'selected' : '' }}> {{ $role->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <button type="submit" class="btn btn-block btn-primary">حفظ</button>
                                                    </form>
                                                </div>
                                            </div>
                                            <div class="modal-footer d-flex align-items-center" style="justify-content: space-between;">
                                                <div class="d-flex">
                                                <form action="{{ route('user.active-user') }}" method="post" class="mr-2">
                                                    @csrf
                                                    <input type="hidden" name="id" value="{{ $row->id }}">
                                                    @if ($row->is_active == 1)
                                                        <button type="submit" class="btn bg-danger text-white" data-toggle="tooltip" data-placement="top" title="ايقاف هذا المستخدم">ايقاف</button>
                                                        @else
                                                        <button type="submit" class="btn bg-info text-white" data-toggle="tooltip" data-placement="top" title="تفعيل هذا المستخدم">تفعيل</button>
                                                    @endif
                                                </form>
                                                @if ($row->user == 'طالب')
                                                    <form action="{{ route('user.active-code') }}" method="post" class="mr-2">
                                                        @csrf
                                                        <input type="hidden" name="id" value="{{ $row->user_id }}">
                                                        <button type="submit" class="btn bg-success text-white" data-toggle="tooltip" data-placement="top" title="تنشيط رمز التفعيل">تنشيط</button>
                                                    </form>
                                                @endif
                                                </div>
                                                <button type="button" class="btn btn-primary" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">اغلاق</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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