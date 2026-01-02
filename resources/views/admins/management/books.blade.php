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
                <div class="col-12 col-md-12 col-lg-12 mb-5 mb-lg-0 rtl-layout overflow-auto">
                    <div class=" d-flex justify-content-between align-items-center">
                        <p class="custom-font mx-3" style="width: fit-content;">قائمة بجميع الكتب</p>
                        <div class="form-group ltr-layout" style="width: 70%;">
                            <div class="input-group">
                                <input class="form-control" id="search" placeholder="بحث" aria-label="Input group" type="text">
                                <div class="input-group-append">
                                    <span class="input-group-text"><span class="fas fa-search"></span></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-5">
                        <table class="table table-hover shadow-inset rounded">
                            <tr>
                                <th class="border-0 text-center" scope="col" rowspan="2">#</th>
                                <th class="border-0 text-center" scope="col" rowspan="2">اسم الكتاب</th>
                                <th class="border-0 text-center" scope="col" rowspan="2">النوع</th>
                                <th class="border-0 text-center" scope="col" rowspan="2">صيغة الملف</th>
                                <th class="border-0 text-center" scope="col" rowspan="2">التصنيف</th>
                                <th class="border-0 text-center" scope="col" rowspan="2">رفع بواسطة</th>
                                <th class="border-0 text-center" scope="col" rowspan="2">التنزيلات</th>
                                <th class="border-0 text-center" scope="col" rowspan="2">الاعجاب</th>
                                <th class="border-0 text-center" scope="col" rowspan="2">الحفظ</th>
                                <th class="border-0 text-center" scope="col" rowspan="2">حالة الكتاب</th>
                                <th class="border-0 text-center" scope="col" rowspan="2">عرض البيانات</th>
                            </tr>
                            <tbody id="tbody">
                                @foreach ($books as $i => $row)
                                <tr class="table-row text-right">
                                    <th>{{ $i+1 }}</th>
                                    <th>{{ $row->title}}</th>
                                    <th>{{ $row->type}}</th>
                                    <th>{{ $row->file_type}}</th>
                                    <th>{{ $categories->where('id', $row->category_id)->first()->name }}</th>
                                    <th>{{ $users->where('id', $row->added_by)->first()->name }}</th>
                                    <th id="download-icon-{{ $row->id }}">{{ $row->download_count}}</th>
                                    <th id="like-icon-{{ $row->id }}">{{ $row->likes_count}}</th>
                                    <th id="save-icon-{{ $row->id }}">{{ $row->save_count}}</th>
                                    @if ($row->is_active == 1)
                                        <td class="contact text-info">فعال</td>
                                    @else
                                        <td class="contact text-danger">موقف</td>
                                    @endif
                                    <td aria-hidden="true" class="" data-toggle="modal" data-target="#info-icon-{{ $row->id }}">
                                        <button class="btn btn-primary" type="button" aria-label="add to cart button" title="عرض الكتاب" data-toggle="modal" data-target="#info-icon-{{ $row->id }}">عرض</button>
                                    </td>

                                </tr>
                                <div class="modal fade ltr-layout" id="info-icon-{{ $row->id }}" tabindex="-1" role="dialog" aria-labelledby="info-icon-{{ $row->id }}" aria-hidden="true" style="display: none;">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content bg-primary">
                                            <div class="modal-header">
                                                <p class="modal-title custom-font" id="modal-title-delete">معلومات الكتاب</p>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">×</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="py-3 text-center">
                                                    <span class="modal-icon display-1-lg"><span class="fa-solid fa-book-open"></span></span>
                                                    <h2 class="h4 my-3 custom-font" data-toggle="tooltip" data-placement="top" title="عنوان الكتاب">{{ $row->title }}</h2>
                                                    <p class="custom-font" data-toggle="tooltip" data-placement="top" title="مؤلف الكتاب">{{ $row->author }}</p>
                                                    <p class="custom-font" data-toggle="tooltip" data-placement="top" title="رفع بوسطة">{{ $users->where('id', $row->added_by)->first()->name }}</p>
                                                    <h3 class="h6 font-weight-light text-gray mt-2 custom-font">{!! nl2br(e($row->description)) !!}</h3>

                                                </div>
                                                <div class="d-flex align-items-center justify-content-center">
                                                    <button class="btn btn-primary mx-1" type="button" aria-label="add to cart button" title="عدد الاعجابات">
                                                        <span aria-hidden="true" class="far fa-thumbs-up" id="like-icon-{{ $row->id }}"></span>
                                                        <span class="save-count" id="like-book-{{ $row->id }}">{{ $row->likes_count }}</span>
                                                    </button>
                                                    <button class="btn btn-primary mx-1" type="button" aria-label="add to cart button" title="حفظ الكتاب الى كتبي">
                                                        <span aria-hidden="true" class="fa-solid fa-bookmark" id="save-icon-{{ $row->id }}"></span>
                                                        <span class="save-count" id="save-book-{{ $row->id }}">{{ $row->save_count }}</span>
                                                    </button>
                                                    <button class="btn btn-primary mx-1" type="button" aria-label="add to cart button" title="تحميل الكتاب">
                                                        <span aria-hidden="true" class="fa-solid fa-download" id="download-icon-{{ $row->id }}"></span>
                                                        <span class="save-count" id="download-book-{{ $row->id }}">{{ $row->download_count }}</span>
                                                    </button>
                                                    @if ($row->file_type == 'Programming Files' || $row->file_type == 'PDF')
                                                        <button class="btn btn-primary mx-1" type="button" aria-label="add to cart button" title="فتح الكتاب">
                                                            <span aria-hidden="true" class="fa-solid fa-folder-open" id="open-icon-{{ $row->id }}"></span>
                                                            <span class="save-count" id="open-info-book-{{ $row->id }}">0</span>
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="modal-footer d-flex align-items-center" style="justify-content: space-between;">
                                                <div>
                                                <form action="{{ route('library.stop-book') }}" method="post">
                                                    @csrf
                                                    <input type="hidden" name="id" value="{{ $row->id }}">
                                                    @if ($row->is_active == 1)
                                                        <button type="submit" class="btn bg-danger text-white">ايقاف</button>
                                                        @else
                                                        <button type="submit" class="btn bg-info text-white">تفعيل</button>
                                                    @endif
                                                </form>
                                                </div>
                                                <button type="button" class="btn btn-primary" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">اغلاق</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('../../scripts.js')
    @include('../../scripts.library-js')
</body>
</html>

