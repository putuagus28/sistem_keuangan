<?php

namespace App\Http\Controllers;

use App\Mahasiswa;
use App\Ukm;
use App\Akun;
use App\AnggotaUkm;
use App\Pemasukan;
use App\Pembayaran;
use App\Pembina;
use App\Pengeluaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $req)
    {

        $role = auth()->user()->role;
        $page = $role;

        if ($role == 'mahasiswa') {
            $data = [
                'ukm' => Ukm::find(Session::get('ukms_id')),
                'title' => 'Dashboard UKM <b>' . strtoupper(Ukm::find(Session::get('ukms_id'))->nama) . '</b>',
                'total_ukm' => AnggotaUkm::where('ukms_id', Session::get('ukms_id'))
                    ->where('jabatan', '!=', 'pembina')->count(),
                'total_saldo' => ($this->pemasukan() - $this->pengeluaran()),
                'total_pemasukan' => $this->pemasukan(),
                'total_pengeluaran' => $this->pengeluaran(),
                'total_cash' => $this->total_metode('kas kecil'),
                'total_transfers' => $this->total_metode('kas bank'),
            ];
            $page = 'pengurus-ukm';
            // jika mhs belum memiliki ukm atau memilih ukm maka tidak bisa masuk ke dashboard
            if (Session::get('ukms_id') == null)
                return redirect()->route('setprivilege')->with(['info' => 'Anda belum memilih ukm anda']);
        } elseif ($role == 'pembina') {
            $data = [
                'ukm' => Ukm::find(Session::get('ukms_id')),
                'title' => 'Dashboard ' . ucwords(auth()->user()->role) . ' UKM <b>' . strtoupper(Ukm::find(Session::get('ukms_id'))->nama) . '</b>',
                'total_ukm' => AnggotaUkm::where('ukms_id', Session::get('ukms_id'))
                    ->where('jabatan', '!=', 'pembina')->count(),
                'total_saldo' => ($this->pemasukan() - $this->pengeluaran()),
                'total_pemasukan' => $this->pemasukan(),
                'total_pengeluaran' => $this->pengeluaran(),
                'total_cash' => $this->total_metode('kas kecil'),
                'total_transfers' => $this->total_metode('kas bank'),
            ];
            $page = 'pengurus-ukm';
            if (Session::get('ukms_id') == null)
                return redirect()->route('setprivilege')->with(['info' => 'Anda belum membina ukm apapun, silahkan hubungi kemahasiswaan untuk input ukm']);
        } elseif ($role == 'kemahasiswaan') {
            $data = [
                'title' => 'Dashboard ' . ucwords(auth()->user()->role),
                'ukm' => Ukm::all()->count(),
                'mhs' => Mahasiswa::all()->count(),
                'pembina' => Pembina::all()->count(),
            ];
            $page = $role;
        }
        $data['bulan'] = [
            "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus",
            "September", "Oktober", "November", "Desember"
        ];

        if (empty($req->tahun)) {
            $data['tahun'] = [null, null];
        } else {
            $data['tahun'] = explode('/', $req->tahun);
        }


        return view('dashboard-' . $page, $data);
    }

    public function pemasukan($tahun1 = null, $tahun2 = null)
    {
        $q1 = Pemasukan::where('ukms_id', Session::get('ukms_id'))
            // ->whereBetween(DB::raw("YEAR(tanggal)"), [date('Y'), date('Y', strtotime("+1 year"))])
            ->sum('nominal');
        // $q2 = Pembayaran::where('ukms_id',Session::get('ukms_id'))
        // ->whereMonth('tanggal',date('m'))
        // ->sum('nominal');

        return $q1 / 2;
    }

    public function pengeluaran()
    {
        $q1 = Pengeluaran::where('ukms_id', Session::get('ukms_id'))
            // ->whereMonth('tanggal', date('m'))
            // ->whereBetween(DB::raw("YEAR(tanggal)"), [date('Y'), date('Y', strtotime("+1 year"))])
            ->sum('nominal');
        return $q1 / 2;
    }

    public function total_metode($metode = null)
    {
        $q1 = Akun::where('keterangan', $metode)
            ->where('ukms_id', Session::get('ukms_id'))
            // ->whereYear('created_at', date('Y'))
            ->sum('debet');
        $q2 = Akun::where('keterangan', $metode)
            ->where('ukms_id', Session::get('ukms_id'))
            // ->whereYear('created_at', date('Y'))
            ->sum('kredit');
        return $q1 - $q2;
    }

    public function chart($tahun1 = null, $tahun2 = null)
    {
        $data['bulan'] = [
            "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus",
            "September", "Oktober", "November", "Desember"
        ];

        $total = [];
        $total2 = [];

        if (empty($tahun1)) {
            $tahun1 = date('Y');
            $tahun2 = date('Y', strtotime("+1 years"));
        }

        if (empty(Session::get('ukms_id'))) {
            for ($i = 1; $i <= 12; $i++) {
                $db = DB::table("pemasukans")
                    ->selectRaw('SUM(nominal) as total')
                    ->whereMonth('tanggal', '=', $i)
                    // ->whereYear('tanggal', '=', date('Y'))
                    ->whereBetween(DB::raw("YEAR(tanggal)"), [$tahun1, $tahun2])
                    ->groupBy(DB::raw("MONTH(tanggal)"))
                    ->get();
                if ($db->count() == null) {
                    $total[] = 0;
                } else {
                    foreach ($db as $val) {
                        $total[] = $val->total / 2;
                    }
                }
            }

            for ($i = 1; $i <= 12; $i++) {
                $db = DB::table("pengeluarans")
                    ->selectRaw('SUM(nominal) as total')
                    ->whereMonth('tanggal', '=', $i)
                    // ->whereYear('tanggal', '=', date('Y'))
                    ->whereBetween(DB::raw("YEAR(tanggal)"), [$tahun1, $tahun2])
                    ->groupBy(DB::raw("MONTH(tanggal)"))
                    ->get();
                if ($db->count() == null) {
                    $total2[] = 0;
                } else {
                    foreach ($db as $val) {
                        $total2[] = $val->total / 2;
                    }
                }
            }
        } else {
            for ($i = 1; $i <= 12; $i++) {
                $db = DB::table("pemasukans")
                    ->selectRaw('SUM(nominal) as total')
                    ->where('ukms_id', Session::get('ukms_id'))
                    ->whereMonth('tanggal', '=', $i)
                    // ->whereYear('tanggal', '=', $tahun1)
                    // ->whereBetween(DB::raw("YEAR(tanggal)"), [date('Y'), date('Y', strtotime("+1 years"))])
                    ->whereBetween(DB::raw("YEAR(tanggal)"), [$tahun1, $tahun2])
                    ->groupBy(DB::raw("MONTH(tanggal)"))
                    ->get();
                if ($db->count() == null) {
                    $total[] = 0;
                } else {
                    foreach ($db as $val) {
                        $total[] = $val->total / 2;
                    }
                }
            }

            for ($i = 1; $i <= 12; $i++) {
                $db = DB::table("pengeluarans")
                    ->selectRaw('SUM(nominal) as total')
                    ->where('ukms_id', Session::get('ukms_id'))
                    ->whereMonth('tanggal', '=', $i)
                    // ->whereYear('tanggal', '=', date('Y'))
                    // ->whereBetween(DB::raw("YEAR(tanggal)"), [date('Y'), date('Y', strtotime("+1 years"))])
                    ->whereBetween(DB::raw("YEAR(tanggal)"), [$tahun1, $tahun2])
                    ->groupBy(DB::raw("MONTH(tanggal)"))
                    ->get();
                if ($db->count() == null) {
                    $total2[] = 0;
                } else {
                    foreach ($db as $val) {
                        $total2[] = $val->total;
                    }
                }
            }
        }
        $data['total'] = $total;
        $data['total2'] = $total2;
        $data['title'] = 'Pemasukan';
        $data['title2'] = 'Pengeluaran';

        return response()->json(array('data' => $data));
    }

    public function chartukm($tahun1 = null, $tahun2 = null)
    {
        $total = [];
        $total2 = [];
        if (empty($tahun1)) {
            $tahun1 = date('Y');
            $tahun2 = date('Y', strtotime("+1 years"));
        }

        $ukm =  Ukm::all();
        foreach ($ukm as $v) {
            $data['ukm'][] = ucwords($v->nama);
            $db = DB::table("pemasukans")
                ->selectRaw('SUM(nominal) as total')
                ->where('ukms_id', $v->id)
                // ->whereBetween(DB::raw("YEAR(tanggal)"), [date('Y'), date('Y', strtotime("+1 years"))])
                // ->groupBy(DB::raw("YEAR(tanggal)"))
                ->whereBetween(DB::raw("YEAR(tanggal)"), [$tahun1, $tahun2])
                ->groupBy('ukms_id')
                ->get();
            if ($db->count() == null) {
                $total[] = 0;
            } else {
                foreach ($db as $val) {
                    $total[] = $val->total / 2;
                }
            }

            $db = DB::table("pengeluarans")
                ->selectRaw('SUM(nominal) as total')
                ->where('ukms_id', $v->id)
                // ->whereBetween(DB::raw("YEAR(tanggal)"), [date('Y'), date('Y', strtotime("+1 years"))])
                // ->groupBy(DB::raw("YEAR(tanggal)"))
                ->whereBetween(DB::raw("YEAR(tanggal)"), [$tahun1, $tahun2])
                ->groupBy('ukms_id')
                ->get();
            if ($db->count() == null) {
                $total2[] = 0;
            } else {
                foreach ($db as $val) {
                    $total2[] = $val->total / 2;
                }
            }
        }

        $data['total'] = $total;
        $data['total2'] = $total2;
        $data['title'] = 'Pemasukan ' . date('Y') . '/' . date('Y', strtotime("+1 years"));
        $data['title2'] = 'Pengeluaran ' . date('Y') . '/' . date('Y', strtotime("+1 years"));

        return response()->json(array('data' => $data));
    }
}
