<section class="section section-lg pt-0 rtl-layout" id="contact">
    <div class="container">
        <div class="row align-items-center justify-content-around">
            <div class="col-md-4 col-lg-3 d-block d-sm-flex d-md-block justify-content-sm-center text-center mb-5 mb-md-0">
                <!-- Start Box -->
                <div class="icon-box mr-sm-5 mr-md-0 mb-4 mb-lg-5">
                    <div class="icon icon-shape-lg shadow-inset border border-light rounded-circle mb-4">
                        <span class="icon icon-md icon-shape-sm shadow-soft border border-light rounded-circle">
                            <span class="fa-brands fa-facebook"></span>
                        </span>
                    </div>
                    <h2 class="h5 icon-box-title custom-font">صفحة الفيسبوك</h2>
                    <a class="h6 custom-font text-behance" target="_blank" href="https://m.facebook.com/tueduye/">فتح صفحة الفيسبوك</a>
                </div>
                <!-- End Box -->
                 <!-- Start Box -->
                <div class="icon-box mr-sm-5 mr-md-0 mb-4 mb-lg-5">
                    <div class="icon icon-shape-lg shadow-inset border border-light rounded-circle mb-4">
                        <span class="icon icon-md icon-shape-sm shadow-soft border border-light rounded-circle">
                            <span class="fa-brands fa-telegram"></span>
                        </span>
                    </div>
                    <h2 class="h5 icon-box-title custom-font">قناة التلجرام</h2>
                    <a class="h6 custom-font text-behance" target="_blank" href="http://t.me/tueduye">فتح قناة التلجرام</a>
                </div>
                <!-- End Box -->
                 <!-- Start Box -->
                <div class="icon-box mr-sm-5 mr-md-0 mb-4 mb-lg-5">
                    <div class="icon icon-shape-lg shadow-inset border border-light rounded-circle mb-4">
                        <span class="icon icon-md icon-shape-sm shadow-soft border border-light rounded-circle">
                            <span class="fa-brands fa-square-x-twitter"></span>
                        </span>
                    </div>
                    <h2 class="h5 icon-box-title custom-font">منصة X (تويتر)</h2>
                    <a class="h6 custom-font text-behance" target="_blank" href="https://twitter.com/tueduye">فتح منصة اكس (تويتر)</a>
                </div>
                <!-- End Box -->
            </div>
            <div class="col-md-8"><!-- Contact Card -->
                <div class="card bg-primary shadow-inset border-light p-3">
                    <div class="card-body shadow-soft rounded border border-light p-2 p-sm-3 p-md-5">
                        <h2 class="h3 custom-font"><span class="fa-regular fa-message"></span> ارسال رسالة</h2>
                        <!-- <p>Cool! Let’s talk about your project</p> -->
                        <form action="{{ route('send-message.store') }}" method="post">
                            @csrf
                            <div class="form-group mt-4"><label for="exampleInputName1">الاسم</label>
                                <div class="input-group">
                                    <div class="input-group-append">
                                        <span class="input-group-text">
                                            <span class="far fa-user"></span>
                                        </span>
                                    </div>
                                    <input id="exampleInputName1" name="name" class="form-control" placeholder="الاسم الرباعي" type="text">
                                </div>
                            </div>
                            <div class="form-group"><label for="exampleInputEmail1">البريد الاكتروني</label>
                                <div class="input-group">
                                    <div class="input-group-append">
                                        <span class="input-group-text">
                                            <span class="fas fa-at"></span></span>
                                        </div>
                                        <input id="exampleInputEmail1" name="email" class="form-control rtl-layout text-left" placeholder="example@site.com" type="email">
                                </div>
                            </div>
                            <div class="form-group"><label for="exampleInputEmail1">الهاتف (اختياري)</label>
                                <div class="input-group">
                                    <div class="input-group-append">
                                        <span class="input-group-text">
                                            <span class="fas fa-phone"></span>
                                        </span>
                                    </div>
                                    <input id="exampleInputEmail1" name="phone" class="form-control" placeholder="--- --- ---" type="tel">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="exampleFormControlTextarea1">الرسالة</label> 
                                <textarea name="message" class="form-control" id="exampleFormControlTextarea1" placeholder="اكتب الرسالة هنا..." rows="3"></textarea></div>
                            <button type="submit" class="btn btn-block btn-primary">ارسال الرسالة</button>
                        </form>
                    </div>
                </div><!-- End of Contact Card -->
            </div>
        </div>
    </div>
</section>