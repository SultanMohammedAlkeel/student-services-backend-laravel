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
    <br>
    <br>
    <br>
    @include('../../layouts.user-body')

    <hr class="m-5">
    @include('components.footer')
    
    @include('scripts.js')
</body>
</html>