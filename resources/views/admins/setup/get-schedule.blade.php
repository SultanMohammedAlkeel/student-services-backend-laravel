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
                <table class="table  table-hover shadow-soft rounded overflow-auto">
                    <tr>
                        <th>الفترات | الايام</th>
                        <th>08:00 - 10:00</th>
                        <th>10:00 - 12:00</th>
                        <th>12:00 - 02:00</th>
                    </tr>
                    @for ($i = 0; $i < 6; $i++)
                    <tr>
                        <th rowspan="3">{{ $days[$i] }}</th>
                        @for ($j = 0; $j < 3; $j++)
                            @if ($schedule[$i][$j]['course'] != '')
                                <td class="rtl-layout alert  text-center">{{ @$courses->where('id', $schedule[$i][$j]['course'])->first()->name}}</td>
                            @else
                                <td class="alert text-center"> - </td>
                            @endif
                        @endfor
                    </tr>
                    <tr>
                        @for ($j = 0; $j < 3; $j++)
                            @if ($schedule[$i][$j]['hall'] != '')
                                <td class="rtl-layout alert text-center {{ $halls->where('id', $schedule[$i][$j]['hall'])->first()->type != 'محاضرات' ? 'alert-secondary shadow-inset' : '' }} ">{{ $halls->where('id', $schedule[$i][$j]['hall'])->first()->name  }}</td>
                            @else
                                <td class="alert text-center"> - </td>
                            @endif
                        @endfor
                    </tr>
                    <tr>
                        @for ($j = 0; $j < 3; $j++)
                            @if ($schedule[$i][$j]['teacher'] != '')
                                <td class="rtl-layout alert text-center">{{ @$teachers->where('id', $schedule[$i][$j]['teacher'])->first()->name }}</td>
                            @else
                                <td class="alert text-center"> - </td>
                            @endif
                        @endfor
                    </tr>
                    @endfor
                </table>
            </div>
        </div>
    </div>

    @include('../../scripts.js')
</body>
</html>

