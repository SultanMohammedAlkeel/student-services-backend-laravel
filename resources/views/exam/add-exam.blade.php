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
    <script>
        let user_id ;
        let contact_id ; 
    </script>

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
            <!-- End of title-->
            <div class="row justify-content-md-around">
                <div class="col-12 col-md-6 col-lg-6 mb-5 mb-lg-0 overflow-auto">
                    <textarea class="form-control lift-text" name="jsonContent" rows="22" id="jsonContent" ></textarea>
                </div>
                <div class="col-12 col-md-6 col-lg-4 mb-5 mb-lg-0">
                    <div class="card bg-primary shadow-soft border-light p-4">
                        <div class="card-header text-center pb-0">
                            <h2 class="h4 custom-font">اضافة اختبار</h2>
                        </div>
                        <div class="card-body">
                            <form method="post" action="{{ route('exam.store') }}" enctype="multipart/form-data">
                                @csrf
                                <!-- Form -->
                                <div class="invalid-feedback show">{{ $errors->first('name') }}</div>
                                <div class="form-group">
                                    <div class="input-group mb-4">
                                        <input class="form-control" name="name" placeholder="اسم الاختبار" aria-label="Input group" type="text" value="{{ old('title') }}">
                                        <div class="input-group-append">
                                            <span class="input-group-text"><span class="fas fa-book"></span></span>
                                        </div>
                                    </div>
                                </div>
                                <!-- End of Form -->
                                <!-- Form -->
                                <div class="invalid-feedback show">{{ $errors->first('language') }}</div>
                                <div class="form-group">
                                    <select class="custom-select my-1 mr-sm-2" name="language">
                                        @foreach ($languages as $type )
                                            <option value="{{ $type }}" {{ old('language') == $type ? 'selected' : '' }}>{{ $type }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <!-- End of Form -->
                                <!-- Form -->
                                <div class="invalid-feedback show">{{ $errors->first('type') }}</div>
                                <div class="form-group">
                                    <select class="custom-select my-1 mr-sm-2" name="type" id="type">
                                        @foreach ($types as $type )
                                            <option value="{{ $type }}" {{ old('type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <!-- End of Form -->
                                <div class="alert bg-white {{ session('user_type') == 'طالب'? 'hide': '' }}" id="select-college">
                                    <!-- Form -->
                                    <div class="invalid-feedback show">{{ $errors->first('department_id') }}</div>
                                    <div class="form-group" id="department">
                                        <select class="custom-select my-1 mr-sm-2" name="department_id" id="department_id">
                                            <option value="{{ session('user_type') == 'طالب'? session('department_id'): '' }}">اختر القسم</option>
                                            @foreach ($departments->where('college_id', session('college')) as $department )
                                                <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <!-- End of Form -->
                                    <!-- Form -->
                                    <div class="invalid-feedback show">{{ $errors->first('level') }}</div>
                                    <div class="form-group" id="level">
                                        <select class="custom-select my-1 mr-sm-2" name="level">
                                            @if (session('user_type') == 'طالب')
                                                <option value="{{session('level')}}">{{ session('level') }}</option>
                                            @endif
                                            @foreach ($levels as $level )
                                                <option value="{{ $level }}" {{ old('type') == $level ? 'selected' : '' }}>{{ $level }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <!-- End of Form -->
                                </div>
                                <!-- Form -->
                                <input type="hidden" name="exam_data" id="exam-data">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" aria-label="File upload" id="jsonFile" accept=".json">
                                    <label class="custom-file-label" for="jsonFile">Choose Book</label>
                                </div>
                                <br> <br>
                                <!-- Form -->
                                <div class="form-group">
                                    <textarea class="form-control right-text rtl-layout" placeholder="اكتب وصف عن الكتاب..." name="description" rows="4" value="{{ old('description') }}"></textarea>
                                </div>
                                <!-- End of Form -->
                                <button type="submit" class="btn btn-block btn-primary">اضافة اختبار</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('scripts.js')
    @include('scripts.chat-js')

    <script>
        $(document).ready(function() {
            $('#jsonFile').change(function(e) {
                const file = e.target.files[0];
                
                if (!file) {
                    alert('لم يتم اختيار ملف!');
                    return;
                }
                
                if (file.type !== "application/json" && !file.name.endsWith('.json')) {
                    alert('الرجاء اختيار ملف JSON صالح!');
                    return;
                }
                
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    try {
                        const content = e.target.result;
                        const jsonObj = JSON.parse(content);
                        const formattedJson = JSON.stringify(jsonObj, null, 4);
                        $('#jsonContent').val(formattedJson);
                        $('#exam-data').val(formattedJson); // Store the raw JSON content in a hidden input
                    } catch (error) {
                        alert('خطأ في تحليل ملف JSON: ' + error.message);
                        $('#jsonContent').val('');
                    }
                };
                
                reader.onerror = function() {
                    alert('حدث خطأ أثناء قراءة الملف!');
                    $('#jsonContent').val('');
                };
                
                reader.readAsText(file);
            });
        });
    </script>

</body>

</html>
