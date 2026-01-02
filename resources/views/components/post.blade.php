<div class="card bg-primary shadow-soft border-light rounded p-4 mb-4">
    <div class="d-flex justify-content-between mb-4">
            <span class="font-small">
            <a href="#">
                <img class="avatar-sm img-fluid rounded-circle mr-2" src="{{ User::where('id', $posts->sender_id)->first()->image_url }}" alt="avatar">
                <span class="font-weight-bold">{{ User::where('id', $posts->sender_id)->first()->name }}</span> 
            </a>
            <span class="ml-2">{{ $posts->created_at }}</span>
        </span>
        <div>
            <button class="btn btn-primary text-danger" aria-label="report button" data-toggle="tooltip" data-placement="top" title="Report comment" data-original-title="Report comment">
                <span class="far fa-flag"></span>
            </button>
        </div>
    </div>
    <p class="m-0"> {{ $posts->content }} </p>
    <div class="mt-4 mb-3 d-flex justify-content-between">
        <div>
            <span class="far fa-thumbs-up text-action text-success mr-3" data-toggle="tooltip" data-placement="top" title="Like comment" data-original-title="Like comment"></span> 
            <span class="font-small font-weight-light">4 likes</span>
        </div>
        <a class="text-action font-weight-light font-small" data-toggle="collapse" role="button" href="#replyContainer1" aria-expanded="false" aria-controls="replyContainer1">
            <span class="fas fa-reply mr-2"></span>Reply
        </a>
    </div>
    <div class="collapse" id="replyContainer1">
        <label class="mb-4" for="exampleFormControlTextarea10">Replay</label>
        <textarea class="form-control border" id="exampleFormControlTextarea10" placeholder="Reply to John Doe" rows="6" data-bind-characters-target="#charactersRemainingReply" maxlength="1000"></textarea>
        <div class="d-flex justify-content-between mt-3">
            <small class="font-weight-light">
                <span id="charactersRemainingReply"><!-- this will be filled with 200 from the textarea's maxlength --></span>
                characters remaining
            </small> 
            <button class="btn btn-primary btn-sm animate-up-2">Send</button>
        </div>
    </div>
</div>

