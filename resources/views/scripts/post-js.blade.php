<?php 
    // session_start();
    echo '<script>'. 'let userId = ' . session('user_id') . '</script>';
?>
<script>
    // جلب البيانات
    $(document).ready(function() {
        fetchData(); 
        scrollToBottom();
        fetchInteraction();
    });

    // تحديث البيانات كل 5 ثواني
    setInterval(fetchData, 5000);
    setInterval(fetchComments, 5000);
    setInterval(fetchInteraction, 1000);

    // عرض الملف عند تحميله
    document.getElementById('imageInput').addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const previewImage = document.getElementById('previewImage');
                previewImage.src = e.target.result;
                previewImage.style.display = 'block';
            }
            reader.readAsDataURL(file);
        }
    });
    // دالة لجلب المنشورات
    function fetchData() {
        $.ajax({
            url: '/get-posts',
            method: 'GET',
            success: function(response) {
                let html = '';
                response.forEach(post => {
                    // التحقق من وجود المنشور بالفعل قبل إضافته
                    if (!$(`#post-${post.id}`).length) {
                        html += postTemplate(post);
                    }
                });
                
                // إضافة البيانات الجديدة إلى العناصر الموجودة
                $('#posts-container').append(html);
            },
            error: function(xhr) {
                console.log(xhr.responseText);
            }
        });
    }
    // دالة لجلب التعليقات
    function fetchComments() {
        $.ajax({
            url: '/get-comments',
            method: 'GET',
            success: function(response) {
                // إنشاء كائن لتجميع التعليقات حسب post_id
                const commentsByPost = {};

                // تجميع التعليقات حسب post_id
                response.forEach(comment => {
                    if (!commentsByPost[comment.post_id]) {
                        commentsByPost[comment.post_id] = [];
                    }
                    commentsByPost[comment.post_id].push(comment);
                });

                // إضافة التعليقات إلى الحاويات الخاصة بكل منشور
                for (const postId in commentsByPost) {
                    let html = '';
                    commentsByPost[postId].forEach(comment => {
                        html += commentTemplate(comment); // إنشاء HTML لكل تعليق
                    });

                    // إضافة التعليقات إلى الحاوية الخاصة بالمنشور
                    $(`#comment-container-${postId}`).html(html);
                }
            },
            error: function(xhr) {
                console.log(xhr.responseText);
            }
        });
    }
    // دالة لتحويل الساعة من 24 إلى 12 ساعة
    function formatTo12Hour(date, type) {
        const hours = date.getHours();
        const minutes = date.getMinutes();
        const seconds = date.getSeconds();
        const ampm = hours >= 12 ? 'PM' : 'AM';

        // تحويل الساعة إلى تنسيق 12 ساعة
        const formattedHours = hours % 12 || 12;

        // إضافة صفر أمام الدقائق والثواني إذا كانت أقل من 10
        const formattedMinutes = minutes < 10 ? `0${minutes}` : minutes;
        const formattedSeconds = seconds < 10 ? `0${seconds}` : seconds;

        if (type == 'd') {
            return `${date.getFullYear()}-${(date.getMonth() + 1).toString().padStart(2, '0')}-${date.getDate().toString().padStart(2, '0')} `;
        }
        // إرجاع التاريخ المنسق
        return `${formattedHours}:${formattedMinutes} ${ampm} `;
    }
    // دالة لنقل المستخدم لاحقا
    function scrollToBottom() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    }
    // دالة لجلب HTML لكل تعليق
    function commentTemplate(comment) {
        let date = new Date(comment.created_at);
        return `
        <div class="card bg-primary shadow-soft border-light rounded p-4 mb-4 comment" id="comment-${comment.id}" data-comment-id="${comment.id}">
            <div class="d-flex justify-content-between mb-4">
                    <span class="font-small">
                        <a href="#">
                            <img class="avatar-sm img-fluid rounded-circle mr-2" src="${comment.user_image}" alt="avatar">
                            <span class="font-weight-bold">${comment.user_name}</span> 
                        </a>
                    </span>
                    <div>
                        <span class="ml-2"><span class="far fa-calendar-alt mr-2"></span> ${formatTo12Hour(date, 'd')}</span>
                    </div>
                </div>
                <br>
                <p class="m-0 custom-font preserve-formatting rtl-layout"> ${comment.content} </p>
                <span class="ml-2 custom-font"><span class="fa-solid fa-clock mr-2"></span> ${formatTo12Hour(date, 't')}</span>
        </div>
        `;
    }
    // دالة لجلب HTML لكل منشور
    function postTemplate(post) {
        let date = new Date(post.created_at);
        return `
            <div class="card bg-primary shadow-soft border-light rounded p-4 mb-4 post" id="post-${post.id}" data-post-id="${post.id}">
                <div class="d-flex justify-content-between mb-4">
                    <span class="font-small">
                        <a href="{{ route('user.user-posts', '') }}/${post.code}">
                            <img class="avatar-sm img-fluid rounded-circle mr-2" src="${post.user_image}" alt="avatar">
                            <span class="font-weight-bold">${post.user_name}</span> 
                        </a>
                    </span>
                    <div>
                        <span class="ml-2"><span class="far fa-calendar-alt mr-2"></span> ${formatTo12Hour(date, 'd')}</span>
                    </div>
                </div>
                
                ${post.file_type == 'صوت'? '<audio class="card-img-top rounded" id="file-url-'+ post.id +'" controls src="'+ post.file_url +'"></audio>': ''}
                ${post.file_type == 'فيديو'? '<video class="card-img-top rounded" id="file-url-'+ post.id +'" controls src="'+ post.file_url +'"></video>' : ''}
                ${post.file_type == 'صورة'? '<img class="card-img-top rounded" id="file-url-'+ post.id +'" src="'+ post.file_url +'">': ''}
                ${post.file_type == 'ملف'? bubbleFile(post.file_url, post.file_name, post.file_size): ''}
                
                <br>
                <p class="m-0 custom-font preserve-formatting rtl-layout"> ${post.content} </p>
                <div class="mt-4 mb-3 d-flex justify-content-between">
                    <div>
                        ${post.file_url == null? '': '<span class="font-small font-weight-light mr-3 views-count">'+ convertBytes(post.file_size) +'</span>'}
                        <span class="fa-solid fa-eye mr-1 text-success" data-toggle="tooltip" data-placement="top" title="عدد المشاهدات" data-original-title="Views"></span>
                        <span class="font-small font-weight-light mr-3 views-count" id="views-count-${post.id}">${post.views_count}</span>
                        <span class="far fa-thumbs-up text-action mr-1" id="likes-icon-${post.id}" data-toggle="tooltip" title="اعجاب ب المنشور" data-original-title="Like" onclick="likePost(${post.id})"></span> 
                        <span class="font-small font-weight-light mr-3" id="likes-count-${post.id}">${post.likes_count}</span>
                        <span class="fa-solid fa-bookmark text-action mr-2" id="saved-icon-${post.id}" data-toggle="tooltip" title="حفظ المنشور" data-original-title="Save" onclick="savePost(${post.id})"></span> 
                        
                        ${post.file_url == null ? '': '<a class="fa-solid fa-download mr-2" href="'+ post.file_url +'" data-toggle="tooltip" title="تحميل المنشور" download></a>'}
                        ${post.file_url == null ? '': '<a class="fa-solid fa-folder-open mr-2" href="'+ post.file_url +'" data-toggle="tooltip" title="تحميل المنشور" target="_blank" ></a>'}
                        ${post.sender_id == userId ? '<span class="fa-solid fa-trash text-danger mr-2" data-toggle="modal" data-target="#modal-delete-'+post.id+'"></span> ' : ''}
                    </div>
                        <div class="modal fade" id="modal-delete-${post.id}" tabindex="-1" role="dialog" aria-labelledby="modal-delete-${post.id}" aria-hidden="true" style="display: none;">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content bg-primary">
                                    <div class="modal-header">
                                        <p class="modal-title custom-font" id="modal-title-delete">حذف المنشور</p>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">×</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="py-3 text-center">
                                            <span class="modal-icon display-1-lg"><span class="fa-solid fa-trash"></span></span>
                                            <h2 class="h4 my-3 custom-font">حذف المنشور المحدد</h2>
                                            <p class="custom-font">هل أنت متأكد من حذف المنشور؟</p>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <form method="post" action="/delete-post">
                                            @csrf
                                            <input type="hidden" name="post_id" value="${post.id}">
                                            <button type="submit" class="btn btn-sm btn-primary">حذف</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <a class="text-action font-weight-light font-small" data-toggle="collapse" role="button" href="#replyContainer-${post.id}" aria-expanded="false" aria-controls="replyContainer1">
                        <span class="fas fa-reply mr-2"></span> تعليق
                        <span class="ml-2 mr-2 comments-count-${post.id}" id="comments-count-${post.id}">${post.comments_count}</span>
                    </a>
                </div>
                <span class="ml-2 custom-font"><span class="fa-solid fa-clock mr-2"></span> ${formatTo12Hour(date, 't')}</span>
                <div class="collapse" id="replyContainer-${post.id}">
                    <label class="mb-4" for="comment-input-${post.id}">التعليقات</label>
                    <!-- Comment Here -->
                    <div class="alert alert-success shadow-inset d-flex justify-content-center flex-column" id="comment-container-${post.id}">
                        <button class="btn btn-primary" type="button" disabled="">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            <span class="ml-1">Loading...</span>
                        </button>  
                    </div>
                    <textarea class="form-control border rtl-layout" id="comment-input-${post.id}" placeholder="اكتب تعليق ..." rows="6" data-bind-characters-target="#charactersRemainingReply" maxlength="1000"></textarea>
                    <div class="d-flex justify-content-between mt-3">
                        <small class="font-weight-light">
                            <span id="charactersRemainingReply"></span>
                            characters remaining
                        </small> 
                        <button class="btn btn-primary btn-sm animate-up-2" onclick="commentPost(${post.id}, '${post.user_name}', '${post.user_image}')">Send</button>
                    </div>
                </div>
            </div>
        `;
    }

    
    function bubbleFile(file_url, file_name, size) {
        const fileExt = file_url.split('.').pop().toLowerCase();
        const fileSizeFormatted = convertBytes(size); // Assuming you have this function in JS

        return`
        <div class="card-body text-center">
            <i class="${getFileIcon(fileExt)} fa-6x mb-3"></i>
            <h6 class="card-title text-truncate custom-font">${file_name}</h6>
            
            <div class="d-flex justify-content-between small text-muted mb-3">
                <span class="font-weight-bolder">${fileExt.toUpperCase()} ملف</span>
                <span class="font-weight-bolder">${fileSizeFormatted}</span>
            </div>
        </div>
        `;
    }

    function fetchInteraction() {
        $.ajax({
            url: `/get-interaction/`,
            method: 'GET',
            success: function(response) {
                response.post.forEach(post => {
                    
                    $(`#views-count-${post.id}`).html(post.views_count);
                    $(`#likes-count-${post.id}`).html(post.likes_count);
                    $(`#comments-count-${post.id}`).html(post.comments_count);
                });
                response.interaction.forEach(post => {

                    if (post.like == 1) {
                        $(`#likes-icon-${post.post_id}`).addClass('text-behance');
                    } else {
                        $(`#likes-icon-${post.post_id}`).removeClass('text-behance');
                    }
                    if (post.save == 1) {
                        $(`#saved-icon-${post.post_id}`).addClass('text-behance');
                    } else {
                        $(`#saved-icon-${post.post_id}`).removeClass('text-behance');
                    }
                });
            },
            error: function(xhr) {
                console.log(xhr.responseText);
            }
        });
    }
    
    function convertBytes(bytes) {
        // التحقق من أن القيمة رقم وليست undefined أو null
        if (typeof bytes !== 'number' || isNaN(bytes)) {
            return 'Invalid input'; // أو يمكنك إرجاع قيمة افتراضية مثل '0 B'
        }

        const units = ['B', 'KB', 'MB', 'GB', 'TB'];
        let unitIndex = 0;

        while (bytes >= 1024 && unitIndex < units.length - 1) {
            bytes /= 1024;
            unitIndex++;
        }

        return `${bytes.toFixed(2)} ${units[unitIndex]}`;
    }

    // حفظ المنشور
    function savePost(postId) {
         $.ajax({
            url: '/post/save-interaction',
            method: 'POST',
            data: {
                post_id: postId,
                _token: '{{ csrf_token() }}' // CSRF token
            },
            success: function(response) {
                if (response.success) {
                }
            },
            error: function(xhr) {
                console.error('Error recording interaction:', xhr.responseText);
            }
        });
    }
    // اعجاب ب المنشور
    function likePost(postId) {
         $.ajax({
            url: '/post/like-interaction',
            method: 'POST',
            data: {
                post_id: postId,
                _token: '{{ csrf_token() }}' // CSRF token
            },
            success: function(response) {
                if (response.success) {
                }
            },
            error: function(xhr) {
                console.error('Error recording interaction:', xhr.responseText);
            }
        });
    }
    // تعليق
    function commentPost(postId, user_name, user_image) {
        let comment = document.querySelector(`#comment-input-${postId}`);
        if (comment.value.trim() == '') {
            return;
        }
         $.ajax({
            url: '/post/comment-interaction',
            method: 'POST',
            data: {
                post_id: postId,
                comment: comment.value,
                _token: '{{ csrf_token() }}' // CSRF token
            },
            success: function(response) {
                if (response.success) {
                    comment.value = '';
                }
            },
            error: function(xhr) {
                console.error('Error recording interaction:', xhr.responseText);
            }
        });

    }
    // تسجيل التفاعل
    $(document).ready(function() {
        let recordedPosts = []; 
        $(window).scroll(function() {
            $('.post').each(function() {
                const postId = $(this).data('post-id');
                const postElement = $(this);

                // التحقق مما إذا كان المنشور مرئيًا على الشاشة
                if (isElementInViewport(postElement) && !recordedPosts.includes(postId)) {
                    recordedPosts.push(postId);
                    

                    // إرسال طلب AJAX لتسجيل التفاعل
                    $.ajax({
                        url: '/post/record-interaction',
                        method: 'POST',
                        data: {
                            post_id: postId,
                            _token: '{{ csrf_token() }}' // CSRF token
                        },
                        success: function(response) {
                            if (response.success) {
                                
                            }
                        },
                        error: function(xhr) {
                            console.error('Error recording interaction:', xhr.responseText);
                        }
                    });
                }
            });
        });

        function isElementInViewport(el) {
            const rect = el[0].getBoundingClientRect();

            return (
                rect.top < (window.innerHeight || document.documentElement.clientHeight) &&
                rect.bottom > 0 &&
                rect.left < (window.innerWidth || document.documentElement.clientWidth) &&
                rect.right > 0
            );
        }
    });


    
    function getFileIcon(ext) {
       const icons = {
            'pdf': 'far fa-file-pdf text-danger',
            'doc': 'far fa-file-word text-primary',
            'docx': 'far fa-file-word text-primary',
            'xls': 'far fa-file-excel text-success',
            'xlsx': 'far fa-file-excel text-success',
            'txt': 'far fa-file-alt text-secondary',
            'zip': 'far fa-file-archive text-warning',
            'rar': 'far fa-file-archive text-warning',
            'php': 'fab fa-php text-info',
            'js': 'fab fa-js text-warning',
            'html': 'fab fa-html5 text-danger',
            'css': 'fab fa-css3 text-primary',
            'json': 'fas fa-code text-dark',
            'sql': 'fas fa-database text-info',
            'csv': 'fas fa-file-csv text-success'
        };
        
        return icons[ext.toLowerCase()] || 'far fa-file';
    }
</script>