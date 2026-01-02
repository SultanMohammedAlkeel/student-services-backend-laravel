
<div class="toast fade show notification" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="toast-header text-dark">
        <span class="fa-solid fa-circle-info"></span>
        <strong class="mr-auto ml-2">اشعار</strong>
        <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
            <span aria-hidden="true">×</span>
        </button>
    </div>
    <div class="toast-body rtl-layout text-danger">
        {{ $notification->first()->content }}
        <br>
        <br>
        <a class="text-action font-weight-light font-small collapsed text-info" data-toggle="collapse" role="button" href="#replyContainer2" aria-expanded="false" aria-controls="replyContainer2"><span class="fas fa-reply mr-2"></span> الرد</a>
        <div class="collapse" id="replyContainer2">            
            <form action="{{ route('notification.responses') }}" method="post">
                @csrf
                <div class="d-flex justify-content-between m-3">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="confirmation" id="ok" value="1" checked>
                        <label class="form-check-label" for="ok">تاكيد الحضور</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="confirmation" id="no" value="0">
                        <label class="form-check-label" for="no">الاعتذار عن الحضور</label>
                    </div>
                </div>
                <input type="hidden" name="notification_id" value="{{ $notification->first()->id }}">
                <input type="hidden" name="hall_id" value="{{ $notification->first()->hall_id }}">
                <input type="hidden" name="course_id" value="{{ $notification->first()->course_id }}">
                <input type="hidden" name="teacher_id" value="{{ $notification->first()->teacher_id }}">
                <input type="hidden" name="period" value="{{ $notification->first()->period }}">
                <input type="hidden" name="schedule_id" value="{{ $notification->first()->schedule_id }}">
                <textarea class="form-control border" id="exampleFormControlTextarea11" placeholder="اكتب العذر في حالة الاعتذار..." rows="3" data-bind-characters-target="#charactersRemainingReply2" maxlength="1000" name="content"></textarea>
                <div class="d-flex justify-content-between mt-3">
                    <button class="btn btn-primary btn-sm animate-up-2" type="submit">ارسال</button>
                </div>
            </form>
        </div>
    </div>

</div>
