<a href="{{route('users.profile',$row->id)}}">
    <div class="d-flex align-items-center">
        <div class="">
            <img style="width: 40px;height: 40px; border-radius: 50%;" src="{{getProfileImage(@$row->image)}}"
                 class="img-cover" alt="profile photo">
        </div>
        <div class="ml-3">
            <span class="d-block">{{$row->name}}</span>
            <span class="d-block">{{$row->email}}</span>
        </div>
    </div>
</a>
