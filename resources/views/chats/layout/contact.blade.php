
@foreach ($users as $user)
    @if ($user_type != 'الكل')
        @if ($user->user != $user_type)
            @continue        
        @endif        
    @endif
    
    <div class="btn d-flex justify-content-between align-items-center py-1 my-1 list-contant" 
         id="contact-{{ $contact[$user->id] }}" onclick="goToChat('{{ $user->id }}', '{{ $contact[$user->id] }}')">
        <span class="font-small d-flex align-items-center">
            <img class="avatar-lg img-fluid rounded-circle ml-3" src="{{ $user->image_url }}" alt="avatar">
            <div class="">
                <p class="mb-0 custom-font text-right">{{ $user->name }}</p> 
                <small class="custom-font text-success" id="last-msg-{{ $user->id }}">{{ Str::limit(@$lmsg[$user->id], 25, '...') }}</small> 
            </div>
        </span>
        <div id="unread-{{ $user->id }}"> 
            @if ($unread->where('sender_id', $user->id)->count() > 0)
                <span class="avatar-sm img-fluid rounded-circle ml-3 bg-success text-white d-flex align-items-center justify-content-center">
                    {{ $unread->where('sender_id', $user->id)->count() }}
                </span>
            @endif
        </div>
    </div>  
@endforeach
