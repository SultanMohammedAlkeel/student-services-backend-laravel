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
                <div class="col-12 col-md-6 col-lg-6 mb-5 mb-lg-0 rtl-layout overflow-auto">
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
                                <th class="border-0" scope="col" id="user">الاسم</th>
                                <th class="border-0" scope="col" id="college">الهاتف</th>
                                <th class="border-0" scope="col" id="contact">البريد</th>
                                <th class="border-0" scope="col" id="contact">الصلاحيات</th>
                            </tr>
                            <tbody id="tbody">
                                @foreach ($users as $user)
                                <tr class="table-row">
                                    <th scope="row">{{ $user->id }}</th>
                                    <th scope="row" headers="user">{{ $user->name }}</th>
                                    <td headers="contact">{{ $user->phone_number }}</td>
                                    <td headers="contact">{{ $user->email }}</td>
                                    <td headers="college">{{ $roles->where('id', $user->role_id)->first()->name }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-4 mb-5 mb-lg-0">
                    <div class="card bg-primary shadow-soft border-light p-4">
                        <div class="card-header text-center pb-0">
                            <h2 class="h4 custom-font">اضافة حساب جديد</h2>
                        </div>
                        <div class="card-body">
                            <form method="post" action="{{ route('user.store') }}" enctype="multipart/form-data">
                                @csrf
                                <!-- Form -->
                                <div class="invalid-feedback show">{{ $errors->first('name') }}</div>
                                <div class="form-group">
                                    <div class="input-group mb-4">
                                        <input class="form-control" name="name" placeholder="اسم المستخدم"
                                            aria-label="Input group" type="text">
                                        <div class="input-group-append">
                                            <span class="input-group-text"><span class="fas fa-user"></span></span>
                                        </div>
                                    </div>
                                </div>
                                <!-- End of Form -->
                                <!-- Form -->
                                <div class="invalid-feedback show">{{ $errors->first('email') }}</div>
                                <div class="form-group">
                                    <div class="input-group mb-4">
                                        <input class="form-control left-text" name="email" placeholder="البريد الإلكتروني"
                                            aria-label="Input group" type="email">
                                        <div class="input-group-append">
                                            <span class="input-group-text"><span class="fas fa-envelope"></span></span>
                                        </div>
                                    </div>
                                </div>
                                <!-- End of Form -->
                                <!-- Form -->
                                <div class="invalid-feedback show">{{ $errors->first('password') }}</div>
                                <div class="form-group">
                                    <div class="input-group mb-4">
                                        <input class="form-control" name="password"
                                            placeholder="كلمة المرور" type="password" aria-label="Password">
                                        <div class="input-group-append">
                                            <span class="input-group-text"><span
                                                    class="fas fa-unlock-alt"></span></span>
                                        </div>
                                    </div>
                                </div>
                                <!-- End of Form -->
                                <!-- Form -->
                                <div class="invalid-feedback show">{{ $errors->first('phone_number') }}</div>
                                <div class="form-group">
                                    <div class="input-group mb-4">
                                        <input class="form-control" name="phone_number" placeholder="رقم الهاتف"
                                            aria-label="Input group" type="tel">
                                        <div class="input-group-append">
                                            <span class="input-group-text"><span class="fas fa-phone"></span></span>
                                        </div>
                                    </div>
                                </div>
                                <!-- End of Form -->
                                <!-- Form -->
                                <div class="invalid-feedback show">{{ $errors->first('gender') }}</div>
                                <div class="form-group">
                                    <select class="custom-select my-1 mr-sm-2" name="gender">
                                        @foreach ($genders as $row)
                                        <option value="{{ $row }}" {{ $row == old('gender') ? 'selected' : '' }}> {{ $row }}</option>
                                        @endforeach    
                                    </select>
                                </div>
                                <!-- End of Form -->
                                <!-- Form -->
                                <div class="invalid-feedback show">{{ $errors->first('role_id') }}</div>
                                <div class="form-group">
                                    <select class="custom-select my-1 mr-sm-2" name="role_id">
                                        @foreach ($roles as $role)
                                        @if ($role->id == 1)
                                            @continue
                                        @endif
                                            <option value="{{ $role->id }}" {{ $role->id == old('role_id') ? 'selected' : '' }}> {{ $role->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <!-- End of Form -->
                                <!-- Form -->
                                <div class="invalid-feedback show">{{ $errors->first('image_url') }}</div>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" name="image_url" aria-label="File upload">
                                    <label class="custom-file-label" for="customFile">Choose Profile Image</label>
                                </div>
                                <!-- End of Form -->
                                <br> <br>
                                <button type="submit" class="btn btn-block btn-primary">انشاء الحساب</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('../../scripts.js')
</body>
</html>

