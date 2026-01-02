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
                <div class="col-12 col-md-6 col-lg-6 mb-5 mb-lg-0 rtl-layout overflow-auto">
                    <div class="mb-5">
                        <div class="form-group ltr-layout">
                            <div class="input-group mb-4">
                                <input class="form-control" id="search" placeholder="بحث" aria-label="Input group" type="text">
                                <div class="input-group-append">
                                    <span class="input-group-text"><span class="fas fa-search"></span></span>
                                </div>
                            </div>
                        </div>
                        <table class="table table-hover shadow-inset rounded">
                            <tr>
                                <th class="border-0 text-center" scope="col" rowspan="2">#</th>
                                <th class="border-0 text-center" scope="col" rowspan="2">الفصل الدارسي</th>
                                <th class="border-0 text-center" scope="col" rowspan="2">البداية</th>
                                <th class="border-0 text-center" scope="col" rowspan="2">النهائية</th>
                                <th class="border-0 text-center" scope="col" rowspan="2">العمليات</th>
                            </tr>
                            <tbody id="tbody">
                                @foreach ($periods as $i => $period)
                                <tr class="table-row text-center">
                                    <th scope="row">{{ $i+1 }}</th>
                                    <th scope="row">{{ $period->term }}</th>
                                    <th scope="row" headers="academic_year">{{ $period->start_date }}</th>
                                    <th scope="row" headers="academic_year">{{ $period->end_date }}</th>
                                    <th scope="row" headers="academic_year">
                                        <form method="POST" action="{{ route('periods.destroy', ['period' => $period->id]) }}" class="d-inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">حذف</button>
                                        </form>
                                    </th>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-4 mb-5 mb-lg-0">
                    <div class="card bg-primary shadow-soft border-light p-4">
                        <div class="card-header text-center pb-0">
                            <h2 class="h4 custom-font">تحديد الفترة الداسية ل فصل دراسي الاول و الثاني</h2>
                        </div>
                        <div class="card-body">
                            
                            @if ($periods->count() == 0)
                                <form method="post" action="{{ route('periods.store') }}">
                            @else
                                <form method="post" action="{{ route('periods.update', [$periods->first()->academic_year_id]) }}">
                            @endif
                                @csrf
                                <div class="form-group text-right">
                                    <select class="custom-select my-1 mr-sm-2" name="academic_year" id="academic_year" data-toggle="tooltip" data-placement="top" title="السنة الدارسية">
                                        @foreach ($academic_years as $academic_year )
                                            <option value="{{ $academic_year->id }}" {{ old('academic_year') == $academic_year->id ? 'selected' : '' }}>{{ $academic_year->start_date }} - {{ $academic_year->end_date }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <!-- Form -->
                                <div class="form-group text-right">
                                    <select class="custom-select my-1 mr-sm-2" name="term_1" id="term" data-toggle="tooltip" data-placement="top" title="فصل دراسي">
                                            <option value="الاول">الاول</option>
                                    </select>
                                </div>
                                <div class="row align-items-center">
                                    <div class="col"><label class="h6" for="exampleInputDate2">From</label>
                                        <div class="form-group">
                                            <div class="input-group input-group-border">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">
                                                        <span class="far fa-calendar-alt"></span>
                                                    </span>
                                                </div>
                                                <input class="form-control datepicker" id="exampleInputDate2" placeholder="Start date" type="text" name="start_date_1" value="{{ @$periods->first()->start_date }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group">
                                            <label class="h6" for="exampleInputDate3">To</label>
                                            <div class="input-group input-group-border">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">
                                                        <span class="far fa-calendar-alt"></span>
                                                    </span>
                                                </div>
                                                <input class="form-control datepicker" id="exampleInputDate3" placeholder="End date" type="text" name="end_date_1" value="{{ @$periods->first()->end_date }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group text-right">
                                    <select class="custom-select my-1 mr-sm-2" name="term_2" id="term" data-toggle="tooltip" data-placement="top" title="فصل دراسي">
                                            <option value="الثاني">الثاني</option>
                                    </select>
                                </div>
                                <div class="row align-items-center">
                                    <div class="col">
                                        <label class="h6" for="exampleInputDate2">From</label>
                                        <div class="form-group">
                                            <div class="input-group input-group-border">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">
                                                        <span class="far fa-calendar-alt"></span>
                                                    </span>
                                                </div>
                                                <input class="form-control datepicker" id="exampleInputDate2" placeholder="Start date" type="text" name="start_date_2" value="{{ @$periods[1]->start_date }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group">
                                            <label class="h6" for="exampleInputDate3">To</label>
                                            <div class="input-group input-group-border">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">
                                                        <span class="far fa-calendar-alt"></span>
                                                    </span>
                                                </div>
                                                <input class="form-control datepicker" id="exampleInputDate3" placeholder="End date" type="text" name="end_date_2" value="{{ @$periods[1]->end_date }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @if ($periods->count() != 2)
                                    <button type="submit" class="btn btn-block btn-primary">حفظ</button>
                                @else
                                    <button type="submit" class="btn btn-block btn-danger">تعديل</button>
                                @endif
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('../../scripts.js')

</body>
</html>

