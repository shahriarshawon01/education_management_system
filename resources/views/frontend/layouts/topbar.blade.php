<div class="container py-1" style="background-color: #4a8bdf;">
    @php
        $school_info = school_info();
        $school_logo = school_logo();
    @endphp
    <div class="container">
        <div class="row align-items-center">
            <!-- Left: Logo + Title -->
            <div class="col-md-6 text-start text-white">
                <div class="d-flex align-items-center gap-2">
                    @if ($school_logo)
                        <img style="height: 45px;" src="{{ asset('storage/app/public/' . $school_logo->value) }}"
                            alt="{{ $school_logo->display_name }}">
                    @else
                        <img style="height: 45px;" src="{{ asset('frontend_assets/images/no_logo.png') }}"
                            alt="Logo">
                    @endif
                    <span class="fw-semibold fs-5 uppercase">{{ $school_info->title ?? '' }}</span>
                </div>
            </div>
            <!-- Right: Login Button -->
            {{-- <div class="col-md-6 text-end" style="text-align: right;">
                <a href="{{ route('login') }}" class="custom-login-btn">
                    EMPLOYEE LOGIN
                </a>
            </div> 
            <div class="col-md-6 text-end" style="text-align: right;">
                <a href="{{ route('student_login') }}" class="custom-login-btn">
                    STUDENT LOGIN
                </a>
            </div> --}}
             <div class="col-md-6 text-end d-flex justify-content-end gap-2">
            <a href="{{ route('login') }}" class="custom-login-btn">
               EMPLOYEE LOGIN
            </a>
            <a href="{{ route('student_login') }}" class="custom-login-btn">
              STUDENT LOGIN
            </a>
         </div>
        </div>
    </div>
</div>


{{-- <div class="container">
   <div class="rs-toolbar">
      <div class="container">
         @php
            $school_info = school_info();
         @endphp
         <div class="row">
            <div class="col-6 col-sm-6 col-md-6">
               <div class="rs-toolbar-left">
                  <div class="welcome-message">
                     <div class="large-device"><a href="javascript:void(0)"><i class="fa fa-bank"></i><span>Welcome to {{$school_info->title ?? ''}}</span></a></div>
                     <div class="extra-small-device"><a href="javascript:void(0)"><i class="fa fa-bank"></i><span>Welcome to {{$school_info->title ?? ''}}</span></a></div>
                     <div class="medium-device"><a href="javascript:void(0)"><i class="fa fa-bank"></i><span>Welcome to {{$school_info->title ?? ''}}</span></a></div>
                  </div>
               </div>
            </div>
            <div class="col-6 col-sm-6 col-md-6">
               <div class="rs-toolbar-right">
                  <div class="toolbar-share-icon">
                     <ul>
                        <span class="large-device">
                           @if (Auth::check())
                              <li><a href="{{url('admission_request')}}">Admission</a></li>
                              @else
                                 <li><a href="{{url('applicant_register')}}">Admission</a></li>
                              @endif
                           <li><a href="{{route('login')}}">Login</a></li>
                          
                        </span>
                        <span class="medium-device">
                           @if (Auth::check())
                           <li><a href="{{url('admission_request')}}">Admission</a></li>
                           @else
                              <li><a href="{{url('applicant_register')}}">Admission</a></li>
                           @endif
                           <li><a href="{{route('login')}}">Login</a></li>
                          
                        </span>
                        <span class="small-device">
                           @if (Auth::check())
                           <li><a href="{{url('admission_request')}}">Admission</a></li>
                           @else
                              <li><a href="{{url('applicant_register')}}">Admission</a></li>
                           @endif
                           <li><a href="{{route('login')}}">Login</a></li>
                          
                        </span>
                        <span class="extra-small-device">
                           <div class="">
                              <ul>
                                 @if (Auth::check())
                              <li><a href="{{url('admission_request')}}">Admission</a></li>
                              @else
                                 <li><a href="{{url('applicant_register')}}">Admission</a></li>
                              @endif
                                <li><a href="{{route('login')}}">Login</a></li>
                                 
                              </ul>
                           </div>
                        </span>
                     </ul>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
 --}}

<style>
    .custom-login-btn {
        display: inline-block;
        padding: 6px 22px;
        background: linear-gradient(145deg, #3a6fb0, #3263a5);
        color: #fff;
        font-weight: bold;
        border-radius: 50px;
        text-decoration: none;
        font-size: 15px;
        transition: all 0.3s ease;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        letter-spacing: 1px;
    }

    .custom-login-btn:hover {
        background: linear-gradient(145deg, #2f5f95, #2a5489);
        transform: scale(1.04);
        color: #fff;
    }
</style>
