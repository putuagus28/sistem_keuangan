@extends('layouts.app')

@section('title', $title)

@section('css')
    <!-- Sweetalert2 -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.css') }}">
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <!-- DatePicker -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/datepicker/bootstrap-datepicker.min.css') }}">
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
                    <div class="card card-danger">
                        <div class="card-header d-flex flex-row align-items-center">
                            <h3 class="card-title">Laporan LPJ</h3>
                        </div>
                        <form method="POST">
                            @csrf
                            <div class="card-body row">
                                <div class="col-12 col-md-2 my-auto">
                                    <label for="">Pilih Periode</label>
                                </div>
                                <div class="col-12 col-md-3">
                                    <input type="hidden" name="jenis" value="lpj">
                                    <select class="form-control" name="periode" id="periode">
                                        <option value="" disabled selected>Pilih</option>
                                        @for ($i = date('Y', strtotime('-1 year')); $i < 2050; $i++)
                                            <option value="{{ $i . '/' . ($i + 1) }}">{{ $i . '/' . ($i + 1) }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            <div class="card-footer d-flex flex-row align-items-center">
                                {{-- <button type="button" id="cetak" class="btn btn-danger"><i class="fa fa-print"
                                        aria-hidden="true"></i> Cetak</button> --}}
                                <button type="submit" id="lihat" class="btn btn-danger">Preview</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="row">
                {{-- Laporan Transaksi Simpanan --}}
                <div class="col-12 col-md-12">
                    <div class="card card-dark">
                        <div class="card-header d-flex flex-row align-items-center">
                            <h3 class="card-title">Laporan Penanggung Jawab Kegiatan <span id="periode"></span></h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered table-striped" id="table1">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Nama Kegiatan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <!--/. container-fluid -->
    </section>

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
    <!-- DatePicker -->
    <script src="{{ asset('assets/plugins/datepicker/bootstrap-datepicker.min.js') }}"></script>
    <!-- ChartJS -->
    <script src="{{ asset('assets/plugins/chart.js/Chart.min.js') }}"></script>
    <script>
        $(document).ready(function() {

            function openInNewTab(url) {
                window.open(url, '_blank').focus();
            }

            var validator = $("form").validate({
                rules: {
                    periode: {
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
                    var data = $(form).serialize();
                    var periode = $(form).find('#periode').val();
                    $('span#periode').text(periode);
                    $.ajax({
                        type: "POST",
                        url: "{{ route('post.laporan') }}",
                        data: data,
                        dataType: "json",
                        success: function(res) {
                            if (res.query) {
                                $('#table1').find('tbody').html('');
                                var html = '';
                                if (res.query.length >= 1) {
                                    $.each(res.query, function(i, val) {
                                        var url =
                                            "{{ url('laporan-pdf/lpj/') }}" + "/" +
                                            periode.replace('/', '-') + "/" + val
                                            .id;
                                        html += '<tr>';
                                        html += '<td>' + (i + 1) + '</td>';
                                        html += '<td>' + val.tanggal + '</td>';
                                        html += '<td>' + val.nama_kegiatan +
                                            '</td>';
                                        html +=
                                            '<td><a class="btn btn-danger" target="_blank" href="' +
                                            url +
                                            '"><i class="fa fa-print" aria-hidden="true"></i> Cetak</a></td>';
                                        html += '</tr>';
                                    });
                                } else {
                                    html += '<tr>';
                                    html +=
                                        '<td colspan="4" class="text-center">No Data</td>';
                                    html += '</tr>';
                                }

                                $('#table1').find('tbody').append(html);
                            }
                        }
                    });
                }
            });
        });
    </script>
@endsection
