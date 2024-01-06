@extends('backend.master')

@php
    $table_name='blogs';
@endphp
@section('table')
    {{$table_name}}
@endsection
@section('mainContent')
    <link rel="stylesheet" href="{{asset('Modules/Blog/Resources/views/assets/taginput/tagsinput.css')}}"/>

    {!! generateBreadcrumb() !!}
    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid ">
            <div class="white_box mb_30">
                <div class="white_box_tittle list_header">
                    <h4> {{__('blog.Blog Setting')}}</h4>
                </div>

                <div class="col-lg-12">
                    <div class="QA_section QA_section_heading_custom check_box_table">
                        <div class="QA_table ">
                            <form action="" id="form_data_id" method="post">
                                @csrf

                                <div class="row">
                                    <div class="col-lg-4">
                                        <label class="primary_input_label w-100">
                                            {{ __('blog.Auto Approval') }}
                                        </label>
                                        <div class="row">
                                            <div class="col-lg-6 mb-25">
                                                <div class="input-effect">
                                                    <div class="">
                                                        <div class="text-left float-left">
                                                            <label
                                                                class="primary_checkbox d-flex mr-12"
                                                                for="auto_approval_yes">
                                                                <input type="radio"
                                                                       {{ Settings('blog_auto_approval') == 1 ? 'checked':'' }}
                                                                       name="blog_auto_approval"

                                                                       id="auto_approval_yes"
                                                                       value="1"
                                                                       class="common-radio relationButton read-only-input">
                                                                <span
                                                                    class="checkmark mr-2"></span> {{__('common.Yes')}}
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 mb-25">
                                                <div class="input-effect">
                                                    <div class="">
                                                        <div class="text-left float-left">
                                                            <label
                                                                class="primary_checkbox d-flex mr-12"
                                                                for="auto_approval_no">
                                                                <input type="radio"
                                                                       {{ Settings('blog_auto_approval') == 0 ? 'checked':'' }}
                                                                       name="blog_auto_approval"
                                                                       id="auto_approval_no"

                                                                       value="0"
                                                                       class="common-radio relationButton read-only-input">

                                                                <span
                                                                    class="checkmark mr-2"></span> {{__('common.No')}}
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>


                                <div class="row mt-5">
                                    <div class="col d-flex justify-content-center">
                                        <button clas id="general_info_sbmt_btn" class="primary_btn_large">Save</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
@push('scripts')
    <script>
        $(document).ready(function () {

            var submit_btn = $('#general_info_sbmt_btn');

            $('#form_data_id').on('submit', function (event) {
                event.preventDefault();
                submit_btn.html('Saving...');
                $.ajax({
                    url: "{{ route('company_information_update') }}",
                    method: "POST",
                    data: new FormData(this),
                    dataType: 'JSON',
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function (data) {
                        if (data == 1) {
                            toastr.success("Operation Done Successfully", 'Success');
                            location.reload();

                        } else if (data == 2) {
                            toastr.success("For demo version,Update only time zone & currency ", 'Success');
                            location.reload();

                        } else {
                            toastr.error(
                                "Something went wrong", "Warning"
                            );
                        }
                        submit_btn.html('<i class="ti-check"></i> Save');

                    }
                })
            });

        });

    </script>
@endpush
