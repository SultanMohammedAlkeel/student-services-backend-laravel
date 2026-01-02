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

@include('../../layouts.header')
    
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
                                <th class="border-0" scope="col" id="id">#</th>
                                <th class="border-0" scope="col" id="academic_year">تاريخ البداية</th>
                                <th class="border-0" scope="col" id="college">تاريخ النهائية</th>
                                <th class="border-0" scope="col" id="college">الحالة</th>
                            </tr>
                            <tbody id="tbody">
                                @foreach ($academic_years as $academic_year)
                                <tr class="table-row">
                                    <th scope="row">{{ $academic_year->id }}</th>
                                    <th scope="row" headers="academic_year">{{ $academic_year->start_date }}</th>
                                    <th scope="row" headers="academic_year">{{ $academic_year->end_date }}</th>
                                    @if ($academic_year->status == 1)
                                        <td class="contact text-info">نشطة</td>
                                    @else
                                        <td class="contact text-danger">موقفه</td>
                                    @endif
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-4 mb-5 mb-lg-0">
                    <div class="card bg-primary shadow-soft border-light p-4">
                        <div class="card-header text-center pb-0">
                            <h2 class="h4 custom-font">اضافة سنة دراسية جديد</h2>
                        </div>
                        <div class="card-body">
                            <form method="post" action="{{ route('academic-year.store') }}" enctype="multipart/form-data">
                                @csrf
                                <div class="col">
                                    <label class="h6" for="exampleInputDate2">From</label>
                                    <div class="form-group">
                                        <div class="input-group input-group-border">
                                            <div class="input-group-prepend"><span class="input-group-text"><span class="far fa-calendar-alt"></span></span></div>
                                            <input class="form-control datepicker" name="start_date" id="exampleInputDate2" placeholder="Start date" type="text" onchange="EndValue(this.value)">
                                        </div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group ">
                                        <label class="h6" for="exampleInputDate3">To</label>
                                        <div class="input-group input-group-border">
                                            <div class="input-group-prepend"><span class="input-group-text"><span class="far fa-calendar-alt"></span></span></div>
                                            <input class="form-control datepicker" name="end_date" id="EndDate" placeholder="End date" type="text">
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-block btn-primary">اضافة سنة دراسية</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('../../scripts.js')
    <script>
        let endDate = document.getElementById('EndDate');

        function EndValue(val) {
            let date = new Date(val);
            let year = +date.getFullYear() + 1;
            endDate.value = val.replace(date.getFullYear(), year);
        }
    </script>
</body>
</html>

