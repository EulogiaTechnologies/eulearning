@extends('backend.master')
@push('styles')
    <link rel="stylesheet" href="{{ asset('public/backend/css/daterangepicker.css') }}">
@endpush
@section('mainContent')

    {!! generateBreadcrumb() !!}

    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">

            <div class="row justify-content-center">
                <div class="col-lg-12">
                    <div class="white_box mb_30">
                        <div class="white_box_tittle list_header">
                            <h4>{{__('courses.Advanced Filter')}} </h4>
                        </div>
                        <form action="#" method="POST">
                            @csrf
                            <div class="row">


                                <div class="col-lg-3 mb-3">
                                    <label class="primary_input_label" for="f_type">{{__('common.Type')}}</label>
                                    <select class="primary_select" name="f_type" id="f_type">
                                        <option value="">{{__('common.Select One')}}</option>
                                        <option value="1">{{trans('courses.Course')}}</option>
                                        <option value="2">{{trans('courses.Quiz')}}</option>
                                        <option value="3">{{trans('courses.Virtual Class')}}</option>
                                    </select>
                                </div>

                                <div class="col-lg-3">
                                    <label class="primary_input_label"
                                           for="f_category">{{__('courses.Category')}}</label>
                                    <select class="primary_select" name="f_category" id="f_category">
                                        <option data-display="{{__('common.Select')}} {{__('courses.Category')}}"
                                                value="">{{__('common.Select')}} {{__('courses.Category')}}</option>
                                        @foreach($categories as $category)
                                            @if($category->parent_id==0)
                                                @include('backend.categories._single_select_option',['category'=>$category,'level'=>1])
                                            @endif
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-lg-3">
                                    <div class="primary_input mb-15 date_range">
                                        <div class="primary_datepicker_input filter">
                                            <label class="primary_input_label" for="">{{__('common.Date')}}</label>
                                            <div class="no-gutters input-right-icon">
                                                <input placeholder="{{__('common.Date')}}" readonly
                                                       class="primary_input_field date_range_input" type="text"
                                                       name="date_range_filter" value="">
                                                <button class="" type="button">
                                                    <i class="fa fa-refresh" id="reset-date-filter"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <div class="col-lg-3 mt-3">
                                    <div class="search_course_btn">
                                        <a class="primary-btn radius_30px mr-10 fix-gr-bg reset_btn mt-20">{{__('common.Reset')}} </a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="col-12">
                    <div class="box_header common_table_header">
                        <div class="main-title d-md-flex">
                            <h3 class="mb-0 mr-30 mb_xs_15px mb_sm_20px"> {{ trans('courses.My Topics') }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="QA_section QA_section_heading_custom check_box_table">
                        <div class="QA_table ">
                            <!-- table-responsive -->
                            <div class="">
                                <table id="lms_table" class="table Crm_table_active3">
                                    <thead>
                                    <tr>
                                        <th scope="col">{{__('common.SL')}}</th>
                                        <th scope="col">{{__('common.Title')}}</th>
                                        <th scope="col">{{__('common.Type')}}</th>
                                        <th scope="col">{{__('courses.Curriculum')}}</th>
                                        <th scope="col">{{__('courses.Enroll Date')}}</th>
                                        <th scope="col">{{__('common.Action')}}</th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>


            </div>

            <input type="hidden" value="{{route('users.my_topics.datatable')}}" id="my_topics_route">
        </div>
    </section>
@endsection

@push('scripts')
    <script src="{{asset('public/backend/js/daterangepicker.min.js')}}"></script>
    <script src="{{asset('public/modules/common/date_range_init.js')}}"></script>
    <script src="{{asset('public/modules/my_panel/my_topics.js')}}"></script>
@endpush
