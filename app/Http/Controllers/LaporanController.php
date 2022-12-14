<?php

namespace App\Http\Controllers;

use App\Kegiatan;
use App\Pemasukan;
use App\Pengeluaran;
use App\Ukm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use PDF;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    public function index($jenis = "")
    {
        if ($jenis == "lpj") {
            $data = [
                'title' => 'Laporan ' . strtoupper($jenis)
            ];
            return view('laporan.lpj', $data);
        } elseif ($jenis == "aruskas") {
            $data = [
                'title' => 'Laporan ' . strtoupper($jenis),
                'ukm' => Ukm::all(),
            ];
            return view('laporan.aruskas', $data);
        }

        return abort(404);
    }

    public function getLaporan(Request $request)
    {
        if ($request->jenis == "lpj") {
            $periode = explode('/', $request->periode);
            $query = Kegiatan::select('*', DB::raw('DATE_FORMAT(tanggal, "%d-%m-%Y") as tanggal'))
                ->whereYear('tanggal', '>=', $periode[0])
                ->whereYear('tanggal', '<=', $periode[1])
                ->orderBy('tanggal', 'desc')
                ->get();
            $data = [
                'query' => $query,
                'jenis' => $request->jenis
            ];
        } else if ($request->jenis == "aruskas") {
            $data = [
                'query' => '',
                'jenis' => $request->jenis
            ];
        }
        return response()->json($data);
    }

    // Generate PDF
    public function generatePDF($jenis, $periode, $id = null, $ukms_id = null)
    {

        $periode = explode('-', $periode);
        if ($jenis == "lpj") {
            $query = Kegiatan::with('users', 'detail')
                ->select('*', DB::raw('DATE_FORMAT(tanggal, "%d-%m-%Y") as tanggal'))
                ->where('id', $id)
                ->whereYear('tanggal', '>=', $periode[0])
                ->whereYear('tanggal', '<=', $periode[1])
                ->orderBy('tanggal', 'desc')
                ->get();
            $data = [
                'query' => $query,
                'ukm' => Ukm::find(Session::get('ukms_id')),
                'periode' => $periode[0] . '/' . $periode[1],
                'jenis' => $jenis
            ];
        } else {
            if (auth()->user()->role == 'kemahasiswaan') {
                $ukm = Ukm::find($ukms_id);
                $ukm_id = $ukm->id;
            } else {
                $ukm = Ukm::find(Session::get('ukms_id'));
                $ukm_id = $ukm->id;
            }
            $query1 = Pemasukan::select('*', DB::raw('DATE_FORMAT(tanggal, "%d-%m-%Y") as tanggal'))
                ->whereYear('tanggal', '>=', $periode[0])
                ->whereYear('tanggal', '<=', $periode[1])
                ->where('ukms_id', $ukm_id)
                ->orderBy('tanggal', 'desc')
                ->get();
            $query2 = Pengeluaran::select('*', DB::raw('DATE_FORMAT(tanggal, "%d-%m-%Y") as tanggal'))
                ->whereYear('tanggal', '>=', $periode[0])
                ->whereYear('tanggal', '<=', $periode[1])
                ->where('ukms_id', $ukm_id)
                ->orderBy('tanggal', 'desc')
                ->get();
            $data = [
                'query1' => $query1,
                'query2' => $query2,
                'ukm' => $ukm,
                'periode' => $periode[0] . '/' . $periode[1],
                'jenis' => $jenis
            ];
        }

        // $pdf = PDF::loadView('laporan.pdf_view', $data);
        // $pdf = PDF::loadview('laporan.pdf_view', $data)->setPaper('A4', 'potrait');
        // return $pdf->download('laporan-pdf.pdf');
        // return $pdf->stream();
        return view('laporan.pdf_view', $data);
    }
}
