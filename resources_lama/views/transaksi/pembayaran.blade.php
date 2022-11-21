@extends('layouts.app')

@section('title', 'Data Pembayaran')

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
                    <h1 class="m-0 text-dark">Pembayaran</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Pembayaran</li>
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
                <div class="col-12 col-sm-4 col-md-4">
                    <div class="small-box bg-dark shadow-sm">
                        <div class="inner">
                            <p>Total Pembayaran</p>
                            <h3>Rp <span id="rupiah"></span></h3>
                        </div>
                        <div class="icon">
                            <i class="fas fa-money-bill"></i>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-4 col-md-4">
                    <div class="small-box bg-success shadow-sm">
                        <div class="inner">
                            <p>Total Cash</p>
                            <h3>Rp <span id="rupiah"></span></h3>
                        </div>
                        <div class="icon">
                            <i class="fas fa-money-bill"></i>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-4 col-md-4">
                    <div class="small-box bg-info shadow-sm">
                        <div class="inner">
                            <p>Total Transfers</p>
                            <h3>Rp <span id="rupiah"></span></h3>
                        </div>
                        <div class="icon">
                            <i class="fas fa-paper-plane"></i>
                        </div>
                    </div>
                </div>
            </div>

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
                            <ul class="nav nav-tabs" id="custom-content-below-tab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="custom-content-below-home-tab" data-toggle="pill"
                                        href="#custom-content-below-home" role="tab"
                                        aria-controls="custom-content-below-home" aria-selected="true">Sudah Bayar</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="custom-content-below-profile-tab" data-toggle="pill"
                                        href="#custom-content-below-profile" role="tab"
                                        aria-controls="custom-content-below-profile" aria-selected="false">Belum Bayar</a>
                                </li>
                            </ul>

                            <div class="tab-content" id="custom-content-below-tabContent">
                                <div class="tab-pane fade active show pt-4" id="custom-content-below-home" role="tabpanel">
                                    <table class="table table-bordered table-hover" id="table1">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Tanggal</th>
                                                <th>Bulan</th>
                                                <th>NIM</th>
                                                <th>Nama</th>
                                                <th>Jumlah</th>
                                                <th>Keterangan</th>
                                                <th>Metode</th>
                                                <th>User Added</th>
                                                <th>Opsi</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                                <div class="tab-pane fade pt-4" id="custom-content-below-profile" role="tabpanel">
                                    <table class="table table-bordered table-hover" id="table2">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>NIM</th>
                                                <th>Nama</th>
                                                <th>Email</th>
                                                <th>NoHp</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
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
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <label for="">Tanggal</label>
                                    <input type="date" name="tanggal" id="tanggal" class="form-control">
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <label for="">Anggota</label>
                                    <select class="form-control select2" name="mahasiswas_id" id="mahasiswas_id">
                                        <option disabled selected value="">Pilih</option>
                                        @foreach ($mhs as $item)
                                            <option value="{{ $item->mhs->id }}">
                                                {{ $item->mhs->nim . ' - ' . $item->mhs->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <label for="">Nominal</label>
                                    <input type="text" name="nominal" id="nominal" placeholder="Rp "
                                        class="form-control">
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <label for="">Metode Bayar</label>
                                    <select class="form-control select2" name="metode" id="metode">
                                        <option disabled selected value="">Pilih</option>
                                        <option value="cash">Cash</option>
                                        <option value="transfers">Transfers</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-sm-12">
                                <div class="form-group">
                                    <label for="">Keterangan</label>
                                    <input type="text" name="keterangan" id="keterangan" class="form-control">
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <label for="">Pengurus</label>
                                    <input type="text" name="pengurus" id="pengurus" class="form-control" disabled
                                        value="{{ auth()->user()->name }}">
                                </div>
                            </div>
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
    <!-- SELECT2 -->
    <script src="{{ asset('assets/plugins/select2/js/select2.full.min.js') }}"></script>
    <script>
        function get_total(tb, metode, bulan = '') {
            var result = '';
            $.ajax({
                type: "get",
                url: "{{ url('total_keuangan') }}/" + tb + "/" + metode + "/" + bulan,
                async: false,
                success: function(res) {
                    result = res;
                }
            });

            return result;
        }

        refresh_total();

        function refresh_total(m = '') {
            $('.small-box').eq(0).find('#rupiah').text(get_total('pembayaran', 'all', m));
            $('.small-box').eq(1).find('#rupiah').text(get_total('pembayaran', 'cash', m));
            $('.small-box').eq(2).find('#rupiah').text(get_total('pembayaran', 'transfers', m));
        }


        $(document).ready(function() {
            var role = "{{ auth()->user()->role }}";
            var jabatan = "{{ Session::get('jabatan') }}";
            var isVisibleColumns = (jabatan.includes("ketua", "pembina") ? false : true);

            $.fn.modal.Constructor.prototype._enforceFocus = function() {};

            var bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September',
                'Oktober', 'November', 'Desember'
            ];


            Date.prototype.toDateInputValue = (function() {
                var local = new Date(this);
                local.setMinutes(this.getMinutes() - this.getTimezoneOffset());
                return local.toJSON().slice(0, 10);
            });

            document.getElementById('tanggal').value = new Date().toDateInputValue();
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
                    'url': "{{ route('json.pembayaran') }}",
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
                        data: 'bulan',
                        render: function(data, type, row, meta) {
                            return bulan[row.bulan - 1];
                        },
                    },
                    {
                        data: 'mhs.nim',
                        name: 'mhs.nim'
                    },
                    {
                        data: 'mhs.name',
                        name: 'mhs.name'
                    },
                    {
                        data: 'nominal',
                        name: 'nominal'
                    },
                    {
                        data: 'keterangan',
                        name: 'keterangan'
                    },
                    {
                        data: 'metode',
                        render: function(data, type, row, meta) {
                            if (row.metode == "cash") {
                                return '<span class="badge badge-pill badge-success">' + row
                                    .metode +
                                    '</span>';
                            } else {
                                return '<span class="badge badge-pill badge-danger">' + row.metode +
                                    '</span>';
                            }
                        },
                    },
                    {
                        data: 'users.mhs.name',
                        name: 'users.mhs.name'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        visible: isVisibleColumns
                    },
                ],
                order: [
                    [1, "desc"]
                ]
            });

            var table2 = $('#table2').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                autoWidth: false,
                ajax: {
                    'url': "{{ route('json.belum.pembayaran') }}",
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
                        data: 'mhs.nim',
                        name: 'mhs.nim'
                    },
                    {
                        data: 'mhs.name',
                        name: 'mhs.name'
                    },
                    {
                        data: 'mhs.email',
                        name: 'mhs.email'
                    },
                    {
                        data: 'mhs.noTlpn',
                        name: 'mhs.noTlpn'
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
                table2.draw();
                refresh_total(m);
            });


            $('a[data-toggle="pill"]').on('shown.bs.tab', function(e) {
                e.target // newly activated tab
                e.relatedTarget // previous active tab
                table.ajax.reload();
                table2.ajax.reload();
            })

            // open modal tambah
            $('#tambah').click(function(e) {
                e.preventDefault();
                $('#modal').modal('show');
                $('#modal').find('.modal-title').text('Tambah Pembayaran');
            });

            // reset all input in form after clicking modal
            $('#modal').on('hidden.bs.modal', function(e) {
                validator.resetForm();
                $("#modal").find('.is-invalid').removeClass('is-invalid');
                $(this)
                    .find("input,textarea,select")
                    .not('input[name="_token"],#kode,#tanggal,#pengurus')
                    .val('')
                    .end()
                    .find("input[type=checkbox], input[type=radio]")
                    .prop("checked", "")
                    .end();
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
                $('#modal').find('.modal-title').text('Edit Pembayaran');
                $('#modal form').show().find('#id').val(id);
                $.ajax({
                    url: "{{ route('edit.pembayaran') }}",
                    type: "GET",
                    dataType: "JSON",
                    cache: false,
                    data: {
                        'id': id
                    },
                    success: function(data) {
                        $('#modal form').find('#tanggal').val(data.tanggal);
                        $('#modal form').find('#mahasiswas_id').val(data.mahasiswas_id)
                            .change();
                        var nominal = "Rp " + data.nominal.toString()
                            .replace(/\D/g, "")
                            .replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                        $('#modal form').find('#nominal').val(nominal);
                        $('#modal form').find('#metode').val(data.metode)
                            .change();
                        $('#modal form').find('#keterangan').val(data.keterangan);

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

            $('#nominal').keyup(function(event) {

                // skip for arrow keys
                if (event.which >= 37 && event.which <= 40) return;
                // format number
                $(this).val(function(index, v) {
                    return "Rp " + v.toString()
                        .replace(/\D/g, "")
                        .replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                });
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
                rules: {
                    tanggal: {
                        required: true,
                    },
                    mahasiswas_id: {
                        required: true,
                    },
                    nominal: {
                        required: true,
                    },
                    metode: {
                        required: true,
                    },
                    keterangan: {
                        required: true,
                    },
                },
                errorElement: "div",
                errorPlacement: function(error, element) {
                    if (element.hasClass('select2-hidden-accessible') && element.next(
                            '.select2-container').length) {
                        error.insertAfter(element.next('.select2-container'));
                    } else if (element.parent('.input-group').length) {
                        error.insertAfter(element.parent());
                    } else if (element.prop('type') === 'radio' && element.parent('.radio-inline')
                        .length) {
                        error.insertAfter(element.parent().parent());
                    } else if (element.prop('type') === 'checkbox' || element.prop('type') ===
                        'radio') {
                        error.appendTo(element.parent().parent());
                    } else {
                        error.insertAfter(element);
                    }
                    error.addClass('invalid-feedback');
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
                            url: "{{ route('insert.pembayaran') }}",
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
                            url: "{{ route('update.pembayaran') }}",
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
                    refresh_total();
                }
            });
        });
    </script>
@endsection
