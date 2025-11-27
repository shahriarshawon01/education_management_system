<!doctype html>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg"
    data-sidebar-image="none" data-preloader="disable">

<head>
    <meta charset="utf-8" />
    <title>SIGN IN | TPSC</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="Themesbrand" name="author" />
    <link rel="shortcut icon" href="{{ assets('backend_assets/images/favicon.icojs') }}">
    <script src="{{ assets('backend_assets/js/layout.js') }}"></script>
    <link href="{{ assets('backend_assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ assets('backend_assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ assets('backend_assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ assets('backend_assets/css/custom.min.css') }}" rel="stylesheet" type="text/css" />
    <style>
        body {
            background-color: #f7f8fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .auth-page-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .auth-logo img {
            width: 80px;
            height: auto;
        }

        .card {
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            border: none;
        }

        .card-body {
            padding: 30px;
        }

        .card-body h5 {
            font-weight: 600;
            color: #2a3d5f;
        }

        .form-control {
            border-radius: 10px;
            border: 1px solid #d4d9e1;
            padding: 15px;
            font-size: 14px;
        }

        .form-control:focus {
            border-color: #67c5a6;
            box-shadow: 0 0 0 0.2rem rgba(103, 197, 166, 0.25);
        }

        .btn {
            border-radius: 50px;
            padding: 12px;
            font-size: 16px;
        }

        .btn-success {
            background-color: #67c5a6;
            border-color: #67c5a6;
        }

        .btn-success:hover {
            background-color: #56b191;
        }

        .alert {
            border-radius: 8px;
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
            background-color: #f8d7da;
            color: #721c24;
            font-weight: bold;
        }

        footer {
            margin-top: 30px;
        }

        footer .text-muted {
            font-size: 13px;
        }

        .school-text-color {
            color: #099885 !important;
            font-size: 2.5rem;
            text-align: left;
        }

        .password-addon {
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            font-size: 18px;
        }

        .text-center a {
            font-size: 14px;
            color: #67c5a6;
        }

        .text-center a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="auth-page-wrapper">
        <div class="auth-page-content">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="text-center mt-5 mb-4 d-flex justify-content-center align-items-center">
                        <a href="{{ url('/') }}" class="d-inline-block auth-logo me-3">
                            <img src="{{ asset('storage/app/public/uploads/logotpsc.png') }}" alt="Logo" height="100">
                        </a>
                        <h1 class="school-text-color mb-0">TMSS Public School & College (TPSC)</h1>
                    </div>

                    <div class="col-lg-6 col-md-8 col-sm-10">
                        @if ($errors->any())
                            <div class="alert alert-danger" id="error-message">
                                <p>{{ $errors->first('message') }}</p>
                            </div>
                        @endif

                        <div class="card">
                            <div class="card-body">
                                <div class="text-center">
                                    <h5 class="text-primary">Thanks for Coming Back!</h5>
                                    <p class="text-muted">Please sign in to continue to your dashboard.</p>
                                </div>
                                <form action="{{ url('login') }}" method="post">
                                    {{ csrf_field() }}
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email Address</label>
                                        <input type="text" name="email" class="form-control" id="email"
                                            placeholder="Enter Email">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="password-input">Password</label>
                                        <div class="position-relative auth-pass-inputgroup mb-3">
                                            <input type="password" name="password"
                                                class="form-control pe-5 password-input" placeholder="Enter password"
                                                id="password-input">

                                            <button
                                                class="btn btn-link position-absolute end-0 top-1 text-decoration-none text-muted password-addon"
                                                type="button" id="password-addon"><i
                                                    class="ri-eye-fill align-middle"></i></button>
                                        </div>
                                    </div>
                                    <div class="mt-4">
                                        <button class="btn btn-success w-100" type="submit">Login</button>
                                    </div>
                                </form>
                                {{-- <div class="text-center mt-3">
                                    <a href="#" class="text-muted">Forgot Password?</a>
                                </div> --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <footer class="footer">
            <div class="container text-center">
                <p class="mb-0 text-muted">&copy; {{ date('Y') }} ICT. Developed with <i
                        class="mdi mdi-heart text-danger"></i> by TMSS ICT LTD</p>
            </div>
        </footer>
    </div>

    <script src="{{ assets('backend_assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ assets('backend_assets/libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ assets('backend_assets/libs/node-waves/waves.min.js') }}"></script>
    <script src="{{ assets('backend_assets/libs/feather-icons/feather.min.js') }}"></script>
    <script src="{{ assets('backend_assets/js/pages/plugins/lord-icon-2.1.0.js') }}"></script>
    <script src="{{ assets('backend_assets/js/plugins.js') }}"></script>
    <script src="{{ assets('backend_assets/libs/particles.js/particles.js') }}"></script>
    <script src="{{ assets('backend_assets/js/pages/particles.app.js') }}"></script>
    <script src="{{ assets('backend_assets/js/pages/password-addon.init.js') }}"></script>

    <script>
        setTimeout(function() {
            var errorMessage = document.getElementById('error-message');
            if (errorMessage) {
                errorMessage.style.display = 'none';
            }
        }, 5000);
    </script>
</body>

</html>
