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
                            <h3 class="card-title">Laporan ARUSKAS</h3>
                        </div>
                        <form method="POST">
                            @csrf
                            <div class="card-body row">
                                <div class="col-12 col-md-2 my-auto">
                                    <label for="">Pilih Periode</label>
                                </div>
                                @if (auth()->user()->role == 'kemahasiswaan')
                                    <div class="col-12 col-md-3">
                                        <input type="hidden" name="jenis" value="lpj">
                                        <select class="form-control" name="ukms_id" id="ukms_id">
                                            <option value="" disabled selected>Pilih UKM</option>
                                            @foreach ($ukm as $item)
                                                <option value="{{ $item->id }}">{{ ucwords($item->nama) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif
                                <div class="col-12 col-md-3">
                                    <input type="hidden" name="jenis" value="lpj">
                                    <select class="form-control" name="periode" id="periode">
                                        <option value="" disabled selected>Pilih</option>
                                        @for ($i = date('Y'); $i > date('Y', strtotime('-2 year')); $i--)
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

            var validator = $("form").validate({
                rules: {
                    ukms_id: {
                        required: true,
                    },
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
                    var ukms_id = $(form).find('#ukms_id').val();
                    var periode = $(form).find('#periode').val();
                    $('span#periode').text(periode);
                    var url =
                        "{{ url('laporan-pdf/aruskas/') }}" + "/" +
                        periode.replace('/', '-') + "/" + null + "/" + (ukms_id === undefined ? 'null' :
                            ukms_id);
                    window.open(url, '_blank');
                }
            });
        });
    </script>
@endsection
