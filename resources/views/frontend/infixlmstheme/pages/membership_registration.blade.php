<!-- hero area:start -->
@extends(theme('layouts.master'))
@section('title')
    {{ Settings('site_title') ? Settings('site_title') : 'Infix LMS' }} | {{ __('membership.Membership Registration') }}
@endsection
@section('css')
    <style>
        .primary-btn.icon-only {
            padding: 0 9px;
            width: 30px;
            height: 30px;
            line-height: 30px;
            border-radius: 50px;
            background: #fb1159;
        }

        .primary-btn {
            display: inline-block;
            color: #ffffff;
            letter-spacing: 1px;
            font-family: "Poppins", sans-serif;
            font-size: 12px;
            font-weight: 500;
            line-height: 40px;
            padding: 0px 20px;
            outline: none !important;
            text-align: center;
            cursor: pointer;
            text-transform: uppercase;
            border: 0;
            border-radius: 5px;
            position: relative;
            overflow: hidden;
            -webkit-transition: all 0.4s ease 0s;
            -moz-transition: all 0.4s ease 0s;
            -o-transition: all 0.4s ease 0s;
            transition: all 0.4s ease 0s;
        }
    </style>
@endsection
@section('mainContent')
    <div>


        <div class="checkout_wrapper" id="mainFormData">
            {!! Form::open([ 'route'=>'membership.registration.store', 'id' => 'orderFrom', 'method' => 'POST', 'enctype'=>'multipart/form-data']) !!}
            <div class="billing_details_wrapper">
                <div class="row billing_form">

                    <div class="col-lg-6">


                        <label class="primary_label2">{{ __('frontend.First Name') }} <span
                                class="required_mark">*</span></label>
                        <input id="first_name" name="first_name" placeholder="{{ __('frontend.Enter First Name') }}"
                               class="primary_input3" value="{{old('first_name')}}" type="text"
                            {{ $errors->first('first_name') ? 'autofocus' : '' }}>
                        <span class="text-danger">{{ $errors->first('first_name') }}</span>
                    </div>

                    <div class="col-lg-6">
                        <label class="primary_label2">{{ __('frontend.Last Name') }} </label>
                        <input id="last_name" name="last_name" placeholder="{{ __('frontend.Enter Last Name') }}"
                               onfocus="this.placeholder = ''"
                               onblur="this.placeholder = '{{ __('frontend.Enter Last Name') }}'" class="primary_input3"
                               value="{{old('last_name')}}"
                               type="text" {{ $errors->first('last_name') ? 'autofocus' : '' }}>
                        <span class="text-danger">{{ $errors->first('last_name') }}</span>
                    </div>
                    <input type="hidden" value=" {{ isset($request) ? $request->plan_id :'' }}" name="plan_id">
                    <input type="hidden" value=" {{ isset($type) ? $type : '' }}" name="type">
                    <div class="col-lg-6 mt_20">
                        <label class="primary_label2">{{ __('frontend.Email') }} <span
                                class="required_mark">*</span></label>
                        <input id="email" name="email" placeholder="{{ __('frontend.Enter Email') }}"
                               class="primary_input3" value="{{old('email')}}" type="text"
                            {{ $errors->first('email') ? 'autofocus' : '' }}>
                        <span class="text-danger">{{ $errors->first('email') }}</span>
                    </div>

                    <div class="col-lg-6 mt_20">
                        <label class="primary_label2">{{ __('frontend.Phone No') }}</label>
                        <input id="phone" name="phone" placeholder="01XXXXXXXXXX" onfocus="this.placeholder = ''"
                               onblur="this.placeholder = '01XXXXXXXXXX'" class="primary_input3" type="text"
                               value="{{old('phone')}}"
                            {{ $errors->first('phone') ? 'autofocus' : '' }}>
                        <span class="text-danger">{{ $errors->first('phone') }}</span>
                    </div>
                    <div class="col-lg-6 mt_20">
                        <label class="primary_label2">{{ __('frontend.Password') }} <span class="required_mark">*</span></label>
                        <input id="password" name="password" placeholder="{{ __('frontend.Enter Password') }}"
                               class="primary_input3" value="" type="password"
                            {{ $errors->first('password') ? 'autofocus' : '' }}>
                        <span class="text-danger">{{ $errors->first('password') }}</span>
                    </div>
                    <div class="col-lg-6 mt_20">
                        <label class="primary_label2">{{ __('frontend.CV') }} <span
                                class="required_mark">*</span></label>
                        <input id="cv" name="cv" placeholder="{{ __('frontend.Upload Your CV') }}"
                               onfocus="this.placeholder = ''"
                               onblur="this.placeholder = '{{ __('frontend.Upload Your CV') }}'" class="primary_input3"
                               type="file" value="" {{ $errors->first('cv') ? 'autofocus' : '' }}>
                        <span class="text-danger">{{ $errors->first('cv') }}</span>
                    </div>
                    <div class="col-lg-6 mt_20">
                        <label class="primary_label2">{{ __('frontend.Qualification') }}</label>
                        <input id="qualifications" name="qualifications[]" placeholder="{{ __('common.Choose File') }}"
                               onfocus="this.placeholder = ''"
                               onblur="this.placeholder = '{{ __('common.Choose File') }}'"
                               class="primary_input3" type="file" value=""
                            {{ $errors->first('qualifications') ? 'autofocus' : '' }}>
                        <span class="text-danger">{{ $errors->first('qualifications') }}</span>
                    </div>
                    <div class="col-lg-6 mt_20">
                        <div class="mt_30 text-right">
                            <button type="button" class="primary-btn icon-only fix-gr-bg"
                                    id="addRowBtn">
                                <span class="ti-plus pr-2"></span></button>
                        </div>
                    </div>
                    <div class="col-lg-12 appendDiv">

                    </div>
                </div>
                <div class="mt_20">
                    <button type="submit" id="submitBtn"
                            class="theme_btn w-100">{{ __('frontend.Register As Member') }}</button>
                </div>
            </div>

            {!! Form::close() !!}
            <div class="order_wrapper">
                {!! Form::open(['route' => 'login']) !!}
                <h3 class="font_22 f_w_700 mb_30">{{ __('frontend.Login') }}</h3>
                <div class="row">
                    <div class="col-12">
                        <div class="input-group custom_group_field">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon3">
                                    <!-- svg -->
                                    <svg xmlns="http://www.w3.org/2000/svg" width="13.328" height="10.662"
                                         viewBox="0 0 13.328 10.662">
                                        <path id="Path_44" data-name="Path 44"
                                              d="M13.995,4H3.333A1.331,1.331,0,0,0,2.007,5.333l-.007,8a1.337,1.337,0,0,0,1.333,1.333H13.995a1.337,1.337,0,0,0,1.333-1.333v-8A1.337,1.337,0,0,0,13.995,4Zm0,9.329H3.333V6.666L8.664,10l5.331-3.332ZM8.664,8.665,3.333,5.333H13.995Z"
                                              transform="translate(-2 -4)" fill="#687083"/>
                                    </svg>
                                    <!-- svg -->
                                </span>
                            </div>
                            <input type="email" value="{{old('email')}}"
                                   class="form-control {{ $errors->has('email') ? ' is-invalid' : '' }}"
                                   placeholder="{{__('common.Enter Email')}}" name="email" aria-label="Username"
                                   aria-describedby="basic-addon3">
                        </div>
                        @if($errors->first('email'))
                            <span class="text-danger" role="alert">{{$errors->first('email')}}</span>
                        @endif
                    </div>

                    <div class="col-12 mt_20">
                        <div class="input-group custom_group_field">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon4">
                                    <!-- svg -->
                                    <svg xmlns="http://www.w3.org/2000/svg" width="10.697" height="14.039"
                                         viewBox="0 0 10.697 14.039">
                                    <path id="Path_46" data-name="Path 46"
                                          d="M9.348,11.7A1.337,1.337,0,1,0,8.011,10.36,1.341,1.341,0,0,0,9.348,11.7ZM13.36,5.68h-.669V4.343a3.343,3.343,0,0,0-6.685,0h1.27a2.072,2.072,0,0,1,4.145,0V5.68H5.337A1.341,1.341,0,0,0,4,7.017V13.7a1.341,1.341,0,0,0,1.337,1.337H13.36A1.341,1.341,0,0,0,14.7,13.7V7.017A1.341,1.341,0,0,0,13.36,5.68Zm0,8.022H5.337V7.017H13.36Z"
                                          transform="translate(-4 -1)" fill="#687083"/>
                                    </svg>
                                    <!-- svg -->
                                </span>
                            </div>
                            <input type="password" name="password" class="form-control"
                                   placeholder="{{__('common.Enter Password')}}" aria-label="password"
                                   aria-describedby="basic-addon4">
                        </div>
                        @if($errors->first('password'))
                            <span class="text-danger" role="alert">{{$errors->first('password')}}</span>
                        @endif
                    </div>
                    <div class="col-12 mt_20">
                        @if(saasEnv('NOCAPTCHA_FOR_LOGIN')=='true')
                            @if(saasEnv('NOCAPTCHA_IS_INVISIBLE')=="true")
                                {!! NoCaptcha::display(["data-size"=>"invisible"]) !!}
                            @else
                                {!! NoCaptcha::display() !!}
                            @endif

                            @if ($errors->has('g-recaptcha-response'))
                                <span class="text-danger"
                                      role="alert">{{$errors->first('g-recaptcha-response')}}</span>
                            @endif
                        @endif
                    </div>
                    <div class="col-12 mt_20">
                        <div class="remember_forgot_pass d-flex justify-content-between">
                            <label class="primary_checkbox d-flex">
                                <input type="checkbox" name="remember"
                                       {{ old('remember') ? 'checked' : '' }} value="1">
                                <span class="checkmark mr_15"></span>
                                <span class="label_name">{{__('common.Remember Me')}}</span>
                            </label>
                            <a href="{{route('SendPasswordResetLink')}}"
                               class="forgot_pass">{{__('common.Forgot Password ?')}}</a>
                        </div>
                    </div>
                    <div class="col-12">

                        @if(saasEnv('NOCAPTCHA_FOR_LOGIN')=='true' && saasEnv('NOCAPTCHA_IS_INVISIBLE')=="true")

                            <button type="button" class="g-recaptcha theme_btn text-center w-100"
                                    data-sitekey="{{saasEnv('NOCAPTCHA_SITEKEY')}}" data-size="invisible"
                                    data-callback="onSubmit"
                                    class="theme_btn text-center w-100"> {{__('common.Login')}}</button>
                            <script src="https://www.google.com/recaptcha/api.js" async defer></script>
                            <script>
                                function onSubmit(token) {
                                    document.getElementById("loginForm").submit();
                                }
                            </script>
                        @else
                            <button type="submit"
                                    class="theme_btn text-center w-100"> {{__('common.Login')}}</button>
                        @endif
                    </div>
                </div>


                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        $(document).ready(function () {
            $(document).on('click', '#addRowBtn', function () {

                var row = `
                <div class="row">
                <div class="col-lg-6 mt_20">
                        <label class="primary_label2">{{ __('frontend.Qualification') }}</label>
                        <input id="cv" name="qualifications[]" placeholder="{{ __('common.Choose File') }}"
                            onfocus="this.placeholder = ''" onblur="this.placeholder = '{{ __('common.Choose File') }}'"
                            class="primary_input3" type="file" value=""
                            {{ $errors->first('cv') ? 'autofocus' : '' }}>
                        <span class="text-danger">{{ $errors->first('cv') }}</span>
                    </div>
                <div class="col-lg-6 mt_20">
                        <div class="mt_30 text-right">
                            <button type="button" class="primary-btn icon-only fix-gr-bg removeQualification">
                                <span class="ti-trash pr-2"></span></button>
                        </div>
                    </div></div>`;
                $('.appendDiv').append(row);
            })
            $(document).on('click', '.removeQualification', function () {
                $(this).parent().parent().parent().remove();
            })
        })
    </script>
@endsection
