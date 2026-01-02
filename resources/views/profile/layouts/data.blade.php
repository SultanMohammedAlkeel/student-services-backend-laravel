<div class="card-header text-center pb-0">
    <h2 class="h4 custom-font">بياناتي الاكاديمية</h2>
</div>
<div class="col-12 col-md-12 col-lg-8 mx-auto mb-2">
    <div class="card card-sm card-body bg-primary border-light mb-0">
        <a href="#panel-4" data-target="#panel-4" class="accordion-panel-header" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="panel-1">
            <span class="icon-title h6 mb-0 font-weight-bold custom-font"><span class="fa-solid fa-id-card"></span>بياناتي الاساسي</span>
            <span class="icon"><span class="fas fa-plus"></span></span>
        </a>
        <div class="collapse" id="panel-4">
            <div class="pt-3">
                <p class="mb-0">
                <div class="form-group disabled">
                    <div class="input-group mb-4">
                        <input class="form-control" placeholder="اسم المستخدم" aria-label="Input group" type="text" value="{{ $student->name }}">
                        <div class="input-group-append">
                            <span class="input-group-text"><span class="fas fa-user"></span></span>
                        </div>
                    </div>
                </div>
                <div class="form-group disabled">
                    <div class="input-group mb-4">
                        <input class="form-control" placeholder="رقم البطاقة" aria-label="Input group" type="text" value="{{  $student->card }}">
                        <div class="input-group-append">
                            <span class="input-group-text"><span class="fa-solid fa-address-card"></span></span>
                        </div>
                    </div>
                </div>
                <div class="form-group disabled">
                    <div class="input-group mb-4">
                        <input class="form-control" placeholder="النوع" aria-label="Input group" type="text" value="{{ $student->gender }}">
                        <div class="input-group-append">
                            <span class="input-group-text"><span class="fa-solid fa-person"></span></span>
                        </div>
                    </div>
                </div>
                <div class="form-group disabled">
                    <div class="input-group mb-4">
                        <input class="form-control" aria-label="Input group" type="text" value="{{ $college->where('id',  $department->where('id', $student->department_id )->first()->college_id )->first()->name }}">
                        <div class="input-group-append">
                            <span class="input-group-text"><span class="fa-solid fa-building-columns"></span></span>
                        </div>
                    </div>
                </div>
                <div class="form-group disabled">
                    <div class="input-group mb-4">
                        <input class="form-control" aria-label="Input group" type="text" value="{{ $department->where('id', $student->department_id )->first()->name }}">
                        <div class="input-group-append">
                            <span class="input-group-text"><span class="fa-solid fa-building-user"></span></span>
                        </div>
                    </div>
                </div>
                <div class="form-group disabled">
                    <div class="input-group mb-4">
                        <input class="form-control" aria-label="Input group" type="text" value="{{  $student->level }}">
                        <div class="input-group-append">
                            <span class="input-group-text"><span class="fa-solid fa-layer-group"></span></span>
                        </div>
                    </div>
                </div>
                <div class="form-group disabled" data-toggle="tooltip" data-placement="top" title="تاريخ تسجيل الدخول">
                    <div class="input-group mb-4">
                        <input class="form-control" placeholder="القسم" aria-label="Input group" type="text" value="{{  $student->updated_at }}">
                        <div class="input-group-append">
                            <span class="input-group-text"><span class="far fa-calendar-alt"></span></span>
                        </div>
                    </div>
                </div>
                </p>
            </div>
        </div>
    </div>
</div>