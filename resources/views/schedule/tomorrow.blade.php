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
        @include('../admins.layouts.header')  
    @elseif ((session('user_type') == 'معلم'))
        @include('../teacher.layouts.header')  
    @else
        @include('../students.layouts.header')  
    @endif
    <div class="section section-lg pt-0">
        <div class="container">
            <!-- Title -->
            
            @if (!$activePeriods)
                <div class="row d-flex justify-content-center align-items-center" style="width: 100%; height: 100vh;">    
                    <div class="col text-center">
                        <h2 class="h5 mb-7 custom-font">{{ $message }}</h2>
                    </div>
                </div>
            @else
                <div class="row">
                    <div class="col text-center">
                        <h2 class="h5 mb-7 custom-font"></h2>
                    </div>
                </div>
                <!-- End of title-->
                <div class="row justify-content-md-around overflow-auto" id="student-table">
                    <table class="table shadow-soft rounded">
                        <tr>
                            <th class="text-center">الفترة</th>                        
                            <th class="text-center">المقرر</th>
                            @if (session('user_type') == 'طالب')
                                <th class="text-center">المدرس</th>
                            @endif
                            @if (session('user_type') != 'طالب')
                                <th class="text-center">القسم</th>
                                <th class="text-center">المستوى</th>
                            @endif
                            <th class="text-center">القاعة</th>
                            <th class="text-center">الحالة</th>
                        </tr>
                        @foreach ($schedule as $s)
                            <tr>
                                <th class="text-center">{{ $period[$s['period']] }}</th>
                                <td class="rtl-layout alert  text-center">{{ @$courses->where('id', $s['course'])->first()->name}}</td>
                                @if (session('user_type') == 'طالب')
                                    <td class="rtl-layout alert  text-center">{{ @$teachers->where('id', $s['teacher'])->first()->name}}</td>
                                @endif
                                @if (session('user_type') != 'طالب')
                                    <td class="rtl-layout alert  text-center">{{ @$departments->where('id', $s['department_id'])->first()->name}}</td>
                                    <td class="text-center">{{ $s['level'] }}</td>
                                @endif
                                <th class="rtl-layout alert text-center">{{ @$halls->where('id', $s['hall'])->first()->name}}</th>
                                @if (session('role_id') == 7)
                                    @if (@$courses->where('id', $s['course'])->first()->name != null)
                                        @if ($notification->where('course_id', $s['course'])->where('hall_id', $s['hall'])->where('period', $period[$s['period']])->count() == 1)
                                            @if ($notification_reply->where('notification_id', $notification->where('course_id', $s['course'])->where('hall_id', $s['hall'])->where('period', $period[$s['period']])->first()->id)->count() == 1)
                                                @if ($notification_reply->where('notification_id', $notification->where('course_id', $s['course'])->where('hall_id', $s['hall'])->where('period', $period[$s['period']])->first()->id)->first()->confirmation == 1)
                                                    <th class="text-center text-info" data-toggle="tooltip" data-placement="top" title="{{ @$notification_reply->where('notification_id', $notification->where('course_id', $s['course'])->where('hall_id', $s['hall'])->where('period', $period[$s['period']])->first()->id)->first()->content }}">تم التأكيد</th>
                                                @else
                                                    <th class="text-center text-danger" data-toggle="tooltip" data-placement="top" title="{{ $notification_reply->where('notification_id', $notification->where('course_id', $s['course'])->where('hall_id', $s['hall'])->where('period', $period[$s['period']])->first()->id)->first()->content }}">اعتذر عن الحضور</th>
                                                @endif
                                            @else
                                                <th class="text-center">تم ارسال</th>
                                            @endif
                                        @else
                                            <form action="{{ route('notification.store') }}" method="post">
                                                @csrf
                                                <input type="hidden" name="teacher_id" value="{{ $s['teacher'] }}">
                                                <input type="hidden" name="sender_id" value="{{ session('user_id') }}">
                                                <input type="hidden" name="title" value="تأكيد حضور المحاضرة غدًا">
                                                <input type="hidden" name="content" value="نرجو منكم تأكيد حضوركم لمحاضرة يوم غد، ضمن الجدول الدراسي المعتمد. في حال وجود أي عذر يرجى الرد على هذا الإشعار في أقرب وقت.">
                                                <input type="hidden" name="department_id" value="{{ session('department_id') }}">
                                                <input type="hidden" name="level" value="{{ session('level') }}">
                                                <input type="hidden" name="course_id" value="{{ $s['course'] }}">
                                                <input type="hidden" name="period" value="{{ $period[$s['period']] }}">
                                                <input type="hidden" name="hall_id" value="{{ $s['hall'] }}">
                                                <input type="hidden" name="schedule_id" value="{{ $s['id'] }}">
                                                <input type="hidden" name="teacher_reply" value="0">
                                                <th class="text-center"><button class="btn" type="submit">ارسال تنبية</button></th>
                                            </form>
                                        @endif
                                    @else
                                        <th></th> 
                                    @endif
                                @else
                                    @if (@$courses->where('id', $s['course'])->first()->name != null)
                                        @if ($notification->where('course_id', $s['course'])->where('hall_id', $s['hall'])->where('period', $period[$s['period']])->count() == 1)
                                            @if ($notification_reply->where('notification_id', $notification->where('course_id', $s['course'])->where('hall_id', $s['hall'])->where('period', $period[$s['period']])->first()->id)->count() == 1)
                                                @if ($notification_reply->where('notification_id', $notification->where('course_id', $s['course'])->where('hall_id', $s['hall'])->where('period', $period[$s['period']])->first()->id)->first()->confirmation == 1)
                                                    <th class="text-center text-info" data-toggle="tooltip" data-placement="top" title="{{ $notification_reply->where('notification_id', $notification->where('course_id', $s['course'])->where('hall_id', $s['hall'])->where('period', $period[$s['period']])->first()->id)->first()->content }}">تم التأكيد</th>
                                                    @else
                                                    <th class="text-center text-danger" data-toggle="tooltip" data-placement="top" title="{{ $notification_reply->where('notification_id', $notification->where('course_id', $s['course'])->where('hall_id', $s['hall'])->where('period', $period[$s['period']])->first()->id)->first()->content }}">اعتذر عن الحضور</th>
                                                @endif
                                            @else
                                                <th class="text-center">تم ارسال</th>
                                            @endif
                                        @else
                                            @if (session('user_type') == 'معلم')
                                                <td class="text-center">
                                                    <div class="btn-group mr-2 mb-2">
                                                        <button type="button" class="btn btn-primary">اختيار الرد</button>
                                                        <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                            <span class="fas fa-angle-down dropdown-arrow"></span>
                                                            <span class="sr-only">Toggle Dropdown</span>
                                                        </button>
                                                        <div class="dropdown-menu">
                                                            <form action="{{ route('notification.store') }}" method="post">
                                                                @csrf
                                                                <input type="hidden" name="teacher_id" value="{{ session('user_ref_id') }}">
                                                                <input type="hidden" name="sender_id" value="{{ session('user_id') }}">
                                                                <input type="hidden" name="title" value="تأكيد حضور المحاضرة غدًا">
                                                                <input type="hidden" name="content" value="المحاضرة غدًا حسب الجدول الدراسي">
                                                                <input type="hidden" name="department_id" value="{{ $s['department_id'] }}">
                                                                <input type="hidden" name="level" value="{{ $s['level'] }}">
                                                                <input type="hidden" name="course_id" value="{{ $s['course'] }}">
                                                                <input type="hidden" name="period" value="{{ $period[$s['period']] }}">
                                                                <input type="hidden" name="hall_id" value="{{ $s['hall'] }}">
                                                                <input type="hidden" name="schedule_id" value="{{ $s['id'] }}">
                                                                <input type="hidden" name="teacher_reply" value="1">
                                                                <input type="hidden" name="confirmation" value="1">
                                                                <button type="submit" class="dropdown-item">تاكيد الحضور</button>
                                                            </form>
                                                            <div class="dropdown-divider"></div>
                                                            <form action="{{ route('notification.store') }}" method="post">
                                                                @csrf
                                                                <input type="hidden" name="teacher_id" value="{{ session('user_ref_id') }}">
                                                                <input type="hidden" name="sender_id" value="{{ session('user_id') }}">
                                                                <input type="hidden" name="title" value="الاعتذار عن الحضور">
                                                                <input type="hidden" name="content" value="اعتذر عن حضور محاضرة غدًا">
                                                                <input type="hidden" name="department_id" value="{{ $s['department_id'] }}">
                                                                <input type="hidden" name="level" value="{{ $s['level'] }}">
                                                                <input type="hidden" name="course_id" value="{{ $s['course'] }}">
                                                                <input type="hidden" name="period" value="{{ $period[$s['period']] }}">
                                                                <input type="hidden" name="hall_id" value="{{ $s['hall'] }}">
                                                                <input type="hidden" name="teacher_reply" value="1">
                                                                <input type="hidden" name="schedule_id" value="{{ $s['id'] }}">
                                                                <input type="hidden" name="confirmation" value="0">
                                                                <button class="dropdown-item" type="submit">الاعتذار عن الحضور</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </td>
                                            @else
                                                <th class="text-center">لا يوجد رد</th>
                                            @endif
                                        @endif
                                    @else
                                        <th></th> 
                                    @endif 
                                @endif
                            </tr>
                        @endforeach
                    </table>
                </div>
            @endif
        </div>
    </div>

    @include('../../scripts.js')

</body>
</html>

