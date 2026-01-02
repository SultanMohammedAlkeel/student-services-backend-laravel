<header class="header-global rtl-layout">

    <nav id="navbar-main" aria-label="Primary navigation"
        class="navbar navbar-main navbar-expand-lg navbar-theme-primary headroom navbar-light navbar-transparent navbar-theme-primary">
        <div class="container position-relative">
            <a class="navbar-brand shadow-soft py-2 px-3 rounded border border-light mr-lg-4" href="/">
                <img class="navbar-brand-dark" src="{{ asset('assets/img/brand/dark.png') }}" alt="Logo light">
                <img class="navbar-brand-light" src="{{ asset('assets/img/brand/dark.png') }}" alt="Logo dark">
            </a>
            <div class="navbar-collapse collapse" id="navbar_global">
                <div class="navbar-collapse-header">
                    <div class="row">
                        <div class="col-6 collapse-brand">
                            <a href="/" class="navbar-brand shadow-soft py-2 px-3 rounded border border-light">
                                <img src="{{ asset('assets/img/brand/dark.png') }}" alt="Themesberg logo">
                            </a>
                        </div>
                        <div class="col-6 collapse-close">
                            <a href="#navbar_global" class="fas fa-times" data-toggle="collapse"
                                data-target="#navbar_global" aria-controls="navbar_global" aria-expanded="false"
                                title="close" aria-label="Toggle navigation"></a>
                        </div>
                    </div>
                </div>
                <ul class="navbar-nav navbar-nav-hover align-items-lg-center right-text">
                    <li class="nav-item dropdown">
                        <a href="#" class="nav-link" data-toggle="dropdown">
                            <span class="nav-link-inner-text">المنشورات</span>
                            <span class="fas fa-angle-down nav-link-arrow ml-2"></span>
                        </a>
                        <ul class="dropdown-menu right-text">
                            <li><a class="dropdown-item" href="{{ route('posts.index') }}">المنشورات</a></li>
                            <li><a class="dropdown-item" href="{{ route('events.index') }}">الانشطة</a></li>
                            <li><a class="dropdown-item" href="html/pages/contact.html">الرسائل</a></li>

                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a href="#" class="nav-link" data-toggle="dropdown">
                            <span class="nav-link-inner-text">الدردشات</span>
                            <span class="fas fa-angle-down nav-link-arrow ml-2"></span>
                        </a>
                        <ul class="dropdown-menu right-text">
                            <li><a class="dropdown-item" href="{{ route('chat.index') }}">الدردشة</a></li>
                            <li><a class="dropdown-item" href="{{ route('events.index') }}">المجموعات</a></li>

                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a href="#" class="nav-link" data-toggle="dropdown">
                            <span class="nav-link-inner-text">المكتبة</span>
                            <span class="fas fa-angle-down nav-link-arrow ml-2"></span>
                        </a>
                        <ul class="dropdown-menu right-text">
                            <li><a class="dropdown-item" href="{{ route('library.index') }}">المكتبة</a></li>
                            <li><a class="dropdown-item" href="{{ url('/my-book') }}">كتبي</a></li>

                        </ul>
                    </li>
                </ul>
            </div>
            <div class="d-flex align-items-center">
                <div class="dropdown pl-1">
                    <div class="d-flex align-items-center" id="dropdownMenuButton" data-toggle="dropdown">
                        
                        <p class="font-small m-0 custom-font">{{ session('username') }}</p>
                        <button
                            class="btn btn-xs btn-circle btn-icon-only btn-soft dropdown-toggle mr-2" type="button"
                            aria-haspopup="true" aria-expanded="false" aria-label="dropdown social link"><span
                                class="fa fa-user"></span>
                        </button>
                    </div>
                    <div class="dropdown-menu dropdown-menu-md rtl-layout" aria-labelledby="dropdownMenuButton">
                        <h2 class="h6 dropdown-header custom-font">مرحبا, {{ session('username') }}</h2>
                        <a class="dropdown-item gap-btwn" href="{{   route('user.my-profile') }}">الملف الشخصي <span class="fa-solid fa-id-card"></span></a>
                        <a class="dropdown-item gap-btwn" href="{{   url('user-mode') }}">الدخول كمسئول <span class="fa-solid fa-user-tie"></span></a>
                        <a class="dropdown-item gap-btwn" href="{{   url('settings') }}">الاعدادات <span class="fa-solid fa-gear"></span></a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item custom-font gap-btwn" href="{{ url('/sign-out') }}">تسجيل الخروج <span class="fas fa-sign-out-alt mr-2"></span></a>
                    </div>
                </div>
                <button class="navbar-toggler ml-2" type="button" data-toggle="collapse" data-target="#navbar_global" aria-controls="navbar_global" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>
        </div>
    </nav>
</header>
