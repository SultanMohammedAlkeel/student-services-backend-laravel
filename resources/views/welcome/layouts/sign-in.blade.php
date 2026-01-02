<section class="section section-lg pt-0 rtl-layout" id="contact">
    <div class="container">
        <div class="row">
            <div class="col text-center">
                <h2 class="font-weight-bold mb-3 custom-font">تسجيل الدخول</h2>
            </div>
        </div>
        <div class="row align-items-center justify-content-around">
            <div class="col-md-6    ">
                <div class="card bg-primary shadow-inset border-light p-3">
                    <div class="card-body shadow-soft rounded border border-light p-2 p-sm-3 p-md-5">
                        <form method="post" action="sign">
                                @csrf
                                <!-- Form -->
                                <div class="invalid-feedback show right-text">{{ $errors->first('user') }}</div>
                                <div class="form-group">
                                    <div class="input-group mb-4">
                                        <div class="input-group-append">
                                            <span class="input-group-text"><span class="fas fa-user"></span></span>
                                        </div>
                                        <input class="form-control" name="name" placeholder="اسم المستخدم" aria-label="Input group" type="text">
                                    </div>
                                </div>
                                <!-- End of Form -->
                                <!-- Form -->
                                <div class="invalid-feedback show right-text">{{ $errors->first('password') }}</div>
                                <div class="form-group">
                                    <div class="input-group mb-4">
                                        <div class="input-group-append"><span class="input-group-text"><span class="fas fa-unlock-alt"></span></span></div>
                                        <input class="form-control" name="password" placeholder="كلمة المرور" type="password" aria-label="Password">
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-block btn-primary">دخول</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>