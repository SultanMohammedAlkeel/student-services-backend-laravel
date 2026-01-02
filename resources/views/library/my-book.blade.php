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
    
@php
    function formatFileSize($bytes) {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' بايت';
        }
    }
@endphp
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
    <div class="section section-md bg-primary text-black pt-0 line-bottom-light">
        <div class="container">
            <div class="row justify-content-center">
                <div class="form-group ltr-layout " style="width: 50%;">
                    <form action="{{ route('library.index') }}" method="post">
                    @csrf
                        <div class="input-group my-4">
                            <button class="btn btn-primary btn-sm">بحث</button>
                            <input class="form-control text-center" name="search" id="search" placeholder="بحث" aria-label="Input group" type="text" value="{{ old('search') }}">
                            <div class="input-group-append">
                                <span class="input-group-text"><span class="fas fa-search"></span></span>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="mt-5 px-5 library-container d-flex align-items-center library-container" id="library-container">
                    @foreach ($books as $book)
                        @include('library.components.book-card', ['book_data' => $book])
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <button class="btn btn-icon-only btn-up library-btn-up" type="button" onclick="scrollToTop()"><span class="fa-solid fa-chevron-up"></span></button>
    <button class="btn btn-icon-only btn-filtter" type="button" aria-label="add button" title="add button" data-toggle="modal" data-target="#modal-form"><span class="fa-solid fa-filter"></span></button>
    <button class="btn btn-icon-only btn-add" onclick="location.reload();"><span aria-hidden="true" class="fa-solid fa-rotate-right"></span></button>

    <div class="modal fade" id="modal-form" tabindex="-1" role="dialog" aria-labelledby="modal-form"
        aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body p-0">
                    <div class="card bg-primary shadow-soft border-light p-4">
                        <button type="button" class="close ml-auto" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                        <div class="card-header text-center pb-0">
                            <h2 class="h4 custom-font">اضافة منشور</h2>
                            <span>ماذا يحدث</span>
                        </div>
                        <div class="card-body">
                            <form method="post" action="{{ route('library.store') }}" enctype="multipart/form-data"
                                class="mt-4">
                                @csrf
                                <div class="card-header p-4">
                                    <embed class="card-img-top rounded" id="previewImage" >
                                </div>
                                <div class="custom-file">
                                    <input type="file" id="imageInput" class="custom-file-input" name="file"
                                        aria-label="File upload">
                                    <label class="custom-file-label" for="customFile">Choose Logo Image</label>
                                </div>
                                <br>
                                <br>
                                <!-- End of Form -->
                                <div class="invalid-feedback show">{{ $errors->first('content') }}</div>
                                <div class="form-group">
                                    <textarea class="form-control right-text rtl-layout" placeholder="اكتب ما تريد نشره هنا ..." name="content"
                                        rows="4" value="{{ old('content') }}"></textarea>
                                </div>
                                <!-- End of Form -->
                                <button type="submit" class="btn btn-block btn-primary">نشر</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End of Modal Content -->

    @include('scripts.js')
    @include('scripts.library-js')
</body>

</html>
