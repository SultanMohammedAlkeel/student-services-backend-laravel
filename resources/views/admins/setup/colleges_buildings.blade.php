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
                    @foreach ($data as $i => $college)
                         <!--Accordion-->
                            <div class="accordion shadow-soft rounded" id="accordionExample1">
                                <div class="card card-sm card-body bg-primary border-light mb-0">
                                    <a href="#panel-{{ $i }}" data-target="#panel-{{ $i }}" class="accordion-panel-header" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="panel-{{ $i }}">
                                        <span class="h6 mb-0 font-weight-bold custom-font"># {{ $colleges->where('id', $college->college_id)->first()->name }}</span>
                                        <span class="icon"><span class="fas fa-plus"></span></span>
                                    </a>
                                    <div class="collapse" id="panel-{{ $i }}">
                                        <div class="pt-3">
                                            <p class="mb-0">
                                                @foreach ($colleges_buildings->where('college_id', $college->college_id) as $building)
                                                    <p class="alert shadow-inset show custom-font">{{ $buildings->where('id', $building->building_id)->first()->name }}</p>    
                                                @endforeach
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <!--End of Accordion-->
                    @endforeach
                </div>
                <div class="col-12 col-md-6 col-lg-4 mb-5 mb-lg-0">
                    <div class="card bg-primary shadow-soft border-light p-4">
                        <div class="card-header text-center pb-0">
                            <h2 class="h4 custom-font">تخصيص مبنى لكلية</h2>
                        </div>
                        <div class="card-body">
                            <form method="post" action="{{ route('colleges_buildings.store') }}" enctype="multipart/form-data">
                                @csrf
                                <!-- Form -->
                                <div class="invalid-feedback show">{{ $errors->first('college_id') }}</div>
                                <div class="form-group">
                                    <select class="custom-select my-1 mr-sm-2" name="college_id">
                                        @foreach ($colleges as $college)
                                            <option value="{{ $college->id }}"
                                                {{ $college->id == old('college_id') ? 'selected' : '' }}>
                                                {{ $college->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <!-- End of Form -->
                                <div class="invalid-feedback show">{{ $errors->first('buildings') }}</div>
                                <div class="form-group checkbox-group shadow-inset fade show alert alert-success">
                                    <!-- Checkboxes -->
                                    <div class="mb-3">
                                        <span class="h6 font-weight-bold custom-font">المباني</span>
                                    </div>
                                    <input type="hidden" name="buildings" id="buildings">
                                        @foreach ($buildings as $building)
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" onclick="selected()" value="{{ $building->id }}" id="check-{{ $building->id }}">
                                            <label class="form-check-label" for="check-{{ $building->id }}" >
                                                {{ $building->name }}
                                            </label> 
                                        </div>
                                        @endforeach
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
        function selected() {
            let select = [];
            let checkbox = document.querySelectorAll('.checkbox-group input[type=checkbox]');
            for (let i = 0; i < checkbox.length; i++) {
                if (checkbox[i].checked) {
                    select.push(checkbox[i].value)
                }
            }
            document.querySelector("#buildings").value = JSON.stringify(select);
        }
        $(document).ready(function() {

            // عند كتابة نص في حقل البحث
            $('#search').on('keyup', function() {
                var keyword = $(this).val(); // الحصول على الكلمة المفتاحية

                // إرسال طلب AJAX
                $.ajax({
                    url: "{{ route('department-search') }}",
                    type: "GET",
                    data: {
                        'keyword': keyword
                    },
                    success: function(response) {
                        // تفريغ الجدول قبل إضافة النتائج الجديدة
                        $('#tbody').empty();



                        // إضافة النتائج إلى الجدول
                        $.each(response, function(index, department) {
                            $('#tbody').append(
                                `
                                <tr  class="table-row">
                                    <th scope="row">${department.id}</th>
                                    <th scope="row" headers="department">${department.department_name}</th>
                                    <td headers="college">${department.college_name}</td>
                                    <td headers="contact">${ department.short_name }</td>
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
