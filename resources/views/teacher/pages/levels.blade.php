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

@include('../teacher.layouts.header')
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
                <table class="table table-hover shadow-soft rounded rtl-layout">
                    <tr>
                        <th class="text-center">#</th>
                        <th class="text-center">القسم</th>
                        <th class="text-center">المستوى</th>
                        <th class="text-center">عرض</th>
                        <th class="text-center">رفع درجات</th>
                        <th class="text-center">الحضور</th>
                    </tr>
                    @foreach ($levels as $i => $row)
                        <tr>
                            <th class="text-center">{{ $i + 1 }}</th>
                            <th class="rtl-layout alert  text-center">{{ @$departments->where('id', $row['department_id'])->first()->name}}</th>
                            <th class="text-center">{{ $row['level'] }}</th>
                            <td class="rtl-layout alert  text-center">
                                <form action="{{ route('teacher.my-students') }}" method="post">
                                    @csrf
                                    <input type="hidden" name="level" value="{{ $row['level'] }}">
                                    <input type="hidden" name="department_id" value="{{ $row['department_id'] }}">
                                    <button class="btn btn-primary text-success" type="submit" >عرض</button>
                                </form>
                            </td>
                            <td class="rtl-layout alert  text-center">
                                <form action="{{ route('teacher.put-grade') }}" method="post">
                                    @csrf
                                    <input type="hidden" name="level" value="{{ $row['level'] }}">
                                    <input type="hidden" name="department_id" value="{{ $row['department_id'] }}">
                                    <button class="btn btn-primary text-success" type="submit" >رفع الدجات</button>
                                </form>
                            </td>
                            <td class="rtl-layout alert  text-center">
                                <form action="{{ route('attendances.record') }}" method="post">
                                    @csrf
                                    <input type="hidden" name="level" value="{{ $row['level'] }}">
                                    <input type="hidden" name="department_id" value="{{ $row['department_id'] }}">
                                    <button class="btn btn-primary text-success" type="submit" >عرض</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>
    </div>

    @include('../../scripts.js')

</body>
</html>

