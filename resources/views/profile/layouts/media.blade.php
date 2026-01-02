<hr>
<div class="col text-center">
    <h2 class="h4 mb-3 custom-font">الوسائط</h2>
</div>

@php
function getFileIcon($ext) {
    $icons = [
        'pdf'  => 'far fa-file-pdf text-danger',
        'doc'  => 'far fa-file-word text-info',
        'docx' => 'far fa-file-word text-info',
        'xls'  => 'far fa-file-excel text-success',
        'xlsx' => 'far fa-file-excel text-success',
        'txt'  => 'far fa-file-alt text-secondary',
        'zip'  => 'far fa-file-archive text-warning',
        'rar'  => 'far fa-file-archive text-warning',
        'php'  => 'fab fa-php text-info',
        'js'   => 'fab fa-js text-warning',
        'html' => 'fab fa-html5 text-danger',
        'css'  => 'fab fa-css3 text-info',
        'json' => 'fas fa-code text-dark',
        'sql'  => 'fas fa-database text-info',
        'csv'  => 'fas fa-file-csv text-success',
        'ppt'  => 'far fa-file-powerpoint text-danger',
        'pptx' => 'far fa-file-powerpoint text-danger',
        'mp3'  => 'fas fa-file-audio text-purple',
        'wav'  => 'fas fa-file-audio text-purple',
        'ogg'  => 'fas fa-file-audio text-purple',
        'm4a'  => 'fas fa-file-audio text-purple',
        'mp4'  => 'fas fa-file-video text-danger',
    ];
    
    return $icons[strtolower($ext)] ?? 'far fa-file';
}

