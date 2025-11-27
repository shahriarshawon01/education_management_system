
<div class="container">
   <footer id="rs-footer" class="bg3 rs-footer" style="background-color: rgba(0, 0, 0, 0.8);margin-top: 250px;">
      <div class="container">
        @php
           $school_info = school_info();
           $school_logo = school_logo();
        @endphp
         <div>
            <div class="row footer-contact-desc">
               <div class="col-md-4">
                  <div class="contact-inner">
                     <i class="fa fa-map-marker"></i>
                     <h4 class="contact-title">Address</h4>
                     <p class="contact-desc"> {{$school_info->address ?? ''}} </p>
                  </div>
               </div>
               <div class="col-md-4">
                  <div class="contact-inner">
                     <i class="fa fa-phone"></i>
                     <h4 class="contact-title">Phone Number</h4>
                     <p class="contact-desc"><a class="contact-desc" href="tel:{{$school_info->address ?? ''}}">+88 {{$school_info->phone ?? ''}}</a></p>
                  </div>
               </div>
               <div class="col-md-4">
                  <div class="contact-inner">
                     <i class="fa fa-map-marker"></i>
                     <h4 class="contact-title">Email Address</h4>
                     <p class="contact-desc"><a class="contact-desc" href="mail:{{$school_info->email ?? ''}}"><span>{{$school_info->email ?? ''}}</span></a></p>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <div class="footer-top">
         <div class="container">
            <div class="row">
               <div class="col-lg-3 col-md-12">
                  <div class="about-widget" style="font-size: 15px; mb-2">
                    @if ($school_logo)
                       <img style="height: 45px; transform: scale(1.20);" src="{{ asset('storage/app/public/' . $school_logo->value) }}" alt="{{ $school_logo->display_name }}" style="width: 120px !important">
                    @else
                          <img style="height: 45px; transform: scale(1.20);" src="{{ asset('frontend_assets/images/no_logo.png') }}" alt="Default Logo" style="width: 120px !important">
                    @endif
                  </div>
                  <div style="font-size: 15px;">
                    <p class="mb-1">{{$school_info->title ?? '' }}</p>
                    <p class="mb-1">{{$school_info->address ?? '' }}</p>
                    <p class="mb-1">{{$school_info->phone ?? '' }}</p>
                 </div>
               </div>
               <div class="col-lg-5 col-md-12">
                 <h5 class="footer-title mb-3">Useful Links</h5>
                 <ul class="sitemap-widget" style="font-size: 16px;">
                    <li class="active"><a href="https://moedu.gov.bd/" target="_blank"><i class="fa fa-angle-right" aria-hidden="true"></i> Ministry of Education</a></li>
                    <li class="active"><a href="https://rajshahieducationboard.gov.bd/" target="_blank"><i class="fa fa-angle-right" aria-hidden="true"></i> Education Board Rajshahi</a></li>
                    <li class="active"><a href="https://tmss-bd.org/" target="_blank"><i class="fa fa-angle-right" aria-hidden="true"></i> TMSS</a></li>
                    <li class="active"><a href="https://tmssmedicalcollege.com/" target="_blank"><i class="fa fa-angle-right" aria-hidden="true"></i> TMSS Medical College</a></li>
                    <li class="active"><a href="https://tmss-ict.com/" target="_blank"><i class="fa fa-angle-right" aria-hidden="true"></i> TMSS-ICT</a></li>
                    <li class="active"><a href="http://pundrauniversity.ac.bd/" target="_blank"><i class="fa fa-angle-right" aria-hidden="true"></i> Pundra University of Science & Technology (PUST)</a></li>
                 </ul>
              </div>
               <div class="col-lg-4 col-md-12">
                  <h5 class="footer-title mb-3">OUR SITEMAP</h5>
                  <ul class="sitemap-widget" style="font-size: 16px;">
                     <li class="active"><a href="{{url('/')}}"><i class="fa fa-angle-right" aria-hidden="true"></i>Home</a></li>
                     <li><a href="{{url('/about-institute')}}"><i class="fa fa-angle-right" aria-hidden="true"></i>About Institute</a></li>
                     <li><a href="{{url('/noticeboard')}}"><i class="fa fa-angle-right" aria-hidden="true"></i>Notice</a></li>
                     <li><a href="{{url('/noticeboard')}}"><i class="fa fa-angle-right" aria-hidden="true"></i>Events</a></li>
                     @if (Auth::check())
                        <li><a href="{{url('/admission_request')}}"><i class="fa fa-angle-right" aria-hidden="true"></i>Admission</a></li>
                     @else
                        <li><a href="{{url('/applicant_register')}}"><i class="fa fa-angle-right" aria-hidden="true"></i>Admission</a></li>
                     @endif
                    
                     <li><a href="{{url('/contact')}}"><i class="fa fa-angle-right" aria-hidden="true"></i>Contact</a></li>
                     <li><a href="#"><i class="fa fa-angle-right" aria-hidden="true"></i>Privacy Policy</a></li>
                  </ul>
               </div>
            </div>
         </div>
      </div>
      <div class="footer-bottom">
         <div class="container">
            <div class="copyright">
               <p class="text-center"> Copyright © TPSC & Developed By: <a href="https://tmss-bd.org/" target="_blank">TMSS ICT</a></p>
            </div>
         </div>
      </div>
   </footer>
</div>


 