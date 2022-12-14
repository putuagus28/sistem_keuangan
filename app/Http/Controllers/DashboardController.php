<?php

namespace App\Http\Controllers;

use App\Mahasiswa;
use App\Ukm;
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
    public function index()
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
                'total_cash' => $this->total_metode('cash'),
                'total_transfers' => $this->total_metode('transfers'),

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
                'total_cash' => $this->total_metode('cash'),
                'total_transfers' => $this->total_metode('transfers'),
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
        return view('dashboard-' . $page, $data);
    }

    public function pemasukan()
    {
        $q1 = Pemasukan::where('ukms_id', Session::get('ukms_id'))
            ->whereMonth('tanggal', date('m'))
            ->sum('nominal');
        // $q2 = Pembayaran::where('ukms_id',Session::get('ukms_id'))
        // ->whereMonth('tanggal',date('m'))
        // ->sum('nominal');

        return $q1;
    }

    public function total_metode($metode = null)
    {
        $q1 = Pemasukan::where('ukms_id', Session::get('ukms_id'))
            ->where('metode', $metode)
            ->whereMonth('tanggal', date('m'))
            ->sum('nominal');
        // $q2 = Pembayaran::where('ukms_id', Session::get('ukms_id'))
        //     ->where('metode', $metode)
        //     ->whereMonth('tanggal', date('m'))
        //     ->sum('nominal');
        $q3 = Pengeluaran::where('ukms_id', Session::get('ukms_id'))
            ->where('metode', $metode)
            ->whereMonth('tanggal', date('m'))
            ->sum('nominal');

        return $q1 - $q3;
    }

    public function pengeluaran()
    {
        $q1 = Pengeluaran::where('ukms_id', Session::get('ukms_id'))
            ->whereMonth('tanggal', date('m'))
            ->sum('nominal');
        return $q1;
    }

    public function chart()
    {
        $data['bulan'] = [
            "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus",
            "September", "Oktober", "November", "Desember"
        ];

        $total = [];
        $total2 = [];
        for ($i = 1; $i <= 12; $i++) {
            $db = DB::table("pemasukans")
                ->selectRaw('SUM(nominal) as total')
                ->where('ukms_id', Session::get('ukms_id'))
                ->whereMonth('tanggal', '=', $i)
                ->whereYear('tanggal', '=', date('Y'))
                ->groupBy(DB::raw("MONTH(tanggal)"))
                ->get();
            if ($db->count() == null) {
                $total[] = 0;
            } else {
                foreach ($db as $val) {
                    $total[] = $val->total;
                }
            }
        }

        for ($i = 1; $i <= 12; $i++) {
            $db = DB::table("pengeluarans")
                ->selectRaw('SUM(nominal) as total')
                ->where('ukms_id', Session::get('ukms_id'))
                ->whereMonth('tanggal', '=', $i)
                ->whereYear('tanggal', '=', date('Y'))
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
        $data['total'] = $total;
        $data['total2'] = $total2;
        $data['title'] = 'Pemasukan ' . date('Y');
        $data['title2'] = 'Pengeluaran ' . date('Y');

        return response()->json(array('data' => $data));
    }

    public function chartukm()
    {
        $total = [];
        $total2 = [];
        $ukm =  Ukm::all();
        foreach ($ukm as $v) {
            $data['ukm'][] = ucwords($v->nama);
            $db = DB::table("pemasukans")
                ->selectRaw('SUM(nominal) as total')
                ->where('ukms_id', $v->id)
                ->whereMonth('tanggal', '=', date('n'))
                ->whereYear('tanggal', '=', date('Y'))
                ->groupBy(DB::raw("MONTH(tanggal)"))
                ->get();
            if ($db->count() == null) {
                $total[] = 0;
            } else {
                foreach ($db as $val) {
                    $total[] = $val->total;
                }
            }

            $db = DB::table("pengeluarans")
                ->selectRaw('SUM(nominal) as total')
                ->where('ukms_id', $v->id)
                ->whereMonth('tanggal', '=', date('n'))
                ->whereYear('tanggal', '=', date('Y'))
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

        $data['total'] = $total;
        $data['total2'] = $total2;
        $data['title'] = 'Pemasukan ' . date('Y');
        $data['title2'] = 'Pengeluaran ' . date('Y');

        return response()->json(array('data' => $data));
    }
}