function formatFileSize($bytes) {
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } else {
        return $bytes . ' بايت';
    }
}
@endphp
<div class="col-12 col-md-12 col-lg-12">
    <nav>
        <div class="nav nav-pills nav-fill flex-sm-row rtl-layout" id="nav-tab-ecommerce" role="tablist">
            <a class="badge badge-secondary text-uppercase mr-2 active" id="all-media-tab" data-toggle="tab" href="#all-media" role="tab" aria-controls="all-media" aria-selected="true">الكل</a>
            <a class="badge badge-secondary text-uppercase mr-2" id="images-tab" data-toggle="tab" href="#images" role="tab" aria-controls="images" aria-selected="false">صور</a>
            <a class="badge badge-secondary text-uppercase mr-2" id="videos-tab" data-toggle="tab" href="#videos" role="tab" aria-controls="videos" aria-selected="false">فيديوهات</a>
            <a class="badge badge-secondary text-uppercase mr-2" id="audios-tab" data-toggle="tab" href="#audios" role="tab" aria-controls="audios" aria-selected="false">صوتيات</a>
            <a class="badge badge-secondary text-uppercase mr-2" id="files-tab" data-toggle="tab" href="#files" role="tab" aria-controls="files" aria-selected="false">ملفات</a>
        </div>
    </nav>
    <div class="tab-content mt-4 mt-lg-5 mb-5" id="nav-tabContent-ecommerce">
        <!-- تبويب لجميع الوسائط -->
        <div class="tab-pane fade show active" id="all-media" role="tabpanel" aria-labelledby="all-media-tab">
            <div class="d-flex flex-wrap">
                @foreach ($media as $md)
                    <div class="col-12 col-md-6 col-lg-4 mb-4">
                        <div class="card bg-primary shadow-soft border-light">
                            @if ($md->type == 'صورة')
                                <div class="card-header p-4">
                                    <div class="image-container" style="width: 100%; height: 200px; overflow: hidden; display: flex; justify-content: center; align-items: center;">
                                        <a href="{{asset($md->file_url)}}" data-lightbox="gallery" data-title="صورة المنتج" target="_blank">
                                            <img src="{{asset($md->file_url)}}" class="img-fluid" style="width: 100%; height: 100%; object-fit: contain;" alt="Wood Portrait">
                                        </a>
                                    </div>
                                </div>
                            @elseif ($md->type == 'فيديو')
                                <div class="card-header p-4">
                                    <div class="image-container" style="width: 100%; height: 200px; overflow: hidden; display: flex; justify-content: center; align-items: center;">
                                        <video controls style="width: 100%; height: 100%; object-fit: contain;">
                                            <source src="{{asset($md->file_url)}}" type="video/mp4">
                                            متصفحك لا يدعم عنصر الفيديو.
                                        </video>
                                    </div>
                                </div>
                            @else
                                <div class="card-body text-center">
                                    @php
                                        $fileExt = pathinfo($md->file_url, PATHINFO_EXTENSION);
                                        $fileIcon = getFileIcon($fileExt);
                                    @endphp
                                    <i class="{{ $fileIcon }} fa-4x mb-3 "></i>
                                    <h6 class="card-title text-truncate custom-font">{{ basename($md->file_name) }}</h6>
                                    
                                    @if ($md->type == 'صوت')
                                        <audio controls style="width: 100%;" oncontextmenu="return false;">
                                            <source src="{{ asset($md->file_url) }}" type="audio/mpeg">
                                            متصفحك لا يدعم عنصر الصوت.
                                        </audio>
                                    @endif
                                    
                                    <div class="d-flex justify-content-between small text-muted mb-3">
                                        <span>{{ strtoupper($fileExt) }} ملف</span>
                                        <span>{{ formatFileSize($md->size) }}</span>
                                    </div>
                                </div>
                            @endif
                            
                            <div class="card-footer bg-transparent border-top">
                                <div class="btn-group w-100">
                                    <a href="{{ asset($md->file_url) }}" class="btn btn-sm" download>
                                        <i class="fas fa-download"></i> تحميل
                                    </a>
                                </div>
                                <small class="text-muted d-flex justify-content-between mt-2">
                                    <span>
                                        <i class="far fa-clock"></i> {{ $md->created_at->format('Y-m-d H:i') }} 
                                    </span> 
                                    <span class="badge bg-secondary text-white">{{ $md->type }}</span>
                                </small>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <!-- تبويب للصور -->
        <div class="tab-pane fade" id="images" role="tabpanel" aria-labelledby="images-tab">
            <div class="d-flex flex-wrap">
                @foreach ($media as $md)
                    @if ($md->type == 'صورة')
                        <div class="col-12 col-md-6 col-lg-4 mb-4">
                            <div class="card bg-primary shadow-soft border-light">
                                <div class="card-header p-4">
                                    <div class="image-container" style="width: 100%; height: 200px; overflow: hidden; display: flex; justify-content: center; align-items: center;">
                                        <a href="{{asset($md->file_url)}}" data-lightbox="gallery" data-title="صورة المنتج" target="_blank">
                                            <img src="{{asset($md->file_url)}}" class="img-fluid" style="width: 100%; height: 100%; object-fit: contain;" alt="Wood Portrait">
                                        </a>
                                    </div>
                                    
                                    <div class="btn-group w-100 mt-2">
                                        <a href="{{ asset($md->file_url) }}" class="btn btn-sm" download>
                                            <i class="fas fa-download"></i> تحميل
                                        </a>
                                    </div>
                                </div>
                                <div class="card-footer bg-transparent border-top">
                                    <small class="text-muted">
                                        <i class="far fa-clock"></i> 
                                        {{ $md->created_at->format('Y-m-d H:i') }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
        <!-- تبويب الفيديوهات -->
        <div class="tab-pane fade" id="videos" role="tabpanel" aria-labelledby="videos-tab">
            <div class="d-flex flex-wrap">
                @foreach ($media as $md)
                @if ($md->type == 'فيديو')
                <div class="col-12 col-md-6 col-lg-4 mb-5 mb-md-0">
                    <div class="card bg-primary shadow-soft border-light">
                        <div class="card-header p-4">
                            <div class="image-container" style="width: 100%; height: 200px; overflow: hidden; display: flex; justify-content: center; align-items: center;">
                                <a href="{{asset($md->file_url)}}" data-lightbox="gallery" data-title="صورة المنتج" target="_blank">
                                    <video controls style="width: 100%; height: 100%; object-fit: contain;">
                                        <source src="{{asset($md->file_url)}}" type="video/mp4">
                                        متصفحك لا يدعم عنصر الفيديو.
                                    </video>
                                </a>
                                        
                            </div>
                            <div class="btn-group w-100 mt-2">
                                <a href="{{ asset($md->file_url) }}" class="btn btn-sm" download>
                                    <i class="fas fa-download"></i> تحميل
                                </a>
                            </div>
                        </div>
                        
                        <div class="card-footer bg-transparent border-top">
                            <small class="text-muted">
                                <i class="far fa-clock"></i> 
                                {{ $md->created_at->format('Y-m-d H:i') }}
                            </small>
                        </div>
                    </div>
                </div>
                @endif
                @endforeach
            </div>
        </div>
        <!-- تبويب الصوتيات -->
        <div class="tab-pane fade" id="audios" role="tabpanel" aria-labelledby="videos-tab">
            <div class="d-flex flex-wrap">
                @foreach ($media as $md)
                @if ($md->type == 'صوت')
                
                @php
                    $fileExt = pathinfo($md->file_url, PATHINFO_EXTENSION);
                    $fileIcon = getFileIcon($fileExt);
                    $canPreview = in_array(strtolower($fileExt), ['pdf', 'txt', 'doc', 'docx', 'xls', 'xlsx']);
                @endphp

                <div class="col-12 col-md-6 col-lg-4 mb-4">
                    <div class="card bg-primary shadow-soft border-light">
                        <div class="card-body text-center">
                            <i class="{{ $fileIcon }} fa-4x mb-3 "></i>
                            <h6 class="card-title text-truncate custom-font">{{ basename($md->file_name) }}</h6>
                            <audio controls style="width: 100%;" oncontextmenu="return false;">
                                <source src="{{ asset($md->file_url) }}" type="audio/mpeg">
                                متصفحك لا يدعم عنصر الصوت.
                            </audio>
                            <div class="d-flex justify-content-between small text-muted mb-3">
                                <span>{{ strtoupper($fileExt) }} ملف</span>
                                <span>{{ formatFileSize($md->size) }}</span>
                            </div>
                            
                            <div class="btn-group w-100">
                                <a href="{{ asset($md->file_url) }}" class="btn btn-sm" download>
                                    <i class="fas fa-download"></i> تحميل
                                </a>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-top">
                            <small class="text-muted">
                                <i class="far fa-clock"></i> 
                                {{ $md->created_at->format('Y-m-d H:i') }}
                            </small>
                        </div>
                    </div>
                </div>
                @endif
                @endforeach
            </div>
        </div>
        <!-- تبويب الملفات -->
        <div class="tab-pane fade" id="files" role="tabpanel" aria-labelledby="files-tab">
            <div class="d-flex flex-wrap">
                @foreach ($media as $md)
                    @if($md->type == 'ملف')
                        @php
                            $fileExt = pathinfo($md->file_url, PATHINFO_EXTENSION);
                            $fileIcon = getFileIcon($fileExt);
                            $canPreview = in_array(strtolower($fileExt), ['pdf', 'txt', 'doc', 'docx', 'xls', 'xlsx']);
                        @endphp
                        
                        <div class="col-12 col-md-6 col-lg-4 mb-4">
                            <div class="card bg-primary shadow-soft border-light">
                                <div class="card-body text-center">
                                    <i class="{{ $fileIcon }} fa-4x mb-3 "></i>
                                    <h6 class="card-title text-truncate custom-font">{{ basename($md->file_name) }}</h6>
                                    
                                    <div class="d-flex justify-content-between small text-muted mb-3">
                                        <span>{{ strtoupper($fileExt) }} ملف</span>
                                        <span>{{ formatFileSize($md->size) }}</span>
                                    </div>
                                    
                                    <div class="btn-group w-100">
                                        <a href="{{ asset($md->file_url) }}" class="btn btn-sm" download>
                                            <i class="fas fa-download"></i> تحميل
                                        </a>
                                    </div>
                                </div>
                                <div class="card-footer bg-transparent border-top">
                                    <small class="text-muted">
                                        <i class="far fa-clock"></i> 
                                        {{ $md->created_at->format('Y-m-d H:i') }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
</div>


<style>
.card {
    transition: transform 0.2s;
}
.card:hover {
    transform: translateY(-5px);
}
.text-truncate {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
</style>
