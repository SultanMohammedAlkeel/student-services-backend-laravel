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

    @if (session('user_type') == 'طالب')
        @include('../students.layouts.header')  
    @else
        @include('../components.404')  
    @endif
    <br>
    <br>
    <br>
    <div class="section section-md bg-primary text-black pt-0 line-bottom-light">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-lg-8">
                    <div>
                        <div class="mt-5 posts-container" id="posts-container">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="btn-function">
        <button class="btn btn-icon-only" type="button" aria-label="add button" title="اضافة منشور" data-toggle="modal" data-target="#modal-form-post">
            <span aria-hidden="true" class="fas fa-pen-to-square" data-toggle="tooltip" data-placement="left" title="اضافة منشور"></span>
        </button>
        <button class="btn btn-icon-only" type="button" aria-label="add button" title="رفع وسائط" data-toggle="modal" data-target="#modal-form-media">
            <span aria-hidden="true" class="fas fa-paperclip" data-toggle="tooltip" data-placement="left" title="رفع وسائط"></span>
        </button>
        <button class="btn btn-icon-only" type="button" aria-label="add button" title="استفتاء" data-toggle="modal" data-target="#modal-form-poll">
            <span aria-hidden="true" class="fas fa-poll" data-toggle="tooltip" data-placement="left" title="استفتاء"></span>
        </button>
        <button class="btn btn-icon-only" type="button" aria-label="add button" title="كود برمجي" data-toggle="modal" data-target="#modal-form-code">
            <span aria-hidden="true" class="fas fa-code" data-toggle="tooltip" data-placement="left" title="كود برمجي"></span>
        </button>
        <button class="btn btn-icon-only" type="button" aria-label="add button" title="اضافة رابط" data-toggle="modal" data-target="#modal-form-link">
            <span aria-hidden="true" class="fas fa-link" data-toggle="tooltip" data-placement="left" title="اضافة رابط"></span>
        </button>
        <button class="btn btn-icon-only" type="button" onclick="scrollToBottom()">
            <span class="fa-solid fa-chevron-up"></span>
        </button>
    </div>

    <div class="modal fade" id="modal-form-post" tabindex="-1" role="dialog" aria-labelledby="modal-form-post"
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
                            <span>اكتب ما تريد نشره</span>
                        </div>
                        <div class="card-body">
                            <form method="post" action="{{ route('posts.store') }}" enctype="multipart/form-data"
                                class="mt-4">
                                @csrf
                                <!-- End of Form -->
                                <div class="invalid-feedback show">{{ $errors->first('content') }}</div>
                                <div class="form-group">
                                    <textarea class="form-control right-text rtl-layout" placeholder="اكتب ما تريد نشره هنا ..." name="content"
                                        rows="5" value="{{ old('content') }}"></textarea>
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
    <div class="modal fade" id="modal-form-media" tabindex="-1" role="dialog" aria-labelledby="modal-form-media"
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

                            <form method="post" action="{{ route('posts.store') }}" enctype="multipart/form-data"
                                class="mt-4">
                                @csrf
                                <div class="card-header p-4">
                                    <embed class="card-img-top rounded" id="previewImage" >
                                </div>
                                <div class="custom-file">
                                    <input type="file" id="imageInput" class="custom-file-input" name="file" aria-label="File upload">
                                    <label class="custom-file-label" for="customFile">Choose Media</label>
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
    <div class="modal fade" id="modal-form-code" tabindex="-1" role="dialog" aria-labelledby="modal-form-code"
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
                            <span>اكتب ما تريد نشره</span>
                        </div>
                        <div class="card-body">
                            <form method="post" action="{{ route('posts.store') }}" enctype="multipart/form-data"
                                class="mt-4">
                                @csrf
                                <!-- End of Form -->
                                <div class="invalid-feedback show">{{ $errors->first('content') }}</div>
                                <div class="form-group">
                                    <textarea class="form-control right-text rtl-layout" placeholder="اكتب ما تريد نشره هنا ..." name="content"
                                        rows="5" value="{{ old('content') }}"></textarea>
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

    @include('scripts.js')
    @include('scripts.post-js')
</body>

</html>
