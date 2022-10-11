@extends('layouts.app')

@section('title', 'Data Kegiatan UKM')

@section('css')
    <!-- Sweetalert2 -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.css') }}">
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/ekko-lightbox/ekko-lightbox.css') }}">
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
                    <h1 class="m-0 text-dark">Kegiatan UKM</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Kegiatan UKM</li>
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
                    <div class="card card-danger card-outline">
                        <div class="card-header d-flex flex-row align-items-center">
                            @if (Session::get('jabatan') === 'bendahara')
                                <button class="btn btn-danger" id="tambah">Tambah</button>
                            @endif
                            <div class="form-inline w-100">
                                <div class="form-group ml-auto">
                                    <label for="">Filter</label>
                                    <select name="filter_bulan" id="filter_bulan" class="form-control ml-2">
                                        <option value="" disabled selected>Pilih Bulan</option>
                                        @php
                                            $bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                                        @endphp
                                        @foreach ($bulan as $i => $item)
                                            <option value="{{ $i + 1 }}">
                                                {{ $item }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered table-hover" id="table1">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Nama Kegiatan</th>
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
    <div class="modal fade" id="modal" data-backdrop="static" tabindex="-1" role="dialog"
        aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" id="modalForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" id="id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12 col-sm-3">
                                <div class="form-group">
                                    <label for="">Tanggal</label>
                                    <input type="date" name="tgl" id="tgl" class="form-control">
                                </div>
                            </div>
                            <div class="col-12 col-sm-3">
                                <div class="form-group">
                                    <label for="">Nama Kegiatan</label>
                                    <input type="text" name="nama_kegiatan" id="nama_kegiatan" class="form-control">
                                </div>
                            </div>
                            <div class="col-12 col-sm-1">
                                <div class="form-group">
                                    <label for="">&nbsp;</label>
                                    <button type="submit" class="btn btn-info btn-block">Simpan</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <table class="table table-bordered table-hover" id="table2">
                            <thead>
                                <tr>
                                    {{-- <th class="py-2">No</th> --}}
                                    <th class="py-2">Tanggal</th>
                                    <th class="py-2">Status</th>
                                    <th class="py-2">Keterangan</th>
                                    <th class="py-2">Jumlah</th>
                                    <th class="py-2">Pengurus</th>
                                    <th class="py-2">Opsi</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
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
    <script src="{{ asset('assets/plugins/jquery-validation/jquery.validate.js') }}"></script>
    <script src="{{ asset('assets/plugins/jquery-validation/additional-methods.min.js') }}"></script>
    <!-- Sweetalert2 -->
    <script src="{{ asset('assets/plugins/sweetalert2/sweetalert2.all.min.js') }}"></script>
    <!-- JQuery mask -->
    <script src="{{ asset('assets/plugins/jquery-mask/jquery.mask.min.js') }}"></script>
    <!-- SELECT2 -->
    <script src="{{ asset('assets/plugins/select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/ekko-lightbox/ekko-lightbox.min.js') }}"></script>
    <script>
        function load_keyup() {
            $('[id*="nominal"]').keyup(function(event) {
                event.preventDefault();
                // skip for arrow keys
                if (event.which >= 37 && event.which <= 40) return;
                // format number
                $(this).val(function(index, v) {
                    return "Rp " + v.toString()
                        .replace(/\D/g, "")
                        .replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                });
            });
            load_bukti();
        }

        function load_bukti() {
            $('[id*="status"]').change(function(e) {
                e.preventDefault();
                var v = $(this).val();
                if (v == "pengeluaran") {
                    $(this).closest('td').find('#bukti').removeClass('d-none');
                } else {
                    $(this).closest('td').find('#bukti').addClass('d-none');
                }
            });
        }
        $(document).ready(function() {
            var row = 0;
            var role = "{{ auth()->user()->role }}";
            $.fn.modal.Constructor.prototype._enforceFocus = function() {};

            var bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September',
                'Oktober', 'November', 'Desember'
            ];

            Date.prototype.toDateInputValue = (function() {
                var local = new Date(this);
                local.setMinutes(this.getMinutes() - this.getTimezoneOffset());
                return local.toJSON().slice(0, 10);
            });

            document.getElementById('tgl').value = new Date().toDateInputValue();
            $('.select2').select2({
                theme: 'bootstrap4',
                container: 'body'
            });

            var table = $('#table1').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                autoWidth: false,
                ajax: {
                    'url': "{{ route('json.kegiatan') }}",
                    'data': function(data) {
                        var f_bulan = $('#filter_bulan').val();
                        data.searchByBulan = f_bulan;
                    }
                },
                columns: [{
                        data: null,
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        },
                    },
                    {
                        data: 'tanggal',
                        name: 'tanggal'
                    },
                    {
                        data: 'nama_kegiatan',
                        name: 'nama_kegiatan'
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

            // filter custom
            $('#filter_bulan').change(function() {
                var m = $(this).val();
                table.draw();
            });

            // open modal tambah
            $('#tambah').click(function(e) {
                e.preventDefault();
                $('#modal').modal('show');
                $('#modal').find('.modal-title').text('Tambah Kegiatan UKM');
                var tb = $('#table2 tbody');
                tb.html(
                    '<tr>' +
                    '<td class="py-1" width="100">' +
                    '<input type="date" name="tanggal[]" id="tanggal" class="form-control">' +
                    '</td>' +
                    '<td class="py-1" width="200">' +
                    '<select name="status[]" id="status" class="form-control">' +
                    '<option value="" disabled selected>Pilih</option>' +
                    '<option value="pemasukan">Pemasukan</option>' +
                    '<option value="pengeluaran">Pengeluaran</option>' +
                    '</select>' +
                    '<input type="file" name="bukti' + row +
                    '[]" id="bukti" class="form-control d-none" accept="image/*" multiple>' +
                    '</td>' +
                    '<td class="py-1">' +
                    '<input type="text" name="keterangan[]" id="keterangan" class="form-control">' +
                    '</td>' +
                    '<td class="py-1">' +
                    '<input type="text" name="nominal[]" id="nominal" class="form-control" placeholder="Rp ">' +
                    '</td>' +
                    '<td class="py-1">' +
                    '<input type="text" readonly name="pengurus[]" id="pengurus" class="form-control" value="{{ auth()->user()->name }}">' +
                    '</td>' +
                    '<td class="py-1">' +
                    '<button class="btn btn-info btn-sm" id="add">' +
                    '<i class="fa fa-plus-circle" aria-hidden="true"></i>' +
                    '</button>' +
                    '</td>' +
                    '</tr>');

                load_keyup();
                row++;
            });

            // reset all input in form after clicking modal
            $('#modal').on('hidden.bs.modal', function(e) {
                validator.resetForm();
                $("#modal").find('.is-invalid').removeClass('is-invalid');
                $(this)
                    .find("input,textarea,select,[name*='bukti']")
                    .not('input[name="_token"],#kode,#tgl,#pengurus')
                    .val('')
                    .end()
                    .find("input[type=checkbox], input[type=radio]")
                    .prop("checked", "")
                    .end();
                $('#modal form').find("input,textarea,select").attr(
                    "disabled", false);
                $('#modal form').find('button[type="submit"]').attr('disabled', false).show();

            });

            // modal show 
            $('#modal').on('shown.bs.modal', function() {
                $(this).find('#nama').focus();
            });

            // open modal edit
            table.on("click", "#edit", function(e) {
                e.preventDefault();
                var id = $(this).data('id');
                $('#modal').modal('show');
                $('#modal').find('.modal-title').text('Edit Kegiatan UKM');
                $('#modal form').show().find('#id').val(id);
                $('#table2 tbody').html('');
                $.ajax({
                    url: "{{ route('edit.kegiatan') }}",
                    type: "GET",
                    dataType: "JSON",
                    cache: false,
                    data: {
                        'id': id
                    },
                    success: function(data) {
                        $('#modal form').find('#tgl').val(data.tanggal);
                        $('#modal form').find('#nama_kegiatan').val(data.nama_kegiatan);
                        $.each(data.detail, function(i, v) {
                            var nominal = "Rp " + v.nominal.toString()
                                .replace(/\D/g, "")
                                .replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                            var tb = $('#table2 tbody');
                            var img = "";
                            var bukti = v.bukti;
                            var file = bukti.split(",");
                            for (var x = 0; x < file.length; x++) {
                                img += '<img src="bukti/' + file[x] +
                                    '" class="img-fluid my-1 w-100" alt="">';
                            }
                            var btn = '';
                            if (i == 0) {
                                btn +=
                                    '<button class="btn btn-info btn-sm" id="add">' +
                                    '<i class="fa fa-plus-circle" aria-hidden="true"></i>' +
                                    '</button>';
                            } else {
                                btn +=
                                    '';
                            }
                            tb.append(
                                '<tr>' +
                                '<td class="py-1" width="100">' +
                                '<input type="hidden" name="id_detail[]" id="id_detail" class="form-control" value="' +
                                v.id + '">' +
                                '<input type="date" name="tanggal[]" id="tanggal" class="form-control" value="' +
                                v.tanggal + '">' +
                                '</td>' +
                                '<td class="py-1" width="200">' +
                                '<select name="status[]" id="status" class="form-control">' +
                                '<option value="" disabled selected>Pilih</option>' +
                                '<option value="pemasukan" ' + (v.status ==
                                    "pemasukan" ? "selected" : "") +
                                '>Pemasukan</option>' +
                                '<option value="pengeluaran" ' + (v.status ==
                                    "pengeluaran" ? "selected" : "") +
                                '>Pengeluaran</option>' +
                                '</select>' +
                                '<input type="file" name="bukti' + i +
                                '[]" id="bukti" class="form-control ' + (v.status ==
                                    "pengeluaran" ? "" : "d-none") +
                                '" accept="image/*" multiple>' +
                                img +
                                '</td>' +
                                '<td class="py-1">' +
                                '<input type="text" name="keterangan[]" id="keterangan" class="form-control" value="' +
                                v.keterangan + '">' +
                                '</td>' +
                                '<td class="py-1">' +
                                '<input type="text" name="nominal[]" id="nominal" class="form-control" placeholder="Rp " value="' +
                                nominal + '">' +
                                '</td>' +
                                '<td class="py-1">' +
                                '<input type="text" readonly name="pengurus[]" id="pengurus" class="form-control" value="{{ auth()->user()->name }}">' +
                                '</td>' +
                                '<td class="py-1">' + btn +
                                '</td>' +
                                '</tr>');

                            load_keyup();
                        });

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

            table.on("click", "#detail", function(e) {
                e.preventDefault();
                var id = $(this).data('id');
                $('#modal').modal('show');
                $('#modal').find('.modal-title').text('Detail Kegiatan UKM');
                $('#modal form').show().find('#id').val(id);
                $('#modal form').find('button[type="submit"]').attr('disabled', true).hide();
                $('#table2 tbody').html('');
                $.ajax({
                    url: "{{ route('edit.kegiatan') }}",
                    type: "GET",
                    dataType: "JSON",
                    cache: false,
                    data: {
                        'id': id
                    },
                    success: function(data) {
                        $('#modal form').find('#tgl').val(data.tanggal);
                        $('#modal form').find('#nama_kegiatan').val(data.nama_kegiatan);
                        $.each(data.detail, function(i, v) {
                            var nominal = "Rp " + v.nominal.toString()
                                .replace(/\D/g, "")
                                .replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                            var tb = $('#table2 tbody');
                            var img = "";
                            var bukti = v.bukti;
                            var file = bukti.split(",");
                            for (var x = 0; x < file.length; x++) {
                                img += '<img src="bukti/' + file[x] +
                                    '" class="img-fluid my-1 w-100" alt="">';
                            }
                            tb.append(
                                '<tr>' +
                                '<td class="py-1" width="100">' +
                                '<input type="hidden" name="id_detail[]" id="id_detail" class="form-control" value="' +
                                v.id + '">' +
                                '<input type="date" name="tanggal[]" id="tanggal" class="form-control" value="' +
                                v.tanggal + '">' +
                                '</td>' +
                                '<td class="py-1" width="200">' +
                                '<select name="status[]" id="status" class="form-control">' +
                                '<option value="" disabled selected>Pilih</option>' +
                                '<option value="pemasukan" ' + (v.status ==
                                    "pemasukan" ? "selected" : "") +
                                '>Pemasukan</option>' +
                                '<option value="pengeluaran" ' + (v.status ==
                                    "pengeluaran" ? "selected" : "") +
                                '>Pengeluaran</option>' +
                                '</select>' +
                                img +
                                '</td>' +
                                '<td class="py-1">' +
                                '<input type="text" name="keterangan[]" id="keterangan" class="form-control" value="' +
                                v.keterangan + '">' +
                                '</td>' +
                                '<td class="py-1">' +
                                '<input type="text" name="nominal[]" id="nominal" class="form-control" placeholder="Rp " value="' +
                                nominal + '">' +
                                '</td>' +
                                '<td class="py-1">' +
                                '<input type="text" readonly name="pengurus[]" id="pengurus" class="form-control" value="{{ auth()->user()->name }}">' +
                                '</td>' +
                                '<td class="py-1">' +
                                '' +
                                '</td>' +
                                '</tr>');
                            $('#modal form').find("input,textarea,select").attr(
                                "disabled", true);
                        });

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

            $('#table2').on('click', '#add', function(e) {
                e.preventDefault();
                var tb = $('#table2 tbody');
                tb.append(
                    '<tr>' +
                    '<td class="py-1" width="100">' +
                    '<input type="date" name="tanggal[]" id="tanggal" class="form-control">' +
                    '<input type="hidden" name="id_detail[]" id="id_detail" class="form-control" value="">' +
                    '</td>' +
                    '<td class="py-1" width="200">' +
                    '<select name="status[]" id="status" class="form-control">' +
                    '<option value="" disabled selected>Pilih</option>' +
                    '<option value="pemasukan">Pemasukan</option>' +
                    '<option value="pengeluaran">Pengeluaran</option>' +
                    '</select>' +
                    '<input type="file" name="bukti' + row +
                    '[]" id="bukti" class="form-control d-none" accept="image/*" multiple>' +
                    '</td>' +
                    '<td class="py-1">' +
                    '<input type="text" name="keterangan[]" id="keterangan" class="form-control">' +
                    '</td>' +
                    '<td class="py-1">' +
                    '<input type="text" name="nominal[]" id="nominal" class="form-control" placeholder="Rp ">' +
                    '</td>' +
                    '<td class="py-1">' +
                    '<input type="text" readonly name="pengurus[]" id="pengurus" class="form-control" value="{{ auth()->user()->name }}">' +
                    '</td>' +
                    '<td class="py-1">' +
                    '<button class="btn btn-danger btn-sm" id="minus">' +
                    '<i class="fa fa-minus-circle" aria-hidden="true"></i>' +
                    '</button>' +
                    '</td>' +
                    '</tr>');
                load_keyup();
                row++;
                // set all file upload name
                $('[name*="bukti"]').each(function(i, e) {
                    $(e).attr('name', 'bukti' + i + '[]');
                });
            });

            $('#table2 tbody').on('click', '#minus', function(e) {
                e.preventDefault();
                var rows = $(this).closest('tr').index();
                $('#table2 tbody tr').eq(rows).remove();
            });

            // delete data
            table.on("click", "#hapus", function(e) {
                e.preventDefault();
                var id = $(this).data('id');
                Swal.fire({
                    title: 'Yakin untuk menghapus ini ?',
                    showDenyButton: true,
                    showCancelButton: false,
                    showConfirmButton: true,
                    confirmButtonText: `Hapus`,
                    denyButtonText: `Batal`,
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('delete.kegiatan') }}",
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

            // remove invalid in change
            $('select').on('change', function(e) {
                e.preventDefault();
                var id = $(this).val();
                if (id != null) {
                    $(this).removeClass('is-invalid');
                }
            });

            // tambah data
            var validator = $("#modalForm").validate({
                ignore: [],
                rules: {
                    tgl: {
                        required: true,
                    },
                    nama_kegiatan: {
                        required: true,
                    },
                    'tanggal[]': {
                        required: true,
                    },
                    'status[]': {
                        required: true,
                    },
                    'nominal[]': {
                        required: true,
                    },
                    'keterangan[]': {
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
                    if (id == "") {
                        $.ajax({
                            url: "{{ route('insert.kegiatan') }}",
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
                            url: "{{ route('update.kegiatan') }}",
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
