<div class="d-flex align-items-center ml-3" id="dropdownMenuButton" data-toggle="dropdown">
    <button
        class="btn btn-xs btn-circle btn-icon-only btn-soft dropdown-toggle mr-2" type="button"
        aria-haspopup="true" aria-expanded="false" aria-label="dropdown social link">
        <span class="fa-solid fa-ellipsis-vertical"></span>
    </button>
</div>
<div class="dropdown-menu dropdown-menu-md rtl-layout" aria-labelledby="dropdownMenuButton">
    <a class="dropdown-item gap-btwn" id="profile">الملف الشخصي <span class="fa-solid fa-id-card"></span></a>
    <a class="dropdown-item gap-btwn" id="media">الوسائط <span class="fa-solid fa-paperclip"></span></a>
    
    <form method="post" action="{{ route('contact.block') }}">
        @csrf
        <input type="hidden" name="contact_id" id="contact_id">
        <button class="dropdown-item gap-btwn" type="submit"> 
            <span id="block-title">حظر</span>
            <span class="fa-solid fa-ban"></span></button>
    </form>
</div>