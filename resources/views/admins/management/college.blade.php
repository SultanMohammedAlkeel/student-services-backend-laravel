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
                            <h1 class="font-weight-bold mb-3 custom-font">بيانات الكلية و الاقسام</h1>
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
                            <h1 class="font-weight-bold mb-3 custom-font">الاقسام</h1>
                        </div>
                    </div>
                    <div class="row mt-6 rtl-layout d-flex align-items-center justify-content-center">
                        <div class="col-12 col-md-7 col-lg-8">
                            <nav>
                                <div class="nav nav-pills nav-fill flex-column flex-sm-row" id="nav-tab-ecommerce" role="tablist">
                                    @foreach ($departments as $i => $row)
                                        <a class="nav-item nav-link m-1" id="department-card-{{ $i }}-tab" data-toggle="tab" href="#department-card-{{ $i }}" role="tab" aria-controls="department-card-{{ $i }}" aria-selected="false">{{ $row->name }} - ({{ $row->short_name }})</a>
                                    @endforeach
                                </div>
                            </nav>
                            <div class="tab-content mt-4 mt-lg-5" id="nav-tabContent-ecommerce">
                                @foreach ($departments as $i => $row)
                                    <div class="tab-pane fade" id="department-card-{{ $i }}" role="tabpanel" aria-labelledby="department-card-{{ $i }}-tab">
                                        <table class="table table-hover shadow-inset rounded">
                                            <tr>
                                                <th class="border-0 text-center" scope="col" rowspan="2">#</th>
                                                <th class="border-0 text-center" scope="col" rowspan="2">المستوى</th>
                                                <th class="border-0 text-center" scope="col" rowspan="2">عدد الطلاب</th>
                                                <th class="border-0 text-center" scope="col" rowspan="2">عدد المقررات</th>
                                                <th class="border-0 text-center" scope="col" rowspan="2">الطلاب</th>
                                                <th class="border-0 text-center" scope="col" rowspan="2">المقررات</th>
                                            </tr>
                                            <tbody id="tbody">
                                                @for($j = 0; $j < $row->levels; $j++)
                                                    <tr class="table-row text-right">
                                                        <th>{{ $j+1 }}</th>
                                                        <th>{{ $levels[$j] }}</th>
                                                        <th>{{ $students->where('level', $levels[$j] ?? null)->where('department_id', $row->id)->count() }}</th>
                                                        <th>{{ $courses->where('level', $levels[$j] ?? null)->where('department_id', $row->id)->count() }}</th>
                                                        <th>
                                                            <form action="{{ route('university.students') }}" method="post">
                                                                @csrf
                                                                <input type="hidden" name="department_id" value="{{ $row->id }}">
                                                                <input type="hidden" name="level" value="{{ $levels[$j] }}">
                                                                <button type="submit" class="btn btn-block btn-primary" id="result">عرض</button>
                                                            </form>
                                                        </th>
                                                        <th>
                                                            <form action="{{ route('university.courses') }}" method="post">
                                                                @csrf
                                                                <input type="hidden" name="department_id" value="{{ $row->id }}">
                                                                <input type="hidden" name="level" value="{{ $levels[$j] }}">
                                                                <button type="submit" class="btn btn-block btn-primary" id="result">عرض</button>
                                                            </form>
                                                        </th>
                                                    </tr>
                                                @endfor
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
            
            <div class="container">
                <div class="row">
                    <div class="col text-center">
                        <h1 class="font-weight-bold mb-3 custom-font">المعلمين</h1>
                    </div>
                </div>
                <div class="row mt-6 rtl-layout d-flex align-items-center justify-content-center">
                    <div class="col-12 col-md-7 col-lg-10">
                        <table class="table table-hover shadow-inset rounded">
                            <tr>
                                <th class="border-0 text-center">#</th>
                                <th class="border-0 text-center" onclick="toggleCode()" style="cursor: pointer; width: 100px;">الكود <i class="fas fa-eye-slash"></i></th>
                                <th class="border-0 text-center">اسم المعلم</th>
                                <th class="border-0 text-center">النوع</th>
                                <th class="border-0 text-center">القسم</th>
                                <th class="border-0 text-center">الدرجة الدراسية</th>
                                <th class="border-0 text-center">التخصص</th>
                                <th class="border-0 text-center">التسجيل</th>
                            </tr>
                            <tbody id="tbody">
                                @if ($teachers->count() == 0)
                                    <tr class="table-row text-right">
                                        <th colspan="8" class="text-center text-danger">لا يوجد معلمين في هذا الكلية</th>
                                    </tr>
                                    
                                @else
                                    @foreach($teachers as $i => $row)
                                        <tr class="table-row text-right">
                                            <th>{{ $i+1 }}</th>
                                            <th data-toggle="tooltip" data-placement="top" title="{{ $row->code }}">
                                                <span class="code cipher-code">@for($i = 0; $i < strlen($row->code); $i++)*@endfor </span>
                                                <span class="code plain-code hide">{{ $row->code }}</span>
                                            </th>
                                            <th>{{ $row->name }}</th>
                                            <th>{{ $row->gender }}</th>
                                            <th>{{ $departments->where('id', $row->department_id)->first()->name }}</th>
                                            <th>{{ $row->academic_degree }}</th>
                                            <th>{{ $row->specialization }}</th>
                                            @if ($row->is_login == 1)
                                                <td class="contact text-info">مسجل</td>
                                            @else
                                                <td class="contact text-danger">غير مسجل</td>
                                            @endif
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

    @include('../../scripts.js')
    <script>
        function toggleCode() { 
            $('.cipher-code').toggleClass('hide');
            $('.plain-code').toggleClass('hide');
            $('.fa-eye-slash').toggleClass('fa-eye');
            $('.fa-eye').toggleClass('fa-eye-slash');
        }
        $(document).ready(function() {
            
        });
    </script>
</body>
</html>

