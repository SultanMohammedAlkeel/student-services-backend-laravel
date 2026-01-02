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

    @include('../setup/layouts/header')
    @include('../setup/layouts/body')
    @include('components.divider')
    @include('../setup/layouts/steps')
    @include('components.divider')  
    @include('../setup/layouts/services')
    @include('components.divider')
    @include('../setup/layouts/team')
    
    @include('../scripts.js')
</body>
</html>