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
            @include('layouts.header')  
        @endif
    @elseif ((session('user_type') == 'معلم'))
        @include('../teacher.layouts.header')  
    @else
        @include('../students.layouts.header')  
    @endif
    <div class="section section-lg pt-0">
        <div class="container">
            <!-- Title -->
            
            <div class="row">
                <div class="col text-center">
                    <h2 class="h5 mb-7 custom-font"></h2>
                </div>
            </div>
            <div class="container z-2">
                <div class="row my-5 justify-content-center text-center">
                    <div class="col-lg-8">
                        <img src="{{ asset(session('image_url')) }}" class="rounded-circle img-thumbnail image-lg border-light shadow-inset p-3 profile-img" alt="{{ session('username') }}">
                        <h1 class="h2 my-4 custom-font">{{ session('username') }}</h1>
                        <h2 class="h5 font-weight-normal text-gray mb-4 custom-font">{{ $roles->where('id', session('role_id'))->first()->name }}</h2>
                        <ul class="list-unstyled d-flex justify-content-center mt-3 mb-0">
                            @foreach ($contactInfos as $contactInfo)
                                <li><a href="{{ $contactInfo->url }}" target="_blank" class="icon icon-xs mr-3" ><?php echo $icons[$contactInfo->platform]?></a></li>
                            @endforeach
                        </ul>
                        <table class="table table-hover shadow-inset rounded">
                            <tr>
                                <th class="text-center">{{ $contact }}</th>
                                <th class="text-center">{{ $friend }}</th>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if (session('user_type') == 'طالب')
        @include('profile.layouts.data', ['college' => $college, 'department' => $department])
        @include('profile.layouts.attendance', ['schedules' => $schedules, 'record' => $record, 'courses' => $courses, 'teacher' => $teacher])
    @endif




    
    <hr class="m-5">
    <div class="container">
        <div class="row">
            <div class="col">
                <a href="{{ url('/welcome') }}" target="_self" class="d-flex justify-content-center"><img src="assets/img/brand/dark.png" height="35" class="mb-3" alt="Themesberg Logo"></a>
                <div class="text-center" role="contentinfo">
                    <p class="font-weight-normal font-small mb-0">Copyright © Yotta Soft Team <span class="current-year">2025</span>. All rights reserved.</p>
                    <p>Developed By: <span class="text-behance">Arafat Aref</span> </p>
                </div>
            </div>
        </div>
    </div>
    @include('../../scripts.js')
</body>
</html>

