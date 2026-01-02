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
                            <span class="h4 text-center custom-font">{{ $exam->name }}</span>
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
                                            <span class="h6 mb-0 font-weight-bold custom-font d-flex align-items-center" id="question-{{ $i }}"><span class="{{ $exam->language == 'عربي'? 'ml-2' : 'mr-2' }}"></span> {{ $row['question'] }}</span>
                                            <n class="icon"><span class="fas fa-plus"></span>
                                        </a>
                                        <p id="encrypted-data-{{ $i }}" data-encrypted="{{ base64_encode($row['correct_answer']) }}"></p>
                                        <div class="collapse" id="panel-{{ $i }}">
                                            <div class="pt-3">
                                                <div class="mb-0" id="answers-{{ $i }}">
                                                    <p class="custom-font alert alert-secondary shadow-soft" onclick="selectThisAnswer(this, '{{ $i }}')">{{ $row['option_1'] }}</p>
                                                    <p class="custom-font alert alert-secondary shadow-soft" onclick="selectThisAnswer(this, '{{ $i }}')">{{ $row['option_2'] }}</p>
                                                    <p class="custom-font alert alert-secondary shadow-soft" onclick="selectThisAnswer(this, '{{ $i }}')">{{ $row['option_3'] }}</p>
                                                    <p class="custom-font alert alert-secondary shadow-soft" onclick="selectThisAnswer(this, '{{ $i }}')">{{ $row['option_4'] }}</p>
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
                                            <span class="h6 mb-0 font-weight-bold custom-font d-flex align-items-center"><span id="mark-{{ $i }}" class="{{ $exam->language == 'عربي'? 'ml-2' : 'mr-2' }}"></span> <span id="question-{{ $i }}">{{ $row['question'] }}</span></span>
                                            <span id="answer-{{ $i }}" class="fa-solid" style="font-size: 20px;"></span>
                                        </a>
                                        <p id="encrypted-answer-{{ $i }}" data-encrypted="{{ base64_encode($row['answer']) }}"></p>
                                    </div>
                                @endforeach
                            </div>
                        <!--End of Accordion-->
                        @endif
                        <br>
                        <br>
                        <form action="{{ route('exam.result') }}" method="post">
                            @csrf
                            <input type="hidden" name="exam_id" value="{{ $exam->id }}">
                            <input type="hidden" name="answers" id="answers">
                            <input type="hidden" name="score" id="score">
                            <input type="hidden" name="correct" id="correct">
                            <input type="hidden" name="wrong" id="wrong">
                            <button type="submit" class="btn btn-block btn-primary disabled" id="result">النتيجة</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    @include('scripts.js')
    <script>
        let length = document.querySelectorAll('.card').length; 
        let myAnswer =  Array.from({ length:  length}, () =>
            ({ question: '', answer: ''  , state: ''})
        );
        let putAnswer = 0;

        function selectThisAnswer(element, index) {
            let question = document.getElementById(`question-${index}`);
            let answers = document.querySelectorAll(`#answers-${index} p`);
            
            answers.forEach((item) => {
                item.classList.remove('bg-info');
                item.classList.remove('text-white');

                element.classList.add('bg-info');
                element.classList.add('text-white');
            });
            question.querySelector('span').classList.add('fa-solid', 'fa-circle-check');
            let answer = atob(document.getElementById(`encrypted-data-${index}`).getAttribute('data-encrypted'))
            myAnswer[index].question = question.innerHTML;
            myAnswer[index].answer = element.innerHTML;
            myAnswer[index].state = element.innerHTML == decodeArabic(answer) ? true : false;  
            
            document.getElementById('answers').value = JSON.stringify(myAnswer);
            let grade = myAnswer.filter(item => item.state == true).length;
            document.getElementById('score').value = (100 / length) * grade;
            document.getElementById('correct').value = grade;
            document.getElementById('wrong').value = length - grade;
            
            putAnswer = document.querySelectorAll('.fa-circle-check').length;
            if (putAnswer == length) {
                document.getElementById('result').classList.remove('disabled');
            } else {
                document.getElementById('result').classList.add('disabled');
            }
        }

        let true_false = 0;
        function PutAnswer(element, index) {
            let question = document.getElementById(`question-${index}`);
            let answer = document.getElementById(`answer-${index}`);
            let correct_answer = document.getElementById(`encrypted-answer-${index}`).getAttribute('data-encrypted');
            let decryptedAnswer = atob(correct_answer);
            document.querySelector('#mark-'+ index).classList.add('fa-solid', 'fa-hashtag');
            let theAnswer = answer.classList.contains('fa-check') ? true : false;
            
            if (theAnswer == false) {
                answer.classList.add('fa-check');
                answer.classList.remove('fa-xmark');
            } else {
                answer.classList.remove('fa-check');
                answer.classList.add('fa-xmark');
            }
            theAnswer = answer.classList.contains('fa-check') ? true : false;
            myAnswer[index].question = question.innerHTML;
            myAnswer[index].answer = theAnswer;
            myAnswer[index].state = theAnswer == decodeArabic(decryptedAnswer) ? true : false;
            
            document.getElementById('answers').value = JSON.stringify(myAnswer);
            let grade = myAnswer.filter(item => item.state == true).length;
            document.getElementById('score').value = (100 / length) * grade;
            document.getElementById('correct').value = grade;
            document.getElementById('wrong').value = length - grade;

            let myAnswerLength = document.querySelectorAll('.fa-hashtag').length;
            if (myAnswerLength == length) {
                document.getElementById('result').classList.remove('disabled');
            } else {
                document.getElementById('result').classList.add('disabled');
            }
        }

        function decodeArabic(text) {
            return decodeURIComponent(escape(text));
        }
    </script>
</body>

</html>
