<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Yotta Uni App</title>

    @include('../scripts.css')
</head>

<body>

    @include('../welcome/layouts/header')
    @include('../welcome/layouts/body')
    @include('components.divider')
    @include('../welcome/layouts/sign-in')
    @include('components.divider')
    @include('../welcome/layouts/admin-services')

    <hr class="m-5">
    @include('components.footer')
        
    @include('../scripts.js')
</body>

</html>