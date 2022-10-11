@extends('layouts.app')

@section('title', 'Dashboard Nasabah')

@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            @if (session('info'))
                <div class="alert alert-danger">
                    <strong><i class="fas fa-exclamation-triangle"></i></strong>
                    {!! session('info') !!}
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
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- Info boxes -->
            <div class="row">

                <div class="col-12 col-sm-6 col-md-4">
                    <div class="small-box bg-dark">
                        <div class="inner">
                            <h3>{{ $ukm }}</h3>
                            <p>Total UKM</p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-book" aria-hidden="true"></i>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-md-4">
                    <div class="small-box bg-purple">
                        <div class="inner">
                            <h3>{{ $mhs }}</h3>
                            <p>Total Mahasiswa</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-md-4">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3>{{ $pembina }}</h3>
                            <p>Total Pembina UKM</p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-users" aria-hidden="true"></i>
                        </div>
                    </div>
                </div>

            </div>


            {{-- Chart --}}
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-dark">
                        <!-- /.card-header -->
                        <div class="card-header">
                            <h3 class="card-title font-weight-bold"><i class="fas fa-chart-bar"></i> <span
                                    id="chart_title"></span> Grafik Keuangan Per UKM Bulan
                                {{ $bulan[date('n', strtotime('-1 months'))] }}</h3>
                        </div>
                        <div class="card-body">
                            <div class="chart">
                                <canvas id="barChart"
                                    style="min-height: 350px; height: 350px; max-height: 350px;max-width: 100%;"></canvas>
                            </div>
                        </div>
                        <!-- ./card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>
        </div>
        <!--/. container-fluid -->
    </section>
@endsection

@section('script')
    <!-- ChartJS -->
    <script src="{{ asset('assets/plugins/chart.js/Chart.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            var url = "{{ route('chartukm') }}";
            var Total = [];
            var Total2 = [];
            var Ukm = [];
            var Title = [];
            var Title2 = [];
            $.get(url, function(response) {
                $.each(response, function(index, data) {
                    Total.push(data.total);
                    Total2.push(data.total2);
                    Ukm.push(data.ukm);
                    Title.push(data.title);
                    Title2.push(data.title2);
                });

                var areaChartData = {
                    labels: Ukm[0],
                    datasets: [{
                            label: Title2,
                            backgroundColor: 'rgba(255,55,55,1)',
                            borderColor: 'rgba(255,55,55,1)',
                            pointRadius: false,
                            pointColor: 'rgba(255,55,55,1)',
                            pointStrokeColor: '#c1c7d1',
                            pointHighlightFill: '#fff',
                            pointHighlightStroke: 'rgba(220,220,220,1)',
                            data: Total2[0]
                        },
                        {
                            label: Title,
                            backgroundColor: 'rgb(53,161,255)',
                            borderColor: 'rgba(210, 214, 222, 1)',
                            pointRadius: false,
                            pointColor: 'rgba(210, 214, 222, 1)',
                            pointStrokeColor: '#c1c7d1',
                            pointHighlightFill: '#fff',
                            pointHighlightStroke: 'rgba(220,220,220,1)',
                            data: Total[0]
                        }
                    ]
                }
                //-------------
                //- BAR CHART -
                //-------------
                var barChartCanvas = $('#barChart').get(0).getContext('2d');
                var barChartData = $.extend(true, {}, areaChartData);
                var temp0 = areaChartData.datasets[0]
                var temp1 = areaChartData.datasets[1]
                barChartData.datasets[0] = temp1
                barChartData.datasets[1] = temp0

                var barChartOptions = {
                    responsive: true,
                    maintainAspectRatio: false,
                    datasetFill: false,
                    // tooltips: {
                    //     callbacks: {
                    //         label: function(tooltipItem, data) {
                    //             return tooltipItem.yLabel.toFixed(2).replace(/\d(?=(\d{3})+\.)/g,
                    //                 '$&,');
                    //         }
                    //     }
                    // }

                }
                Chart.scaleService.updateScaleDefaults('linear', {
                    ticks: {
                        callback: function(tick) {
                            return '$' + tick.toLocaleString();
                        }
                    }
                });
                Chart.defaults.global.tooltips.callbacks.label = function(tooltipItem, data) {
                    var dataset = data.datasets[tooltipItem.datasetIndex];
                    var datasetLabel = dataset.label || '';
                    return datasetLabel + ": Rp " + dataset.data[tooltipItem.index].toLocaleString();
                };

                new Chart(barChartCanvas, {
                    type: 'bar',
                    data: barChartData,
                    options: barChartOptions
                });

            });
        });
    </script>
@endsection
