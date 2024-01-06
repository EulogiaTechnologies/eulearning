<div class="tab-pane active" id="basic_information_tab">
    <div class="account_profile_form row">
        <div class="col-12">

            <h3>{{__('profile.basic_information')}}</h3>
            <hr>

            <form action="{{route('users.basic_info.update')}}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-6 mt_20">
                        <label class="primary_label2" for="name">{{__('common.Name')}}
                            <span class="required_mark">*</span></label>
                        <input id="name" name="name" placeholder="{{__('common.Name')}}"
                               onfocus="this.placeholder = ''"
                               onblur="this.placeholder = '{{__('common.Name')}}'"
                               class="primary_input4" {{$errors->first('name') ? 'autofocus' : ''}}
                               value="{{old('name')??@$user->name}}" type="text">
                        <span class="text-danger" role="alert">{{$errors->first('name')}}</span>
                    </div>
                    <div class="col-md-6 mt_20">
                        <label class="primary_label2" for="email">{{__('common.Email')}}
                            <span class="required_mark">*</span></label>
                        <input id="email" name="email" placeholder="{{__('common.Email')}}"
                               onfocus="this.placeholder = ''"
                               onblur="this.placeholder = '{{__('common.Email')}}'"
                               class="primary_input4" {{$errors->first('email') ? 'autofocus' : ''}}
                               value="{{old("email")??@$user->email}}" type="email">
                        <span class="text-danger" role="alert">{{$errors->first('email')}}</span>
                    </div>
                    <div class="col-md-6 mt_20">
                        <label class="primary_label2" for="phone">{{__('common.Phone')}}
                            <span class="required_mark">*</span></label>
                        <input id="phone" name="phone" placeholder="{{__('common.Phone')}}"
                               onfocus="this.placeholder = ''"
                               onblur="this.placeholder = '{{__('common.Phone')}}'"
                               class="primary_input4" {{$errors->first('phone') ? 'autofocus' : ''}}
                               value="{{old("phone")??@$user->phone }}" type="text">
                        <span class="text-danger" role="alert">{{$errors->first('phone')}}</span>
                    </div>
                    <div class="col-md-6 mt_20">
                        <div class="single_input ">
                            <span class="primary_label2">{{__('common.Currency')}}  </span>
                            <select class="theme_select wide mb_20"
                                    name="currency" {{$errors->first('currency') ? 'autofocus' : ''}}>
                                <option value=""> {{__('profile.select_one')}}</option>
                                @foreach ($currencies as $currency)
                                    <option
                                        value="{{@$currency->id}}"
                                        @if(old('currency'))
                                            @if (old('currency')==$currency->id) selected @endif
                                        @else
                                            @if(@$user->currency_id==$currency->id) selected @endif
                                        @endif>
                                        {{@$currency->name}}
                                    </option>
                                @endforeach
                            </select>
                            <span class="text-danger" role="alert">{{$errors->first('currency')}}</span>
                        </div>

                    </div>
                    <div class="col-md-6 mt_20">
                        <div class="single_input ">
                            <span class="primary_label2">{{__('common.Language')}}  </span>
                            <select class="theme_select wide mb_20"
                                    name="language" {{$errors->first('language') ? 'autofocus' : ''}}>
                                <option value=""> {{__('profile.select_one')}}</option>
                                @foreach ($languages as $language)
                                    <option value="{{@$language->id}}"
                                            @if(old('language'))
                                                @if(old('language')==$language->id) selected @endif
                                            @else
                                                @if(@$user->language_id==$language->id) selected @endif
                                        @endif>
                                        {{@$language->native}}
                                    </option>
                                @endforeach
                            </select>
                            <span class="text-danger" role="alert">{{$errors->first('language')}}</span>
                        </div>

                    </div>
                    <div class="col-md-6 mt_20">
                        <div class="single_input ">
                            <span class="primary_label2">{{__('profile.timezone')}}  </span>
                            <select class="theme_select wide mb_20"
                                    name="timezone" {{$errors->first('timezone') ? 'autofocus' : ''}}>
                                <option value=""> {{__('profile.select_one')}}</option>
                                @foreach ($timezones as $timezone)
                                    <option value="{{@$timezone->id}}"
                                            @if(old('timezone'))
                                                @if(old('timezone')==$timezone->id) selected @endif
                                            @else
                                                @if(@$user->userInfo->timezone_id == $timezone->id) selected @endif
                                        @endif>
                                        {{@$timezone->code}}
                                    </option>
                                @endforeach
                            </select>
                            <span class="text-danger" role="alert">{{$errors->first('timezone')}}</span>
                        </div>

                    </div>
                    <div class="col-12 text-right">
                        <hr class="d-block">
                        <button class="theme_btn small_btn text-center" type="submit"><i
                                class="ti-check mr-1"></i> {{__('common.Save')}}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
