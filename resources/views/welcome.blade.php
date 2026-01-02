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

    @include('layouts.header')
    <br>
    <br>
    <br>
    @include('layouts.body')
    @include('layouts.statistics')

    <hr class="m-5">
    @include('components.footer')

    @include('scripts.js')
</body>
</html>