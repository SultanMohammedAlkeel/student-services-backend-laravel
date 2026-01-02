<div class="col-md-12 col-lg-12 rtl-layout mb-4" id="chat-${id}">
    <div class="toast fade show bd-white" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header text-dark">
            <small class="text-gray date">${formatTo12Hour(date, 'd')}</small>
            <strong class="mr-auto ml-2">${my_name}</strong>
        </div>
        ${type == 'صوت'? '<audio class="card-img-top rounded" id="file-url-'+ id +'" controls src="'+ media +'"></audio>': ''}
        ${type == 'فيديو'? '<video class="card-img-top rounded" id="file-url-'+ id +'" controls src="'+ media +'"></video>' : ''}
        ${type == 'صورة'? '<img class="card-img-top rounded" id="file-url-'+ id +'" src="'+ media +'">': ''}
        ${type == 'ملف'? '<embed class="card-img-top rounded" id="file-url-'+ id +'" src="'+ media +'">': ''}
        <div class="toast-body"> ${he.encode(message)} </div>
        <div class="toast-header text-dark d-flex justify-content-between">
            <div>
                ${!media ? '': '<span class="font-small font-weight-light mr-3 views-count">'+ convertBytes(size) +'</span>'}
                ${!media ? '': '<span class="fa-solid fa-download mr-2" data-toggle="tooltip" title="تحميل المنشور" data-original-title="Download" onclick="downloadFile('+id+')"></span> '}
                ${!media ? '': '<span class="fa-solid fa-folder-open mr-2" data-toggle="tooltip" title="فتح الوسائط في المتصفح" data-original-title="Open" onclick="openFileInNewTab('+id+')"></span> '}
                <span class="fa-solid fa-trash text-danger mr-2" data-toggle="modal" data-target="#modal-delete-${id}"></span>
            </div>
            <small class="text-gray time">${formatTo12Hour(date, 't') }</small>
            ${ read ?'<i class="fa-solid fa-check-double"></i>' : '<i class="fa-solid fa-check"></i>' }
        </div>
    </div>
</div>