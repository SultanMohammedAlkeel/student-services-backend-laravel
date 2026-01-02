<div class="col-12 col-sm-12 col-md-6 col-lg-4 mb-5">
    <!-- card -->
    <div class="card bg-primary shadow-soft border-light book-card">
        <div class="card-header book-type">{!! $fileIcons[$book_data->file_type] !!}</div>
        <small class="text-center h6">{{ $book_data->file_type }}</small>
        <div class="card-body border-top border-light rtl-layout">
            <div>
                <p class="h5 custom-font text-behance">{{ $book_data->title }}</p>
                <p class="custom-font text-black mt-1">المؤلف : {{ $book_data->author }}</p>
                <h3 class="h6 font-weight-light text-gray mt-2 custom-font">{!! nl2br(e($book_data->description)) !!}</h3>
            </div>
            <div class="d-flex mt-3 align-items-center ltr-layout">
                
                <span aria-hidden="true" class="fa-solid fa-circle-info mr-2" data-toggle="modal" data-target="#info-icon-{{ $book_data->id }}"></span>
                <span aria-hidden="true" class="far fa-thumbs-up" id="like-icon-{{ $book_data->id }}" onclick="likeBook('{{ $book_data->id }}')"></span>
                <span class="badge badge-pill badge-gray ml-2" id="like-book-{{ $book_data->id }}">{{ $book_data->likes_count }}</span>
            </div>
        </div>
        <div class="card-footer border-top border-light p-4">
            <div class="d-flex align-items-center">
                <div class="size">{{ formatFileSize($book_data->file_size) }}</div>
                <div class="ml-auto">
                    <button class="btn btn-primary" type="button" aria-label="add to cart button" title="حفظ الكتاب الى كتبي" onclick="saveBook('{{ $book_data->id }}')">
                        <span aria-hidden="true" class="fa-solid fa-bookmark" id="save-icon-{{ $book_data->id }}"></span>
                        <span class="save-count" id="save-book-{{ $book_data->id }}">{{ $book_data->save_count }}</span>
                    </button>
                    <button class="btn btn-primary" type="button" aria-label="add to cart button" title="تحميل الكتاب"  onclick="downloadBook('{{ $book_data->id  }}', '{{ $book_data->file_url }}', '{{ $book_data->title }}')">
                        <span aria-hidden="true" class="fa-solid fa-download" id="download-icon-{{ $book_data->id }}"></span>
                        <span class="save-count" id="download-book-{{ $book_data->id }}">{{ $book_data->download_count }}</span>
                    </button>
                    @if ($book_data->file_type == 'Programming Files' || $book_data->file_type == 'PDF')
                        <button class="btn btn-primary" type="button" aria-label="add to cart button" title="فتح الكتاب" onclick="OpenBook('{{ $book_data->id  }}', '{{ $book_data->file_url }}')">
                            <span aria-hidden="true" class="fa-solid fa-folder-open" id="open-icon-{{ $book_data->id }}"></span>
                            <span class="save-count" id="open-book-{{ $book_data->id }}">0</span>
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <!-- card -->
</div>

<div class="modal fade" id="info-icon-{{ $book_data->id }}" tabindex="-1" role="dialog" aria-labelledby="info-icon-{{ $book_data->id }}" aria-hidden="true" style="display: none;">
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
                    <h2 class="h4 my-3 custom-font" data-toggle="tooltip" data-placement="top" title="عنوان الكتاب">{{ $book_data->title }}</h2>
                    <p class="custom-font" data-toggle="tooltip" data-placement="top" title="مؤلف الكتاب">{{ $book_data->author }}</p>
                    <p class="custom-font" data-toggle="tooltip" data-placement="top" title="رفع بوسطة">{{ $users->where('id', $book_data->added_by)->first()->name }}</p>
                </div>
                <div class="d-flex align-items-center justify-content-center">
                    <button class="btn btn-primary mx-1" type="button" aria-label="add to cart button" title="عدد الاعجابات" onclick="likeBook('{{ $book_data->id }}')">
                        <span aria-hidden="true" class="far fa-thumbs-up" id="like-icon-{{ $book_data->id }}"></span>
                        <span class="save-count" id="like-book-{{ $book_data->id }}">{{ $book_data->likes_count }}</span>
                    </button>
                    <button class="btn btn-primary mx-1" type="button" aria-label="add to cart button" title="حفظ الكتاب الى كتبي" onclick="saveBook('{{ $book_data->id }}')">
                        <span aria-hidden="true" class="fa-solid fa-bookmark" id="save-icon-{{ $book_data->id }}"></span>
                        <span class="save-count" id="save-book-{{ $book_data->id }}">{{ $book_data->save_count }}</span>
                    </button>
                    <button class="btn btn-primary mx-1" type="button" aria-label="add to cart button" title="تحميل الكتاب"  onclick="downloadBook('{{ $book_data->id  }}', '{{ $book_data->file_url }}', '{{ $book_data->title }}')">
                        <span aria-hidden="true" class="fa-solid fa-download" id="download-icon-{{ $book_data->id }}"></span>
                        <span class="save-count" id="download-book-{{ $book_data->id }}">{{ $book_data->download_count }}</span>
                    </button>
                    @if ($book_data->file_type == 'Programming Files' || $book_data->file_type == 'PDF')
                        <button class="btn btn-primary mx-1" type="button" aria-label="add to cart button" title="فتح الكتاب" onclick="OpenBook('{{ $book_data->id  }}', '{{ $book_data->file_url }}')">
                            <span aria-hidden="true" class="fa-solid fa-folder-open" id="open-icon-{{ $book_data->id }}"></span>
                            <span class="save-count" id="open-info-book-{{ $book_data->id }}">0</span>
                        </button>
                    @endif
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">تم</span>
                </button>
            </div>
        </div>
    </div>
</div>
