<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="utf-8">
      <meta http-equiv="x-ua-compatible" content="ie=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <title>@yield('title')</title>
      <meta name="application-name" content="School">
      <link rel="icon" sizes="512x512" href="images/icons/icon-512x512.png">
      
      <meta name="keywords" content="">
      <meta name="author" content="">
      <meta property="og:locale" content="en_US">
      <meta property="og:type" content="website">
      <link rel="shortcut icon" type="image/x-icon" href="favicons.png">
      <link rel="stylesheet" type="text/css" href="{{assets('frontend_assets/')}}/css/all.min.css">
      <link rel="stylesheet" type="text/css" href="{{assets('frontend_assets/')}}/css/custom.min.css">
      <link rel="stylesheet" type="text/css" href="{{assets('frontend_assets/')}}/css/owl.theme.default.min.css">
      <link rel="stylesheet" type="text/css" href="{{assets('frontend_assets/')}}/css/owl.carousel.min.css">
      
      <noscript><img height="1" width="1" style="display:none" src="tr?id=333868642744773&ev=PageView&noscript=1"></noscript>
      <style> body { margin: 0; padding: 0; z-index: 1; } ::-webkit-scrollbar { width: 10px; background-color: #fff; border-radius: 5px; } ::-webkit-scrollbar-thumb { background: linear-gradient(transparent, #4A8BDF); border-radius: 5px; } ::-webkit-scrollbar-thumb:hover { background: linear-gradient(transparent, #4A8BDF); } .useful-link li { width: 100% !important; } .toolbar-share-icon-active { border-bottom: 2px solid #FFFFFF; } </style>
      <style> .item img { width: 100%; max-height: 650px; object-fit: 100% 100%; } .notice-section { background-image: linear-gradient(#FFFFFF, #4A8BDF); } .about-section { background: #ddd; } .card { background: transparent !important; } .back-image { background-attachment: fixed; background-position: center top; } .slider-desc { max-width: 850px; margin: 0 auto; text-align: center; } .image-fluid { height: 1100px; } @media (max-width: 680px) { .image-fluid { height: 500px; } }</style>

      @stack('css')
      <link rel="stylesheet" href="http://cdn.bootcss.com/toastr.js/latest/css/toastr.min.css">
      <style>
         .page-title{
            color: #fcfcfc !important;
         }
         .header_login_btn{
            display: inline-block;
            width: 100px;
            height: auto;
            background-color: tomato;
            color: #fff;
            padding: 1px 15px;
            float: right !important;
         }
      </style>

   </head>
   <body class="home1" style="background-image: url({{asset('frontend_assets/images/dot-grid.png')}})">
         <div class="full-width-header">
            @include('frontend.layouts.topbar')
            @include('frontend.layouts.header')
         </div>
        
         @yield('content')

         @include('frontend.layouts.footer')

         <div id="scrollUp"><i class="fa fa-angle-up"></i></div>
   
      <script src="{{assets('frontend_assets/')}}/js/jquery.min.js"></script>
      {{-- <script src="{{asset('frontend_assets/')}}/js/bootstrap.min.js"></script>
      <script src="{{asset('frontend_assets/')}}/js/popper.min.js"></script> --}}
      <script src="{{assets('frontend_assets/')}}/js/all.min.js"></script>
      <script src="{{assets('frontend_assets/')}}/js/owl.carousel.js"></script>
      <script src="{{assets('frontend_assets/')}}/js/custom.js"></script>
      @stack('script')
      <script src="http://cdn.bootcss.com/toastr.js/latest/js/toastr.min.js"></script>
      {!! Toastr::message() !!}
   
   </body>
</html>