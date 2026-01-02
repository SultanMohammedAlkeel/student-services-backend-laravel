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

@include('../teacher.layouts.header')
    
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
                <div class="col-12 col-md-12 col-lg-7 mb-5 mb-lg-0 rtl-layout overflow-auto">
                    <div class="mb-5">
                        <div class="form-group ltr-layout d-flex justify-content-between">
                            <div class="input-group mb-4 mr-2" style="width: 25%;">
                                <input class="form-control text-danger" id="max-grade" placeholder="اعلى درجة" aria-label="Input group" type="number" min="0" max="100">
                                <div class="input-group-append">
                                    <span class="input-group-text"><span class="fas fa-award"></span></span>
                                </div>
                            </div>
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
                                <th class="border-0" scope="col" id="row">اسم الطالب</th>
                                <th class="border-0" scope="col" id="row">النوع</th>
                                <th class="border-0" scope="col" id="row">الدرجة</th>
                            </tr>
                            <tbody id="tbody">
                                @foreach ($students as $i => $row)
                                <tr class="table-row" data-name="{{ $row->name }}">
                                    <th scope="row">{{ $i + 1 }}</th>
                                    <th scope="row" headers="row" class="data-item">{{ $row->name }}</th>
                                    <th scope="row">{{ $row->gender }}</th>
                                    <th style="width: 150px;">
                                        <div class="input-group mb-4">
                                            <input class="form-control text-center input" id="student-{{ $i }}" value="0" aria-label="Input group" type="number" min="0" max="100">
                                        </div>
                                    </th>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('../../scripts.js')

    <script>

        $(document).ready(function() {
            // عند كتابة نص في حقل البحث
            $(document).ready(function() {
                $('#search').on('input', function() {
                    var searchText = $(this).val().toLowerCase(); // النص المدخل للبحث
                    $('.table-row').each(function() {
                        var itemName = $(this).data('name').toLowerCase(); // اسم العنصر
                        console.log(itemName);

                        // إظهار أو إخفاء العنصر بناءً على البحث
                        if (itemName.includes(searchText)) {
                            $(this).removeClass('hide');
                        } else {
                            $(this).addClass('hide');
                        }
                    });
                });
            });
            const inputs = document.querySelectorAll('input');

            
            const maxGradeInput = document.getElementById('max-grade');
            let maxGrade = 100;
            // إضافة مستمع حدث لتحديث القيمة القصوى عند تغييرها
            maxGradeInput.addEventListener('input', function() {
                maxGrade = parseInt(maxGradeInput.value) || 100;
            });
            
            inputs.forEach((input, index) => {
                // عند الضغط على مفتاح في الحقل
                input.addEventListener('keypress', function(e) {
                    // إذا كان المفتاح المضغوط هو Enter (كود 13)
                    if (e.key === 'Enter') {
                        // منع السلوك الافتراضي (إرسال النموذج إذا كان داخل form)
                        e.preventDefault();
                        // إذا لم يكن هذا الحقل الأخير
                        if (index < inputs.length - 1) {// تحديد النص في الحقل التالي
                            if (inputs[index].value == "") {
                                inputs[index].value = 0;   
                            } if (inputs[index].value > maxGrade) {
                                inputs[index].value = maxGrade;   
                            } else if (inputs[index].value < 0) {
                                inputs[index].value = 0;   
                            }
                            // الانتقال إلى الحقل التالي
                            inputs[index + 1].focus();
                            inputs[index + 1].select(); 
                        } else {
                            // إذا كان الحقل الأخير، يمكنك إضافة أي سلوك تريده هنا
                            // مثلاً إرسال النموذج أو الرجوع إلى الحقل الأول
                            inputs[0].focus();
                        }
                    }
                });
            });
        });
    </script>
</body>
</html>

