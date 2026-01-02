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
                            <span class="nav-link-inner-text">التهيئة</span>
                            <span class="fas fa-angle-down nav-link-arrow ml-2"></span>
                        </a>
                        <ul class="dropdown-menu right-text">
                            <li class="dropdown-submenu"><a href="#" class="dropdown-toggle dropdown-item d-flex justify-content-between align-items-center" aria-haspopup="true" aria-expanded="false">الكليات <span class="fas fa-angle-left nav-link-arrow"></span></a>
                                <ul class="dropdown-menu right-text">
                                    <li>
                                        <li><a class="dropdown-item" href="{{ route('college.index') }}">الكليات</a></li>
                                        <li><a class="dropdown-item" href="{{ route('department.index') }}">الاقسام</a></li>
                                        <li><a class="dropdown-item" href="{{ route('course.index') }}">المقررات</a></li>
                                    </li>
                                </ul>
                            </li>
                            <li class="dropdown-submenu"><a href="#" class="dropdown-toggle dropdown-item d-flex justify-content-between align-items-center" aria-haspopup="true" aria-expanded="false">المباني <span class="fas fa-angle-left nav-link-arrow"></span></a>
                                <ul class="dropdown-menu right-text">
                                    <li>
                                        <li><a class="dropdown-item" href="{{ route('building.index') }}">المباني</a></li>
                                        <li><a class="dropdown-item" href="{{ route('colleges_buildings.index') }}">مباني الاقسام</a></li>
                                        <li><a class="dropdown-item" href="{{ route('hall.index') }}">القاعات</a></li>
                                    </li>
                                </ul>
                            </li>
                            <li class="dropdown-submenu"><a href="#" class="dropdown-toggle dropdown-item d-flex justify-content-between align-items-center" aria-haspopup="true" aria-expanded="false">الافراد <span class="fas fa-angle-left nav-link-arrow"></span></a>
                                <ul class="dropdown-menu right-text">
                                    <li>
                                        <li><a class="dropdown-item" href="{{ route('teacher.index') }}">المعلمين</a></li>
                                        <li><a class="dropdown-item" href="{{ route('students-data.index') }}">الطلاب</a></li>
                                        <li><a class="dropdown-item" href="{{ route('user.index') }}">مشرفين</a></li>
                                    </li>
                                </ul>
                            </li>
                            <li class="dropdown-submenu"><a href="#" class="dropdown-toggle dropdown-item d-flex justify-content-between align-items-center" aria-haspopup="true" aria-expanded="false">تهيئة <span class="fas fa-angle-left nav-link-arrow"></span></a>
                                <ul class="dropdown-menu right-text">
                                    <li>
                                        <a href="{{ route('academic-year.index') }}" class="dropdown-item">السنة الدراسية</a>
                                        <a href="{{ route('schedule.index') }}" class="dropdown-item">الجداول الدراسية</a>
                                        <a href="{{ route('role.index') }}" class="dropdown-item">الصلاحيات</a>
                                        <a href="{{ route('category.index') }}" class="dropdown-item">التصنيفات الكتب</a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a href="#" class="nav-link" data-toggle="dropdown">
                            <span class="nav-link-inner-text">الادارة</span>
                            <span class="fas fa-angle-down nav-link-arrow ml-2"></span>
                        </a>
                        <ul class="dropdown-menu right-text">
                        <li><a class="dropdown-item" href="{{ route('university.index') }}">الجامعة</a></li>
                            <li class="dropdown-submenu"><a href="#" class="dropdown-toggle dropdown-item d-flex justify-content-between align-items-center" aria-haspopup="true" aria-expanded="false">المكتبة <span class="fas fa-angle-left nav-link-arrow"></span></a>
                                <ul class="dropdown-menu right-text">
                                    <li>
                                        <a href="{{ route('category.index') }}" class="dropdown-item">التصنيفات</a>
                                        <a href="{{ route('library.management') }}" class="dropdown-item">الكتب</a>
                                    </li>
                                </ul>
                            </li>
                            <li class="dropdown-submenu"><a href="#" class="dropdown-toggle dropdown-item d-flex justify-content-between align-items-center" aria-haspopup="true" aria-expanded="false">التهيئة <span class="fas fa-angle-left nav-link-arrow"></span></a>
                                <ul class="dropdown-menu right-text">
                                    <li>
                                        <a href="{{ route('schedule.index') }}" class="dropdown-item">السنة الدراسية</a>
                                        <a href="{{ route('periods.index') }}" class="dropdown-item">الفترة الدراسية</a>
                                    </li>
                                </ul>
                            </li>
                            <li><a class="dropdown-item" href="{{ route('users-management') }}">الحسابات</a></li>
                            <li><a class="dropdown-item" href="{{ route('posts-management') }}">المنشورات</a></li>
                            <li><a class="dropdown-item" href="html/pages/pricing.html">الاشعارات</a></li>
                            <li><a class="dropdown-item" href="html/pages/contact.html">الشكاوي</a></li>
                            <li><a class="dropdown-item" href="html/pages/contact.html">الاختبارات</a></li>
                            <li><a class="dropdown-item" href="html/pages/contact.html">التقيمات</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a href="#" class="nav-link" data-toggle="dropdown">
                            <span class="nav-link-inner-text">الصلاحيات</span>
                            <span class="fas fa-angle-down nav-link-arrow ml-2"></span>
                        </a>
                        <ul class="dropdown-menu right-text">
                            <li><a class="dropdown-item" href="html/pages/contact.html">الحسابات</a></li>
                            <li><a class="dropdown-item" href="html/pages/about.html">الادارة</a></li>
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
                        <a class="dropdown-item gap-btwn" href="{{ route('user.my-profile') }}">الملف الشخصي <i class="fa-solid fa-id-card"></i></a>
                        <a class="dropdown-item gap-btwn" href="{{ url('admin-mode') }}">الدخول كمستخدم <span class="fa-solid fa-user"></span></a>
                        <a class="dropdown-item gap-btwn" href="{{ url('settings') }}">الاعدادات <span class="fa-solid fa-gear"></span></a>
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
