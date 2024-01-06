<div class="tab-pane fade" id="experience_tab">
    <div class="row">
        <div class="col-12">

            <div class="d-flex justify-content-between align-items-center">
                <h3>{{__('profile.experience')}}</h3>
                <div class="d-flex align-items-center">
                    <label data-toggle="tooltip" data-placement="top" title="{{$show_experience_tooltip}}" class="lmsSwitch_toggle pr-2 mr-3" for="autoNext1">
                        <input value="{{$show_experience?0:1}}" class="hide_show_tab" data-filed="show_experience" type="checkbox" id="autoNext1" {{$show_experience?'checked':""}}>
                        <div class="slider round"></div>
                    </label>
                    <a href="javascript:void(0)"
                       class="link_value theme_btn small_btn4 add_experience_btn">{{__('profile.add_experience')}}</a>
                </div>

            </div>
            <hr>
            <div id="experience_list">
                @include(theme('profile.experience.list'))
            </div>

        </div>
    </div>
</div>
