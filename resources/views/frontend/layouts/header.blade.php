<div class="container">
   <header id="rs-header" class="rs-header">
      @php
         $school_info = school_info();
         $school_logo = school_logo();
      @endphp
       <div class="rs-header-top">
          <div class="container">
             <div class="row">
                <div class="col-md-4 col-sm-12">
                   <div class="header-contact">
                      <div id="info-details" class="widget-text">
                         <i class="fa fa-envelope-open-o"></i>
                         <div class="info-text"><a href="mailto:{{$school_info->email ?? ''}}"><span>Mail Us</span> <span style="font-weight: 400 !important">{{$school_info->email ?? ''}}</span> </a></div>
                      </div>
                   </div>
                </div>
                <div class="col-md-4 col-sm-12 top-header-logo">
                   <div class="logo-area text-center">
                     <a href="{{ url('/') }}">
                        @if ($school_logo)
                            <img style="height: 45px; transform: scale(1.20);" src="{{ asset('storage/app/public/' . $school_logo->value) }}" alt="{{ $school_logo->display_name }}">
                        @else
                            <img style="height: 45px; transform: scale(1.1);" src="{{ asset('frontend_assets/images/no_logo.png') }}" alt="Default Logo">
                        @endif
                    </a>
                  </div>
                </div>
                <div class="col-md-4 col-sm-12">
                   <div class="header-contact pull-right">
                      <div id="phone-details" class="widget-text">
                         <i class="fa fa-phone"></i>
                         <div class="info-text"><a href="tel:{{$school_info->phone ?? ''}}"><span>Call Us</span> +88 {{$school_info->phone ?? ''}} </a></div>
                      </div>
                   </div>
                </div>
             </div>
             <div class="row">
                <div class="col-3 offset-2">
                   <div class="logo-area mobile-logo">
                     <a href="{{ url('/') }}">
                        @if ($school_logo)
                            <img style="height: 45px; transform: scale(1.20);" src="{{ asset('storage/' . $school_logo->value) }}" alt="{{ $school_logo->display_name }}">
                        @else
                            <img style="height: 45px; transform: scale(1.1);" src="{{ asset('frontend_assets/images/no_logo.png') }}" alt="Default Logo">
                        @endif
                    </a>
                  </div>
                </div>
                <div class="col-5">
                   <div class="header-contact mobile-top-header-menu">
                      <div class="widget-text">
                         <div class="info-text"><a href="mailto:{{$school_info->email ?? ''}}"> <span>{{$school_info->email ?? ''}}</span> </a><br><a href="tel:{{$school_info->phone ?? ''}}"> +880 {{$school_info->phone ?? ''}} </a></div>
                      </div>
                   </div>
                </div>
             </div>
          </div>
       </div>
       <div class="menu-area menu-sticky">
          <div class="container">
             <div class="main-menu">
                <div class="row">
                   <div class="col-sm-12">
                      <a class="rs-menu-toggle"><i class="fa fa-bars"></i>Menu</a>
                      <nav class="rs-menu">
                         <ul class="nav-menu text-center">
                            <li class="{{Request::is('/') ? 'active' : ''}}"><a href="{{route('home')}}">Home</a></li>
                            <li class="menu-item-has-children dropdown">
                              <a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Institute
                              </a>
                              <div class="dropdown-menu">
                                <a class="dropdown-item" href="{{route('about_institute')}}">About The Institution</a>
                                <a class="dropdown-item" href="{{route('institute_history')}}">History</a>
                                <a class="dropdown-item" href="{{route('mission_vision')}}">Mission & Vission</a>
                                <a class="dropdown-item" href="{{route('ed_message')}}">Executive Director Message</a>
                                <a class="dropdown-item" href="{{route('chairman_message')}}">Chairman Message</a>
                                <a class="dropdown-item" href="{{route('principal_message')}}">Principle Message</a>
                                <a class="dropdown-item" href="{{route('vice_principal_message')}}">Vice Principle Message</a>
                                <a class="dropdown-item" href="{{route('md_message')}}">MD Message</a>
                                <a class="dropdown-item" href="{{route('assistant_professor_message')}}">Assitant Professor Message</a>
                              </div>
                            </li>
                            <li class="{{Request::is('noticeboard') ? 'active' : ''}}"><a href="{{route('noticeboard')}}">Notice Board</a></li>
{{--                             
                            @if (Auth::check())
                               <li><a href="{{url('admission_request')}}">Admission</a></li>
                            @else
                              <li><a href="{{url('applicant_register')}}">Admission</a></li>
                            @endif --}}
                           
                            <li><a href="{{route('live_class_schedule')}}">Live Class Schedule</a></li>
                           
                            <li class="{{Request::is('contact') ? 'active' : ''}}"><a href="{{asset('contact')}}">Contact</a></li>
                          @if (Auth::check())
                          <li class="menu-item-has-children dropdown" style="float: right">
                           <a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                              @auth
                                  {{Auth::user()->username ?? ''}}
                              @endauth
                           </a>
                           <div class="dropdown-menu">
                              @if(auth()->user()->type==7)
                                 <a class="dropdown-item" href="{{url('applicant_dashboard')}}">Applicant Dashboard</a>
                                 <a class="dropdown-item" href="{{url('applicant_logout')}}">Logout</a>
                                 @elseif (auth()->user()->type==0)   
                                 <a class="dropdown-item" href="{{url('admin/dashboard')}}">Super Admin Dashboard</a>
                                 <a class="dropdown-item" href="{{url('logout')}}">Logout</a>
                                 @elseif (auth()->user()->type==1)   
                                    <a class="dropdown-item" href="{{url('admin/dashboard')}}">Admin Dashboard</a>
                                    <a class="dropdown-item" href="{{url('logout')}}">Logout</a>
                                 @elseif (auth()->user()->type==5)   
                                    <a class="dropdown-item" href="#">Student Dashboard</a>
                                    <a class="dropdown-item" href="{{url('logout')}}">Logout</a>
                              @endif
                           </div>
                         </li>
                          @else
                            <li style="float: right"><a href="{{route('login')}}" class="header_login_btn">Sign In</a></li>
                          @endif
                          
                         </ul>
                      </nav>
                   </div>
                </div>
             </div>
          </div>
       </div>
    </header>
</div>