@extends('layouts.app')

@section('title', $title)

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
                            <h3 class="card-title"><i class="fa fa-2x fa-book" aria-hidden="true"></i></h3>
                            @if (Session::get('jabatan') === 'bendahara')
                                <button class="btn btn-danger ml-auto" id="tambah">Tambah</button>
                            @endif
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered table-hover" id="table1">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Nama Akun</th>
                                        <th>No Reff</th>
                                        <th>Debet</th>
                                        <th>Kredit</th>
                                        <th>Keterangan</th>
                                        <th>User Create</th>
                                        <th>Opsi</th>
                                    </tr>
                                </thead>
                                <tfoot align="left" class="font-weight-bold">
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </tfoot>
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
                            <div class="w-100"></div>
                            <div class="col-12 col-sm-3">
                                <div class="form-group">
                                    <label for="">Nama Akun</label>
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
                                    <label for="">No Reff</label>
                                    <input type="number" name="no_reff" id="no_reff" readonly class="form-control">
                                </div>
                            </div>

                            <div class="col-12 col-sm-3">
                                <div class="form-group">
                                    <label for="">Jenis Saldo</label>
                                    <select class="form-control select2" name="jenis_saldo" id="jenis_saldo">
                                        <option disabled selected value="">Pilih</option>
                                        <option value="debet">Debet</option>
                                        <option value="kredit">Kredit</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-12 col-sm-3">
                                <label for="">&nbsp;</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="basic-addon1">Rp</span>
                                    </div>
                                    <input type="number" class="form-control" name="saldo" id="saldo">
                                </div>
                            </div>

                        </div>
                        <button type="submit" class="btn btn-danger">Tambahkan</button>
                    </form>
                    <table class="table table-bordered table-hover mt-2" id="table2">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Nama Akun</th>
                                <th>No Reff</th>
                                <th>Debet</th>
                                <th>Kredit</th>
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
    <!-- Sweetalert2 -->
    <script src="{{ asset('assets/plugins/sweetalert2/sweetalert2.all.min.js') }}"></script>
    <!-- JQuery mask -->
    <script src="{{ asset('assets/plugins/jquery-mask/jquery.mask.min.js') }}"></script>
    <!-- SELECT2 -->
    <script src="{{ asset('assets/plugins/select2/js/select2.full.min.js') }}"></script>
    <script>
        function capitalizeFirstLetter(string) {
            return string.charAt(0).toUpperCase() + string.slice(1);
        }
        /* Fungsi formatRupiah */
        function formatRupiah(num) {
            var p = num.toFixed(0).split(".");
            return "Rp " + p[0].split("").reverse().reduce(function(acc, num, i, orig) {
                return num + (num != "-" && i && !(i % 3) ? "." : "") + acc;
            }, "");
        }

        $(document).ready(function() {
            var role = "{{ auth()->user()->role }}";
            $.fn.modal.Constructor.prototype._enforceFocus = function() {};

            $('.select2').select2({
                theme: 'bootstrap4',
                container: 'body'
            });

            function cart() {
                $.get("{{ route('json.cart') }}",
                    function(data, textStatus, jqXHR) {
                        var html = '';
                        if (data.length > 0) {
                            $('.modal-footer').show();
                            $.each(data, function(i, v) {
                                html += '<tr>';
                                html += '<td>' + v.tanggal + '</td>';
                                html += '<td>' + v.akun.keterangan + '</td>';
                                html += '<td>' + v.no_reff + '</td>';
                                html += '<td>' + formatRupiah(v.debet) + '</td>';
                                html += '<td>' + formatRupiah(v.kredit) + '</td>';
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

            cart()

            var table = $('#table1').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                autoWidth: false,
                'language': {
                    'loadingRecords': '&nbsp;',
                    'processing': '<i class="fas fa-spinner"></i>'
                },
                ajax: "{{ route('json.jurnal') }}",
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
                        data: 'akun.keterangan',
                        render: function(data, type, row, meta) {
                            var css = '';
                            if (row.debet == 'Rp 0') {
                                var css = 'text-right';
                            } else {
                                var css = 'text-left';
                            }

                            return '<span class="d-block ' + css + '">' + row.akun.keterangan +
                                '</span>';
                        },
                    },
                    {
                        data: 'akun.no_reff',
                        name: 'akun.no_reff'
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
                        data: 'keterangan',
                        render: function(data, type, row, meta) {
                            var btn = '';
                            if (row.keterangan == 'jurnal umum') {
                                btn = '<span class="p-2 bg-info">' + row.keterangan + '</span>';
                            } else if (row.keterangan == 'pengeluaran') {
                                btn = '<span class="p-2 bg-danger">' + row.keterangan + '</span>';
                            } else if (row.keterangan == 'pemasukan') {
                                btn = '<span class="p-2 bg-success">' + row.keterangan + '</span>';
                            }
                            return btn;
                        },
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
                "footerCallback": function(row, data, start, end, display) {
                    var api = this.api(),
                        data;


                    var numFormat = $.fn.dataTable.render.number('\.', ',', 0, 'Rp ').display;

                    // converting to interger to find total
                    var intVal = function(i) {
                        return typeof i === 'string' ?
                            i.replace(/[\/Rp\,/\.]/g, '') * 1 :
                            typeof i === 'number' ?
                            i : 0;
                    };

                    // computing column Total of the complete result 

                    var debet = api
                        .column(4)
                        .data()
                        .reduce(function(a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);

                    var kredit = api
                        .column(5)
                        .data()
                        .reduce(function(a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);

                    var balanced = (debet == kredit ?
                        '<span class="p-2 bg-success d-block text-center">BALANCED</span>' :
                        '<span class="p-2 bg-danger d-block text-center">NOT BALANCED</span>');


                    // Update footer by showing the total with the reference of the column index 
                    $(api.column(0).footer()).html('');
                    $(api.column(1).footer()).html('');
                    $(api.column(2).footer()).html('');
                    $(api.column(3).footer()).html('Total');
                    $(api.column(4).footer()).html(numFormat(debet));
                    $(api.column(5).footer()).html(numFormat(kredit));
                    $(api.column(6).footer()).html(balanced);
                    $(api.column(7).footer()).html('');
                    $(api.column(8).footer()).html('');
                },
                order: [
                    [1, "desc"]
                ]
            });

            $('#akuns_id').change(function(e) {
                e.preventDefault();
                var id = $(this).val();
                $.get("{{ route('getakun') }}", {
                        'id': id
                    },
                    function(data, textStatus, jqXHR) {
                        $('#no_reff').val(data.no_reff);
                    },
                );
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
                    title: 'Yakin untuk menghapus ini ?',
                    showDenyButton: true,
                    showCancelButton: false,
                    showConfirmButton: true,
                    confirmButtonText: `Hapus`,
                    denyButtonText: `Batal`,
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('delete.jurnal') }}",
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
                            url: "{{ route('delete.cart') }}",
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
                    url: "{{ route('edit.jurnal') }}",
                    type: "GET",
                    dataType: "JSON",
                    cache: false,
                    data: {
                        'id': id
                    },
                    success: function(data) {
                        $('#modal form').find('#tanggal').val(data.tanggal);
                        $('#modal form').find('#no_reff').val(data.no_reff);
                        $('#modal form').find('#akuns_id').val(data.akuns_id).change();
                        $('#modal form').find('#jenis_saldo').val((data.debet == 0 ?
                            'kredit' :
                            'debet')).change();
                        $('#modal form').find('#saldo').val((data.debet == 0 ? data.kredit :
                            data.debet));
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

            // tambah cart
            var validator = $("#modalForm").validate({
                rules: {
                    tanggal: {
                        required: true,
                    },
                    akuns_id: {
                        required: true,
                    },
                    jenis_saldo: {
                        required: true,
                    },
                    saldo: {
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
                        url: "{{ route('insert.cart') }}",
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

            // finish cart
            var validator = $("#formFinish").validate({
                rules: {

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
                        url: "{{ route('insert.jurnal') }}",
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
                            }
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

            // tambah data lama
            // var validator = $("#modalForm").validate({
            //     rules: {
            //         tanggal: {
            //             required: true,
            //         },
            //         akuns_id: {
            //             required: true,
            //         },
            //         jenis_saldo: {
            //             required: true,
            //         },
            //         saldo: {
            //             required: true,
            //         },
            //     },
            //     errorElement: "div",
            //     errorPlacement: function(error, element) {
            //         error.addClass('invalid-feedback');
            //         element.closest('.input-group, .form-group').append(error);
            //     },
            //     highlight: function(element, errorClass, validClass) {
            //         $(element).addClass('is-invalid');
            //     },
            //     unhighlight: function(element, errorClass, validClass) {
            //         $(element).removeClass('is-invalid');
            //     },
            //     submitHandler: function(form) {
            //         var id = $(form).find('#id').val();
            //         if (id == "") {
            //             $.ajax({
            //                 url: "{{ route('insert.jurnal') }}",
            //                 type: "POST",
            //                 dataType: "JSON",
            //                 cache: false,
            //                 contentType: false,
            //                 processData: false,
            //                 data: new FormData(form),
            //                 success: function(response) {
            //                     if (response.status) {
            //                         Swal.fire({
            //                             icon: 'success',
            //                             title: response.message,
            //                             showCancelButton: false,
            //                             showConfirmButton: true
            //                         }).then(function() {
            //                             $('#modal').modal('hide');
            //                             table.ajax.reload();
            //                         });
            //                     } else {
            //                         Swal.fire({
            //                             icon: 'error',
            //                             title: response.message,
            //                             showCancelButton: false,
            //                             showConfirmButton: true
            //                         }).then(function() {
            //                             table.ajax.reload();
            //                         });
            //                     }
            //                 },
            //                 error: function(response) {
            //                     Swal.fire({
            //                         icon: 'error',
            //                         title: 'Opps!',
            //                         text: 'server error!'
            //                     });
            //                     console.log(response);
            //                 }
            //             });
            //         } else {
            //             $.ajax({
            //                 url: "{{ route('update.jurnal') }}",
            //                 type: "POST",
            //                 dataType: "JSON",
            //                 cache: false,
            //                 contentType: false,
            //                 processData: false,
            //                 data: new FormData(form),
            //                 success: function(response) {
            //                     if (response.status) {
            //                         Swal.fire({
            //                             icon: 'success',
            //                             title: response.message,
            //                             showCancelButton: false,
            //                             showConfirmButton: true
            //                         }).then(function() {
            //                             $('#modal').modal('hide');
            //                             table.ajax.reload();
            //                         });
            //                     } else {
            //                         Swal.fire({
            //                             icon: 'error',
            //                             title: response.message,
            //                             showCancelButton: false,
            //                             showConfirmButton: true
            //                         }).then(function() {
            //                             table.ajax.reload();
            //                         });
            //                     }
            //                 },
            //                 error: function(response) {
            //                     Swal.fire({
            //                         icon: 'error',
            //                         title: 'Opps!',
            //                         text: 'server error!'
            //                     });
            //                     console.log(response);
            //                 }
            //             });
            //         }
            //     }
            // });
        });
    </script>
@endsection
