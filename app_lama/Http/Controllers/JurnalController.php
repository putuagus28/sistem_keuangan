<?php

namespace App\Http\Controllers;

use App\Akun;
use App\Cart;
use App\Jurnal;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;

class JurnalController extends Controller
{
    function getAkun(Request $req)
    {
        return Akun::find($req->id);
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Jurnal::with('users', 'akun')->where('ukms_id', Session::get('ukms_id'))->get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = "";
                    // $btn .= '<a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-success btn-sm mx-1" id="edit"><i class="fas fa-edit"></i></a>';
                    $btn .= '<a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-danger btn-sm mx-1" id="hapus"><i class="fa fa-trash" aria-hidden="true"></i></a>';
                    return $btn;
                })
                ->addColumn('debet', function ($row) {
                    return 'Rp ' . number_format($row->debet, 0, ',', '.');
                })
                ->addColumn('kredit', function ($row) {
                    return 'Rp ' . number_format($row->kredit, 0, ',', '.');
                })
                ->rawColumns(['action', 'saldo', 'debet', 'kredit'])
                ->make(true);
        }
        $data = [
            'title' => 'Jurnal',
            'akun' => Akun::where('ukms_id', Session::get('ukms_id'))->get(),
        ];
        return view('jurnal.index', $data);
    }

    public function insert(Request $req)
    {
        try {
            $cart = Cart::with('akun')
                ->where('ukms_id', Session::get('ukms_id'))
                ->orderBy('created_at', 'desc')
                ->get();
            foreach ($cart as $item) {
                $jenis_saldo = ($item->debet == 0 ? 'kredit' : 'debet');
                $saldo = ($item->debet == 0 ? $item->kredit : $item->debet);

                $q = new Jurnal;
                $akun = Akun::find($item->akuns_id);
                $q->tanggal = date('Y-m-d', strtotime($item->tanggal));
                $q->no_reff = $item->no_reff;
                $q->keterangan = 'jurnal umum';
                $q->akuns_id = $item->akuns_id;
                // jika akun modal, maka kebalikan
                if ($akun->nama_reff == "Modal") {
                    $up = Akun::find($item->akuns_id);
                    // kalkulasi saldo akun
                    if ($jenis_saldo == "debet") {
                        $up->saldo_awal -= $saldo;
                    } else {
                        $up->saldo_awal += $saldo;
                    }
                    $up->{$jenis_saldo} += $saldo;
                    $up->save();
                } else {
                    $up = Akun::find($item->akuns_id);
                    $up->{$jenis_saldo} += $saldo;
                    $up->save();

                    $m = Akun::where('nama_reff', 'Modal')->first();
                    $modal = Akun::find($m->id);
                    // kalkulasi saldo akun
                    if ($jenis_saldo == "debet") {
                        $modal->saldo_awal += $saldo;
                    } else {
                        $modal->saldo_awal -= $saldo;
                    }
                    $modal->save();
                }
                $q->{$jenis_saldo} = $saldo;
                $q->ukms_id = Session::get('ukms_id');
                $q->users_id = auth()->user()->id;
                $q->save();
            }
            $this->delAll_cart();
            return response()->json(['status' => true, 'message' => 'Tersimpan']);
        } catch (\Exception $err) {
            return response()->json(['status' => false, 'message' => $err->getMessage()]);
        }
    }

    public function edit(Request $request)
    {
        $q = Jurnal::with('users')->where('id', $request->id)->first();
        return response()->json($q);
    }

    public function delete(Request $request)
    {
        $q = Jurnal::find($request->id);
        $nominal = $q->debet == 0 ? $q->kredit : $q->debet;
        $jenis = $q->debet == 0 ? 'kredit' : 'debet';
        // select akun modal
        $m = Akun::where('nama_reff', 'Modal')->first();
        // kurangi saldo akun
        $a = Akun::find($q->akuns_id);
        $hasil = ($a->{$jenis} - $nominal) <= 0 ? 0 : $a->{$jenis} - $nominal;
        $a->{$jenis} = $hasil;
        $a->save();
        // update saldo awal modal
        $modal = Akun::find($m->id);
        if ($jenis == "kredit") {
            $modal->saldo_awal += $nominal;
        } else {
            $modal->saldo_awal -= $nominal;
        }
        $modal->save();
        // delete jurnal
        $del = $q->delete();
        if ($del) {
            return response()->json(['status' => $del, 'message' => 'Hapus Sukses']);
        } else {
            return response()->json(['status' => $del, 'message' => 'Gagal']);
        }
    }

    public function update(Request $req)
    {
        try {
            $q = Jurnal::find($req->id);
            $debet_lama = $q->debet;
            $kredit_lama = $q->kredit;
            $jenis_lama = ($debet_lama == 0 ? 'kredit' : 'debet');

            $q->tanggal = date('Y-m-d', strtotime($req->tanggal));
            $q->no_reff = $req->no_reff;
            $q->keterangan = 'jurnal umum';
            // jika akun masih sama, update biasa
            if ($q->akuns_id == $req->akuns_id) {
                $type = Akun::find($req->akuns_id);
                // update debet kredit jurnal
                $jenis = $req->jenis_saldo;
                if ($jenis == "debet") {
                    $q->debet = $req->saldo;
                    $q->kredit = 0;
                } else {
                    $q->kredit = $req->saldo;
                    $q->debet = 0;
                }
                // reset saldo lama
                $akun_min = Akun::find($req->akuns_id);
                $cek = $akun_min->{$jenis} -= $q->{$jenis};
                if ($cek <= 0) {
                    $nominal = 0;
                } else {
                    $nominal = $akun_min->{$jenis} -= $q->{$jenis};
                }
                $akun_min->{$jenis} = $nominal;
                $akun_min->save();
                // update saldo baru
                $akun_plus = Akun::find($req->akuns_id);
                $act = '';
                // jika jenis sama
                if ($jenis_lama == $jenis) {
                    $akun_plus->{$jenis} = $req->saldo;
                    if ($jenis == "debet") {
                        $act = '+';
                    } else {
                        $act = '-';
                    }
                } else {
                    if ($jenis == "debet") {
                        $act = '+';
                        $akun_plus->kredit -= $req->saldo;
                    } else {
                        $act = '-';
                        $akun_plus->debet -= $req->saldo;
                    }
                    $akun_plus->{$jenis} = $req->saldo;
                }
                $akun_plus->save();
                // update saldo akun modal
                $akun_m = Akun::where('nama_reff', 'Modal')->first();
                $akun_modal = Akun::find($akun_m->id);
                $modal = 0;
                if ($q->debet == 0) {
                    $modal = $req->saldo - $kredit_lama;
                } else {
                    $modal = $req->saldo - $debet_lama;
                }
                // kebalikan jika akunnya modal
                if ($type->nama_reff == "Modal") {
                    if ($act == '+') {
                        $akun_modal->saldo_awal -= $modal;
                    } else {
                        $akun_modal->saldo_awal += $modal;
                    }
                } else {
                    if ($act == '+') {
                        $akun_modal->saldo_awal += $modal;
                    } else {
                        $akun_modal->saldo_awal -= $modal;
                    }
                }

                $akun_modal->save();
            } else {
                $type = Akun::find($req->akuns_id);
                // update debet kredit jurnal
                $jenis = $req->jenis_saldo;
                if ($jenis == "debet") {
                    $q->debet = $req->saldo;
                    $q->kredit = 0;
                } else {
                    $q->kredit = $req->saldo;
                    $q->debet = 0;
                }
                $q->akuns_id = $req->akuns_id;
                // reset saldo lama
                $akun_min = Akun::find($req->akuns_id);
                $cek = $akun_min->{$jenis} -= $q->{$jenis};
                if ($cek <= 0) {
                    $nominal = 0;
                } else {
                    $nominal = $akun_min->{$jenis} -= $q->{$jenis};
                }
                $akun_min->{$jenis} = $nominal;
                $akun_min->save();
                // update saldo baru
                $akun_plus = Akun::find($req->akuns_id);
                $act = '';
                // jika jenis sama
                if ($jenis_lama == $jenis) {
                    $akun_plus->{$jenis} = $req->saldo;
                    if ($jenis == "debet") {
                        $act = '+';
                    } else {
                        $act = '-';
                    }
                } else {
                    if ($jenis == "debet") {
                        $act = '+';
                        $akun_plus->kredit -= $req->saldo;
                    } else {
                        $act = '-';
                        $akun_plus->debet -= $req->saldo;
                    }
                    $akun_plus->{$jenis} = $req->saldo;
                }
                $akun_plus->save();
                // update saldo akun modal
                $akun_m = Akun::where('nama_reff', 'Modal')->first();
                $akun_modal = Akun::find($akun_m->id);
                $modal = 0;
                if ($q->debet == 0) {
                    $modal = $req->saldo - $kredit_lama;
                } else {
                    $modal = $req->saldo - $debet_lama;
                }
                // kebalikan jika akunnya modal
                if ($type->nama_reff == "Modal") {
                    if ($act == '+') {
                        $akun_modal->saldo_awal -= $modal;
                    } else {
                        $akun_modal->saldo_awal += $modal;
                    }
                } else {
                    if ($act == '+') {
                        $akun_modal->saldo_awal += $modal;
                    } else {
                        $akun_modal->saldo_awal -= $modal;
                    }
                }
                $akun_modal->save();
            }

            $q->save();
            return response()->json(['status' => true, 'message' => 'Sukses Update ' . $act]);
        } catch (\Exception $err) {
            return response()->json(['status' => false, 'message' => $err->getMessage()]);
        }
    }

    // cart

    public function list()
    {
        $cart = Cart::with('akun')
            ->where('ukms_id', Session::get('ukms_id'))
            ->orderBy('created_at', 'desc')->get();
        return response()->json($cart);
    }

    public function add(Request $req)
    {
        $cart = new Cart;
        $cart->tanggal = date('Y-m-d', strtotime($req->tanggal));
        $cart->no_reff = $req->no_reff;
        $cart->akuns_id = $req->akuns_id;
        $cart->{$req->jenis_saldo} = $req->saldo;
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
            ->where('ukms_id', Session::get('ukms_id'))
            ->where('users_id', auth()->user()->id)
            ->delete();
        return $cart;
    }
}
