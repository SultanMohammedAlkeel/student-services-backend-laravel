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
            <div class="row justify-content-md-around">
                <div class="col-12 col-md-6 col-lg-6 mb-5 mb-lg-0 rtl-layout overflow-auto">
                    <div class="mb-5">
                        <div class="form-group ltr-layout">
                            <div class="input-group mb-4">
                                <input class="form-control" id="search" placeholder="بحث" aria-label="Input group" type="text">
                                <div class="input-group-append">
                                    <span class="input-group-text"><span class="fas fa-search"></span></span>
                                </div>
                            </div>
                        </div>
                        <table class="table table-hover shadow-inset rounded">
                            <tr>
                                <th class="border-0" scope="col" id="id">#</th>
                                <th class="border-0" scope="col" id="building">اسم المبنى</th>
                                <th class="border-0" scope="col" id="college">الموقع</th>
                                <th class="border-0" scope="col" id="contact">الوصف</th>
                            </tr>
                            <tbody id="tbody">
                                @foreach ($buildings as $building)
                                <tr class="table-row">
                                    <th scope="row">{{ $building->id }}</th>
                                    <th scope="row" headers="building">{{ $building->name }}</th>
                                    <td headers="college">{{ $building->location }}</td>
                                    <td headers="college">{{ $building->description }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-4 mb-5 mb-lg-0">
                    <div class="card bg-primary shadow-soft border-light p-4">
                        <div class="card-header text-center pb-0">
                            <h2 class="h4 custom-font">اضافة مبنى جديد</h2>
                        </div>
                        <div class="card-body">
                            <form method="post" action="{{ route('building.store') }}">
                                @csrf
                                <!-- Form -->
                                <div class="invalid-feedback show">{{ $errors->first('name') }}</div>
                                <div class="form-group">
                                    <div class="input-group mb-4">
                                        <input class="form-control" name="name" placeholder="اسم المبنى"
                                            aria-label="Input group" type="text" value="{{ old('name') }}">
                                        <div class="input-group-append">
                                            <span class="input-group-text"><span class="fa-solid fa-building"></span></span>
                                        </div>
                                    </div>
                                </div>
                                <!-- End of Form -->
                                <!-- Form -->
                                <div class="invalid-feedback show">{{ $errors->first('location') }}</div>
                                <div class="form-group">
                                    <div class="input-group mb-4">
                                        <input class="form-control" name="location" placeholder="الموقع"
                                            aria-label="Input group" type="text" value="{{ old('location') }}">
                                        <div class="input-group-append">
                                            <span class="input-group-text"><span class="fa-solid fa-location-dot"></span></span>
                                        </div>
                                    </div>
                                </div>
                                <!-- End of Form -->
                                <!-- Form -->
                                <div class="form-group">
                                    <textarea class="form-control right-text rtl-layout" placeholder="اكتب وصف عن المبنى..." name="description" rows="4" value="{{ old('description') }}"></textarea>
                                </div>
                                <!-- End of Form -->
                                <button type="submit" class="btn btn-block btn-primary">اضافة قسم</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('../../scripts.js')
    <script>
        $(document).ready(function() {
            // عند كتابة نص في حقل البحث
            $('#search').on('keyup', function() {
                var keyword = $(this).val(); // الحصول على الكلمة المفتاحية
    
                // إرسال طلب AJAX
                $.ajax({
                    url: "{{ route('building-search') }}",
                    type: "GET",
                    data: {'keyword': keyword},
                    success: function(response) {
                        // تفريغ الجدول قبل إضافة النتائج الجديدة
                        $('#tbody').empty();
                        
                        
                        
                        // إضافة النتائج إلى الجدول
                        $.each(response, function(index, building) {
                            $('#tbody').append(
                                `
                                <tr class="table-row">
                                    <th scope="row">${building.id}</th>
                                    <th scope="row" headers="building">${building.name}</th>
                                    <td headers="college">${building.location}</td>
                                    <td headers="contact">${ building.description }</td>
                                </tr>
                                `
                            );
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>

