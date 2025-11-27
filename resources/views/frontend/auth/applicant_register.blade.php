@extends('frontend.layouts.master')

@section('title', 'Applicant Register')

@push('css')
    <style>
        body {
            margin: 0;
            padding: 0;
            z-index: 1;
        }

        ::-webkit-scrollbar {
            width: 10px;
            background-color: #fff;
            border-radius: 5px;
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(transparent, #4A8BDF);
            border-radius: 5px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(transparent, #4A8BDF);
        }

        .useful-link li {
            width: 100% !important;
        }

        .toolbar-share-icon-active {
            border-bottom: 2px solid #FFFFFF;
        }
    </style>
    <link rel="stylesheet" type=text/css href="front/css/timeline.css">
    <style>
        .page-header {
            background-image: url(front/images/bg/page-header-bg.jpg);
            background-size: cover;
            background-position: center;
            margin: 0px;
            padding: 0px;
            border: none;
        }

        .section-notch:before {
            content: "";
            position: absolute;
            background-repeat: repeat-x;
            display: block;
            top: 0;
            width: 100%;
            height: 7px;
            z-index: 2;
        }

        .page-header .overlay {
            padding: 25px 0px;
            background-color: rgba(125, 172, 230, 0.8);
            text-align: center;
        }

        .documents-title {
            font-size: 22px;
            text-decoration: underline;
            color: #4A8BDF;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .contact-address p {
            font-size: 13px;
        }
        .error{
            color: rgb(197 16 16);
        }
    </style>
@endpush

@section('content')
    <div>
        <form method="POST" action="{{route('applicant.register_store')}}" style="max-width: 540px;margin: auto;padding: 70px 0;">
            @csrf
            <div class="card bg-light mb-2">
                <div class="card-header text-dark">
                    <h4 class="mb-0" style="color: #4d4d4d !important">REGISTRATION AS APPLICANT</h4>
                </div>
                <div class="card-body bg-secondary">
                    <div class="row">
                        <div class="col-12 mb-1">
                            <label for="name" style="color: #f0f0f0 !important">Username *</label>
                            <input type="text" name="username" class="form-control" value="{{old('username')}}" required
                                placeholder="Enter username">
                                @if($errors->has('username'))
                                    <div class="error">{{ $errors->first('username') }}</div>
                                @endif
                        </div>
                        <div class="col-12 mb-1">
                            <label for="name" style="color: #ececec !important">Email (Optional)</label>
                            <input type="email" name="email" value="{{old('email')}}" class="form-control" required
                                placeholder="Enter Email">
                                @if($errors->has('email'))
                                    <div class="error">{{ $errors->first('email') }}</div>
                                @endif
                        </div>
                        <div class="col-12 mb-1">
                            <label for="name" style="color: #ececec !important">Password *</label>
                            <input type="password" name="password" class="form-control" required
                                placeholder="Enter Password">
                                @if($errors->has('password'))
                                    <div class="error">{{ $errors->first('password') }}</div>
                                @endif
                        </div>
                        <div class="col-12 mb-1">
                            <label for="name" style="color: #ececec !important">Confirm Password *</label>
                            <input type="password" name="password_confirmation" class="form-control" required
                                placeholder="Enter Password again">
                                @if($errors->has('password_confirmation'))
                                    <div class="error">{{ $errors->first('password_confirmation') }}</div>
                                @endif
                        </div>
                    </div>
                </div>
            </div>
    
            <div class="col-lg-12 col-md-12 col-sm-12 mb-1 text-center">
                <button type="submit" class="btn btn-sm btn-primary">Submit</button>
            </div>
        </form>
    </div>
  

@endsection

@push('script')
@endpush
