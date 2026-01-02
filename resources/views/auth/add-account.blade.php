

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Yotta Uni App</title>
    @include('scripts.css')
</head>
<body>
    
    <section class="min-vh-100 d-flex bg-primary align-items-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-md-8 col-lg-6 justify-content-center">
                    <div class="card bg-primary shadow-soft border-light p-4">
                        <div class="card-header text-center pb-0">
                            <h2 class="mb-0 h5 custom-font">نشاء حساب <span class="text-behance">({{ session('user_type') }})</span> </h2>
                        </div>
                        <div class="card-body">
                            <form method="post" action="{{ route('user.account') }}" enctype="multipart/form-data">
                                @csrf
                                <div id="auth" class="">
                                    <p class="h6 custom-font text-center text-behance">بيانات المصادقة</p>
                                    <!-- Form -->
                                    <div class="invalid-feedback show right-text"></div>
                                    <div class="form-group">
                                        <div class="input-group mb-4">
                                            <input class="form-control" id="code" placeholder="الرقم الجامعي" aria-label="Input group" type="text">
                                            <div class="input-group-append">
                                                <span class="input-group-text"><span class="fa-solid fa-id-card-clip"></span></span>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- End of Form -->
                                    <!-- Form -->
                                    <div class="invalid-feedback show right-text"></div>
                                    <div class="form-group">
                                        <div class="input-group mb-4">
                                            <input class="form-control" id="active-code" placeholder="رمز التفعيل" type="password" aria-label="Password">
                                            <div class="input-group-append">
                                                <span class="input-group-text">
                                                    <span class="fas fa-key"></span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="id" id="id-teacher">
                                </div>
                                <div id="data" class="hide">
                                    <p class="h6 custom-font text-center text-behance">بيانات المستخدم</p>
                                    <!-- Form -->
                                    <div class="invalid-feedback show right-text">{{ $errors->first('user') }}</div>
                                    <div class="form-group">
                                        <div class="input-group mb-4" id="user-name">
                                            <input class="form-control" name="name" placeholder="اسم المستخدم" aria-label="Input group" type="text">
                                            <div class="input-group-append">
                                                <span class="input-group-text"><span class="fas fa-user"></span></span>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- End of Form -->
                                    <!-- Form -->
                                    <div class="invalid-feedback show right-text">{{ $errors->first('password') }}</div>
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
                                <div class="invalid-feedback show">{{ $errors->first('image_url') }}</div>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" name="image_url" aria-label="File upload">
                                    <label class="custom-file-label" for="customFile">Choose Profile Image</label>
                                </div>
                                <!-- End of Form -->
                                <br>
                                <br>
                                </div>
                                <div id="contact" class="hide">
                                    <p class="h6 custom-font text-center text-behance">بيانات التواصل</p>
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
                                    <button type="submit" class="btn btn-block btn-primary">انشاء الحساب</button>
                                </div>
                            </form>
                            <button class="btn" id="next-btn" onclick="NextAction()">التالي <span class="fa-solid fa-angle-right"></span></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @include('scripts.js')
    <script>
        let step = 1;
        function NextAction() { 
            let code = document.getElementById('code').value;
            let active_code = document.getElementById('active-code').value;
            if (step == 1) {
                $.ajax({
                    url: '/user/auth-account',
                    method: 'POST',
                    data: {
                        code: code,
                        active_code: active_code,
                        _token: '{{ csrf_token() }}' // CSRF token
                    },
                    success: function(response) {
                        if (response.is_login) {
                            alert(response.message);
                        }

                        if (response.success) {
                            document.getElementById('id-teacher').value = response.id;
                            $('#auth').addClass('hide');
                            $('#data').removeClass('hide');
                            step = 2;
                        } else {
                            alert(response.message);
                        }

                    },
                    error: function(xhr) {
                        console.error('Error recording interaction:', xhr.responseText);
                    }
                });
            } else {
                if ($('#user-name').val.length == 0) {
                    alert('اسم المستخدم متطلب');
                    return;
                }
                $('#data').addClass('hide');
                $('#contact').removeClass('hide');
                $('#next-btn').addClass('hide');
            }
        }
    </script>
</body>
</html>