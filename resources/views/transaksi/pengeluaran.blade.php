@extends('layouts.app')

@section('title', 'Data Pengeluaran')

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
                    <h1 class="m-0 text-dark">Pengeluaran</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Pengeluaran</li>
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
                            <p>Total Pengeluaran</p>
                            <h3>Rp <span id="rupiah"></span></h3>
                        </div>
                        <div class="icon">
                            <i class="fas fa-money-bill"></i>
                        </div>
                    </div>
                </div>

                {{-- <div class="col-12 col-sm-4 col-md-4">
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
                </div> --}}
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
                                <div class="form-group">
                                    <select name="filter_tahun" id="filter_tahun" class="form-control ml-2">
                                        <option value="" disabled selected>Pilih Tahun</option>
                                        @php
                                            $tahun = date('Y');
                                        @endphp
                                        @for ($i = 2022; $i <= $tahun; $i++)
                                            <option value="{{ $i }}">
                                                {{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="form-group mx-1">
                                    <button type="button" class="btn btn-info" id="filter">FILTER</button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered table-hover" id="table1">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Bulan</th>
                                        <th>Debet</th>
                                        <th>Kredit</th>
                                        <th>Nama Akun</th>
                                        <th>Nota</th>
                                        <th>Keterangan</th>
                                        <th>User Added</th>
                                        <th>Date Added</th>
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
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <input type="hidden" name="id" id="id">
                <div class="modal-body">
                    <form action="" id="modalForm">
                        @csrf
                        <div class="row">
                            <div class="col-12 col-sm-3">
                                <div class="form-group">
                                    <label for="">Tanggal</label>
                                    <input type="date" name="tanggal" id="tanggal" class="form-control">
                                </div>
                            </div>
                            <div class="col-12 col-sm-3">
                                <div class="form-group">
                                    <label for="">Pilih Akun</label>
                                    <select class="form-control select2" name="akuns_id" id="akuns_id">
                                        <option disabled selected value="">Pilih</option>
                                        @foreach ($akun as $item)
                                            <option value="{{ $item->id }}">{{ $item->keterangan }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-sm-3">
                                <div class="form-group">
                                    <label for="">Nominal</label>
                                    <input type="text" name="nominal" id="nominal" placeholder="Rp "
                                        class="form-control">
                                </div>
                            </div>
                            <div class="col-12 col-sm-3">
                                <div class="form-group">
                                    <label for="">Upload Nota</label>
                                    <input type="file" name="nota" id="nota" class="form-control">
                                </div>
                            </div>
                            <div class="col-12 col-sm-3">
                                <div class="form-group">
                                    <label for="">Keterangan</label>
                                    <input type="text" name="keterangan" id="keterangan" class="form-control">
                                </div>
                            </div>
                            <div class="col-12 col-sm-3">
                                <div class="form-group">
                                    <label for="">Pengurus</label>
                                    <input type="text" name="pengurus" id="pengurus" class="form-control" disabled
                                        value="{{ auth()->user()->name }}">
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-danger">Tambahkan</button>
                    </form>
                    <hr>
                    <table class="table table-bordered table-hover mt-2" id="table2">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Nama Akun</th>
                                <th>Debet</th>
                                <th>Kredit</th>
                                <th>Keterangan</th>
                                <th>Opsi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <form action="" method="post" id="formFinish">
                        @csrf
                        <button type="submit" class="btn btn-danger" id="finish">Finish</button>
                    </form>
                </div>
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
    <script src="{{ asset('assets/plugins/jquery-validation/additional-methods.min.js') }}"></script>
    <!-- Sweetalert2 -->
    <script src="{{ asset('assets/plugins/sweetalert2/sweetalert2.all.min.js') }}"></script>
    <!-- JQuery mask -->
    <script src="{{ asset('assets/plugins/jquery-mask/jquery.mask.min.js') }}"></script>
    <!-- SELECT2 -->
    <script src="{{ asset('assets/plugins/select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/ekko-lightbox/ekko-lightbox.min.js') }}"></script>
    <script>
        function get_total(tb, metode, bulan = '', tahun = '') {
            var result = '';
            $.ajax({
                type: "get",
                url: "{{ url('total_keuangan') }}/" + tb + "/" + metode + "/" + bulan + "/" + tahun,
                async: false,
                success: function(res) {
                    result = res;
                }
            });

            return result;
        }

        /* Fungsi formatRupiah */
        function formatRupiah(num) {
            var p = num.toFixed(0).split(".");
            return "Rp " + p[0].split("").reverse().reduce(function(acc, num, i, orig) {
                return num + (num != "-" && i && !(i % 3) ? "." : "") + acc;
            }, "");
        }

        function cart() {
            $.get("{{ route('json.cart.pengeluaran') }}",
                function(data, textStatus, jqXHR) {
                    var html = '';
                    if (data.length > 0) {
                        $('.modal-footer').show();
                        $.each(data, function(i, v) {
                            html += '<tr>';
                            html += '<td>' + v.tanggal + '</td>';
                            html += '<td>' + v.akun.keterangan + '</td>';
                            html += '<td>' + formatRupiah(v.debet) + '</td>';
                            html += '<td>' + formatRupiah(v.kredit) + '</td>';
                            html += '<td>' + v.keterangan + '</td>';
                            html +=
                                '<td><button class="btn btn-sm btn-danger" id="hapus" data-id="' +
                                v.id +
                                '"><i class="fa fa-trash" aria-hidden="true"></i></button></td>';
                            html += '</tr>';
                        });
                    } else {
                        $('.modal-footer').hide();
                        html += '<tr class="text-center">';
                        html += '<td colspan="6">No Data</td>';
                        html += '</tr>';
                    }
                    $('#table2 tbody').html(html);
                },
                "JSON"
            );
        }

        cart();
        refresh_total();

        function refresh_total(m = '', y = '') {
            $('.small-box').eq(0).find('#rupiah').text(get_total('pengeluaran', 'all', m, y));
            $('.small-box').eq(1).find('#rupiah').text(get_total('pengeluaran', 'cash', m, y));
            $('.small-box').eq(2).find('#rupiah').text(get_total('pengeluaran', 'transfers', m, y));
        }

        $(document).ready(function() {
            var role = "{{ auth()->user()->role }}";
            var jabatan = "{{ Session::get('jabatan') }}";
            var isVisibleColumns = (jabatan.includes("ketua", "pembina") ? false : true);

            $.fn.modal.Constructor.prototype._enforceFocus = function() {};

            var bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September',
                'Oktober', 'November', 'Desember'
            ];

            $(function() {
                $(document).on('click', '[data-toggle="lightbox"]', function(event) {
                    event.preventDefault();
                    $(this).ekkoLightbox({
                        alwaysShowClose: true
                    });
                });
            });



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
                    'url': "{{ route('json.pengeluaran') }}",
                    'data': function(data) {
                        var f_bulan = $('#filter_bulan').val();
                        var f_tahun = $('#filter_tahun').val();
                        data.searchByBulan = f_bulan;
                        data.searchByTahun = f_tahun;
                        data._token = '{{ csrf_token() }}';
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
                        data: 'debet',
                        name: 'debet'
                    },
                    {
                        data: 'kredit',
                        name: 'kredit'
                    },
                    {
                        data: 'akun.keterangan',
                        name: 'akun.keterangan'
                    },
                    {
                        data: 'nota',
                        render: function(data, type, row, meta) {
                            return '<a href="{{ url('nota') }}' + "/" + row.nota +
                                '" class="badge badge-primary" data-toggle="lightbox" data-title="' +
                                row.keterangan + '"><i class="fas fa-image"></i> lihat</a>';
                        }
                    },
                    {
                        data: 'keterangan',
                        name: 'keterangan'
                    },
                    {
                        data: 'users.mhs.name',
                        name: 'users.mhs.name'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        visible: isVisibleColumns,
                    },
                ],
                order: [
                    [9, "asc"]
                ],
                drawCallback: function(settings) {
                    var span = 1;
                    var prevTD = "";
                    var prevTDVal = "";

                    $("#table1 tr td:last-child").each(
                        function() { //for each first td in every tr
                            var $this = $(this);
                            if ($this.text() ==
                                prevTDVal) { // check value of previous td text
                                span++;
                                if (prevTD != "") {
                                    prevTD.attr("rowspan",
                                        span); // add attribute to previous td
                                    prevTD.addClass('align-middle');
                                    $this.remove(); // remove current td
                                }
                            } else {
                                prevTD = $this; // store current td 
                                prevTDVal = $this.text();
                                span = 1;
                            }
                        });
                    $("#table1").find('span').remove();
                },
            });

            // filter custom
            $('#filter').click(function() {
                var m = $('#filter_bulan').val();
                var y = $('#filter_tahun').val();
                if (m == null || y == null) {
                    alert('form filter tidak boleh kosong!');
                    return false;
                }
                table.draw();
                refresh_total(m, y);
            });

            // open modal tambah
            $('#tambah').click(function(e) {
                e.preventDefault();
                $('#modal').modal('show');
                $('#modal').find('.modal-title').text('Tambah Pengeluaran');
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
                    url: "{{ route('edit.pengeluaran') }}",
                    type: "GET",
                    dataType: "JSON",
                    cache: false,
                    data: {
                        'id': id
                    },
                    success: function(data) {
                        $('#modal form').find('#tanggal').val(data.tanggal);
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
                            url: "{{ route('delete.pengeluaran') }}",
                            type: "GET",
                            dataType: "JSON",
                            data: {
                                'id': id
                            },
                            cache: false,
                            success: function(response) {
                                if (response.status) {
                                    table.ajax.reload();
                                    refresh_total();
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

            // delete cart
            $('#table2').on("click", "#hapus", function(e) {
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
                            url: "{{ route('delete.cart.pengeluaran') }}",
                            type: "GET",
                            dataType: "JSON",
                            data: {
                                'id': id
                            },
                            cache: false,
                            success: function(response) {
                                cart();
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

            // tambah cart
            var validator = $("#modalForm").validate({
                rules: {
                    tanggal: {
                        required: true,
                    },
                    nominal: {
                        required: true,
                    },
                    akuns_id: {
                        required: true,
                    },
                    saldo: {
                        required: true,
                    },
                    nota: {
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
                    $.ajax({
                        url: "{{ route('insert.cart.pengeluaran') }}",
                        type: "POST",
                        dataType: "JSON",
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: new FormData(form),
                        success: function(response) {
                            cart();
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

            // tambah data
            var validator = $("#formFinish").validate({
                rules: {
                    tanggal: {
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
                    nota: {
                        required: function() {
                            return $('#modalForm #id').val() == "";
                        },
                        extension: "jpeg|png|jpg",
                    }
                },
                errorElement: "div",
                errorPlacement: function(error, element) {
                    if (element.hasClass('select2-hidden-accessible') && element.next(
                            '.select2-container').length) {
                        error.insertAfter(element.next('.select2-container'));
                    } else if (element.parent('.input-group').length) {
                        error.insertAfter(element.parent());
                    } else if (element.prop('type') === 'radio' && element.parent(
                            '.radio-inline')
                        .length) {
                        error.insertAfter(element.parent().parent());
                    } else if (element.prop('type') === 'checkbox' || element.prop(
                            'type') ===
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
                    // if (id == "") {
                    $.ajax({
                        url: "{{ route('insert.pengeluaran') }}",
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
                    // } else {
                    //     $.ajax({
                    //         url: "{{ route('update.pengeluaran') }}",
                    //         type: "POST",
                    //         dataType: "JSON",
                    //         cache: false,
                    //         contentType: false,
                    //         processData: false,
                    //         data: new FormData(form),
                    //         success: function(response) {
                    //             if (response.status) {
                    //                 Swal.fire({
                    //                     icon: 'success',
                    //                     title: response.message,
                    //                     showCancelButton: false,
                    //                     showConfirmButton: true
                    //                 }).then(function() {
                    //                     $('#modal').modal('hide');
                    //                     table.ajax.reload();
                    //                 });
                    //             } else {
                    //                 Swal.fire({
                    //                     icon: 'error',
                    //                     title: response.message,
                    //                     showCancelButton: false,
                    //                     showConfirmButton: true
                    //                 }).then(function() {
                    //                     table.ajax.reload();
                    //                 });
                    //             }
                    //         },
                    //         error: function(response) {
                    //             Swal.fire({
                    //                 icon: 'error',
                    //                 title: 'Opps!',
                    //                 text: 'server error!'
                    //             });
                    //             console.log(response);
                    //         }
                    //     });
                    // }
                    cart();
                    refresh_total();
                }
            });
        });
    </script>
@endsection
