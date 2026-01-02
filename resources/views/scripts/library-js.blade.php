<script>  
    $(document).ready(function() {
        ConvertSize();
        fetchBookInof();
    });

    setInterval(fetchBookInof, 1000);
    function ConvertSize() {
        let size = document.querySelectorAll('#library-container .size');
        size.forEach(s => {
            s.innerHTML = convertBytes(+s.innerHTML);
        })
    }

    async function downloadFile(src, name)  {
        let fileUrl = src;
        let filename = name;

        
        try {
            // جلب الصورة من الرابط
            const response = await fetch(fileUrl);
            if (!response.ok) {
                throw new Error('فشل في جلب الصورة');
            }

            // تحويل الصورة إلى Blob
            const blob = await response.blob();

            // إنشاء رابط تنزيل
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = filename; // اسم الملف الذي سيتم تنزيله
            document.body.appendChild(a);
            a.click(); // تشغيل النقر على الرابط

            // تنظيف الذاكرة
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);
        } catch (error) {
            console.error('حدث خطأ أثناء تنزيل الصورة:', error);
        }
    }
    
    async function openFileInNewTab(src) {
        let fileUrl = src;

        try {
            // جلب الصورة من الرابط
            const response = await fetch(fileUrl);
            if (!response.ok) {
                throw new Error('فشل في جلب الصورة');
            }

            // تحويل الصورة إلى Blob
            const blob = await response.blob();

            // إنشاء رابط للصورة
            const url = window.URL.createObjectURL(blob);

            // فتح الرابط في نافذة جديدة
            window.open(url, '_blank');

            // تنظيف الذاكرة بعد فتح الرابط
            window.URL.revokeObjectURL(url);
        } catch (error) {
            console.error('حدث خطأ أثناء فتح الصورة:', error);
        }
    }

    function saveBook(bookId) {
        $.ajax({
            url: '/library/save-book',
            method: 'POST',
            data: {
                book_id: bookId,
                _token: '{{ csrf_token() }}' // CSRF token
            },
            success: function(response) {
                if (response.success) {
                    console.log(response.message);
                }
            },
            error: function(xhr) {
                console.error('Error recording interaction:', xhr.responseText);
            }
        });
    }
    
    function downloadBook(bookId, src, name) {
        $.ajax({
            url: '/library/download-book',
            method: 'POST',
            data: {
                book_id: bookId,
                _token: '{{ csrf_token() }}' // CSRF token
            },
            success: function(response) {
                if (response.success) {
                    downloadFile(src, name);
                }
            },
            error: function(xhr) {
                console.error('Error recording interaction:', xhr.responseText);
            }
        });
    }
        
    function OpenBook(bookId, src) {
        $.ajax({
            url: '/library/open-book',
            method: 'POST',
            data: {
                book_id: bookId,
                _token: '{{ csrf_token() }}' // CSRF token
            },
            success: function(response) {
                if (response.success) {
                    openFileInNewTab(src);
                }
            },
            error: function(xhr) {
                console.error('Error recording interaction:', xhr.responseText);
            }
        });
    }
    
    function likeBook(bookId) {
        $.ajax({
            url: '/library/like-book',
            method: 'POST',
            data: {
                book_id: bookId,
                _token: '{{ csrf_token() }}' // CSRF token
            },
            success: function(response) {
                if (response.success) {
                    console.log(response.message);
                }
            },
            error: function(xhr) {
                console.error('Error recording interaction:', xhr.responseText);
            }
        });
    }
    
    function fetchBookInof() {
        $.ajax({
            url: `/get-book-info/`,
            method: 'GET',
            success: function(response) {
                response.book.forEach(book => {                    
                    $(`#like-book-${book.id}`).html(book.likes_count);
                    $(`#download-book-${book.id}`).html(book.download_count);
                    $(`#open-book-${book.id}`).html(book.opens_count);
                    $(`#open-info-book-${book.id}`).html(book.opens_count);
                    $(`#save-book-${book.id}`).html(book.save_count);
                });
                response.myBookInfo.forEach(book => {
                    if (book.likes == 1) {
                        $(`#like-icon-${book.book_id}`).addClass('text-behance');
                    } else {
                        $(`#like-icon-${book.book_id}`).removeClass('text-behance');
                    }
                    if (book.save == 1) {
                        $(`#save-icon-${book.book_id}`).addClass('text-behance');
                    } else {
                        $(`#save-icon-${book.book_id}`).removeClass('text-behance');
                    }
                    if (book.opens_count > 0) {
                        $(`#open-icon-${book.book_id}`).addClass('text-behance');
                    } else {
                        $(`#open-icon-${book.book_id}`).removeClass('text-behance');
                    }
                    if (book.downloads > 0) {
                        $(`#download-icon-${book.book_id}`).addClass('text-behance');
                    } else {
                        $(`#download-icon-${book.book_id}`).removeClass('text-behance');
                    }
                });
            },
            error: function(xhr) {
                console.log(xhr.responseText);
            }
        });
    }

    function scrollToTop() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    }
</script>