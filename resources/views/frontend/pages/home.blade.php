@extends('frontend.layouts.master')

@section('title', 'Welcome to Home')

@push('css')
@endpush

@section('content')
    @include('frontend.layouts.slider')
    {{-- <div class="services_wrap rs-services rs-services-style1">
        <div class="container">
            <div class="row">
                @foreach ($services as $service)
                    <div class="col-sm-12 col-md-6 col-lg-4 px-0">
                        <div class="servies_box">
                            <div class="service_icon" style="margin: 0 !important">
                                <img src="{{asset('storage/app/public/'.$service->image)}}" alt="Service-image" style="width: 50px">
                            </div>
                            <div class="content ml-2">
                                <h4 class="title">{{$service->title ?? ''}}</h4>
                                <p class="desc" style="text-align: initial !important; font-size: 16px !important; line-height: 22px !important; font-weight: 400 !important;">{{$service->description ?? ''}}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div> --}}

    <div class="container">
        <div class="row">
            <div class="col-md-10">
                <div id="rs-about" class="rs-about sec-spacer about-us" style="padding-top: 50px">
                    <div class="container">
                        <div class="sec-title mb-50 text-center">
                            <h2>ABOUT US</h2>
                        </div>
                        <div class="row">
                            @php
                                $school_info = school_info();
                            @endphp
                            <div class="col-lg-6 col-md-12">
                                <div class="about-img rs-animation-hover">
                                    @if ($school_info && $school_info->logo)
                                        <img src="{{ asset('storage/app/public/' . $school_info->logo ?? '') }}"
                                            alt="{{ $school_info->title ?? '' }}">
                                    @endif

                                    @if ($video && $video->video_link)
                                        <!-- <a class="popup-youtube rs-animation-fade" href="{{ $video->video_link }}" title="Video Icon"></a> -->
                                        <!-- <a class="popup" href="{{ $video->video_link }}" target="_blank" title="Video Icon"> -->
                                        <a class="popup-youtube rs-animation-fade" href="{{ $video->video_link }}"
                                            title="Video Icon"></a>
                                        <div class="overly-border"></div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-12">
                                <div class="about-desc">
                                    <h3>WELCOME TO {{ $school_info->title ?? '' }}</h3>
                                    <p>{{ Str::limit($about_us->value ?? '', 500, '.') }} <a
                                            href="{{ route('about_institute') }}"> See more..</a></p>
                                </div>
                                <div id="accordion" class="rs-accordion-style1">
                                    <div class="card">
                                        <div class="card-header" id="ourMission">
                                            <h3 class="acdn-title collapsed" data-toggle="collapse"
                                                data-target="#collapseOurMission" aria-expanded="true"
                                                aria-controls="collapseOurMission"> Our Mission & Vision </h3>
                                        </div>
                                        <div id="collapseOurMission" class="collapse" aria-labelledby="ourMission"
                                            data-parent="#accordion">
                                            <div class="card-body">{{ $mission_vission->value ?? '' }}</div>
                                        </div>
                                    </div>

                                    <div class="card">
                                        <div class="card-header mb-0" id="itsFounder">
                                            <h3 class="acdn-title collapsed" data-toggle="collapse"
                                                data-target="#collapseThree" aria-expanded="false"
                                                aria-controls="collapseThree">Founder Info</h3>
                                        </div>
                                        <div id="collapseThree" class="collapse" aria-labelledby="itsFounder"
                                            data-parent="#accordion">
                                            <div class="card-body"> {{ $founder_info->founder_info ?? '' }} </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="container">
                    <div id="latest_news" class="latest_news notice-section pt-5">
                        <div class="container">
                            <div class="row">
                                <div class="col-md-6">
                                    <h2 class="title">OUR LATEST NOTICE</h2>
                                    <div class="latest_notice_box">
                                        @foreach ($notices as $notice)
                                            <div class="notice_items d-flex align-items-baseline">
                                                <div class="notice_date" style="margin-right: 15px">
                                                    <div class="date">
                                                        <span>{{ \Carbon\Carbon::parse($notice->created_at ?? '')->format('d M, Y') }}</span>
                                                    </div>
                                                </div>
                                                <div class="notice_desc">
                                                    <h4 class="des_title"> <a
                                                            href="{{ url('notice_details', $notice->id) }}"
                                                            class="text-dark">{{ Str::limit($notice->title ?? '', 45, '...') }}</a>
                                                    </h4>
                                                </div>
                                            </div>
                                        @endforeach
                                        @if (count($notices) > 0)
                                            <div class="notice_btn_box">
                                                <div class="notice_btn">
                                                    <a href="{{ url('noticeboard') }}"
                                                        class="btn float-right btn-success">See more...</a>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h2 class="title">OUR LATEST EVENTS</h2>
                                    <div class="latest_notice_box">
                                        @foreach ($events as $event)
                                            <div class="notice_items d-flex align-items-baseline">
                                                <div class="notice_date" style="margin-right: 15px">
                                                    <div class="date">
                                                        <span>{{ \Carbon\Carbon::parse($event->created_at ?? '')->format('d M, Y') }}</span>
                                                    </div>
                                                </div>
                                                <div class="notice_desc">
                                                    <h4 class="des_title"> <a
                                                            href="{{ url('event_details', $event->slug) }}"
                                                            class="text-dark">{{ Str::limit($event->title ?? '', 45, '...') }}</a>
                                                    </h4>
                                                </div>
                                            </div>
                                        @endforeach
                                        @if (count($events) > 0)
                                            <div class="notice_btn_box">
                                                <div class="notice_btn">
                                                    <a href="{{ url('noticeboard') }}"
                                                        class="btn float-right btn-success">See more...</a>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="col-md-2">
                <div class="right_sidebar pt-5">

                    <div class="card mb-3">
                        <div class="card-title" style="background-color:#4A8BDF; color: #fff; padding: 5px">
                            <h5 class="text-white mb-0 text-center">Founder Executive Director, TMSS</h5>
                        </div>
                        <div class="card-body">
                            <img src="{{ asset('storage/app/public/uploads/founder.jpg') }}" alt="Founder">
                            <p class="mb-0">Prof. Dr. Hosne-Ara Begum</p>
                        </div>
                    </div>

                    <div class="card mb-3">
                        <div class="card-title" style="background-color:#4A8BDF; color: #fff; padding: 5px">
                            <h5 class="text-white mb-0 text-center">Chairman, TPSC Governing Body</h5>
                        </div>
                        <div class="card-body">
                            <img src="{{ asset('storage/app/public/uploads/chairman.jpg') }}" alt="Founder">
                            <p class="mb-0">Aysha Begum</p>
                        </div>
                    </div>

                    <div class="card mb-3">
                        <div class="card-title" style="background-color:#4A8BDF; color: #fff; padding: 5px">
                            <h5 class="text-white mb-0 text-center">Principal</h5>
                        </div>
                        <div class="card-body">
                            <img src="{{ asset('storage/app/public/uploads/principal.jpg') }}" alt="Founder">
                            <p class="mb-0" style="text-align: center;">Prof. Md. Shahjahan Ali</p>
                        </div>
                    </div>

                    <div class="card mb-3">
                        <div class="card-title" style="background-color:#4A8BDF; color: #fff; padding: 5px">
                            <h5 class="text-white mb-0 text-center">Vice-Principal</h5>
                        </div>
                        <div class="card-body">
                            <img src="{{ asset('storage/app/public/uploads/vice_principal.jpg') }}" alt="Founder">
                            <p class="mb-0">Mrs. Gulshan Ara Parvin</p>
                        </div>
                    </div>
                    <div class="card mb-3">
                        <div class="card-title" style="background-color:#4A8BDF; color: #fff; padding: 5px">
                            <h5 class="text-white mb-0 text-center">Associate Professor</h5>
                        </div>
                        <div class="card-body">
                            <img src="{{ asset('storage/app/public/uploads/assistant_professor.jpg') }}" alt="Founder">
                            <p class="mb-0">Md. Anwar Kamal</p>
                        </div>
                    </div>
                    <div class="card mb-3">
                        <div class="card-title" style="background-color:#4A8BDF; color: #fff; padding: 5px">
                            <h5 class="text-white mb-0 text-center">Admin Officer</h5>
                        </div>
                        <div class="card-body">
                            <img src="{{ asset('storage/app/public/uploads/admin_officer.jpg') }}" alt="Founder">
                            <p class="mb-0">Md.Momthazur Rahman Sarker</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div id="rs-about" class="rs-about sec-spacer about-section mt-5 back-image"
        style="background-image: url({{ asset('frontend_assets/images/dot-grid.png') }})">
        <div class="container">
            <div class="sec-title mb-50 text-center">
                <h2>Campus / Other Facilities</h2>
            </div>
            <div class="row">
                <div class="col-lg-6 col-md-12">
                    <div class="about-desc">
                        <h3>WELCOME TO OUR SCHOOL CAMPUS</h3>
                    </div>
                    <div class="about-desc">
                        @if ($school_info && $school_info->logo)
                            <img src="{{ asset('storage/app/public/' . $school_info->logo) }}"
                                alt="{{ $school_info->title ?? '' }}" height="100%; width: 100%">
                        @endif
                    </div>
                </div>
                <div class="col-lg-6 col-md-12">
                    <div class="facility_title">
                        <h3>Other Facilities</h3>
                    </div>
                    <div id="facilitiesAccordion" class="rs-accordion-style1">
                        @foreach ($facilities as $key => $facility)
                            <div class="card">
                                <div class="card-header" id="PermanentCampus{{ $key }}">
                                    <h3 class="acdn-title collapsed" data-toggle="collapse"
                                        data-target="#facilitiesAccordion{{ $key }}" aria-expanded="true"
                                        aria-controls="facilitiesAccordion{{ $key }}">
                                        {{ $facility->title ?? '' }} </h3>
                                </div>
                                <div id="facilitiesAccordion{{ $key }}" class="collapse"
                                    aria-labelledby="PermanentCampus{{ $key }}" data-parent="#campusAccordion">
                                    <div class="card-body">{{ $facility->description ?? '' }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="school_life_wrap" class="sec-color sec-spacer back-image"
        style="background-image: url({{ asset('frontend_assets/images/dot-grid.png') }})">
        <div class="container">
            <div class="sec-title mb-50 text-center">
                <h2>Our School Life</h2>
            </div>
            <div class="school_life owl-carousel owl-theme">
                @foreach ($school_lifes as $school_life)
                    <div class="item">
                        <div class="item_box">
                            <div class="image_box">
                                <a href=""> <img src="{{ asset('storage/app/public/' . $school_life->image) }}"
                                        alt="Carousel Image" class="img-fluid"
                                        style="height: 350px; object-fit: cover"></a>
                                <div class="school_item">
                                    <h4 class="m-0">{{ $school_life->title ?? '' }}</h4>
                                </div>
                            </div>
                            <div class="overlay content_box text-center">
                                <div>
                                    <h3 class="text-white">{{ $school_life->title ?? '' }}</h3>
                                    <p>{{ $school_life->description ?? '' }}</p>
                                    <div class="border_overlay"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="container">
        <div class="rs-counter pt-45 pb-70 back-image universitiy-achievement mt-3"
            style="background-image: linear-gradient(#FFFFFF, #4A8BDF);">
            <div class="container">
                <div class="white-text mb-30 text-center">
                    <h2 class="text-white">Current Statistics: Our Strength</h2>
                </div>
                <div class="row">
                    <div class="col-lg-3 col-md-6">
                        <div class="rs-counter-list">
                            <div class="count-icon"><i class="fa fa-users text-white fa-3x" aria-hidden="true"></i></div>
                            <h2 class="counter-number plus">{{ $total_student }}</h2>
                            <h4 class="counter-desc">Total Students</h4>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="rs-counter-list">
                            <div class="count-icon"><i class="fa fa-user-o text-white fa-3x" aria-hidden="true"></i>
                            </div>
                            <h2 class="counter-number plus">{{ $current_student }}</h2>
                            <h4 class="counter-desc">Current Students</h4>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="rs-counter-list">
                            <div class="count-icon"><i class="fa fa-user-secret text-white fa-3x" aria-hidden="true"></i>
                            </div>
                            <h2 class="counter-number plus">{{ $teachers }}</h2>
                            <h4 class="counter-desc">Active teachers</h4>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="rs-counter-list">
                            <div class="count-icon"><i class="fa fa-users text-white fa-3x" aria-hidden="true"></i></div>
                            <h2 class="counter-number plus">{{ $staffs }}</h2>
                            <h4 class="counter-desc">Active staffs</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div id="rs-partner" class="rs-partner pt-30 pb-70 back-image">

    </div>

@endsection


@push('script')
@endpush
