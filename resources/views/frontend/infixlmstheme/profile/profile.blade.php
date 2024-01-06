@extends(theme('layouts.master'))

@section('title')
    {{Settings('site_title')  ? Settings('site_title')  : 'Infix LMS'}} | {{__('profile.profile')}}
@endsection

@section('css')
    <style>
        .profile-info ul li a i.fa-linkedin-in {
            color: #0078b6;
        }

        .profile-info .badges {
            display: block;
        }

        .profile-left {
            border: none;
            padding: 0;
        }


        .profile-info .badges li img {
            width: var(--width);
            height: var(--width)
        }

        .badgesSlider {
            width: 50%
        }

        @media (min-width: 992px) and (max-width: 1279px) {
            .badgesSlider {
                width: 42%;
            }
        }

        @media (max-width: 767px) {
            .profile-info .social_media {
                grid-template-columns: repeat(4, minmax(var(--width), 1fr));
                margin-bottom: 15px;
                width: 100%;
            }

            .badgesSlider {
                width: 100%;
                margin-bottom: 15px;
            }
        }

        .badgesSlider img {
            width: var(--width) !important;
            height: var(--width) !important;
            object-fit: contain;
        }

        .profile-wrapper {
            padding: 20px;
            background-color: #fff;
            box-shadow: -12px 16px 40px 0px rgba(0, 0, 0, 0.1);
        }

        .profile-img .img {
            border: 1px solid #c5c5c5;
        }

        .profile-right {
            background-color: var(--system_primery_color);
            padding: 35px 30px;
            border-radius: 10px;
        }

        .mr-20 {
            margin-right: 20px !important;
        }

    </style>
@endsection

@section('js')
    <script>
        $(".badgesSlider").owlCarousel({
            loop: false,
            margin: 14,
            autoplay: false,
            items: 5,
            navText: [
                '<i class="fa fa-angle-left"></i>',
                '<i class="fa fa-angle-right"></i>',
            ],
            nav: false,
            dots: false,
            autoplayHoverPause: true,
            autoplaySpeed: 800,
            autoWidth: true,
            // responsive: {
            //     0: {
            //         items: 1,
            //     },
            //     767: {
            //         items: 6,
            //     },
            //     992: {
            //         items: 4,
            //     },
            //     1400: {
            //         items: 5,
            //     },
            // },
        });

    </script>
@endsection

