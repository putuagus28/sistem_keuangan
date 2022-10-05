<?php

namespace App\Http\Controllers;

use App\Mahasiswa;
use App\Ukm;
use App\AnggotaUkm;
use App\Pemasukan;
use App\Pembayaran;
use App\Pengeluaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class Ajax extends Controller
{
    public function total_keuangan($table, $metode = null, $b = null)
    {
        $bulan = !empty($b) ? $b : date('m');
        if ($table == 'pembayaran') {
            if ($metode != 'all') {
                $q = Pembayaran::where('ukms_id', Session::get('ukms_id'))
                    ->where('metode', $metode)
                    ->whereMonth('tanggal', $bulan)
                    ->where('ukms_id', Session::get('ukms_id'))
                    ->sum('nominal');
            } else {
                $q = Pembayaran::where('ukms_id', Session::get('ukms_id'))
                    ->whereMonth('tanggal', $bulan)
                    ->where('ukms_id', Session::get('ukms_id'))
                    ->sum('nominal');
            }
        } else if ($table == 'pemasukan') {
            if ($metode != 'all') {
                $q = Pemasukan::where('ukms_id', Session::get('ukms_id'))
                    ->where('metode', $metode)
                    ->whereMonth('tanggal', $bulan)
                    ->where('ukms_id', Session::get('ukms_id'))
                    ->sum('nominal');
            } else {
                $q = Pemasukan::where('ukms_id', Session::get('ukms_id'))
                    ->whereMonth('tanggal', $bulan)
                    ->where('ukms_id', Session::get('ukms_id'))
                    ->sum('nominal');
            }
        } else if ($table == 'pengeluaran') {
            if ($metode != 'all') {
                $q = Pengeluaran::where('ukms_id', Session::get('ukms_id'))
                    ->where('metode', $metode)
                    ->whereMonth('tanggal', $bulan)
                    ->where('ukms_id', Session::get('ukms_id'))
                    ->sum('nominal');
            } else {
                $q = Pengeluaran::where('ukms_id', Session::get('ukms_id'))
                    ->whereMonth('tanggal', $bulan)
                    ->where('ukms_id', Session::get('ukms_id'))
                    ->sum('nominal');
            }
        } else {
            $q = [];
        }

        return number_format($q, 0, ',', '.');
    }


    function hari($date)
    {
        $daftar_hari = array(
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu'
        );

        $namahari = date('l', strtotime($date));
        return $daftar_hari[$namahari];
    }

    function getTotal()
    {
        $total = 0;
        foreach ((array) session('cart') as $id => $details) {
            $total += $details['nominal'];
        }
        return $total;
    }

    function formatMoney($money = null)
    {
        return 'Rp ' . number_format($money, 0, ',', '.');
    }

    // list cart barang
    public function listCart(Request $request)
    {
        $html = '';
        $status = false;
        if (session('cart')) {
            foreach (session('cart') as $id => $item) {
                $html .= '<tr>';
                $html .= '<td>' . $item['tanggal'] . '</td>';
                $html .= '<td>' . $item['status'] . '</td>';
                $html .= '<td>' . $item['keterangan'] . '</td>';
                $html .= '<td>' . $this->formatMoney($item['nominal']) . '</td>';
                $html .= '<td class="text-right">
                <a href="javascript:void(0)" data-id="' . $id . '" data-cart="cart" class="btn btn-danger btn-sm m-1" id="delete"><i class="fa fa-trash" aria-hidden="true"></i></a>
                </td>';
                $html .= '</tr>';
            }
            $status = true;
        } else {
            $html .= '<td colspan="5" class="text-center">Belum ada transaksi</td>';
            $status = false;
        }
        return response()->json(['html' => $html, 'status' => $status, 'total' => 'Rp ' . number_format($this->getTotal(), 0, ',', '.')]);
    }

    // cart barang
    public function addToCart(Request $request)
    {
        try {
            $id = rand();
            $cart = session()->get('cart', []);
            // insert cart
            $cart[$id] = [
                "tanggal" => date('Y-m-d', strtotime($request->tanggal)),
                "status" => $request->status,
                "keterangan" => $request->keterangan,
                "nominal" => $request->nominal,
            ];

            session()->put('cart', $cart);
        } catch (\Exception $er) {
            return response()->json(['status' => false, 'message' => $er->getMessage()]);
        }
    }

    public function removeAll()
    {
        Session::forget('cart');
        return response()->json(['status' => true, 'message' => 'Success Remove']);
    }

    public function removeOne(Request $request)
    {
        if ($request->id) {
            $cart = session()->get('cart');
            if (isset($cart[$request->id])) {
                unset($cart[$request->id]);
                session()->put('cart', $cart);
            }
            return response()->json(['status' => true, 'message' => 'Success Remove']);
        }
    }

    // finish cart
    public function finish(Request $request)
    {
        try {
            DB::transaction(function () use ($request) {
                // insert trx
                $trx = new Kegiatan;
                $trx->tanggal = date('Y-m-d', strtotime($request->tanggal));
                $trx->nama_kegiatan = $request->nama_kegiatan;
                $trx->ukms_id = Session::get('ukms_id');
                $trx->users_id = auth()->user()->id;
                $trx->save();

                $id_trx = $trx->id;
                // insert detail
                foreach ((array) session('cart') as $id => $details) {
                    $detail = new DetailKegiatan;
                    $detail->tanggal = $details['tanggal'];
                    $detail->nominal = $details['nominal'];
                    $detail->status = $details['status'];
                    $detail->keterangan = $details['keterangan'];
                    $detail->kegiatans_id = $id_trx;
                    $detail->save();
                }
                $this->removeAll(true);
            });
            return response()->json(['status' => true, 'message' => 'Tersimpan']);
        } catch (\Exception $e) {
            // DB::rollBack();
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }
}
