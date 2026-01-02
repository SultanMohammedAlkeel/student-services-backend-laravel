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
            
            <div class="row">
                <div class="col text-center">
                    <h2 class="h5 mb-7 custom-font"></h2>
                </div>
            </div>
            <div class="container z-2">
                <div class="row my-5 justify-content-center text-center">
                    <div class="col-lg-8">
                        <img src="{{ asset($user->image_url) }}" class="rounded-circle img-thumbnail image-lg border-light shadow-inset p-3 profile-img" alt="{{ $user->name }}">
                        <h1 class="h2 my-4 custom-font">{{ $user->name }}</h1>
                        <h2 class="h5 font-weight-normal text-gray mb-4 custom-font">{{ $roles->where('id', $user->role_id )->first()->name }}</h2>
                        <ul class="list-unstyled d-flex justify-content-center mt-3 mb-0">
                            @foreach ($contactInfos as $contactInfo)
                                <li><a href="{{ $contactInfo->url }}" target="_blank" class="icon icon-xs icon-{{ $contactInfo->platform }} mr-3" ><?php echo $icons[$contactInfo->platform]?></a></li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if (@$all_posts)
        @include('profile.layouts.post')
    @endif

    @if (@$is_media)
        @include('profile.layouts.media')
    @endif

    @if (@$is_profile)
        @if ($user->user == 'طالب')
            @include('profile.layouts.data', ['college' => $college, 'department' => $department])
            @include('profile.layouts.attendance', ['schedules' => $schedules, 'record' => $record, 'courses' => $courses, 'teacher' => $teacher])
        @endif
    @endif
    


    @include('../../scripts.js')
</body>
</html>

