<div class="tab-pane fade" id="images_tab">
    <div class="row">
        <div class="col-12">

            <h3>{{__('profile.images')}}</h3>
            <hr>

            <form action="{{route('users.photo.update')}}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="profile-image-div text-center">
                            <p> {{__('common.Recommend size')}} (300 x 300) px</p>
                            <img id="profile_picture_show" class="profile-photo center"
                                 src="{{getProfileImage(@$user->image)}}" alt="Profile Photo">
                            <button class="primary-btn fix-gr-bg pointer-event mt-15" type="button">
                                <label class="fix-gr-bg cursor-pointer for_student" for="profile_picture"><i
                                        class="fas fa-arrow-up"></i> {{__('profile.select_profile_picture')}} </label>
                                <input type="file" class="d-none" name="profile_picture" id="profile_picture">
                            </button>
                        </div>
                    </div>
                    <div class="col-md-6">

                        <div class="profile-image-div text-center w-100">
                            <p> {{__('common.Recommend size')}} (1920 x 500) px</p>
                            <img id="cover_photo_show" class="cover-photo center"
                                 src="{{@$user->userInfo->cover_photo? showImage(@$user->userInfo->cover_photo):showImage(null,'cover_photo')}}"
                                 alt="Cover Photo">
                            <button class="primary-btn fix-gr-bg pointer-event mt-15" type="button">
                                <label class="fix-gr-bg cursor-pointer for_student" for="cover_photo">
                                    <i class="fas fa-arrow-up"></i> {{__('profile.select_cover_photo')}}
                                </label>
                                <input type="file" class="d-none" name="cover_photo" id="cover_photo">
                            </button>
                        </div>

                    </div>
                </div>

                <div class="row">

                    <div class="col-12 text-right">
                        <hr class="d-block">
                        <button class="primary-btn fix-gr-bg" type="submit"><i
                                class="ti-check"></i> {{__('common.Save')}}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
