@extends('frontend.layouts.master')

@section('title','Principal Message')

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
             <h1 class="page-title m-0">Principal Message</h1>
             <ul>
                <li><a class="active" href="{{url('/')}}">Home</a> / <span style="color: #FFFFFF!important;">Principal Message</span></li>
             </ul>
          </div>
       </div>
    </div>
</div>
   
</section>

<div id="rs-about" class="rs-about sec-spacer about-us" style="padding-top: 80px">
    <div class="container" style="padding: 10px;">
        <div class="abt-title">
            <h2>Principal Message</h2>
        </div>
        <div class="about-desc">
          <img style="float: right; margin: -50px 0px 10px 10px;" width="20%" src="{{assets('frontend_assets/images/principal.jpg')}}">
            <p style="text-align: justify;">{{$principal_message->value ?? '' }} </p>
        </div>
    </div>
</div>

@endsection

@push('script')
 
@endpush