@section('mainContent')

    <!-- profile cover photo -->
    <div class="profile-cover">
        <img
            src="{{(@$user->userInfo && @$user->userInfo->cover_photo)? showImage(@$user->userInfo->cover_photo):showImage(null,'cover_photo')}}"
            alt="cover photo">
    </div>
    <!-- profile cover photo -->
    @php
        $already_count=[];
    @endphp
    @foreach(@$user->userLatestBadges as $badge)
        @php
            $b =$badge->badge;

            if (in_array($b->type,$already_count)){
                continue;
            }else{
                $already_count[]=$b->type;
            }
        @endphp

    @endforeach

    <!-- profile info -->
    <div class="profile">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="profile-wrapper d-flex flex-wrap align-items-center">
                        <div class="profile-left d-flex flex-wrap align-items-center">
                            <div class="profile-img text-center">
                                <div class="img"><img
                                        src="{{$user->role_id==3?getStudentImage(@$user->image):getInstructorImage(@$user->image)}}"
                                        alt="Profile Photo">
                                </div>
                                <a href="{{$follow_btn_route}}" class="theme_btn rounded-pill">{{$follow_btn_text}}</a>
                                <p class="f_w_500">{{$total_followers}} {{__('profile.follower')}}
                                    | {{$total_following}} {{__('profile.following')}}</p>
                            </div>
                            <div class="profile-info">
                                <h4>{{@$user->name}}</h4>
                                {{--                                @if($section_review)--}}
                                {{--                                    <blockquote class="d-block"><span><i class="fa fa-star"></i> 4.6</span> (46,105--}}
                                {{--                                        Reviews)--}}
                                {{--                                    </blockquote>--}}
                                {{--                                @endif--}}
                                @if($user->userInfo && $user->userInfo->short_description)
                                    <p>{{@$user->userInfo->short_description}} </p>
                                @endif

                                <div class="d-flex align-items-enter flex-wrap">

                                    @if($section_badge && @$user->userLatestBadges->count() > 0)
                                        <ul class="{{count($already_count) > 5 ? 'badgesSlider owl-carousel': "badges d-flex align-items-center"}}">
                                            @php
                                                $already=[];
                                            @endphp
                                            @foreach(@$user->userLatestBadges as $badge)
                                                @php
                                                    $b =$badge->badge;

                                                    if (in_array($b->type,$already)){
                                                        continue;
                                                    }else{
                                                        $already[]=$b->type;
                                                    }
                                                @endphp
                                                <li><img src="{{asset($b->image)}}"
                                                         alt="{{$b->title}} {{ucfirst($b->type)}} {{trans('setting.Badge')}}">
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                    @if($user->facebook || $user->twitter|| $user->linkedin|| $user->instagram )
                                        <ul class="social_media">
                                            @if($user->facebook)
                                                <li><a href="{{@$user->facebook??"javascript:void(0)"}}"><i
                                                            class="fab fa-facebook-f"></i></a></li>
                                            @endif

                                            @if($user->twitter)
                                                <li><a href="{{@$user->twitter??"javascript:void(0)"}}"><i
                                                            class="fab fa-twitter"></i></a></li>
                                            @endif
                                            @if($user->linkedin)
                                                <li><a href="{{@$user->linkedin??"javascript:void(0)"}}"><i
                                                            class="fab fa-linkedin-in"></i></a></li>
                                            @endif
                                            @if($user->instagram)
                                                <li><a href="{{@$user->instagram??"javascript:void(0)"}}"><i
                                                            class="fab fa-instagram"></i></a></li>
                                            @endif
                                            {{--                                        <li>--}}
                                            {{--                                            <div class="dropdown">--}}
                                            {{--                                                <a href="javascript:void(0)" data-toggle="dropdown" aria-expanded="false">--}}
                                            {{--                                                    <i class="fa fa-ellipsis-h"></i>--}}
                                            {{--                                                </a>--}}
                                            {{--                                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">--}}
                                            {{--                                                    <li><a href="#"><i class="fab fa-algolia"></i></a></li>--}}
                                            {{--                                                    <li><a href="#"><i class="fab fa-amazon"></i></a></li>--}}
                                            {{--                                                    <li><a href="#"><i class="fab fa-apple"></i></a></li>--}}
                                            {{--                                                </ul>--}}
                                            {{--                                            </div>--}}
                                            {{--                                        </li>--}}
                                        </ul>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="profile-right">
                            <ul>
                                @if($section_total_instructor)
                                    <li>
                                        <svg width="29" height="26" viewBox="0 0 29 26" fill="none"
                                             xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M19.2578 13.2603C21.902 13.2603 24.0456 11.1167 24.0456 8.47247C24.0456 6.42701 22.7629 4.68115 20.958 3.99536"
                                                stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                                stroke-linejoin="round"/>
                                            <path
                                                d="M11.2055 12.3114C14.0718 12.3114 16.3954 9.98784 16.3954 7.12155C16.3954 4.25524 14.0718 1.93164 11.2055 1.93164C8.33922 1.93164 6.01562 4.25524 6.01562 7.12155C6.01562 9.98784 8.33922 12.3114 11.2055 12.3114Z"
                                                stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                                stroke-linejoin="round"/>
                                            <path
                                                d="M19.1506 19.6029C21.6893 21.2937 19.975 24.5548 16.9249 24.5548H5.47059C2.42043 24.5548 0.706214 21.2937 3.24485 19.6029C5.52196 18.0863 8.25663 17.2025 11.1977 17.2025C14.1389 17.2025 16.8735 18.0863 19.1506 19.6029Z"
                                                stroke="currentColor" stroke-width="2.5"/>
                                            <path
                                                d="M16.4844 24.5543H23.7443C26.5583 24.5543 28.1396 21.546 25.7976 19.986C25.151 19.5553 24.4642 19.18 23.7443 18.8667"
                                                stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
                                        </svg>
                                        {{$total_instructors}} {{trans('profile.instructors')}}
                                    </li>
                                @endif

                                @if($section_total_review)
                                    <li>
                                        <svg width="27" height="26" viewBox="0 0 27 26" fill="none"
                                             xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M11.0066 3.01965C11.8925 0.875304 14.8612 0.805786 15.7513 2.94836C16.3677 4.43186 16.8379 5.9595 17.3116 7.95552C19.4048 7.93418 21.041 7.97944 22.6924 8.12785C25.0142 8.3365 25.9512 11.1597 24.1437 12.6318C23.1221 13.4639 22.025 14.2335 20.6499 15.0965C20.2629 15.3394 20.0887 15.8147 20.2291 16.2496C20.8708 18.2395 21.3042 19.8546 21.6413 21.5492C22.09 23.8027 19.716 25.522 17.7866 24.2741C16.3072 23.3176 14.9635 22.2165 13.3096 20.6456C11.6707 22.1544 10.3335 23.225 8.87114 24.1755C6.93055 25.4374 4.50552 23.7365 4.95605 21.466C5.28169 19.825 5.71584 18.2288 6.37658 16.259C6.52379 15.8201 6.34962 15.3364 5.95684 15.0914C4.53994 14.2076 3.41466 13.4203 2.35891 12.5527C0.580534 11.0911 1.48841 8.30536 3.78054 8.09356C5.45806 7.93854 7.13307 7.90567 9.30755 7.95552C9.8801 6.00796 10.3941 4.50223 11.0066 3.01965Z"
                                                stroke="currentColor" stroke-width="2.5" stroke-linejoin="round"/>
                                        </svg>
                                        {{$total_review}} {{__('profile.reviews')}}
                                    </li>
                                @endif
                                @if($section_total_student)
                                    <li>
                                        <svg width="29" height="26" viewBox="0 0 29 26" fill="none"
                                             xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M19.2578 13.2603C21.902 13.2603 24.0456 11.1167 24.0456 8.47247C24.0456 6.42701 22.7629 4.68115 20.958 3.99536"
                                                stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                                stroke-linejoin="round"/>
                                            <path
                                                d="M11.2055 12.3114C14.0718 12.3114 16.3954 9.98784 16.3954 7.12155C16.3954 4.25524 14.0718 1.93164 11.2055 1.93164C8.33922 1.93164 6.01562 4.25524 6.01562 7.12155C6.01562 9.98784 8.33922 12.3114 11.2055 12.3114Z"
                                                stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                                stroke-linejoin="round"/>
                                            <path
                                                d="M19.1506 19.6029C21.6893 21.2937 19.975 24.5548 16.9249 24.5548H5.47059C2.42043 24.5548 0.706214 21.2937 3.24485 19.6029C5.52196 18.0863 8.25663 17.2025 11.1977 17.2025C14.1389 17.2025 16.8735 18.0863 19.1506 19.6029Z"
                                                stroke="currentColor" stroke-width="2.5"/>
                                            <path
                                                d="M16.4844 24.5543H23.7443C26.5583 24.5543 28.1396 21.546 25.7976 19.986C25.151 19.5553 24.4642 19.18 23.7443 18.8667"
                                                stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
                                        </svg>
                                        {{$total_students}} {{trans('profile.students')}}
                                    </li>
                                @endif
                                @if($section_total_course)
                                    <li>
                                        <svg width="29" height="27" viewBox="0 0 29 27" fill="none"
                                             xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M2.70034 17.3814C2.85209 18.3963 3.73155 19.1535 4.75726 19.1228C7.90038 19.0288 11.1024 18.9078 14.4581 18.9078C17.8006 18.9078 21.0032 18.9836 24.1281 19.0999C25.1668 19.1385 26.0666 18.3781 26.2205 17.3503C26.5422 15.202 26.9161 12.9637 26.9161 10.664C26.9161 8.37225 26.5448 6.14157 26.2239 4.00014C26.0684 2.96301 25.1539 2.20042 24.1062 2.24443C21.0168 2.37417 17.7849 2.42016 14.4581 2.42016C11.1177 2.42016 7.88551 2.32964 4.77851 2.22097C3.74385 2.18477 2.85036 2.94419 2.69711 3.96807C2.37514 6.11931 2 8.3608 2 10.664C2 12.9748 2.37765 15.2236 2.70034 17.3814Z"
                                                stroke="currentColor" stroke-width="2.5"/>
                                            <path d="M9.49219 25H19.4254" stroke="currentColor" stroke-width="2.5"
                                                  stroke-linecap="round"/>
                                            <path d="M14.4531 18.9126V24.9994" stroke="currentColor" stroke-width="2.5"
                                                  stroke-linecap="round"/>
                                        </svg>
                                        {{$total_courses}}

                                        {{__('profile.courses')}}
                                    </li>
                                @endif
                                @if($section_blog_tab)
                                    <li>


                                        <svg width="29" height="29" viewBox="0 0 29 29" fill="none"
                                             xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M25.375 8.45841V20.5417C25.375 24.1667 23.5625 26.5834 19.3333 26.5834H9.66667C5.4375 26.5834 3.625 24.1667 3.625 20.5417V8.45841C3.625 4.83341 5.4375 2.41675 9.66667 2.41675H19.3333C23.5625 2.41675 25.375 4.83341 25.375 8.45841Z"
                                                stroke="currentColor" stroke-width="2.5" stroke-miterlimit="10"
                                                stroke-linecap="round" stroke-linejoin="round"/>
                                            <path
                                                d="M17.5208 5.4375V7.85417C17.5208 9.18333 18.6083 10.2708 19.9374 10.2708H22.3541"
                                                stroke="currentColor" stroke-width="2.5" stroke-miterlimit="10"
                                                stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M9.66675 15.7083H14.5001" stroke="currentColor" stroke-width="2.5"
                                                  stroke-miterlimit="10" stroke-linecap="round"
                                                  stroke-linejoin="round"/>
                                            <path d="M9.66675 20.5417H19.3334" stroke="currentColor" stroke-width="2.5"
                                                  stroke-miterlimit="10" stroke-linecap="round"
                                                  stroke-linejoin="round"/>
                                        </svg>


                                        {{$total_blogs}}  {{__('profile.blogs')}}
                                    </li>
                                @endif
                                @if($section_certificate_tab)
                                    <li>
                                        <svg width="29" height="29" viewBox="0 0 17 20" fill="none"
                                             xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M15.4307 18.2183C15.3801 18.9256 14.5305 19.2561 14.0153 18.7691L9.39537 14.4015C8.80981 13.848 7.89387 13.848 7.30831 14.4015L2.68845 18.7691C2.17324 19.2561 1.32357 18.9256 1.27306 18.2183C0.929826 13.4131 0.91535 8.59416 1.20047 3.78824C1.29405 2.21097 2.61479 1 4.19482 1H12.5089C14.0889 1 15.4096 2.21097 15.5031 3.78824C15.7884 8.59416 15.7738 13.4131 15.4307 18.2183Z"
                                                stroke="currentColor" stroke-width="1.71429" stroke-linecap="round"
                                                stroke-linejoin="round"/>
                                        </svg>
                                        {{$total_certificates}}  {{__('profile.certificates')}}
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>


                <div class="col-12">
                    <ul class="nav nav-pills" id="pills-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="pills-about-tab" data-toggle="pill"
                                    data-target="#pills-about" type="button" role="tab" aria-controls="pills-about"
                                    aria-selected="true">{{__('common.About')}}
                            </button>
                        </li>
                        @if($section_course_tab)
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="pills-course-tab" data-toggle="pill"
                                        data-target="#pills-course" type="button" role="tab"
                                        aria-controls="pills-course"
                                        aria-selected="false">{{__('profile.courses')}}
                                </button>
                            </li>
                        @endif

                        @if($section_instructor_tab)
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="pills-instructor-tab" data-toggle="pill"
                                        data-target="#pills-instructor" type="button" role="tab"
                                        aria-controls="pills-instructor"
                                        aria-selected="false"> {{trans('profile.instructors')}}
                                </button>
                            </li>
                        @endif
                        @if($section_education_tab)
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="pills-education-tab" data-toggle="pill"
                                        data-target="#pills-education" type="button" role="tab"
                                        aria-controls="pills-education"
                                        aria-selected="false">{{__('profile.education')}}
                                </button>
                            </li>
                        @endif
                        @if($section_experience_tab)
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="pills-experience-tab" data-toggle="pill"
                                        data-target="#pills-experience" type="button" role="tab"
                                        aria-controls="pills-experience"
                                        aria-selected="false">{{__('profile.experience')}}
                                </button>
                            </li>
                        @endif
                    </ul>


                    <div class="tab-content" id="pills-tabContent">
                        {{--about tab--}}
                        <div class="tab-pane fade show active" id="pills-about" role="tabpanel"
                             aria-labelledby="pills-about-tab">
                            <div class="about">
                                <h3 class="h2">{{__('common.About')}}</h3>
                                <div>
                                    {!!  @$user->about !!}
                                </div>
                                {{--                                <div class="profile-info p-0 d-flex align-items-center flex-wrap">--}}
                                {{--                                    <strong class="f_w_600 mb-0 mr-3">Contact Us:</strong>--}}
                                {{--                                    <ul class="bg-transparent p-0 m-0">--}}
                                {{--                                        <li><a href="#"><i class="fab fa-skype"></i></a></li>--}}
                                {{--                                        <li><a href="#"><i class="fab fa-discord"></i></a></li>--}}
                                {{--                                        <li><a href="#"><i class="fab fa-facebook-messenger"></i></a></li>--}}
                                {{--                                    </ul>--}}
                                {{--                                </div>--}}
                                @if($user->userSkill && $user->userSkill->skills)
                                    <div class="skiils flex-wrap">
                                        <strong class="f_w_600 mb-0">{{__('profile.skills')}}: </strong>
                                        <ul>
                                            @foreach(explode(',',$user->userSkill->skills) as $skill)
                                                <li>{{$skill}}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </div>
                        </div>
                        @if($section_course_tab)
                            {{--course tab--}}
                            <div class="tab-pane fade" id="pills-course" role="tabpanel"
                                 aria-labelledby="pills-profile-tab">
                                @if($courses->count() > 0)
                                    <div class="courses_area m-0 p-0">
                                        <div class="row">
                                            @foreach ($courses as $course)
                                                <div class="col-lg-6 col-xl-4">
                                                    <div class="couse_wizged">
                                                        <a href="{{courseDetailsUrl(@$course->id,@$course->type,@$course->slug)}}">
                                                            <div class="thumb">

                                                                <div class="thumb_inner lazy"
                                                                     data-src="{{ getCourseImage($course->thumbnail) }}">
                                                                </div>
                                                                <x-price-tag :price="$course->price"
                                                                             :discount="$course->discount_price"/>
                                                            </div>
                                                        </a>
                                                        <div class="course_content">
                                                            <a href="{{courseDetailsUrl(@$course->id,@$course->type,@$course->slug)}}">

                                                                <h4 class="noBrake" title=" {{$course->title}}">
                                                                    {{$course->title}}
                                                                </h4>
                                                            </a>
                                                            <div class="rating_cart">
                                                                <div class="rateing">
                                                                    <span>{{$course->total_rating}}/5</span>

                                                                    <i class="fas fa-star"></i>
                                                                </div>
                                                                @if(!onlySubscription())
                                                                    @auth()
                                                                        @if(!$course->isLoginUserEnrolled && !$course->isLoginUserCart)
                                                                            <a href="#" class="cart_store"
                                                                               data-id="{{$course->id}}">
                                                                                <i class="fas fa-shopping-cart"></i>
                                                                            </a>
                                                                        @endif
                                                                    @endauth
                                                                    @guest()
                                                                        @if(!$course->isGuestUserCart)
                                                                            <a href="#" class="cart_store"
                                                                               data-id="{{$course->id}}">
                                                                                <i class="fas fa-shopping-cart"></i>
                                                                            </a>
                                                                        @endif
                                                                    @endguest
                                                                @endif
                                                            </div>
                                                            <div class="course_less_students">
                                                                <a>
                                                                    <i class="ti-agenda"></i> {{count($course->lessons)}}
                                                                    {{__('frontend.Lessons')}}</a>
                                                                <a>
                                                                    <i class="ti-user"></i> {{$course->total_enrolled}} {{__('frontend.Students')}}
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach

                                        </div>
                                    </div>
                                @else
                                    @include(theme('profile._no_data'))
                                @endif
                            </div>
                        @endif
                        @if($section_instructor_tab)
                            {{--instructor tab--}}
                            <div class="tab-pane fade" id="pills-instructor" role="tabpanel"
                                 aria-labelledby="pills-contact-tab">
                                <div class="instractors_wrapper p-0 m-0">
                                    @if($instructors->count() > 0)
                                        <div class="row">
                                            @foreach($instructors as $instructor)
                                                <div class="col-md-6 col-lg-4 col-xl-3">
                                                    <div class="single_instractor mb_30">
                                                        <a href="{{route('users.profile',$instructor->id)}}"
                                                           class="thumb">
                                                            <img
                                                                src="{{getProfileImage(@$instructor->image)}}"
                                                                alt="">
                                                        </a>
                                                        <a href="{{route('users.profile',$instructor->id)}}">
                                                            <h4>{{$instructor->name}}</h4></a>
                                                        @if($instructor->job_title)
                                                            <span>{{@$instructor->job_title}}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        @include(theme('profile._no_data'))
                                    @endif
                                </div>
                            </div>
                        @endif
                        @if($section_education_tab)
                            <div class="tab-pane fade" id="pills-education" role="tabpanel"
                                 aria-labelledby="pills-contact-tab">
                                @if($user->userEducations->count() > 0)
                                    <ul class="list-group list-group-flush">
                                        @foreach($user->userEducations as $education )
                                            <li class="list-group-item d-flex justify-content-between align-items-center pl-0">
                                                <div>
                                                    <h4 class="mb-1 f_w_600">{{$education->institution}}</h4>
                                                    <p class="text-muted f_w_500">{{$education->degree}}</p>
                                                    <small
                                                        class="text-muted">{{showDate($education->start_date)}} @if($education->end_date)
                                                            -
                                                        @endif {{showDate($education->end_date)}}</small>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    @include(theme('profile._no_data'))
                                @endif
                            </div>
                        @endif
                        @if($section_experience_tab)
                            <div class="tab-pane fade" id="pills-experience" role="tabpanel"
                                 aria-labelledby="pills-contact-tab">
                                @if($user->userExperiences->count() > 0)
                                    <ul class="list-group list-group-flush">

                                        @foreach($user->userExperiences as $experience )
                                            <li class="list-group-item d-flex justify-content-between align-items-center pl-0">
                                                <div>
                                                    <h4 class="mb-1 f_w_600">{{$experience->title}}</h4>
                                                    <p class="text-muted f_w_500">{{$experience->company_name}}</p>
                                                    @if($experience->duration())
                                                        <small class="text-muted">{{showDate($experience->start_date)}}
                                                            - {{$experience->currently_working?'Present':showDate($experience->end_date)}}
                                                            [ {{$experience->duration()}} ]</small>
                                                    @endif
                                                </div>
                                            </li>
                                        @endforeach


                                    </ul>
                                @else
                                    @include(theme('profile._no_data'))
                                @endif
                            </div>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </div>
    <!-- profile info -->
@endsection

