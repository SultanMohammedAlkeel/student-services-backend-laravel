

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
                            <h2 class="mb-0 h5 custom-font">تسجيل الدخول</h2>
                        </div>
                        <div class="card-body">
                            <form method="post" action="sign">
                                @csrf
                                <!-- Form -->
                                <div class="invalid-feedback show right-text">{{ $errors->first('user') }}</div>
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
                                <div class="invalid-feedback show right-text">{{ $errors->first('password') }}</div>
                                <div class="form-group">
                                    <div class="input-group mb-4">
                                        <input class="form-control" name="password"
                                            placeholder="كلمة المرور" type="password" aria-label="Password">
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                <span class="fas fa-unlock-alt"></span></span>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-block btn-primary">دخول</button>
                            </form>
                            @if (session('user_type') == 'طالب')
                                <div class="d-block d-sm-flex justify-content-center align-items-center mt-4">
                                    <span class="font-weight-normal">نسيت كلمة المرور؟
                                        <a href="#" class="font-weight-bold text-info">استرجاع كلمة المرور</a>
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @include('scripts.js')
</body>
</html>

<?php
    
    print_r($_SESSION);
?>