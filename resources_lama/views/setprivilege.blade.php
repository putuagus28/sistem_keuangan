<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>set privilege</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="{{ asset('assets/dist/img/favicon-32x32.png') }}" type="image/x-icon">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome-free/css/all.min.css') }}">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('assets/dist/css/adminlte.min.css') }}">
    <!-- Sweetalert2 -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.css') }}">
    <!-- Google Font: Source Sans Pro -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    <style>
        .login-page {
            background: #ffffff;
        }
    </style>
</head>

<body class="hold-transition login-page">
    <div class="login-box">
        @if (session('info'))
            <div class="alert alert-warning">
                <strong><i class="fas fa-exclamation-triangle"></i></strong>
                {{ session('info') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        <div class="card card-danger card-outline">
            <div class="card-header text-center">
                <h5 class="m-0"><b>Pilih UKM</b></h5>
            </div>
            <div class="card-body login-card-body">
                @if (count($ukm) < 1)
                    <div class="alert alert-info" role="alert">
                        <strong>Info</strong> <br> Silahkan kontak bagian kemahasiswaan untuk memilih ukm anda
                    </div>
                @else
                    @foreach ($ukm as $item)
                        <form action="{{ route('setprivilege') }}" method="post" class="mb-2">
                            @csrf
                            <input type="hidden" name="ukms_id" value="{{ $item->ukm->id }}">
                            <input type="hidden" name="jabatan" value="{{ $item->jabatan }}">
                            <button type="submit" class="btn btn-secondary btn-block">
                                {{ ucwords($item->ukm->nama) }}
                            </button>
                        </form>
                    @endforeach
                @endif

            </div>
            <div class="card-footer">
                <a href="{{ route('logout') }}" class="btn btn-danger btn-block">
                    Log Out
                </a>
            </div>
            <!-- /.login-card-body -->
        </div>
    </div>
    <!-- /.login-box -->

    <!-- jQuery -->
    <script src="{{ asset('assets/plugins/jquery/jquery.min.js') }}"></script>
    <!-- Bootstrap 4 -->
    <script src="{{ asset('assets/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('assets/dist/js/adminlte.min.js') }}"></script>
    <!-- Jquery Validate -->
    <script src="{{ asset('assets/plugins/jquery-validation/jquery.validate.min.js') }}"></script>
    <!-- Sweetalert2 -->
    <script src="{{ asset('assets/plugins/sweetalert2/sweetalert2.all.min.js') }}"></script>
    <script type="text/javascript"></script>
</body>

</html>
