<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Yotta Uni App</title>
    @include('scripts.css')
</head>

<body>

    
    @if (session('user_type') == 'مشرف')
        @include('../admins.layouts.header')  
    @elseif ((session('user_type') == 'معلم'))
        @include('../teacher.layouts.header')  
    @else
        @include('../students.layouts.header')  
    @endif
    <br>
    <br>
    <br>
    <div class="section  bg-primary text-dark section-lg">
            <div class="container">
                <!-- Title -->
                <div class="row">
                    <div class="col text-center">
                        <div class="mb-5">
                            <p class="h2 text-center custom-font">{{ $exam->name }}</p>
                            <p class="h5 text-center custom-font {{  $record->score < 50 ? 'text-danger' : 'text-behance' }}">{{ $record->score }}%</p>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-center">
                    <div class="col-12 col-md-10 col-lg-8">
                        @if ($exam->type == 'اختيارات')
                            <!--Accordion-->
                            <div class="accordion shadow-soft rounded {{ $exam->language == 'عربي'? 'rtl-layout' : 'ltr-layout' }}" id="accordionExample1">
                                @foreach ($qusetions as $i => $row)
                                    <div class="card card-sm card-body bg-primary border-light mb-0">
                                        <a href="#panel-{{ $i }}" data-target="#panel-{{ $i }}" class="accordion-panel-header"
                                            data-toggle="collapse" role="button" aria-expanded="false" aria-controls="panel-{{ $i }}">
                                            <span class="h6 mb-0 font-weight-bold custom-font d-flex align-items-center {{ $answers[$i]['state'] == false? 'text-danger' : 'text-behance'}}" id="question-{{ $i }}"><span class="{{ $exam->language == 'عربي'? 'ml-2' : 'mr-2' }} {{ $answers[$i]['state'] == false? 'fa-solid fa-circle-xmark' : 'fa-solid fa-circle-check'}}"></span>{{ $row['question'] }}</span>
                                            <n class="icon"><span class="fas fa-plus"></span>
                                        </a>
                                        <div class="collapse" id="panel-{{ $i }}">
                                            <div class="pt-3">
                                                <div class="mb-0" id="answers-{{ $i }}">
                                                    <p class="custom-font alert alert-secondary shadow-soft {{ $row['correct_answer'] == $row['option_1'] ? 'bg-info text-white' : ''}} {{ $answers[$i]['state'] == false && $answers[$i]['answer'] == $row['option_1'] ? 'bg-danger text-white' : ''}} ">{{ $row['option_1'] }}</p>
                                                    <p class="custom-font alert alert-secondary shadow-soft {{ $row['correct_answer'] == $row['option_2'] ? 'bg-info text-white' : ''}} {{ $answers[$i]['state'] == false && $answers[$i]['answer'] == $row['option_2'] ? 'bg-danger text-white' : ''}} ">{{ $row['option_2'] }}</p>
                                                    <p class="custom-font alert alert-secondary shadow-soft {{ $row['correct_answer'] == $row['option_3'] ? 'bg-info text-white' : ''}} {{ $answers[$i]['state'] == false && $answers[$i]['answer'] == $row['option_3'] ? 'bg-danger text-white' : ''}} ">{{ $row['option_3'] }}</p>
                                                    <p class="custom-font alert alert-secondary shadow-soft {{ $row['correct_answer'] == $row['option_4'] ? 'bg-info text-white' : ''}} {{ $answers[$i]['state'] == false && $answers[$i]['answer'] == $row['option_4'] ? 'bg-danger text-white' : ''}} ">{{ $row['option_4'] }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <!--End of Accordion-->
                        @else
                        <!--Accordion-->
                            <div class="accordion shadow-soft rounded {{ $exam->language == 'عربي'? 'rtl-layout' : 'ltr-layout' }}" id="accordionExample1">
                                @foreach ($qusetions as $i => $row)
                                    <div class="card card-sm card-body bg-primary border-light mb-0" onclick="PutAnswer('question-{{ $i }}', '{{ $i }}')">
                                        <a class="accordion-panel-header" role="button" aria-expanded="false">
                                            <span class="h6 mb-0 font-weight-bold custom-font d-flex align-items-center {{ $answers[$i]['state'] == false? 'text-danger' : 'text-info'}}"><span id="mark-{{ $i }}" class="{{ $exam->language == 'عربي'? 'ml-2' : 'mr-2' }}"></span> <span id="question-{{ $i }}">{{ $row['question'] }}</span></span>
                                            <span id="answer-{{ $i }}" class="fa-solid {{ $answers[$i]['answer'] == true? 'fa-check' : 'fa-xmark' }}" style="font-size: 20px;"></span>
                                        </a>
                                        <p id="encrypted-answer-{{ $i }}" data-encrypted="{{ base64_encode($row['answer']) }}"></p>
                                    </div>
                                @endforeach
                            </div>
                        <!--End of Accordion-->
                        @endif
                        <br>
                        <br>
                        <table class="table shadow-soft rounded text-center">
                            <tr>
                                <th class="border-0 bg-info text-white">الاجابات الصحيحة</th>
                                <th class="border-0 bg-danger text-white">الاجابات الخاطئة</th>
                            </tr>
                            <tr>
                                <td class="h4 text-info">{{ $record->correct }}</td>
                                <td class="h4 text-danger">{{ $record->wrong }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    @include('scripts.js')
</body>

</html>
