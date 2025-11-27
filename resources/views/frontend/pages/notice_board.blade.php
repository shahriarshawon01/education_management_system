@extends('frontend.layouts.master')

@section('title','Notice Board')

@push('css')
    <style> body { margin: 0; padding: 0; z-index: 1; } ::-webkit-scrollbar { width: 10px; background-color: #fff; border-radius: 5px; } ::-webkit-scrollbar-thumb { background: linear-gradient(transparent, #4A8BDF); border-radius: 5px; } ::-webkit-scrollbar-thumb:hover { background: linear-gradient(transparent, #4A8BDF); } .useful-link li { width: 100% !important; } .toolbar-share-icon-active { border-bottom: 2px solid #FFFFFF; } </style>
    <style> .page-header { background-image: url(front/images/bg/page-header-bg.jpg); background-size: cover; background-position: center; margin: 0px; padding: 0px; border: none; } .section-notch:before { content: ""; position: absolute; background-repeat: repeat-x; display: block; top: 0; width: 100%; height: 7px; z-index: 2; } .page-header .overlay { padding: 25px 0px; background-color: rgba(125, 172, 230, 0.8); text-align: center; } .notice-section { background-image: url(https://diu.ac/front/images/bg/dot-grid.png) !important; background-attachment: fixed; background-position: center top; padding: 70px 0 50px !important}
    .pagination .page-item > * {
            width: 35px !important;
            height: 35px !important;
            line-height: 35px !important;
            font-size: 16px !important;
            color: #4A8BDF !important;
        }
        .page-item.active .page-link {
            color: #fff !important;
        }
        
     </style>
@endpush

@section('content')
<section class="page-header section-notch">

<div class="container">
    <div class="overlay mt-2" style="padding: 15px 0px !important">
       <div class="container">
          <div class="col-md-12 text-center">
             <h1 class="page-title m-0">Notice Board</h1>
             <ul>
                <li><a class="active" href="{{url('/')}}">Home</a> / <span style="color: #FFFFFF!important;">Notice Board</span></li>
             </ul>
          </div>
       </div>
    </div>
</div>
   
 </section>
 <div id="latest_news" class="latest_news notice-section">
    <div class="container">
       <div class="row">
           <div class="col-md-6">
               <h2 class="title">OUR LATEST NOTICE</h2>
               <div class="latest_notice_box">
                @foreach ($notices as $notice)
                    <div class="notice_items d-flex align-items-center">
                        <div class="notice_date">
                            <div class="date">
                                <span>{{ \Carbon\Carbon::parse($notice->start_date)->format('d M, Y') }}</span>
                            </div>
                        </div>
                        <div class="notice_desc">
                            <h4 class="des_title"> <a href="{{url('notice_details',$notice->id)}}" class="text-dark">{{Str::limit($notice->title ?? '',45, '...') }}</a>
                            </h4>
                        </div>
                    </div>
                @endforeach
                {{$notices->links()}}
               </div>
           </div>
           <div class="col-md-6">
                <h2 class="title">OUR LATEST EVENTS</h2>
                <div class="latest_notice_box">
                    @foreach ($events as $event)
                        <div class="notice_items d-flex align-items-center">
                            <div class="notice_date">
                                <div class="date">
                                    <span>
                                        {{ \Carbon\Carbon::parse($notice->start_date)->format('d M, Y') }}
                                    </span>
                                </div>
                            </div>
                            <div class="notice_desc">
                                <h4 class="des_title"> <a href="{{url('event_details',$event->slug)}}" class="text-dark">{{Str::limit($event->title ?? '',45, '...') }}</a></h4>
                            </div>
                        </div>
                    @endforeach
                    {{$events->links()}}
               </div>
           </div>
       </div>
    </div>
 </div>
@endsection

@push('script')
    <script>
        window.setTimeout(function() {
            var alert = document.getElementById('success-alert');
            if (alert) {
                alert.classList.remove('show');
                alert.classList.add('fade');
                setTimeout(function() {
                    alert.remove();
                }, 150);
            }
        }, 5000);
    </script>
@endpush