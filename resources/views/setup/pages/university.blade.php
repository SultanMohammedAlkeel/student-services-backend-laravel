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

    <section class="min-vh-100 d-flex bg-primary align-items-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-md-8 col-lg-6 justify-content-center">
                    <div class="card bg-primary shadow-soft border-light p-4">
                        <div class="card-header text-center pb-0">
                            <h2 class="mb-0 h5 custom-font">ادخال بيانات الجامعة</h2>
                        </div>
                        <div class="card-body">
                            <form method="post" action="{{ route('university.store') }}" enctype="multipart/form-data">
                                @csrf
                                <!-- Form -->
                                <div class="form-group">
                                    <div class="input-group mb-4">
                                        <input class="form-control" name="name" placeholder="اسم الجامعة"
                                            aria-label="Input group" type="text">
                                        <div class="input-group-append">
                                            <span class="input-group-text"><span class="fas fa-user"></span></span>
                                        </div>
                                    </div>
                                </div>
                                <!-- End of Form -->
                                <!-- Form -->
                                <div class="form-group">
                                    <div class="input-group mb-4">
                                        <input class="form-control left-text" name="email"
                                            placeholder="البريد الإلكتروني" aria-label="Input group" type="email">
                                        <div class="input-group-append">
                                            <span class="input-group-text"><span class="fas fa-envelope"></span></span>
                                        </div>
                                    </div>
                                </div>
                                <!-- End of Form -->
                                <!-- Form -->
                                <div class="form-group">
                                    <div class="input-group mb-4">
                                        <input class="form-control" name="contact_info" placeholder="معلومات الاتصال"
                                            aria-label="Input group" type="tel">
                                        <div class="input-group-append">
                                            <span class="input-group-text"><span class="fas fa-phone"></span></span>
                                        </div>
                                    </div>
                                </div>
                                <!-- End of Form -->
                                <!-- Form -->
                                <div class="form-group">
                                    <div class="input-group mb-4">
                                        <input class="form-control" name="website" placeholder="رابط الموقع"
                                            aria-label="Input group" type="text">
                                        <div class="input-group-append">
                                            <span class="input-group-text"><span class="fas fa-globe"></span></span>
                                        </div>
                                    </div>
                                </div>
                                <!-- End of Form -->
                                <!-- Form -->
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" name="logo_url"
                                        aria-label="File upload">
                                    <label class="custom-file-label" for="customFile">Choose Logo Image</label>
                                </div>
                                <br> <br>
                                <!-- Form -->
                                <div class="form-group">
                                    <textarea class="form-control right-text rtl-layout" placeholder="اكتب وصف عن الجامعة..." name="description" rows="4"></textarea>
                                </div>
                                <!-- End of Form -->
                                <button type="submit" class="btn btn-block btn-primary">انشاء الحساب</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @include('../../scripts.js')
</body>

</html>
