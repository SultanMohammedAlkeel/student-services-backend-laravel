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

    @if (session('user_type') == 'مشرف')
        @if (session('mode') == 'admin')
            @include('../admins.layouts.header')  
        @else
            @include('../../layouts.header')
        @endif
    @else
        @include('../../components.404')
        @php
            return;
        @endphp
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
                            <h1 class="font-weight-bold mb-7 custom-font">بيانات الجامعة و الكليات و الاقسام</h1>
                        </div>
                    </div>
                    <div class="mb-5">
                        <div class="row my-5 justify-content-center text-center">
                            <div class="col-lg-8">
                                <img src="{{ asset($university->logo_url) }}" class="profile-img" alt="{{ $university->name }}">
                                <h1 class="h2 my-4 custom-font">{{ $university->name }}</h1>
                                <h2 class="h5 font-weight-normal text-gray mb-4 custom-font">{{ $university->description }}</h2>
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
                            <h1 class="font-weight-bold mb-7 custom-font">الكليات</h1>
                        </div>
                    </div>
                    <div class="row mt-6 rtl-layout d-flex align-items-center justify-content-center">
                        @foreach ($colleges as $i => $row)
                            <div class="col-md-3 alert shadow-inset m-2">
                                <!-- Icon box -->
                                <div class="icon-box text-center mb-5">
                                    <div class="">
                                        <img src="{{ asset($row->logo_url) }}" class="profile-img" alt="{{ $row->name }}">
                                    </div>
                                    <h2 class="h5 my-3 custom-font">{{ $row->name }}</h2>
                                    <div class="bg-primary border-light mb-0">
                                        <a href="#panel-{{ $i }}" data-target="#panel-{{ $i }}" class="accordion-panel-header" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="panel-{{ $i }}">
                                            <span class="h6 mb-0 font-weight-bold custom-font d-flex align-items-center">الاقسام</span>
                                            <n class="icon"><span class="fas fa-plus"></span>
                                        </a>
                                        <div class="collapse" id="panel-{{ $i }}">
                                            <div class="pt-3">
                                                <table class="table table-hover shadow-inset rounded">
                                                    <tr>
                                                        <th class="border-0 text-center" scope="col" rowspan="2">#</th>
                                                        <th class="border-0 text-center" scope="col" rowspan="2">الاقسام</th>
                                                        <th class="border-0 text-center" scope="col" rowspan="2">رمز القسم</th>
                                                        <th class="border-0 text-center" scope="col" rowspan="2">عدد المستويات</th>
                                                    </tr>
                                                    <tbody id="tbody">
                                                        @foreach ($departments->where('college_id', $row->id) as $j => $dept)
                                                        <tr class="table-row text-right">
                                                            <th>{{ $j+1 }}</th>
                                                            <th>{{ $dept->name}}</th>
                                                            <th>{{ $dept->short_name}}</th>
                                                            <th>{{ $dept->levels}}</th>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="bg-primary border-light mb-0">
                                        <a href="#panel-building-{{ $i }}" data-target="#panel-building-{{ $i }}" class="accordion-panel-header" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="panel-building-{{ $i }}">
                                            <span class="h6 mb-0 font-weight-bold custom-font d-flex align-items-center">المباني</span>
                                            <n class="icon"><span class="fas fa-plus"></span>
                                        </a>
                                        <div class="collapse" id="panel-building-{{ $i }}">
                                            <div class="pt-3">
                                                <table class="table table-hover shadow-inset rounded">
                                                    <tr>
                                                        <th class="border-0 text-center" scope="col" rowspan="2">#</th>
                                                        <th class="border-0 text-center" scope="col" rowspan="2">المبنى</th>
                                                        <th class="border-0 text-center" scope="col" rowspan="2">القاعات</th>
                                                        <!-- <th class="border-0 text-center" scope="col" rowspan="2"></th> -->
                                                    </tr>
                                                    <tbody id="tbody">
                                                        @foreach ($colleges_buildings->where('college_id', $row->id) as $j => $data)
                                                        <tr class="table-row text-right">
                                                            <th>{{ $j+1 }}</th>
                                                            <th>{{ $buildings->where('id', $data->building_id)->first()->name }}</th>
                                                            <th>{{ $halls->where('building_id', $data->building_id)->count() }}</th>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="bg-primary border-light mb-0">
                                        <a href="#panel-student-{{ $i }}" data-target="#panel-student-{{ $i }}" class="accordion-panel-header" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="panel-student-{{ $i }}">
                                            <span class="h6 mb-0 font-weight-bold custom-font d-flex align-items-center">الطلاب و الملعمين</span>
                                            <n class="icon"><span class="fas fa-plus"></span>
                                        </a>
                                        <div class="collapse" id="panel-student-{{ $i }}">
                                            <div class="pt-3">
                                                <table class="table table-hover shadow-inset rounded">
                                                    <tr>
                                                        <th class="border-0 text-center" scope="col" rowspan="2">#</th>
                                                        <th class="border-0 text-center" scope="col" rowspan="2">القسم</th>
                                                        <th class="border-0 text-center" scope="col" rowspan="2">عدد المعلمين</th>
                                                        <th class="border-0 text-center" scope="col" rowspan="2">عدد الطلاب</th>
                                                    </tr>
                                                    <tbody id="tbody">
                                                        @foreach ($departments->where('college_id', $row->id) as $j => $dept)
                                                        <tr class="table-row text-right">
                                                            <th>{{ $j+1 }}</th>
                                                            <th>{{ $dept->name}}</th>
                                                            <th>{{ $teachers->where('department_id', $dept->id)->count() }}</th>
                                                            <th>{{ $students->where('department_id', $dept->id)->count() }}</th>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <br>
                                    <a href="{{ route('university.show', [$row->id]) }}" class="btn btn-primary" type="button">عرض بيانات الكلية <span class="ml-1"><span class="fas fa-book-open"></span></span></a>
                                </div>
                                <!-- End of Icon box -->
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        </div>
    </div>

    @include('../../scripts.js')
    @include('../../scripts.library-js')
</body>
</html>

