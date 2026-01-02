<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Yotta Uni App</title>
    @include('scripts.css')
    {{-- <link rel="stylesheet" href="{{ asset('css/chat.css') }}"> --}}
</head>

<body>
    <script>
        let user_id = 0;
        let contact_id = 0; 
    </script>

    @if (session('user_type') == 'مشرف')
        @include('../admins.layouts.header')  
    @elseif ((session('user_type') == 'معلم'))
        @include('../teacher.layouts.header')  
    @else
        @include('../students.layouts.header')  
    @endif

    <div class="container main-container mt-6 rtl-layout">
        <div class="contact-area py-3">
            <div class="form-group ltr-layout">
                <div class="input-group mb-4">
                    <a href="contact" class="btn btn-icon-only btn-facebook" type="button" aria-label="add button" title="add button" onclick="showAddRow()"> 
                        <span aria-hidden="true" class="fab fa-add"></span>
                    </a>
                    <input class="form-control" id="search" placeholder="بحث" aria-label="Input group" type="text">
                    <div class="input-group-append">
                        <span class="input-group-text"><span class="fas fa-search"></span></span>
                    </div>
                </div>
            </div>
            <div class="contact-list" id="contact-list">
                <nav>
                    <div class="nav nav-pills nav-fill flex-sm-row" role="tablist">
                        <a class="badge badge-secondary text-uppercase m-1 text-center active" id="all-tab" data-toggle="tab" href="#all" role="tab" aria-controls="all" aria-selected="true">الكل</a>
                        <a class="badge badge-secondary text-uppercase m-1 text-center" id="students-tab" data-toggle="tab" href="#students" role="tab" aria-controls="students" aria-selected="false">الطلاب</a>
                        <a class="badge badge-secondary text-uppercase m-1 text-center" id="teachers-tab" data-toggle="tab" href="#teachers" role="tab" aria-controls="teachers" aria-selected="false">المعلمين</a>
                        <a class="badge badge-secondary text-uppercase m-1 text-center" id="admins-tab" data-toggle="tab" href="#admins" role="tab" aria-controls="admins" aria-selected="false">المشرفين</a>
                    </div>
                </nav>
                
                <div class="tab-content mt-4 mt-lg-5" id="nav-tabContent-ecommerce">
                    <div class="tab-pane fade show active" id="all" role="tabpanel" aria-labelledby="all-tab">
                        @include('chats.layout.contact', ['users' => $users, 'user_type' => 'الكل'])            
                    </div>
                    <div class="tab-pane fade" id="students" role="tabpanel" aria-labelledby="students-tab">
                        @include('chats.layout.contact', ['users' => $users, 'user_type' => 'طالب'])            
                    </div>
                    <div class="tab-pane fade" id="teachers" role="tabpanel" aria-labelledby="teachers-tab">
                        @include('chats.layout.contact', ['users' => $users, 'user_type' => 'معلم'])            
                    </div>
                    <div class="tab-pane fade" id="admins" role="tabpanel" aria-labelledby="admins-tab">
                        @include('chats.layout.contact', ['users' => $users, 'user_type' => 'مشرف'])            
                    </div>
                </div>
        </div>
        </div>
        <div class="chat-area">
            <div class="header-chat">
                <div class="bd-navbar d-flex justify-content-between align-items-center py-1 my-1 list-contant">
                    <span class="font-small d-flex align-items-center">
                        <i class="fa-solid fa-chevron-right mr-3 text-success align-self-center back-chat" onclick="backFromChat()"></i>
                        <img class="avatar-lg img-fluid rounded-circle mx-3" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" id="image">
                        <div class="">
                            <p class="mb-0 custom-font" id="name"></p> 
                            <small class=" custom-font" id="status"></small> 
                        </div>
                    </span>
                    <div id="menu-btn">
                        @include('chats.layout.menu')
                    </div>
                </div>  
            </div> 
            <div class="body-chat" id="body-chat"></div>
            <div class="footer-chat bd-navbar py-2 rounded-lg" id="footer-chat">
                <div class="form-group d-flex">
                    <textarea class="form-control rtl-layout mr-1" placeholder="اكتب ..." name="message" id="message" rows="1"></textarea>
                    <div class="input-group-append">
                        <button class="btn btn-icon-only btn-facebook mr-1" type="button" aria-label="send button" title="send button" onclick="sendMessage()"><span aria-hidden="true" class="fa-solid fa-paper-plane"></span></button>
                        <button class="btn btn-icon-only btn-facebook mr-1" type="button" aria-label="send button" title="send button" aria-label="add button" title="add button"data-toggle="modal" data-target="#modal-form"><span aria-hidden="true" class="fa-solid fa-paperclip"></span></button>
                        <button class="btn btn-icon-only btn-facebook mr-1" type="button" onclick="scrollToBottom()"><span class="fa-solid fa-chevron-down"></span></button>
                    </div>
                </div>
            </div>

        </div>
    </div>


    <div class="modal fade" id="modal-form" tabindex="-1" role="dialog" aria-labelledby="modal-form"
        aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body p-0">
                    <div class="card bg-primary shadow-soft border-light p-4">
                        <button type="button" class="close ml-auto" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true" id="close-modal">×</span>
                        </button>
                        <div class="card-header text-center pb-0">
                            <h2 class="h4 custom-font">ارسال وسائط</h2>
                        </div>
                        <div class="card-body">
                                <div class="card-header p-4">
                                    <embed class="card-img-top rounded" id="previewImage" >
                                </div>
                                <div class="custom-file">
                                    <input type="file" id="fileInput" class="custom-file-input" name="file" aria-label="File upload" accept="image/*, video/*, audio/*, application/pdf">
                                    <label class="custom-file-label" for="customFile">Choose Logo Image</label>
                                </div>
                                <!-- End of Form -->
                                <div class="invalid-feedback show">{{ $errors->first('content') }}</div>
                                <div class="form-group">
                                    <textarea class="form-control right-text rtl-layout" placeholder="اكتب ما تريد هنا ..." name="content" id="content" rows="4"></textarea>
                                </div>
                                <!-- End of Form -->
                                <button type="submit" class="btn btn-block btn-primary" onclick="sendMessageWithMedia()">ارسال</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('scripts.js')
    @include('scripts.chat-js')

</body>

</html>
