<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan {{ strtoupper($jenis) }} UKM {{ strtoupper($ukm->nama) }}</title>
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('assets/dist/css/adminlte.min.css') }}">
    <style>
        @media print {

            .hidden-print,
            .hidden-print * {
                display: none !important;
            }
        }

        .tb1 {
            width: 100%;
            /* border: 1px solid rgb(87, 87, 87); */
            border-collapse: collapse;
        }

        .tb1 tr th,
        .tb1 tr td {
            /* border: 1px solid rgb(87, 87, 87); */
            padding: 5px 0px 5px 0px;
        }

        .table2 {
            border-collapse: collapse;
            border-spacing: 0px;
            width: 100%;
        }

        .table2 td,
        .table2 th {
            background-color: transparent;
            border: 1px solid silver;
            padding: 5px 8px;
        }
    </style>
</head>

<body class="p-2">
    <button type="button"class="btn btn-danger hidden-print" id="btnPrint">Print</button>
    @if ($jenis == 'lpj')
        <div id="lpj">
            <h4 class="text-center">Laporan Penanggung Jawab Kegiatan UKM {{ strtoupper($ukm->nama) }} <br> Periode
                {{ $periode }}
            </h4>
            <br>
            <div class="row">
                <div class="col-sm-3">
                    <table class="tb1">
                        <tr>
                            <td>Nama Kegiatan</td>
                            <td>:</td>
                            <td class="px-2">{{ $query[0]->nama_kegiatan }}</td>
                        </tr>
                        <tr>
                            <td>Tanggal </td>
                            <td>:</td>
                            <td class="px-2">{{ date('d-m-Y', strtotime($query[0]->tanggal)) }}</td>
                        </tr>
                    </table>
                </div>
            </div>
            <hr>
            <table class="table2">
                <thead>
                    <tr style="background-color: silver">
                        <th colspan="6" class="text-center">Pengelolaan Keuangan</th>
                    </tr>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th>Keterangan</th>
                        <th>Jumlah</th>
                        <th>Pengurus</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($query as $item)
                        @php
                            $in = 0;
                            $out = 0;
                        @endphp
                        @foreach ($item->detail as $i => $d)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ date('d-m-Y', strtotime($d->tanggal)) }}</td>
                                <td>{{ $d->status }}</td>
                                <td>{{ $d->keterangan }}</td>
                                <td>Rp {{ number_format($d->nominal, 0, ',', '.') }}</td>
                                <td>{{ $item->users->name }}</td>
                            </tr>
                            @php
                                $in += $d->status == 'pemasukan' ? $d->nominal : 0;
                                $out += $d->status == 'pengeluaran' ? $d->nominal : 0;
                            @endphp
                        @endforeach
                        @php
                            $sisa = $in - $out;
                        @endphp
                        <tr>
                            <td colspan="3"></td>
                            <td style="background-color: silver" class="font-weight-bold">Sisa Kegiatan</td>
                            <td style="background-color: silver" class="font-weight-bold">Rp
                                {{ number_format($sisa, 0, ',', '.') }}</td>
                            <td style="background-color: silver"></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <h4 class="text-center">UKM {{ strtoupper($ukm->nama) }} <br> Laporan Arus Kas
        </h4>
        <h5 class="text-center">Periode {{ $periode }}</h5>
        <hr>
        <div class="mt-5 border p-3" style="background-color: #fff8ea;">
            <table class="w-100">
                <tr>
                    <td colspan="3" class="font-weight-bold">Arus Kas Masuk</td>
                </tr>
                @php
                    $total_m = 0;
                @endphp
                @foreach ($query1 as $item)
                    <tr>
                        <td class="pl-5">{{ $item->tanggal }}</td>
                        <td>{{ ucwords($item->keterangan) }}</td>
                        <td>Rp {{ number_format($item->nominal, 0, ',', '.') }}</td>
                    </tr>
                    @php
                        $total_m += $item->nominal;
                    @endphp
                @endforeach
                <tr>
                    <td colspan="" class="font-weight-bold py-3"></td>
                    <td class="font-weight-bold py-3">Total Arus Kas Masuk</td>
                    <td class="font-weight-bold">Rp {{ number_format($total_m, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td colspan="3" class="py-2"></td>
                </tr>
                <tr>
                    <td colspan="3" class="font-weight-bold">Arus Kas Keluar</td>
                </tr>
                @php
                    $total_k = 0;
                @endphp
                @foreach ($query2 as $item)
                    <tr>
                        <td class="pl-5">{{ $item->tanggal }}</td>
                        <td>{{ ucwords($item->keterangan) }}</td>
                        <td>Rp {{ number_format($item->nominal, 0, ',', '.') }}</td>
                    </tr>
                    @php
                        $total_k += $item->nominal;
                    @endphp
                @endforeach
                <tr>
                    <td colspan="" class="font-weight-bold py-3"></td>
                    <td class="font-weight-bold py-3">Total Arus Kas Keluar</td>
                    <td class="font-weight-bold">Rp {{ number_format($total_k, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td colspan="3" class="py-2"></td>
                </tr>
                <tr>
                    <td colspan="2" class="font-weight-bold">Sisa Kas UKM</td>
                    <td class="font-weight-bold">Rp {{ number_format($total_m - $total_k, 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>
    @endif
    {{-- <button id="btnPrint" class="hidden-print">Print</button> --}}
    <script>
        const $btnPrint = document.querySelector("#btnPrint");
        $btnPrint.addEventListener("click", () => {
            window.print();
        });
    </script>
</body>

</html>
