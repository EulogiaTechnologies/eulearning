@extends('setting::layouts.master')

@section('mainContent')

    {!! generateBreadcrumb() !!}

    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="box_header">
                        <div class="main-title d-flex">

                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="">
                        <div class="row">

                            <div class="col-lg-12">
                                <!-- tab-content  -->
                                <div class="tab-content " id="myTabContent">
                                    <!-- General -->
                                    <div class="tab-pane fade white_box_30px show active" id="Activation"
                                         role="tabpanel" aria-labelledby="Activation-tab">
                                        <div class="main-title mb-25">
                                            <div class="main-title mb-25">
                                                <h3 class="mb-0">{{ __('setting.General') }}</h3>
                                            </div>

                                            <form action="{{route('setting.cookieSettingStore')}}" id="" method="POST"
                                                  enctype="multipart/form-data">

                                                @csrf

                                                <div class="single_system_wrap">
                                                    <div class="row">

                                                        <div class="col-xl-2">
                                                            <div class="primary_input mb-25">
                                                                <img height="70" class=" imagePreview1"
                                                                     src="{{ asset('/'.$setting->image)}}"
                                                                     alt="">
                                                            </div>
                                                        </div>
                                                        <div class="col-xl-4">
                                                            <div class="primary_input mb-25">
                                                                <label class="primary_input_label"
                                                                       for="">{{ __('common.Image') }}
                                                                </label>
                                                                <div class="primary_file_uploader">
                                                                    <input
                                                                        class="primary-input  filePlaceholder   "
                                                                        type="text" id=""
                                                                        placeholder="Browse file"
                                                                        readonly="">
                                                                    <button class="" type="button">
                                                                        <label class="primary-btn small fix-gr-bg"
                                                                               for="file1">{{ __('common.Browse') }}</label>
                                                                        <input type="file"
                                                                               class="d-none fileUpload imgInput1"
                                                                               name="image" id="file1">
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>


                                                        <div class="col-xl-6 ">
                                                            <div class="primary_input mb-25">
                                                                <div class="row">
                                                                    <div class="col-md-12 mb-3">
                                                                        <label class="primary_input_label"
                                                                               for=""> {{__('setting.Enable')}}</label>
                                                                    </div>
                                                                    <div class="col-md-6 mb-25">
                                                                        <label class="primary_checkbox d-flex mr-12"
                                                                               for="type1">
                                                                            <input type="radio"
                                                                                   class="common-radio type1"
                                                                                   id="type1"
                                                                                   name="allow"
                                                                                   value="1" {{@$setting->allow==1?"checked":""}}>
                                                                            <span
                                                                                class="checkmark mr-2"></span> {{__('common.Yes')}}
                                                                        </label>
                                                                    </div>
                                                                    <div class="col-md-6  mb-25">
                                                                        <label class="primary_checkbox d-flex mr-12"
                                                                               for="type2">
                                                                            <input type="radio"
                                                                                   class="common-radio type2"
                                                                                   id="type2"
                                                                                   name="allow"
                                                                                   value="0" {{@$setting->allow==0?"checked":""}}>
                                                                            <span
                                                                                class="checkmark mr-2"></span> {{__('common.No')}}
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-xl-3">
                                                            <div class="primary_input mb-25">
                                                                <label class="primary_input_label"
                                                                       for="">{{__('setting.Accept Button Text')}}</label>
                                                                <input class="primary_input_field"
                                                                       placeholder=""
                                                                       type="text" id="btn_text"
                                                                       name="btn_text"
                                                                       value="{{ $setting->btn_text }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-xl-3">
                                                            <div class="primary_input mb-25">
                                                                <label class="primary_input_label"
                                                                       for="">{{__('setting.Customize Button Text')}}</label>
                                                                <input class="primary_input_field"
                                                                       placeholder=""
                                                                       type="text" id="customize_btn_text"
                                                                       name="customize_btn_text"
                                                                       value="{{ $setting->customize_btn_text }}">
                                                            </div>
                                                        </div>

                                                        <div class="col-xl-3">
                                                            <div class="primary_input mb-25">
                                                                <label class="primary_input_label"
                                                                       for="">{{__('setting.Background Color')}}</label>
                                                                <input class="primary_input_field"
                                                                       placeholder="#000000"
                                                                       type="color" id="bg_color"
                                                                       name="bg_color"
                                                                       value="{{ $setting->bg_color }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-xl-3">
                                                            <div class="primary_input mb-25">
                                                                <label class="primary_input_label"
                                                                       for="">{{__('setting.Button Background Color')}}</label>
                                                                <input class="primary_input_field"
                                                                       placeholder="#ffffff"
                                                                       type="color" id="text_color"
                                                                       name="text_color"
                                                                       value="{{ $setting->text_color }}">
                                                            </div>
                                                        </div>


                                                        <div class="col-xl-12">
                                                            <div class="primary_input mb-25">
                                                                <label class="primary_input_label"
                                                                       for="">{{__('setting.Details')}}</label>
                                                                <textarea name="details"
                                                                          class="lms_summernote">{!! $setting->details !!}</textarea>
                                                            </div>
                                                        </div>
                                                        <div class="col-xl-12">
                                                            <div class="primary_input mb-25">
                                                                <label class="primary_input_label"
                                                                       for="">{{__('setting.GDPR')}}</label>
                                                                <textarea name="gdpr_details"
                                                                          class="lms_summernote">{!! $setting->gdpr_details !!}</textarea>
                                                            </div>
                                                        </div>

                                                        <div class="col-xl-12">
                                                            <div class="primary_input mb-25">
                                                                <label class="primary_input_label"
                                                                       for="">{{__('setting.Strictly Necessary')}}</label>
                                                                <textarea name="gdpr_strictly"
                                                                          class="lms_summernote">{!! $setting->gdpr_strictly !!}</textarea>
                                                            </div>
                                                        </div>
                                                        <div class="col-xl-12">
                                                            <div class="primary_input mb-25">
                                                                <label class="primary_input_label"
                                                                       for="">{{__('setting.Performance Cookies')}}</label>
                                                                <textarea name="gdpr_performance"
                                                                          class="lms_summernote">{!! $setting->gdpr_performance !!}</textarea>
                                                            </div>
                                                        </div>
                                                        <div class="col-xl-12">
                                                            <div class="primary_input mb-25">
                                                                <label class="primary_input_label"
                                                                       for="">{{__('setting.Functional Cookies')}}</label>
                                                                <textarea name="gdpr_functional"
                                                                          class="lms_summernote">{!! $setting->gdpr_functional !!}</textarea>
                                                            </div>
                                                        </div>
                                                        <div class="col-xl-12">
                                                            <div class="primary_input mb-25">
                                                                <label class="primary_input_label"
                                                                       for="">{{__('setting.Targeting Cookies')}}</label>
                                                                <textarea name="gdpr_targeting"
                                                                          class="lms_summernote">{!! $setting->gdpr_targeting !!}</textarea>
                                                            </div>
                                                        </div>

                                                        <div class="col-xl-3">
                                                            <div class="primary_input mb-25">
                                                                <label class="primary_input_label"
                                                                       for="">{{__('setting.Confirm My Choices Button Text')}}</label>
                                                                <input class="primary_input_field"
                                                                       placeholder=""
                                                                       type="text" id="gdpr_confirm_choice_btn_text"
                                                                       name="gdpr_confirm_choice_btn_text"
                                                                       value="{{ $setting->gdpr_confirm_choice_btn_text }}">
                                                            </div>
                                                        </div>

                                                        <div class="col-xl-3">
                                                            <div class="primary_input mb-25">
                                                                <label class="primary_input_label"
                                                                       for="">{{__('setting.Accept All Button Text')}}</label>
                                                                <input class="primary_input_field"
                                                                       placeholder=""
                                                                       type="text" id="gdpr_accept_all_btn_text"
                                                                       name="gdpr_accept_all_btn_text"
                                                                       value="{{ $setting->gdpr_accept_all_btn_text }}">
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>


                                                <div class="submit_btn text-center mt-4">
                                                    <button class="primary_btn_large" type="submit"
                                                            data-toggle="tooltip" title=""
                                                            id="general_info_sbmt_btn"><i
                                                            class="ti-check"></i> {{ __('common.Save') }}</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>

                                </div>


                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@include('setting::page_components.script')
@push('scripts')
    <script>
        function readURL1(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $(".imagePreview1").attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]); // convert to base64 string
            }
        }

        $(".imgInput1").change(function () {
            readURL1(this);
        });
    </script>
@endpush
