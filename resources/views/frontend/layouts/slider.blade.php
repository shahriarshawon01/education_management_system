<div class="container">
   <div id="rs-slider" class="slider-overlay-2">
      <div id="home-slider">
        @foreach ($sliders as $slider)
           <div class="item active">
            <div style="height: 450px">
               <img class="image-fluid" src="{{asset('storage/app/public/'.$slider->image)}}" alt="{{$slider->title ?? ''}}" style="object-fit: cover; height: 100%">
               <!-- <img class="image-fluid" src="{{asset('storage/'.$slider->image)}}" alt="{{$slider->title ?? ''}}" style="object-fit: cover; height: 100%"> -->
            </div>
              <div class="slide-content">
                 <div class="display-table">
                    <div class="display-table-cell">
                       <div class="container text-center" style="margin-top: 230px">
                          <h1 class="slider-title" data-animation-in="fadeInLeft" data-animation-out="animate-out">{{$slider->title ?? ''}}</h1>
                          <p data-animation-in="fadeInUp" data-animation-out="animate-out" class="slider-desc">{{$slider->description ?? ''}}</p>
                          {{-- @if (Auth::check())
                             <a href="{{url('admission_request')}}" class="sl-get-started-btn" data-animation-in="lightSpeedIn" data-animation-out="animate-out">APPLY NOW</a>
                          @else
                             <a href="{{url('applicant_register')}}" class="sl-get-started-btn" data-animation-in="lightSpeedIn" data-animation-out="animate-out">APPLY NOW</a>
                          @endif --}}
                       </div>
                    </div>
                 </div>
              </div>
           </div>
         @endforeach
      </div>
   </div>
</div>

