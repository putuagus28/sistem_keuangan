@extends('layouts.app')

@section('title', 'Profile Pengguna')

@section('css')
    <!-- Sweetalert2 -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.css') }}">
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
@endsection
@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            @if (session('info'))
                <div class="alert alert-danger">
                    <strong><i class="fas fa-exclamation-triangle"></i></strong>
                    {{ session('info') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Profile Pengguna</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Profile Pengguna</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-12 col-md-4 mx-auto">
                    <div class="card card-primary card-outline">
                        <div class="card-body box-profile">
                            <div class="text-center">
                                <div id="placeImg"></div>
                            </div>

                            <h3 class="profile-username text-center">{{ auth()->user()->name }}</h3>

                            <p class="text-muted text-center">{{ auth()->user()->role }}</p>

                            <ul class="list-group list-group-unbordered mb-3">
                                <li class="list-group-item">
                                    <b>Alamat</b> <a class="float-right" id="list1"></a>
                                </li>
                                <li class="list-group-item">
                                    <b>Tanggal Lahir</b> <a class="float-right" id="list2"></a>
                                </li>
                                <li class="list-group-item">
                                    <b>Username</b> <a class="float-right" id="list3"></a>
                                </li>
                            </ul>
                            <button data-id="{{ auth()->user()->id }}" class="btn btn-success btn-block"
                                id="edit"><b>Edit</b></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--/. container-fluid -->
    </section>

    <!-- Modal -->
    <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Profile</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" method="post" enctype="multipart/form-data">
                    <div class="modal-body">
                        @csrf
                        <div class="form-group">
                            <label for="">Username</label>
                            <input type="text" name="name" id="name" class="form-control"
                                value="{{ auth()->user()->name }}">
                            <input type="hidden" name="id" value="{{ auth()->user()->id }}">
                        </div>
                        <div class="form-group">
                            <label for="">Email</label>
                            <input type="email" name="email" id="email" class="form-control"
                                value="{{ auth()->user()->email }}">
                        </div>
                        <div class="form-group">
                            <label for="">New Password</label>
                            <input type="password" name="password" id="password" class="form-control" value="">
                            <small class="text-danger">Rubah jika perlu mengganti password</small>
                        </div>
                        <div class="form-group">
                            <label for="">Alamat</label>
                            <input type="text" name="alamat" id="alamat" class="form-control"
                                value="{{ auth()->guard()->user()->alamat }}">
                        </div>
                        <div class="form-group">
                            <label for="">Tanggal Lahir</label>
                            <input type="date" name="tanggalLahir" id="tanggalLahir" class="form-control"
                                value="{{ auth()->user()->tanggalLahir }}">
                        </div>
                        <div class="form-group">
                            <label for="">noKtp</label>
                            <input type="text" name="noKtp" id="noKtp" class="form-control"
                                value="{{ auth()->user()->noKtp }}">
                        </div>
                        <div class="form-group">
                            <label for="">foto</label>
                            <input type="file" name="foto" id="foto" class="form-control">
                            <small class="text-danger">Rubah jika perlu mengganti foto</small>
                        </div>
                        <div class="form-group">
                            <label for="">noTlpn</label>
                            <input type="number" name="noTlpn" id="noTlpn" class="form-control"
                                value="{{ auth()->user()->noTlpn }}">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <!-- DataTables -->
    <script src="{{ asset('assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <!-- Jquery Validate -->
    <script src="{{ asset('assets/plugins/jquery-validation/jquery.validate.min.js') }}"></script>
    <!-- Sweetalert2 -->
    <script src="{{ asset('assets/plugins/sweetalert2/sweetalert2.all.min.js') }}"></script>
    <!-- Select2 -->
    <script src="{{ asset('assets/plugins/select2/js/select2.full.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#edit').on('click', function(e) {
                e.preventDefault();
                $('#modal').modal('show');
            });

            function GetFormattedDate(date) {
                var todayTime = new Date(date);
                var month = (todayTime.getMonth() + 1);
                var day = todayTime.getDate();
                var year = todayTime.getFullYear();
                return day + "/" + month + "/" + year;
            }

            showprofile();

            function showprofile() {
                $.ajax({
                    type: "GET",
                    url: "{{ route('get.profile') }}",
                    dataType: "JSON",
                    success: function(res) {
                        $('#list1').text((res['profile'].alamat == null) ? '-' : res['profile'].alamat
                            .toUpperCase());
                        $('#list2').text(GetFormattedDate(res['profile'].tanggalLahir).toString());
                        $('#list3').text(res['profile'].name);
                        var foto = res['profile'].foto;
                        var fotos = '';
                        if (foto == null) {
                            fotos =
                                '<div class="rounded-circle p-1 border d-inline-block"><i class="fa fa-user-circle fa-5x" aria-hidden="true"></i></div>';
                        } else {
                            fotos = '<img class="profile-user-img img-fluid img-circle" src="users/' +
                                foto +
                                '" alt="User profile picture">';
                        }
                        $('#placeImg').html(fotos);
                    }
                });
            }
            // update data
            var validator = $("#modal form").validate({
                rules: {
                    name: {
                        required: true,
                    },
                    email: {
                        required: true,
                        email: true,
                    },
                    alamat: {
                        required: true,
                    },
                    tanggalLahir: {
                        required: true,
                    },
                    noKtp: {
                        required: true,
                    },
                    noTlpn: {
                        required: true,
                    },
                },
                errorElement: "div",
                errorPlacement: function(error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.input-group, .form-group').append(error);
                },
                highlight: function(element, errorClass, validClass) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element, errorClass, validClass) {
                    $(element).removeClass('is-invalid');
                },
                submitHandler: function(form) {
                    var id = $(form).find('#id').val();
                    $.ajax({
                        url: "{{ route('update.profile') }}",
                        type: "POST",
                        dataType: "JSON",
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: new FormData(form),
                        success: function(response) {
                            if (response.status) {
                                Swal.fire({
                                    icon: 'success',
                                    title: response.message,
                                    showCancelButton: false,
                                    showConfirmButton: true
                                }).then(function() {
                                    $('#modal').modal('hide');
                                    showprofile();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: response.message,
                                    showCancelButton: false,
                                    showConfirmButton: true
                                });
                            }
                        },
                        error: function(response) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Opps!',
                                text: 'server error!'
                            });
                            console.log(response);
                        }
                    });
                }
            });
        });

    </script>
@endsection
