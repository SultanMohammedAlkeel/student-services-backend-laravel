<script>
  
    let name = document.querySelector("#name");
    let image = document.querySelector("#image");
    let status = document.querySelector("#status");

    let my_id = <?php echo session('user_id'); ?>;
    let my_name = '<?php echo session('username'); ?>';
    
    $(document).ready(function() {
        fetchData(); 
        fetchUserInof();
        if (contact_id == 0) {
            $('#footer-chat').hide();
            $('#menu-btn').hide();
        } else {
            fetchUser(contact_id);
            $('#footer-chat').show();
            $('#menu-btn').show();
        }
    });
    
    // تحديث البيانات كل 3 ثواني
    setInterval(fetchData, 1000);
    setInterval(fetchUserInof, 1000);
    fetchUser(user_id);

    function fetchUserInof() {
        $.ajax({
            url: `/get-user-info/`,
            method: 'GET',
            success: function(response) {
                response.forEach(msg => {
                    $(`#last-msg-${msg[0]}`).html(he.encode(limitString(msg[1])));
                    if (msg[2] > 0) {
                        $(`#unread-${msg[0]}`).html(`<span class="avatar-sm img-fluid rounded-circle ml-3 bg-success text-white d-flex align-items-center justify-content-center">${msg[2]}</span>`);
                    } else {
                        $(`#unread-${msg[0]}`).html('');
                    }
                    
                });
            },
            error: function(xhr) {
                // console.log(xhr.responseText);
            }
        });
    }

    function limitString(str, limit = 25, end = '...') {
    if (typeof str !== 'string') return ''; // إذا لم يكن النص نصًا، نرجع قيمة فارغة
    if (str.length <= limit) return str; // إذا كان النص أقصر من الحد، نرجع النص كما هو
    return str.substring(0, limit) + end; // نرجع النص المقتطع مع إضافة النهاية
}

    function goToChat(id) {
        
        
        user_id = id;
        fetchUser(user_id);
         
        if (window.innerWidth > 992) {
            return;
        }

        let chat = document.querySelector(".chat-area");
        let content = document.querySelector(".contact-area");
        let header = document.querySelector(".header-global");
        let mianContainer = document.querySelector(".main-container");
        chat.style.display = "block";
        header.style.display = "none";
        content.classList.add('hide');
        mianContainer.classList.remove('mt-6');
    }

    function backFromChat() {
        let chat = document.querySelector(".chat-area");
        let header = document.querySelector(".header-global");
        let content = document.querySelector(".contact-area");
        let mianContainer = document.querySelector(".main-container");
        content.classList.remove('hide');
        chat.style.display = "none";
        header.style.display = "block";
        mianContainer.classList.add('mt-6');
    }

    function fetchUser(id) {
        fetch(`/get-user/${id}`)
        .then(response => response.json())
        .then(data => {
            
            name.innerHTML = data.user.name;
            status.innerHTML = data.user.status;
            image.src = `${data.user.image_url}`;
                
            $('#profile').attr('href', `user-profile/${ data.user.code}`);
            $('#media').attr('href', `user-profile/${ data.user.code}/media`);
            if(data.contact == null) {
                contact_id = 0;
            } else {
                contact_id = data.contact.id;
            }
            
            $('#body-chat').empty();
            $('#footer-chat').show();
            $('#menu-btn').show();

            $('#contact_id').val(data.contact.id);


            if (data.contact.friend_blocked) {
                $('#footer-chat').hide();
                if (data.contact.friend_id == my_id) {
                    $('#block-title').html('إلغاء حظر');
                } 
            } 
            if (data.contact.user_blocked) {
                $('#footer-chat').hide();
                if (data.contact.user_id == my_id) {
                    $('#block-title').html('إلغاء حظر');
                }
            }
            
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
    
    document.getElementById('fileInput').addEventListener('change', function(event) {
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

    function bubbleSender(id, message, date, read, media, size, type, file_name) {
         
        return  `
            <div class="col-md-12 col-lg-12 rtl-layout mb-4" id="chat-${id}">
                <div class="toast fade show bd-white" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="toast-header text-dark">
                        <small class="text-gray date">${formatTo12Hour(date, 'd')}</small> 
                        <strong class="mr-auto ml-2">${my_name}</strong> 
                    </div>
                    ${type == 'صوت'? '<audio class="card-img-top rounded" id="file-url-'+ id +'" controls src="'+ media +'"></audio>': ''}
                    ${type == 'فيديو'? '<video class="card-img-top rounded" id="file-url-'+ id +'" controls src="'+ media +'"></video>' : ''}
                    ${type == 'صورة'? '<img class="card-img-top rounded" id="file-url-'+ id +'" src="'+ media +'">': ''}
                    ${type == 'ملف'? bubbleFile(media, file_name, size) : ''}
                    
                    <div class="toast-body"> ${convertLinksToClickable(message)} </div>
                    <div class="toast-header text-dark d-flex justify-content-between">
                        <div>
                            ${!media ? '': '<span class="font-small font-weight-light mr-3 views-count">'+ convertBytes(size) +'</span>'}
                            ${!media ? '': '<a class="fa-solid fa-download mr-2" href="'+ media +'" data-toggle="tooltip" title="تحميل المنشور" download></a>'}
                            ${!media ? '': '<a class="fa-solid fa-folder-open mr-2" href="'+ media +'" data-toggle="tooltip" title="تحميل المنشور" target="_blank" ></a>'}
                            <span class="fa-solid fa-trash text-danger mr-2" data-toggle="modal" data-target="#modal-delete-${id}"></span>
                        </div>                   
                        <small class="text-gray time">${formatTo12Hour(date, 't') }</small>
                        <i class="${ read ?'fa-solid fa-check-double' : 'fa-solid fa-check'}" id="read-${id}"></i></i> 
                        
                    </div>
                </div>
        </div> 
        
            <div class="modal fade" id="modal-delete-${id}" tabindex="-1" role="dialog" aria-labelledby="modal-delete-${id}" aria-hidden="true" style="display: none;">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content bg-primary">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                            <p class="modal-title custom-font" id="modal-title-delete">حذف الرسالة</p>
                        </div>
                        <div class="modal-body">
                            <div class="py-3 text-center">
                                <span class="modal-icon display-1-lg"><span class="fa-solid fa-trash"></span></span>
                                <h2 class="h4 my-3 custom-font">حذف الرسالة المحدد</h2>
                                <p class="custom-font">هل أنت متأكد من حذف الرسالة؟</p>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-sm btn-primary" onclick="deleteChat(${id})">حذف</button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    
    function bubbleReciver(id, user, message, date, image, media, size, type, file_name) { 
        return `
        <div class="col-md-12 col-lg-12 ltr-layout mb-4 chat-box" id="chat-${id}" data-chat-id="${id}">
            <div class="toast fade show" style="background-color: #c8d0e7;border-radius: 20px 5px 5px 5px;" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header text-dark">
                <img class="avatar-lg img-fluid rounded-circle" src="${image}" alt="avatar">
                <strong class="mr-auto ml-2">${user}</strong> 
                <small class="text-gray date">${formatTo12Hour(date, 'd')}</small> 
                </div>
                ${type == 'صوت'? '<audio class="card-img-top rounded" id="file-url-'+ id +'" controls src="'+ media +'"></audio>': ''}
                ${type == 'فيديو'? '<video class="card-img-top rounded" id="file-url-'+ id +'" controls src="'+ media +'"></video>' : ''}
                ${type == 'صورة'? '<img class="card-img-top rounded" id="file-url-'+ id +'" src="'+ media +'">': ''}
                ${type == 'ملف'? bubbleFile(media, file_name, size) : ''}
                <div class="toast-body  text-dark text-right"> ${convertLinksToClickable(message)} </div>
                <div class="toast-header text-dark">
                    <div>
                        ${!media ? '': '<span class="font-small font-weight-light mr-3 views-count">'+ convertBytes(size) +'</span>'}
                        ${!media ? '': '<a class="fa-solid fa-download mr-2" href="'+ media +'" data-toggle="tooltip" title="تحميل المنشور" download></a>'}
                        ${!media ? '': '<a class="fa-solid fa-folder-open mr-2" href="'+ media +'" data-toggle="tooltip" title="تحميل المنشور" target="_blank" ></a>'}
                    </div>
                    <small class="text-gray time">${formatTo12Hour(date, 't')}</small> 
                </div>
            </div>
        </div>`;
     }

    function bubbleFile(media, file_name, size) {
        const fileExt = media.split('.').pop().toLowerCase();
        const fileSizeFormatted = convertBytes(size); // Assuming you have this function in JS

        return`
        <div class="card-body text-center">
            <i class="${getFileIcon(fileExt)} fa-4x mb-3"></i>
            <h6 class="card-title text-truncate custom-font">${file_name}</h6>
            
            <div class="d-flex justify-content-between small text-muted mb-3">
                <span class="font-weight-bolder">${fileExt.toUpperCase()} ملف</span>
                <span class="font-weight-bolder">${fileSizeFormatted}</span>
            </div>
        </div>
        `;
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

     function fetchData() {
        $.ajax({
            url: `/get-chat/${contact_id}`,
            method: 'GET',
            success: function(response) {
                let html = '';
                
                response.forEach(chat => {
                    // التحقق من وجود المنشور بالفعل قبل إضافته
                    if (!$(`#chat-${chat.id}`).length) {
                        if (chat.sender_id == my_id) {
                            html += bubbleSender(chat.id, chat.message, chat.created_at, chat.is_read, chat.file_url, chat.size, chat.type, chat.file_name);
                        } else {
                            html += bubbleReciver(chat.id, name.innerHTML, chat.message, chat.created_at, image.src, chat.file_url, chat.size, chat.type, chat.file_name);
                        }
                    }
                    $(`#read-${chat.id}`).removeClass('fa-solid fa-check').addClass(chat.is_read ? 'fa-solid fa-check-double' : 'fa-solid fa-check');
                });
                // إضافة البيانات الجديدة إلى العناصر الموجودة
                $('#body-chat').append(html);
            },
            error: function(xhr) {
                console.error(xhr.responseText);
            }
        });
    }

    function formatTo12Hour(_date, type) {
        const date = new Date(_date);
        const hours = date.getHours();
        const minutes = String(date.getMinutes()).padStart(2, '0');
        const ampm = hours >= 12 ? 'PM' : 'AM';
        const formattedHours = hours % 12 || 12;

        if (type === 'd') {
            return `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')}`;
        }
        return `${formattedHours}:${minutes} ${ampm}`;
    }


    function deleteChat(id) {
        $.ajax({
            url: `/delete-chat/${id}`, // إرسال الـ id كجزء من المسار
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    $(`#modal-delete-${id}`).trigger('click');
                    $(`#chat-${id}`).remove();
                }
            },
            error: function(xhr) {
                console.log(xhr.responseText); // طباعة تفاصيل الخطأ في الـ Console
                alert('An error occurred. Please try again.');
            }
        });
    }

    function sendMessage() {
        let message = document.getElementById('message').value;
        if (!message) {
            document.getElementById('message').focus();
            return; 
        }

        $.ajax({
            url: "{{ route('chat.store') }}", 
            method: 'POST', 
            data: {
                message: message,
                contact_id: contact_id,
                receiver_id: user_id,
                has_media: 0, 
                _token: '{{ csrf_token() }}' 
            },
            success: function(response) {
                if (response.success) {
                    $('#message').val(''); // مسح حقل الإدخال بعد الإرسال
                    scrollToBottom();
                } else {
                    console.error('فشل إرسال الرسالة:', response);
                }
            },
            error: function(xhr) {
                console.error('حدث خطأ أثناء إرسال الرسالة:', xhr.responseText);
            }
        });
    }

    function sendMessageWithMedia() {
        let message = document.getElementById('content').value;
        let fileInput = document.getElementById('fileInput'); // افترض أن لديك عنصر إدخال للملف
        let file = fileInput.files[0]; // الحصول على الملف المحدد

        if (!file) {
            console.error('لم يتم تحديد ملف.');
            return;
        }

        // إنشاء كائن FormData
        let formData = new FormData();
        formData.append('message', message);
        formData.append('contact_id', contact_id);
        formData.append('receiver_id', user_id);
        formData.append('has_media', 1);
        formData.append('file', file); // إضافة الملف
        formData.append('_token', '{{ csrf_token() }}'); // إضافة رمز الحماية

        // إرسال البيانات باستخدام AJAX
        $.ajax({
            url: "{{ route('chat.store') }}", 
            method: 'POST', 
            data: formData,
            processData: false, // مهم: لا تقم بمعالجة البيانات
            contentType: false, // مهم: لا تقم بتعيين نوع المحتوى
            success: function(response) {
                if (response.success) {
                    $('#content').val(''); // مسح حقل الإدخال بعد الإرسال
                    $('#fileInput').val(''); // مسح حقل الإدخال بعد الإرسال
                    $('#previewImage').attr('src', ''); // مسح حقل الإدخال بعد الإرسال
                    $('#close-modal').trigger('click');
                }
            },
            error: function(xhr) {
                console.error('حدث خطأ أثناء إرسال الرسالة:', xhr.responseText);
            }
        });
    }

    function convertLinksToClickable(text) {
    // تعريف regex لاكتشاف الروابط
        const urlRegex = /(\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/ig;

        // استبدال الروابط بـ <a> tags
        
        // الحفاظ على المسافات والسطور الجديدة
        const convertedText = text
        .replace(/</g, '&lt;') // منع تحويل < إلى HTML tag
        .replace(/>/g, '&gt;') // منع تحويل > إلى HTML tag
        .replace(/\n/g, '<br>'); // تحويل السطور الجديدة إلى <br>
        
        const preservedText = convertedText.replace(urlRegex, function(url) {
            return `<a class="text-behance" href="${url}" target="_blank">${url}</a>`;
        });
        return preservedText;
    }

    function scrollToElement() {
        const element = document.getElementById('body-chat'); // الحصول على العنصر باستخدام الـ ID
        element.scrollIntoView({
            behavior: 'smooth', // التمرير السلس
            block: 'start'      // محاذاة العنصر مع أعلى الصفحة
        });
        
    }

    function scrollToBottom() {
        const div = document.getElementById('body-chat'); // الحصول على العنصر باستخدام الـ ID
        if (div) {
            div.scrollTo({
                top: div.scrollHeight, // الانتقال إلى أسفل العنصر
                behavior: 'smooth' // لجعل الحركة سلسة
            });
        } else {
            console.error("العنصر غير موجود!");
        }
    }
    
    $(document).ready(function() {

        let recordedChats = []; 
        $('#body-chat').scroll(function() {
            $('.chat-box').each(function() {
                const chatId = $(this).data('chat-id');
                
                const chatElement = $(this);
                // التحقق مما إذا كان المنشور مرئيًا على الشاشة
                if (isElementInViewport(chatElement) && !recordedChats.includes(chatId)) {
                    recordedChats.push(chatId);
                    
                    // إرسال طلب AJAX لتسجيل التفاعل
                    $.ajax({
                        url: '/chat/read-chat',
                        method: 'POST',
                        data: {
                            chat_id: chatId,
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


