<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Pemasukan;
use App\Cart;
use App\Jurnal;
use App\AnggotaUkm;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use App\User;
use App\Akun;

class PemasukanController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            if (!empty($request->searchByBulan)) {
                $data = Pemasukan::select('*')
                    ->addSelect(DB::raw('MONTH(tanggal) as bulan', '*'))
                    ->with('ukm', 'akun', 'users.mhs')
                    ->where('ukms_id', Session::get('ukms_id'))
                    ->whereMonth('tanggal', $request->searchByBulan)
                    ->get();
            } else {
                $data = Pemasukan::select('*')
                    ->addSelect(DB::raw('MONTH(tanggal) as bulan', '*'))
                    ->with('ukm', 'akun', 'users.mhs')
                    ->where('ukms_id', Session::get('ukms_id'))
                    ->whereMonth('tanggal', date('m'))
                    ->get();
            }
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('tanggal', function ($row) {
                    return date('d-m-Y', strtotime($row->tanggal));
                })
                ->addColumn('created_at', function ($row) {
                    return date('d-m-Y G:i:s', strtotime($row->created_at));
                })
                ->addColumn('nominal', function ($row) {
                    return 'Rp ' . number_format($row->nominal, 0, ',', '.');
                })
                ->addColumn('debet', function ($row) {
                    return 'Rp ' . number_format($row->debet, 0, ',', '.');
                })
                ->addColumn('kredit', function ($row) {
                    return 'Rp ' . number_format($row->kredit, 0, ',', '.');
                })
                ->addColumn('action', function ($row) {
                    $btn = "";
                    if (Session::get('jabatan') == 'bendahara') {
                        if (!in_array($row->jabatan, ['ketua', 'bendahara'])) {
                            // $btn .= '<a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-success btn-sm mx-1" id="edit"><i class="fas fa-edit"></i></a>';
                            $btn .= '<span>' . $row->kode . '</span><a href="javascript:void(0)" data-id="' . $row->id . '" data-kode="' . $row->kode . '" class="btn btn-danger btn-sm mx-1" id="hapus"><i class="fa fa-trash" aria-hidden="true"></i></a>';
                        }
                    }
                    return $btn;
                })
                ->rawColumns(['action', 'tanggal', 'nominal', 'debet', 'kredit', 'created_at'])
                ->make(true);
        }
        $data = [
            'akun' => Akun::where('ukms_id', Session::get('ukms_id'))
                ->whereNotIn('nama_reff', ['Beban', 'Kewajiban'])
                ->orderBy('keterangan', 'asc')
                ->get(),
        ];
        return view('transaksi.pemasukan', $data);
        // dd(Session::get('ukms_id'));
    }

    public function insert_lama(Request $request)
    {
        try {
            $akun = Akun::find($request->akuns_id);
            $pem = new Pemasukan;
            $pem->tanggal = date('Y-m-d', strtotime($request->tanggal));
            $pem->nominal = str_replace(['Rp', '.', ' '], '', trim($request->nominal));
            $pem->metode = $request->metode;
            $pem->keterangan = $request->keterangan;
            $pem->ukms_id = Session::get('ukms_id');
            $pem->users_id = auth()->user()->id;
            // akutansi record
            $pem->no_reff = $akun->no_reff;
            $pem->{$request->jenis_saldo} = str_replace(['Rp', '.', ' '], '', trim($request->nominal));
            $pem->akuns_id = $request->akuns_id;
            $simpan = $pem->save();
            return response()->json(['status' => $simpan, 'message' => 'Sukses']);
        } catch (\Exception $err) {
            return response()->json(['status' => false, 'message' => $err->getMessage()]);
        }
    }

    public function insert(Request $req)
    {
        try {
            // jika balance
            if ($this->balance()) {
                $cart = Cart::with('akun')
                    ->where('kategori', 'pemasukan')
                    ->where('ukms_id', Session::get('ukms_id'))
                    ->orderBy('created_at', 'desc')
                    ->get();
                $total_modal = 0;
                $kode = Str::random(6);
                foreach ($cart as $item) {
                    $jenis_saldo = ($item->debet == 0 ? 'kredit' : 'debet');
                    $saldo = ($item->debet == 0 ? $item->kredit : $item->debet);
                    $total_modal += $saldo;
                    $akun = Akun::find($item->akuns_id);

                    $pem = new Pemasukan;
                    $pem->kode = $kode;
                    $pem->tanggal = date('Y-m-d', strtotime($item->tanggal));
                    $pem->nominal = str_replace(['Rp', '.', ' '], '', trim($saldo));
                    $pem->keterangan = $item->keterangan;
                    $pem->ukms_id = Session::get('ukms_id');
                    $pem->users_id = auth()->user()->id;
                    // jika akun modal, maka kebalikan
                    if (in_array($akun->nama_reff, ["Modal", "Kewajiban", "Pendapatan"])) {
                        $up = Akun::find($item->akuns_id);
                        // khusus modal
                        if ($akun->nama_reff == "Modal") {
                            // if ($jenis_saldo == "debet") {
                            //     $up->saldo_awal -= $saldo;
                            // } else {
                            //     $up->saldo_awal += $saldo;
                            // }
                            $up->{$jenis_saldo} += $saldo;
                            $up->save();
                        } else {
                            // $m = Akun::where('nama_reff', 'Modal')->first();
                            // $modal = Akun::find($m->id);
                            // // kalkulasi saldo akun
                            // if ($jenis_saldo == "debet") {
                            //     $modal->saldo_awal -= $saldo;
                            // } else {
                            //     $modal->saldo_awal += $saldo;
                            // }
                            // $modal->save();
                            $up->{$jenis_saldo} += $saldo;
                            $up->save();
                        }
                    } else {
                        $up = Akun::find($item->akuns_id);
                        $up->{$jenis_saldo} += $saldo;
                        $up->save();
                    }
                    $pem->no_reff = $akun->no_reff;
                    $pem->{$jenis_saldo} = str_replace(['Rp', '.', ' '], '', trim($saldo));
                    $pem->akuns_id = $item->akuns_id;
                    $pem->created_at = $item->created_at;
                    $pem->updated_at = $item->updated_at;
                    $pem->save();

                    // insert jurnal
                    $jurnal = new Jurnal;
                    $jurnal->tanggal = date('Y-m-d', strtotime($item->tanggal));
                    $jurnal->no_reff = $item->no_reff;
                    $jurnal->keterangan = 'pemasukan';
                    $jurnal->akuns_id = $item->akuns_id;
                    $jurnal->{$jenis_saldo} = $saldo;
                    $jurnal->ukms_id = Session::get('ukms_id');
                    $jurnal->users_id = auth()->user()->id;
                    $jurnal->id_transaksi = $kode;
                    $jurnal->created_at = $item->created_at;
                    $jurnal->updated_at = $item->updated_at;
                    $jurnal->save();
                }

                // mengurangi modal 
                $m = Akun::where('nama_reff', 'Modal')->first();
                $modal = Akun::find($m->id);
                $modal->saldo_awal += $total_modal / 2;
                $modal->save();

                $this->delAll_cart();
                return response()->json(['status' => true, 'message' => 'Tersimpan']);
            } else {
                return response()->json(['status' => false, 'message' => 'Transaksi belum balance']);
            }
        } catch (\Exception $err) {
            return response()->json(['status' => false, 'message' => $err->getMessage()]);
        }
    }

    public function edit(Request $request)
    {
        $q = Pemasukan::find($request->id);
        return response()->json($q);
    }

    public function update(Request $request)
    {
        try {
            $pem = Pemasukan::find($request->id);
            $pem->tanggal = date('Y-m-d', strtotime($request->tanggal));
            $pem->nominal = str_replace(['Rp', '.', ' '], '', trim($request->nominal));
            $pem->metode = $request->metode;
            $pem->keterangan = $request->keterangan;
            $pem->ukms_id = Session::get('ukms_id');
            $pem->users_id = auth()->user()->id;
            $simpan = $pem->save();
            if ($simpan) {
                return response()->json(['status' => $simpan, 'message' => 'Sukses']);
            }
        } catch (\Exception $err) {
            return response()->json(['status' => false, 'message' => $err->getMessage()]);
        }
    }

    public function delete(Request $request)
    {
        $id = $request->id;
        $q = Pemasukan::find($id);
        $kode = $q->kode;
        $query = Pemasukan::where('kode', $kode);
        $del = $query->delete();
        if ($del) {
            $this->delete_jurnal($kode);
            return response()->json(['status' => $del, 'message' => 'Hapus Sukses']);
        } else {
            return response()->json(['status' => $del, 'message' => 'Gagal']);
        }
    }

    function delete_jurnal($id_transaksi)
    {
        $get = Jurnal::where(
            [
                'id_transaksi' => $id_transaksi,
            ]
        )->get();

        $total = 0;
        foreach ($get as $item) {
            $jurnal = Jurnal::find($item->id);
            $nominal = $jurnal->debet == 0 ? $jurnal->kredit : $jurnal->debet;
            $total += $nominal;
            $jenis = $jurnal->debet == 0 ? 'kredit' : 'debet';

            // kurangi saldo akun
            $akun = Akun::find($jurnal->akuns_id);
            $hasil = ($akun->{$jenis} - $nominal) <= 0 ? 0 : $akun->{$jenis} - $nominal;
            $akun->{$jenis} = $hasil;
            $akun->save();

            // delete jurnal
            $del = $jurnal->delete();
            // return $del;
        }

        // select akun modal
        $m = Akun::where('nama_reff', 'Modal')->first();
        // update saldo awal modal
        $modal = Akun::find($m->id);
        $modal->saldo_awal -= $total / 2;
        return $modal->save();
        // if ($jenis == "kredit") {
        //     if (in_array($akun->nama_reff, ["Modal", "Kewajiban", "Pendapatan"])) {
        //         $modal->saldo_awal -= $nominal;
        //     } else {
        //         $modal->saldo_awal += $nominal;
        //     }
        // } else {
        //     if (in_array($akun->nama_reff, ["Modal", "Kewajiban", "Pendapatan"])) {
        //         $modal->saldo_awal += $nominal;
        //     } else {
        //         $modal->saldo_awal -= $nominal;
        //     }
        // }
    }

    function balance()
    {
        $debet = Cart::with('akun')
            ->where('kategori', 'pemasukan')
            ->where('ukms_id', Session::get('ukms_id'))
            ->orderBy('created_at', 'desc')
            ->sum('debet');
        $kredit = Cart::with('akun')
            ->where('kategori', 'pemasukan')
            ->where('ukms_id', Session::get('ukms_id'))
            ->orderBy('created_at', 'desc')
            ->sum('kredit');

        return $debet == $kredit;
    }

    // cart
    public function list()
    {
        $cart = Cart::with('akun')
            ->where('kategori', 'pemasukan')
            ->where('ukms_id', Session::get('ukms_id'))
            ->orderBy('created_at', 'asc')->get();
        return response()->json($cart);
    }

    public function add(Request $req)
    {
        $akun = Akun::find($req->akuns_id);
        if (in_array($akun->nama_reff, ["Modal", "Kewajiban", "Pendapatan"])) {
            $jenis_saldo = "kredit";
        } else {
            $jenis_saldo = "debet";
        }
        $cart = new Cart;
        $cart->tanggal = date('Y-m-d', strtotime($req->tanggal));
        $cart->kategori = 'pemasukan';
        $cart->no_reff = $req->no_reff;
        $cart->akuns_id = $req->akuns_id;
        $cart->keterangan = empty($req->keterangan) ? '-' : $req->keterangan;
        $cart->{$jenis_saldo} = str_replace(['Rp', '.', ' '], '', trim($req->nominal));
        $cart->ukms_id = Session::get('ukms_id');
        $cart->users_id = auth()->user()->id;
        $cart->save();
        return response()->json($cart);
    }

    public function del_cart(Request $req)
    {
        $cart = Cart::find($req->id)->delete();
        return $cart;
    }

    public function delAll_cart()
    {
        $cart = Cart::with('akun')
            ->where('kategori', 'pemasukan')
            ->where('ukms_id', Session::get('ukms_id'))
            ->where('users_id', auth()->user()->id)
            ->delete();
        return $cart;
    }
}
