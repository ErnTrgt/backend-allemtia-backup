<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <title>Admin Login</title>
    <!-- CSS -->
    <link rel="stylesheet" type="text/css" href="{{ asset('/admin/vendors/styles/core.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('/admin/vendors/styles/icon-font.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('/admin/vendors/styles/style.css') }}" />
</head>

<body class="login-page">
    <div class="login-header box-shadow">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <div class="brand-logo">
                <a href="{{ route('admin.login') }}">
                    <img src="{{ asset('/admin/vendors/images/deskapp-logo.svg') }}" alt="Logo" />
                </a>
            </div>
        </div>
    </div>
    <div class="login-wrap d-flex align-items-center flex-wrap justify-content-center">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 col-lg-5">
                    <div class="login-box bg-white box-shadow border-radius-10">
                        <div class="login-title">
                            <h2 class="text-center text-primary">Admin Login</h2>
                        </div>
                        @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif


                        <form action="{{ route('admin.login.submit') }}" method="POST">
                            @csrf
                            <div class="input-group custom">
                                <input type="email" class="form-control form-control-lg" placeholder="Email"
                                    name="email" required />
                                <div class="input-group-append custom">
                                    <span class="input-group-text"><i class="icon-copy dw dw-user1"></i></span>
                                </div>
                            </div>
                            <div class="input-group custom">
                                <input type="password" class="form-control form-control-lg" placeholder="Password"
                                    name="password" required />
                                <div class="input-group-append custom">
                                    <span class="input-group-text"><i class="dw dw-padlock1"></i></span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="input-group mb-0">
                                        <button class="btn btn-primary btn-lg btn-block" type="submit">Sign In</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-md-6 col-lg-7">
                    <img src="{{ asset('/admin/vendors/images/login-page-img.png') }}" alt="Admin Login" />
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('/admin/vendors/scripts/core.js') }}"></script>
    <script src="{{ asset('/admin/vendors/scripts/script.min.js') }}"></script>
    <script src="{{ asset('/admin/vendors/scripts/process.js') }}"></script>
</body>

</html>
