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
    <div class="container">
    <div class="row">
        <div class="col">
            <a href="/welcome" class="d-flex justify-content-center"><img src="assets/img/brand/dark.png" height="35" class="mb-3" alt="Themesberg Logo"></a>
            
            <div class="text-center" role="contentinfo">
                <p class="font-weight-normal font-small mb-0">Copyright © Yotta Soft Team <span class="current-year">2025</span>. All rights reserved.</p>
                <p>Developed By: <span class="text-behance">Arafat Aref</span> </p>
            </div>
        </div>
    </div>
    </div>
    @include('scripts.js')
</body>
</html>