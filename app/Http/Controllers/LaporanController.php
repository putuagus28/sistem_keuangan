<?php

namespace App\Http\Controllers;

use App\Kegiatan;
use App\Pemasukan;
use App\Pengeluaran;
use App\Ukm;
use App\Jurnal;
use App\Akun;
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
        } elseif ($jenis == "jurnal") {
            $data = [
                'title' => 'Laporan ' . strtoupper($jenis) . " Umum",
                'ukm' => Ukm::all(),
            ];
            return view('laporan.jurnal', $data);
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
                ->where('ukms_id', Session::get('ukms_id'))
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
        } else if ($request->jenis == "jurnal") {
            $periode = explode('/', $request->periode);
            $query = Jurnal::with('akun')
                ->select('*', DB::raw('DATE_FORMAT(tanggal, "%d-%m-%Y") as tanggal'))
                ->whereYear('tanggal', '>=', $periode[0])
                ->whereYear('tanggal', '<=', $periode[1])
                ->where('ukms_id', Session::get('ukms_id'))
                ->orderBy('created_at', 'asc')
                ->get();
            $data = [
                'query' => $query,
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
            $akun = Akun::where('ukms_id', $ukm_id)
                ->whereIn('nama_reff', ['Activa'])
                ->orderBy('keterangan', 'asc')
                ->get();
            $akuns = [];
            foreach ($akun as $a) {
                $akuns[] = $a->id;
            }
            $query1 = Pemasukan::with('akun')->select('*', DB::raw('DATE_FORMAT(tanggal, "%d-%m-%Y") as tanggal'))
                ->whereYear('tanggal', '>=', $periode[0])
                ->whereYear('tanggal', '<=', $periode[1])
                ->where('ukms_id', $ukm_id)
                ->whereIn('akuns_id', $akuns)
                ->orderBy('tanggal', 'desc')
                ->get();
            $query2 = Pengeluaran::with('akun')->select('*', DB::raw('DATE_FORMAT(tanggal, "%d-%m-%Y") as tanggal'))
                ->whereYear('tanggal', '>=', $periode[0])
                ->whereYear('tanggal', '<=', $periode[1])
                ->where('ukms_id', $ukm_id)
                ->whereIn('akuns_id', $akuns)
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
