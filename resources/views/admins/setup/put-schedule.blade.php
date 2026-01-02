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
            <div class="row justify-content-md-around overflow-auto">
                
                <div class="mb-5">
                    <form method="post" action="{{ route('schedule.store') }}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="department_id" value="{{ $department_id }}">
                        <input type="hidden" name="academic_year" value="{{ $academic_year }}">
                        <input type="hidden" name="term" value="{{ $term }}">
                        <input type="hidden" name="level" value="{{ $level }}">

                        <table class="table shadow-soft rounded rty-layout">
                            <tr>
                                <th>الفترات | الايام</th>
                                <th>08:00 - 10:00</th>
                                <th>10:00 - 12:00</th>
                                <th>12:00 - 02:00</th>
                            </tr>
                            @foreach ($days as $index => $day)
                                
                            <tr>
                                <th rowspan="3">{{ $day }}</th>
                                @for ($i = 0; $i < 3; $i++)
                                <td>
                                    <div class="form-group course-{{ $i }}-day-{{ $index }}">
                                        <select class="custom-select my-1 mr-sm-2" id="course-{{ $i }}-day-{{ $index }}" onchange="Selected('{{ $i }}-day-{{ $index }}', '{{ $index }}', '{{ $i }}')">
                                            <option value="">اجازة</option>
                                            @foreach ($courses as $course )
                                                <option value="{{ $course->id }}">{{ $course->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </td>   
                                @endfor
                            </tr>
                            <tr>
                                @for ($i = 0; $i < 3; $i++)
                                <td>
                                    <div class="form-group disabled hall-{{ $i }}-day-{{ $index }}">
                                        <select class="custom-select my-1 mr-sm-2" id="hall-{{ $i }}-day-{{ $index }}" onchange="Selected('{{ $i }}-day-{{ $index }}', '{{ $index }}', '{{ $i }}')">
                                            @foreach ($halls as $hall )
                                                <option value="{{ $hall->id }}">{{ $hall->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </td>
                                @endfor
                            </tr>
                            <tr>
                                @for ($i = 0; $i < 3; $i++)
                                <td>
                                    <div class="form-group disabled teacher-{{ $i }}-day-{{ $index }}">
                                        <select class="custom-select my-1 mr-sm-2" id="teacher-{{ $i }}-day-{{ $index }}" onchange="Selected('{{ $i }}-day-{{ $index }}', '{{ $index }}', '{{ $i }}')">
                                            @foreach ($teachers as $teacher )
                                                <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </td>
                                @endfor
                            </tr>
                            @endforeach
                        </table>
                        <input type="hidden" name="schedule" id="scheduleInput">
                        <button type="submit" class="btn btn-block btn-primary">حفظ الجدول</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @include('../../scripts.js')
    <script>
        let schedule = Array.from({ length: 6 }, () =>
            Array.from({ length: 3 }, () => ({
                course: '',
                hall: '',
                teacher: ''
            }))
        );
        function Selected(id, day, time) {
            let hall = document.querySelector('.hall-' + id);
            let teacher = document.querySelector('.teacher-' + id);
            let selectCourse = document.querySelector('#course-' + id);
            let selectHall = document.querySelector('#hall-' + id);
            let selectTeacher = document.querySelector('#teacher-' + id);
            
            if (selectCourse.value != '') {
                hall.classList.remove('disabled');
                teacher.classList.remove('disabled');
                
                schedule[day][time].course = selectCourse.value;
                schedule[day][time].hall = selectHall.value;
                schedule[day][time].teacher = selectTeacher.value;
            } else {
                hall.classList.add('disabled');
                teacher.classList.add('disabled');
                schedule[day][time].course = '';
                schedule[day][time].hall = '';
                schedule[day][time].teacher = '';
            }
            
            document.getElementById('scheduleInput').value = JSON.stringify(schedule);
        }
    </script>
</body>
</html>

