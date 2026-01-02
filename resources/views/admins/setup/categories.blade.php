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
                                <th class="border-0" scope="col" id="category">اسم الصنف</th>
                                <th class="border-0" scope="col" id="college">الوصف</th>
                            </tr>
                            <tbody id="tbody">
                                @foreach ($categories as $category)
                                <tr class="table-row">
                                    <th scope="row">{{ $category->id }}</th>
                                    <th scope="row" headers="category">{{ $category->name }}</th>
                                    <td headers="contact">{{ $category->description }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-4 mb-5 mb-lg-0">
                    <div class="card bg-primary shadow-soft border-light p-4">
                        <div class="card-header text-center pb-0">
                            <h2 class="h4 custom-font">اضافة صنف جديد</h2>
                        </div>
                        <div class="card-body">
                            <form method="post" action="{{ route('category.store') }}" enctype="multipart/form-data">
                                @csrf
                                <!-- Form -->
                                <div class="invalid-feedback show">{{ $errors->first('name') }}</div>
                                <div class="form-group">
                                    <div class="input-group mb-4">
                                        <input class="form-control" name="name" placeholder="اسم الصنف"
                                            aria-label="Input group" type="text" value="{{ old('name') }}">
                                        <div class="input-group-append">
                                            <span class="input-group-text"><span class="fa-solid fa-book"></span></span>
                                        </div>
                                    </div>
                                </div>
                                <!-- End of Form -->
                                <!-- Form -->
                                <div class="form-group">
                                    <textarea class="form-control right-text rtl-layout" placeholder="اكتب وصف عن الصنف..." name="description" rows="4" value="{{ old('description') }}"></textarea>
                                </div>
                                <!-- End of Form -->
                                <button type="submit" class="btn btn-block btn-primary">اضافة صنف</button>
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
                    url: "{{ route('category-search') }}",
                    type: "GET",
                    data: {'keyword': keyword},
                    success: function(response) {
                        // تفريغ الجدول قبل إضافة النتائج الجديدة
                        $('#tbody').empty();
                        
                        // إضافة النتائج إلى الجدول
                        $.each(response, function(index, category) {
                            $('#tbody').append(
                                `
                                <tr  class="table-row">
                                    <th scope="row">${category.id}</th>
                                    <th scope="row" headers="category">${category.name}</th>
                                    <td headers="contact">${ category.description }</td>
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

