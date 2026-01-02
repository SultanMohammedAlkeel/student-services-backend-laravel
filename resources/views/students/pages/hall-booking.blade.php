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
    
    @if (session('user_type') == 'طالب')
        @include('../students.layouts.header')  
    @else
        @include('../components.404')  
    @endif
    <div class="section section-lg pt-0">
        <div class="container">
            <!-- Title -->
            
            <div class="row">
                <div class="col text-center">
                    <h2 class="h5 mb-7 custom-font"></h2>
                </div>
            </div>
            <!-- End of title-->
            <div class="row justify-content-md-around">
                <div class="col-12 col-md-12 col-lg-12 mb-5 mb-lg-0 rtl-layout overflow-auto">
                        <div class="row">
                            <div class="col text-center">
                            <h1 class="font-weight-bold mb-2 custom-font">بيانات الكلية و المباني</h1>
                        </div>
                    </div>
                    <div class="mb-5">
                        <div class="row my-5 justify-content-center text-center">
                            <div class="col-lg-8">
                                <img src="{{ asset($college->logo_url) }}" class="profile-img" alt="{{ $college->name }}">
                                <h1 class="h2 my-4 custom-font">{{ $college->name }}</h1>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @include('components.divider')
            
            <section class="section section-lg bg-soft" id="services">
                <div class="container">
                    <div class="row">
                        <div class="col text-center">
                            <h1 class="font-weight-bold mb-2 custom-font">المباني</h1>
                        </div>
                    </div>
                    <div class="row mt-6 rtl-layout d-flex align-items-center justify-content-center">
                        <div class="col-12 col-md-7 col-lg-8">
                            <nav>
                                <div class="nav nav-pills nav-fill flex-column flex-sm-row" id="nav-tab-ecommerce" role="tablist">
                                    @foreach ($buildings as $i => $row)
                                        <a class="nav-item nav-link m-1" id="building-card-{{ $i }}-tab" data-toggle="tab" href="#building-card-{{ $i }}" role="tab" aria-controls="building-card-{{ $i }}" aria-selected="false">{{ $row->name }}</a>
                                    @endforeach
                                </div>
                            </nav>
                            <div class="tab-content mt-4 mt-lg-5 overflow-auto" id="nav-tabContent-ecommerce">
                                @foreach ($buildings as $i => $row)
                                    <div class="tab-pane fade" id="building-card-{{ $i }}" role="tabpanel" aria-labelledby="building-card-{{ $i }}-tab">
                                        <table class="table table-hover shadow-inset rounded ">
                                            <tr>
                                                <th class="border-0 text-center" scope="col" rowspan="2">#</th>
                                                <th class="border-0 text-center" scope="col" rowspan="2">اسم القاعة</th>
                                                <th class="border-0 text-center" scope="col" rowspan="2">النوع</th>
                                                <th class="border-0 text-center" scope="col" rowspan="2">السعه</th>
                                                <th class="border-0 text-center" scope="col" rowspan="2">الحالة</th>
                                                <th class="border-0 text-center" scope="col" rowspan="2">عرض</th>
                                            </tr>
                                            <tbody id="tbody">
                                                @foreach ($halls->where('building_id', $row->id) as $j => $hall)
                                                    <tr class="table-row text-right">
                                                        <th class="border-0 text-center">{{ $j+1 }}</th>
                                                        <th class="border-0 text-center">{{ $hall->name }}</th>
                                                        <th class="border-0 text-center">{{ $hall->type }}</th>
                                                        <th class="border-0 text-center">{{ $hall->capacity }}</th>
                                                        <th class="border-0 text-center">{{ $hall_bookings->where('hall_id', $hall->id)->count() }}</th>
                                                        <td aria-hidden="true" class="text-center" data-toggle="modal" data-target="#info-icon-{{ $row->id }}">
                                                            <button class="btn btn-primary" type="button" aria-label="add to cart button" title="عرض الكتاب" data-toggle="modal" data-target="#info-icon-{{ $row->id }}">عرض</button>
                                                        </td>
                                                    </tr>
                                                    <div class="modal fade ltr-layout" id="info-icon-{{ $row->id }}" tabindex="-1" role="dialog" aria-labelledby="info-icon-{{ $row->id }}" aria-hidden="true" style="display: none;">
                                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                                            <div class="modal-content bg-primary">
                                                                <div class="modal-header">
                                                                    <p class="modal-title custom-font" id="modal-title-delete">عرض حجوزات القاعات للغد</p>
                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                        <span aria-hidden="true">×</span>
                                                                    </button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <div class="py-3 text-center">
                                                                        <h2 class="h4 my-3 custom-font" data-toggle="tooltip" data-placement="top" title="اسم المبنى">{{ $row->name }}</h2>
                                                                        <p class="custom-font" data-toggle="tooltip" data-placement="top" title="اسم القاعة">{{ $hall->name }}</p>
                                                                    </div>
                                                                    <div class="container mt-5 rtl-layout">
                                                                        <div class="list-group">
                                                                            <!-- رأس الجدول -->
                                                                            <div class="list-group-item bg-primary fw-bold text-center">
                                                                                <div class="row">
                                                                                    <div class="col">#</div>
                                                                                    <div class="col">الفترة</div>
                                                                                    <div class="col">الحالة</div>
                                                                                </div>
                                                                            </div>
                                                                            @foreach ($periods as $x => $period)
                                                                                <div class="list-group-item text-center">
                                                                                    <div class="row">
                                                                                        <div class="col">{{ $x+1 }}</div>
                                                                                        <div class="col">{{ $period }}</div>
                                                                                        <div class="col">
                                                                                            @if ($hall_bookings->where('period', $period )->where('hall_id', $hall->id)->count())
                                                                                                <span class="badge badge-success">محجوزة</span>
                                                                                            @else
                                                                                                <span class="badge badge-danger">غير محجوزة</span>
                                                                                            @endif
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            @endforeach
                                                                            <!-- عناصر الجدول -->
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer d-flex align-items-center" style="justify-content: space-between;">
                                                                    <button type="button" class="btn btn-primary" data-dismiss="modal" aria-label="Close">
                                                                        <span aria-hidden="true">اغلاق</span>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            @include('components.divider')
            
            <section class="section section-lg bg-soft" id="services">
                <div class="container">
                    <div class="row">
                        <div class="col text-center">
                            <h1 class="font-weight-bold mb-2 custom-font">القاعات المحجوزة</h1>
                        </div>
                    </div>
                    <div class="row mt-6 rtl-layout d-flex align-items-center justify-content-center">
                        <div class="col-12 col-md-7 col-lg-8">
                            <div class="tab-content mt-4 mt-lg-5 overflow-auto" id="nav-tabContent-ecommerce">
                                <table class="table table-hover shadow-inset rounded ">
                                    <tr>
                                        <th class="border-0 text-center text-info" scope="col" rowspan="2">#</th>
                                        <th class="border-0 text-center text-info" scope="col" rowspan="2">اسم المبنى</th>
                                        <th class="border-0 text-center text-info" scope="col" rowspan="2">اسم القاعة</th>
                                        <th class="border-0 text-center text-info" scope="col" rowspan="2">القسم</th>
                                        <th class="border-0 text-center text-info" scope="col" rowspan="2">المستوى</th>
                                        <th class="border-0 text-center text-info" scope="col" rowspan="2">الفترة</th>
                                    </tr>
                                    <tbody id="tbody">
                                        @foreach ($hall_bookings as $i => $row)
                                            <tr class="table-row text-right">
                                                <th class="border-0 text-center">{{ $i+1 }}</th>
                                                <th class="border-0 text-center">{{ $buildings->where('id', $halls->where('id', $row->hall_id)->first()->building_id)->first()->name }}</th>
                                                <th class="border-0 text-center">{{ $halls->where('id', $row->hall_id)->first()->name }}</th>
                                                <th class="border-0 text-center">{{ $departments->where('id', $row->schedule_id)->first()->name }}</th>
                                                <th class="border-0 text-center">{{ $row->level }}</th>
                                                <th class="border-0 text-center">{{ $row->period }}</th>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    @include('../../scripts.js')
    @include('../../scripts.library-js')
</body>
</html>

