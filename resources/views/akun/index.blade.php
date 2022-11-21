@extends('layouts.app')

@section('title', $title)

@section('css')
    <!-- Sweetalert2 -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.css') }}">
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
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
                    <h1 class="m-0 text-dark">{{ $title }}</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">{{ $title }}</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex flex-row align-items-center">
                            <h3 class="card-title"><i class="fa fa-2x fa-list" aria-hidden="true"></i></h3>
                            <button class="btn btn-danger ml-auto" id="tambah">Tambah</button>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>No Reff</th>
                                        <th>Kategori</th>
                                        <th>Nama Akun</th>
                                        <th>Saldo Awal</th>
                                        <th>Debet</th>
                                        <th>Kredit</th>
                                        <th>User Create</th>
                                        <th>Opsi</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--/. container-fluid -->
    </section>

    <!-- Modal -->
    <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" id="modalForm">
                    @csrf
                    <input type="hidden" name="id" id="id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12 col-sm-3">
                                <div class="form-group">
                                    <label for="">No Reff</label>
                                    <input type="number" name="no_reff" id="no_reff" class="form-control">
                                </div>
                            </div>
                            <div class="w-100"></div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="">Kategori</label>
                                    <select name="nama_reff" id="nama_reff" class="form-control">
                                        <option value="" selected disabled>Pilih</option>
                                        <option value="Activa">Activa</option>
                                        <option value="Kewajiban">Kewajiban</option>
                                        <option value="Modal">Modal</option>
                                        <option value="Pendapatan">Pendapatan</option>
                                        <option value="Beban">Beban</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="">Nama Akun</label>
                                    <input type="text" name="keterangan" id="keterangan" class="form-control">
                                </div>
                            </div>
                            {{-- <div class="col-12">
                                <label for="">Saldo Awal</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="basic-addon1">Rp</span>
                                    </div>
                                    <input type="text" class="form-control" placeholder="0" name="saldo_awal"
                                        id="saldo_awal">
                                </div>
                            </div> --}}
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Simpan</button>
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
    <!-- JQuery mask -->
    <script src="{{ asset('assets/plugins/jquery-mask/jquery.mask.min.js') }}"></script>
    <script>
        function capitalizeFirstLetter(string) {
            return string.charAt(0).toUpperCase() + string.slice(1);
        }
        $(document).ready(function() {
            var role = "{{ auth()->user()->role }}";
            $.fn.modal.Constructor.prototype._enforceFocus = function() {};
            var table = $('table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                autoWidth: false,
                ajax: "{{ route('json.akun') }}",
                columns: [{
                        data: null,
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        },
                    },
                    {
                        data: 'no_reff',
                        name: 'no_reff'
                    },
                    {
                        data: 'nama_reff',
                        name: 'nama_reff'
                    },
                    {
                        data: 'keterangan',
                        name: 'keterangan'
                    },
                    {
                        data: 'saldo',
                        name: 'saldo'
                    },
                    {
                        data: 'debet',
                        name: 'debet'
                    },
                    {
                        data: 'kredit',
                        name: 'kredit'
                    },
                    {
                        data: 'users.name',
                        name: 'users.name'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ],
                order: [
                    [1, "desc"]
                ]
            });

            // open modal tambah
            $('#tambah').click(function(e) {
                e.preventDefault();
                $('#modal').modal('show');
                $('#modal').find('.modal-title').text('Form Tambah');
            });

            // reset all input in form after clicking modal
            $('#modal').on('hidden.bs.modal', function(e) {
                validator.resetForm();
                $("#modal").find('.is-invalid').removeClass('is-invalid');
                $(this)
                    .find("input,textarea,select")
                    .not('input[name="_token"],#kode')
                    .val('')
                    .end()
                    .find("input[type=checkbox], input[type=radio]")
                    .prop("checked", "")
                    .end();
                $('#modal #v_detail').addClass('d-none');
            });

            // modal show 
            $('#modal').on('shown.bs.modal', function() {
                $(this).find('#nama').focus();
            });

            // delete data
            table.on("click", "#hapus", function(e) {
                e.preventDefault();
                var id = $(this).data('id');
                Swal.fire({
                    title: 'Yakin untuk menghapus anggota ini ?',
                    showDenyButton: true,
                    showCancelButton: false,
                    showConfirmButton: true,
                    confirmButtonText: `Hapus`,
                    denyButtonText: `Batal`,
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('delete.akun') }}",
                            type: "GET",
                            dataType: "JSON",
                            data: {
                                'id': id
                            },
                            cache: false,
                            success: function(response) {
                                if (response.status) {
                                    // Swal.fire({
                                    //     icon: 'success',
                                    //     title: response.message,
                                    //     showCancelButton: false,
                                    //     showConfirmButton: true
                                    // }).then(function() {
                                    table.ajax.reload();
                                    // });
                                }
                            },
                            error: function(response) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Opps!',
                                    text: 'server error!'
                                });
                            }
                        });
                    }
                })
            });

            // open modal edit
            table.on("click", "#edit", function(e) {
                e.preventDefault();
                var id = $(this).data('id');
                $('#modal #v_detail').removeClass('d-none');
                $('#modal').modal('show');
                $('#modal').find('.modal-title').text('Form Edit');
                $('#modal form').show().find('#id').val(id);
                if (role == "owner") {
                    $('#modal form').find('#role').closest('.form-group').hide();
                    // $('#modal form').find('#username').closest('.form-group').hide();
                    // $('#modal form').find('#password').closest('.form-group').hide();
                }
                $.ajax({
                    url: "{{ route('edit.akun') }}",
                    type: "GET",
                    dataType: "JSON",
                    cache: false,
                    data: {
                        'id': id
                    },
                    success: function(data) {
                        $('#modal form').find('#no_reff').val(data.no_reff);
                        $('#modal form').find('#nama_reff').val(data.nama_reff).change();
                        $('#modal form').find('#keterangan').val(data.keterangan);
                        $('#modal form').find('#saldo_awal').val(data.saldo_awal);
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
            });
            // tambah data
            var validator = $("#modalForm").validate({
                rules: {
                    no_reff: {
                        required: true,
                    },
                    nama_reff: {
                        required: true,
                    },
                    keterangan: {
                        required: true,
                    },
                    // saldo_awal: {
                    //     required: true,
                    // },
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
                    if (id == "") {
                        $.ajax({
                            url: "{{ route('insert.akun') }}",
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
                                        table.ajax.reload();
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: response.message,
                                        showCancelButton: false,
                                        showConfirmButton: true
                                    }).then(function() {
                                        table.ajax.reload();
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
                    } else {
                        $.ajax({
                            url: "{{ route('update.akun') }}",
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
                                        table.ajax.reload();
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: response.message,
                                        showCancelButton: false,
                                        showConfirmButton: true
                                    }).then(function() {
                                        table.ajax.reload();
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
                }
            });
        });
    </script>
@endsection
