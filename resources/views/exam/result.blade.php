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

    
    @if (session('user_type') == 'مشرف')
        @include('../admins.layouts.header')  
    @elseif ((session('user_type') == 'معلم'))
        @include('../teacher.layouts.header')  
    @else
        @include('../students.layouts.header')  
    @endif
    <br>
    <br>
    <br>
    <div class="section section-lg pt-0">
        <div class="container">
            <div class="col-12 col-lg-12 text-center">
                <h1 class="display-1 mb-4 custom-font">{{ $exam_name }}</h1>
                <h1 class="display-6 mb-4 custom-font">{{ $language }}</h1>
                <div class="row">
                    <div class="col">
                        <div class="card bg-primary shadow-soft border-light p-4">
                            <div class="row">
                                <div class="col-12 col-lg-4 px-md-0 mb-4 mb-lg-0">
                                    <div class="card-body text-center bg-primary py-5">
                                        <div class="icon icon-shape shadow-inset border-light rounded-circle mb-3">
                                            <span class="far fa-eye"></span>
                                        </div>
                                        <!-- Heading -->
                                        <h2 class="h3 mr-2 custom-font">
                                            الاجابات الصحيحة
                                        </h2>
                                        <!-- Text -->
                                        <p class="mb-0 h5">{{ $correct }}</p>
                                    </div>
                                </div>
                                <div class="col-12 col-lg-4 px-md-0 mb-4 mb-lg-0">
                                    <div class="card-body text-center bg-primary py-5">
                                        <div class="icon icon-shape shadow-inset border-light rounded-circle mb-3">
                                            <span class="fas fa-medal"></span>
                                        </div>
                                        <!-- Heading -->
                                        <h2 class="h3 mr-2 custom-font">
                                            النتيجة
                                        </h2>
                                        <!-- Text -->
                                        <p class="mb-0 h5">{{ $score }}%</p>
                                    </div>
                                </div>
                                <div class="col-12 col-lg-4 px-md-0">
                                    <div class="card-body text-center bg-primary py-5">
                                        <div class="icon icon-shape shadow-inset border-light rounded-circle mb-3">
                                            <span class="fas fa-puzzle-piece"></span>
                                        </div>
                                        <!-- Heading -->
                                        <h2 class="h3 mr-2 custom-font">
                                            اجابات خاطئة
                                        </h2>
                                        <!-- Text -->
                                        <p class="mb-0 h5">{{ $wrong }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-12 text-center mt-5">
                <a href="{{ route('exam.show-my-exam', [$exam_code]) }}" class="btn btn-primary" title="فتح الاختبار">
                    <span aria-hidden="true" class="fa-solid fa-folder-open"></span>
                    <span class="save-count">العودة للاختبار</span>
                </a>
            </div>
        </div>
    </div>

    <br>
    <br>

    @include('scripts.js')
</body>

</html>
