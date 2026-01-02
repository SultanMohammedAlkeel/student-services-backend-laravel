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
                <table class="table table-hover shadow-soft rounded">
                    <tr>
                        <th class="text-center">اليوم</th>
                        <th class="text-center">الفترة</th>
                        <th class="text-center">المقرر</th>
                        <th class="text-center">القسم</th>
                        <th class="text-center">المستوى</th>
                        <th class="text-center">القاعة</th>
                    </tr>
                    @foreach ($schedule as $s)
                        <tr>
                            <th class="text-center">{{ $days[$s['day']] }}</th>
                            <th class="text-center">{{ $period[$s['period']] }}</th>
                            <td class="rtl-layout alert  text-center">{{ @$courses->where('id', $s['course'])->first()->name}}</td>
                            <td class="rtl-layout alert  text-center">{{ @$departments->where('id', $s['department_id'])->first()->name}}</td>
                            <td class="text-center">{{ $s['level'] }}</td>
                            <th class="rtl-layout alert text-center">{{ @$halls->where('id', $s['hall'])->first()->name}}</th>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>
    </div>

    @include('../../scripts.js')

</body>
</html>

