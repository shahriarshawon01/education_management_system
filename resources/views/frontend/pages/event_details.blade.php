@extends('frontend.layouts.master')

@section('title','Event Details')

@push('css')
    <style> body { margin: 0; padding: 0; z-index: 1; } ::-webkit-scrollbar { width: 10px; background-color: #fff; border-radius: 5px; } ::-webkit-scrollbar-thumb { background: linear-gradient(transparent, #4A8BDF); border-radius: 5px; } ::-webkit-scrollbar-thumb:hover { background: linear-gradient(transparent, #4A8BDF); } .useful-link li { width: 100% !important; } .toolbar-share-icon-active { border-bottom: 2px solid #FFFFFF; } </style>
    <link rel="stylesheet" type=text/css href="front/css/timeline.css">
    <style> .page-header { background-image: url(front/images/bg/page-header-bg.jpg); background-size: cover; background-position: center; margin: 0px; padding: 0px; border: none; } .section-notch:before { content: ""; position: absolute; background-repeat: repeat-x; display: block; top: 0; width: 100%; height: 7px; z-index: 2; } .page-header .overlay { padding: 25px 0px; background-color: rgba(125, 172, 230, 0.8); text-align: center; } .documents-title { font-size: 22px; text-decoration: underline; color: #4A8BDF; font-weight: 600; margin-bottom: 4px; } .contact-address p { font-size: 13px; } </style>
@endpush

@section('content')
<section class="page-header section-notch">
    <div class="container">
        <div class="overlay mt-2" style="padding: 15px 0px !important">
        <div class="container">
            <div class="col-md-12 text-center">
                <h1 class="page-title m-0">Event Details</h1>
                <ul>
                    <li><a class="active" href="{{url('/')}}">Home</a> / <span style="color: #FFFFFF!important;">Event</span></li>
                </ul>
            </div>
        </div>
        </div>
    </div>

 </section>

 <div class="notice_detail_section sec-spacer">
    <div class="container">
       <div class="row">
        <div class="col-12 pl-0">
            <h4 class="uppercase title pb-50 md-pb-30">{{$eventdetails->title ?? ''}}</h4>
        </div>
       </div>
       <div class="row">
          <div class="col-md-12 pl-0 mx-auto">
            <div class="notice_info">
                <h6>Published Date : {{Carbon\Carbon::parse($eventdetails->created_at ?? '')->format('d / F / y')}}</h6>
                @if ($eventdetails)
                    @if ($eventdetails->image)
                        <p>
                            {!! $eventdetails->description ?? '' !!} 
                            <a target="_blank" href="{{ asset('storage/app/public/'.$eventdetails->image) }}" download="{{ asset('storage/'.$eventdetails->image) }}">
                                <i class="ml-2 fa fa-download"></i> Download
                            </a>
                        </p>
                    @endif
                @endif
              
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