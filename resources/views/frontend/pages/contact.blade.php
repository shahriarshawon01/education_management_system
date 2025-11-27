@extends('frontend.layouts.master')

@section('title', 'Contact')

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
</style>
@endpush

@section('content')
<section class="page-header section-notch">

    <div class="container">
        <div class="overlay mt-2" style="padding: 15px 0px !important">
            <div class="container">
                <div class="col-md-12 text-center">
                    <h1 class="page-title m-0">Contact</h1>
                    <ul>
                        <li><a class="active" href="{{ url('/') }}">Home</a> / <span
                                style="color: #FFFFFF!important;">Contact</span></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>


</section>
<div class="contact-page-section sec-spacer">
    <div class="container">
        <div class="sec-title-2 mb-50 text-center">
            <h2>OUR CAMPUS</h2>
        </div>
        <div class="row contact-address-section">
            <div class="col-md-6 pl-0 mx-auto">
                <div class="contact-info contact-address">
                    <i class="fa fa-map-marker"></i>
                    <h3>Permanent Campus</h3>
                    <p>{{ $contact->address ?? '' }}</p>
                    <p>{{ $contact->phone ?? '' }}</p>
                    <p><a href="mailTo:{{ $contact->email ?? '' }}"><span></span>{{ $contact->email ?? '' }}</a></p>
                    <p><a href="{{ $contact->domain ?? '' }}" target="_blank">{{ $contact->domain ?? '' }}</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="contact-page-section" style="padding: 30px 0">
    <div class="container">
        <div class="contact-comment-section">
            <h3>Have Some Question ?</h3>
            @if (session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert" id="success-alert">
                <strong>Success!</strong> {{ session()->get('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif
            <form id="contact-form" action="{{ route('contact_store') }}" method="POST">
                @csrf
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="inputEmail4">First Name <span class="text-danger">*</span></label>
                        <input type="text" name="first_name" value="{{ old('first_name') }}" class="form-control"
                            id="inputEmail4" placeholder="Enter First Name">
                        @if ($errors->has('first_name'))
                        <div class="text-danger">{{ $errors->first('first_name') }}</div>
                        @endif
                    </div>
                    <div class="form-group col-md-6">
                        <label for="inputEmail4">Last Name <span class="text-danger">*</span></label>
                        <input type="text" name="last_name" value="{{ old('last_name') }}" class="form-control"
                            id="inputEmail4" placeholder="Enter Last Name">
                        @if ($errors->has('last_name'))
                        <div class="text-danger">{{ $errors->first('last_name') }}</div>
                        @endif
                    </div>
                    <div class="form-group col-md-6">
                        <label for="inputEmail4">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" value="{{ old('email') }}" class="form-control"
                            id="inputEmail4" placeholder="Enter Email">
                        @if ($errors->has('email'))
                        <div class="text-danger">{{ $errors->first('email') }}</div>
                        @endif
                    </div>
                    <div class="form-group col-md-6">
                        <label for="inputEmail4">Phone <span class="text-danger">*</span></label>
                        <input type="number" name="phone" value="{{ old('phone') }}" class="form-control"
                            id="inputEmail4" placeholder="Enter Phone">
                        @if ($errors->has('phone'))
                        <div class="text-danger">{{ $errors->first('phone') }}</div>
                        @endif
                    </div>
                    <div class="form-group col-md-12">
                        <label for="inputPassword4">Subject <span class="text-danger">*</span></label>
                        <input type="text" name="subject" value="{{ old('subject') }}" class="form-control"
                            id="inputPassword4" placeholder="Enter Subject">
                        @if ($errors->has('subject'))
                        <div class="text-danger">{{ $errors->first('subject') }}</div>
                        @endif
                    </div>
                    <div class="form-group col-md-12">
                        <label for="inputPassword4">Message <span class="text-danger">*</span></label>
                        <textarea name="message" cols="30" rows="5" class="form-control" placeholder="Enter Message" required>{{ old('message') }} </textarea>
                        @if ($errors->has('message'))
                        <div class="text-danger">{{ $errors->first('message') }}</div>
                        @endif
                    </div>
                    <div class="form-group col-md-4 col-sm-12 offset-lg-4 offset-md-4">
                        <button type="submit" class="btn btn-secondary btn-block contact-button">Send <i
                                class="fa fa-rocket"></i></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="contact-page-section sec-spacer">
    <iframe src="{{ $contact->map_link ?? '' }}" width="100%" height="500"></iframe>

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
            }, 500);
        }
    }, 5000);
</script>
@endpush