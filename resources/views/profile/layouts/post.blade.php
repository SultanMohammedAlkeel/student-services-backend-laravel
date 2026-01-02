<hr>
<div class="col text-center">
    <h2 class="h4 mb-3 custom-font">المنشورات</h2>
</div>
@foreach ($posts as $post)
<div class="col-12 col-md-6 col-lg-6 mb-5 mb-lg-0 m-auto">
    <div class="card bg-primary shadow-soft border-light rounded p-4 mb-4 post" id="post-{{ $post->id }}" data-post-id="{{ $post->id }}">
        <div class="d-flex justify-content-between mb-4">
            <span class="font-small">
                <a href="{{ route('user.user-profile', $post->sender_id) }}">
                    <img class="avatar-sm img-fluid rounded-circle mr-2" src="{{ asset($user->image_url) }}" alt="avatar">
                    <span class="font-weight-bold">{{ $post->user_name }}</span>
                </a>
            </span>
            <div>
                <span class="ml-2"><span class="far fa-calendar-alt mr-2"></span> {{ ($post->created_at) }}</span>
            </div>
        </div>

        @if($post->file_type == 'صوت')
        <audio class="card-img-top rounded" id="file-url-{{ $post->id }}" controls src="{{ asset($post->file_url) }}"></audio>
        @elseif($post->file_type == 'فيديو')
        <video class="card-img-top rounded" id="file-url-{{ $post->id }}" controls src="{{ asset($post->file_url) }}"></video>
        @elseif($post->file_type == 'صورة')
        <img class="card-img-top rounded" id="file-url-{{ $post->id }}" src="{{ asset($post->file_url) }}">
        @elseif($post->file_type == 'ملف')
        <embed class="card-img-top rounded" id="file-url-{{ $post->id }}" src="{{ asset($post->file_url) }}">
        @endif

        <br>
        <p class="m-0 custom-font preserve-formatting rtl-layout">{{ $post->content }}</p>

        <span class="ml-2 custom-font"><span class="fa-solid fa-clock mr-2"></span> {{ ($post->created_at) }}</span>
    </div>
</div>
@endforeach