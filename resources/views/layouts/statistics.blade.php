<section class="section section-lg pt-0">
    <div class="container">
        <div class="row rtl-layout">
            <div class="col-10 col-sm-1 col-lg-2 text-center">
                <!-- Visit Box -->
                <div class="icon-box mb-4">
                    <div class="icon icon-shape shadow-soft border border-light rounded-circle mb-4">
                        <span class="fa-solid fa-building-columns"></span>
                    </div>
                    <h3 class="h5 custom-font">الكليات</h3>
                    <span class="counter display-3 text-gray d-block">{{ $colleges->count() }}</span>
                </div>
                <!-- End of Visit Box -->
            </div>
            <div class="col-10 col-sm-1 col-lg-2 text-center">
                <!-- Call Box -->
                <div class="icon-box mb-4">
                    <div class="icon icon-shape shadow-soft border border-light rounded-circle mb-4">
                        <span class="fa-solid fa-building-user"></span>
                    </div>
                    <h3 class="h5 custom-font">الاقسام</h3>
                    <span class="counter display-3 text-gray d-block">{{ $departments->count() }}</span>
                </div>
                <!-- End of Call Box -->
            </div>
            <div class="col-10 col-sm-1 col-lg-2 text-center">
                <!-- Email Box -->
                <div class="icon-box mb-4">
                    <div class="icon icon-shape shadow-soft border border-light rounded-circle mb-4">
                        <span class="fa-solid fa-building"></span>
                    </div>
                    <h3 class="h5 custom-font">المباني</h3>
                    <span class="counter display-3 text-gray d-block">{{ $buildings->count() }}</span>
                </div>
                <!-- End of Email Box -->
            </div>
            <div class="col-10 col-sm-1 col-lg-2 text-center">
                <!-- Email Box -->
                <div class="icon-box mb-4">
                    <div class="icon icon-shape shadow-soft border border-light rounded-circle mb-4">
                        <span class="fa-solid fa-chalkboard-user"></span>
                    </div>
                    <h3 class="h5 custom-font">القاعات</h3>
                    <span class="counter display-3 text-gray d-block">{{ $halls->count() }}</span>
                </div>
                <!-- End of Email Box -->
            </div>
            <div class="col-10 col-sm-1 col-lg-2 text-center">
                <!-- Email Box -->
                <div class="icon-box mb-4">
                    <div class="icon icon-shape shadow-soft border border-light rounded-circle mb-4">
                        <span class="fa-solid fa-user-tie"></span>
                    </div>
                    <h3 class="h5 custom-font">المدرسين</h3>
                    <span class="counter display-3 text-gray d-block">{{ $teachers->count() }}</span>
                </div>
                <!-- End of Email Box -->
            </div>
            <div class="col-10 col-sm-1 col-lg-2 text-center">
                <!-- Email Box -->
                <div class="icon-box mb-4">
                    <div class="icon icon-shape shadow-soft border border-light rounded-circle mb-4">
                        <span class="fa-solid fa-graduation-cap"></span>
                    </div>
                    <h3 class="h5 custom-font">الطلاب</h3>
                    <span class="counter display-3 text-gray d-block">{{ $students->count() }}</span>
                </div>
                <!-- End of Email Box -->
            </div>
        </div>
    </div>
</section>

