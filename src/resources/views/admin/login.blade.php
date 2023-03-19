<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>E-Dompet | Login</title>

    <link href="{{ asset('admin/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('admin/font-awesome/css/font-awesome.css') }}" rel="stylesheet">

    <link href="{{ asset('admin/css/animate.css') }}" rel="stylesheet">
    <link href="{{ asset('admin/css/style.css') }}" rel="stylesheet">
    <link rel="shortcut icon" href="{{ asset('icon.png') }}" type="image/x-icon">
    <style>
        body {
            background-image: url('../background-2.png');
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-size: cover;
        }
    </style>
</head>

<body>

    <div class="middle-box text-center loginscreen animated fadeInDown">
        <div>
            @include('admin.notification')
            <div>
                <h2 class="logo-name">ED</h2>
            </div>
            <h3 style="color: white
            ">Welcome to E-Dompet</h3>
            {{-- <p>Perfectly designed and precisely prepared admin theme with over 50 pages with extra new web app views.
                <!--Continually expanded and constantly improved Inspinia Admin Them (IN+)-->
            </p> --}}
            <p style="color: white
            "></p>
            <form class="m-t" role="form" action="{{ route('admin.doLogin') }}" method="POST">
                @csrf
                <div class="form-group">
                    <input type="email" name="email" class="form-control" placeholder="Email" required="">
                </div>
                <div class="form-group">
                    <input type="password" name="password" class="form-control" placeholder="Password" required="">
                </div>
                <button type="submit" class="btn btn-warning block full-width m-b">Login</button>

                {{-- <a href="#"><small>Forgot password?</small></a>
                <p class="text-muted text-center"><small>Do not have an account?</small></p>
                <a class="btn btn-sm btn-white btn-block" href="register.html">Create an account</a> --}}
            </form>
            <p class="m-t" style="color: white
            "> <small>Develop With <i class="fa fa-heart"> By <a
                            href="https://github.com/imamdev93" style="color:
                            white">Imam
                            Fahmi Fadillah</a>
                    </i></small> </p>
        </div>
    </div>

    <!-- Mainly scripts -->
    <script src="{{ asset('admin/js/jquery-3.1.1.min.js') }}"></script>
    <script src="{{ asset('admin/js/popper.min.js') }}"></script>
    <script src="{{ asset('admin/js/bootstrap.js') }}"></script>

</body>

</html>
