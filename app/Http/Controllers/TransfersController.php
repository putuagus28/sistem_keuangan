<?php

namespace App\Http\Controllers;

use App\Akun;
use App\Jurnal;
use App\Transfers;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class TransfersController extends Controller
{
    public function index(Request $req)
    {
        if ($req->ajax()) {
            $data = Transfers::with('ukm', 'akun_dari', 'akun_tujuan', 'users')
                ->where('ukms_id', Session::get('ukms_id'))
                ->get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = "";
                    // $btn .= '<a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-success btn-sm mx-1" id="edit"><i class="fas fa-edit"></i></a>';
                    $btn .= '<a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-danger btn-sm mx-1" id="hapus"><i class="fa fa-trash" aria-hidden="true"></i></a>';
                    return $btn;
                })
                ->addColumn('nominal', function ($row) {
                    return 'Rp ' . number_format($row->nominal, 0, ',', '.');
                })
                ->addColumn('tanggal', function ($row) {
                    return date('Y-m-d', strtotime($row->created_at));
                })
                ->rawColumns(['action', 'nominal', 'tanggal'])
                ->make(true);
        }
        $data = [
            'title' => 'Transfers Akun',
            'akun' => Akun::whereIn('nama_reff', ['Activa'])->where('ukms_id', Session::get('ukms_id'))->get(),
        ];

        return view('transfers.index', $data);
    }

    public function insert(Request $req)
    {
        try {
            if ($req->akun_dari == $req->akun_tujuan) {
                return response()->json(['status' => false, 'message' => 'Tidak boleh transfers antar akun yang sama!']);
            } else {
                $a = Akun::find($req->akun_dari);
                $saldo_akun_dari = $a->debet - $a->kredit;
                if (($saldo_akun_dari - $req->nominal) < 0) {
                    return response()->json(['status' => false, 'message' => 'Saldo ' . $a->keterangan . ' tidak cukup, melakukan transfers!']);
                } else {
                    $kode = Str::random(6);
                    // // input ke jurnal
                    // $j = new Jurnal;
                    // $j->debet = $req->nominal;
                    // $j->kredit = 0;
                    // $j->tanggal = date('Y-m-d');
                    // $j->keterangan = 'transfers akun';
                    // $j->id_transaksi = $kode;
                    // $j->akuns_id = $req->akun_tujuan;
                    // $j->ukms_id = Session::get('ukms_id');
                    // $j->users_id = auth()->user()->id;
                    // $j->save();

                    // $j = new Jurnal;
                    // $j->debet = 0;
                    // $j->kredit = $req->nominal;
                    // $j->tanggal = date('Y-m-d');
                    // $j->keterangan = 'transfers akun';
                    // $j->id_transaksi = $kode;
                    // $j->akuns_id = $req->akun_dari;
                    // $j->ukms_id = Session::get('ukms_id');
                    // $j->users_id = auth()->user()->id;
                    // $j->save();

                    // pengurangan saldo akun_dari
                    $akun_d = Akun::find($req->akun_dari);
                    $akun_d->ukms_id = Session::get('ukms_id');
                    $akun_d->kredit += $req->nominal;
                    $akun_d->save();

                    // penambahan saldo akun_tujuan
                    $akun_d = Akun::find($req->akun_tujuan);
                    $akun_d->ukms_id = Session::get('ukms_id');
                    $akun_d->debet += $req->nominal;
                    $akun_d->save();

                    // input ke transfers
                    $q = new Transfers;
                    $q->kode = $kode;
                    $q->akun_dari = $req->akun_dari;
                    $q->nominal = $req->nominal;
                    $q->akun_tujuan = $req->akun_tujuan;
                    $q->ukms_id = Session::get('ukms_id');
                    $q->users_id = auth()->user()->id;
                    $q->save();
                    return response()->json(['status' => true, 'message' => 'Tersimpan']);
                }
            }
        } catch (\Exception $err) {
            return response()->json(['status' => false, 'message' => $err->getMessage()]);
        }
    }

    public function edit(Request $req)
    {
        $q = Transfers::with('users')->where('id', $req->id)->first();
        return response()->json($q);
    }

    public function delete(Request $req)
    {
        $q = Transfers::find($req->id);
        $kode = $q->kode;
        $nominal = $q->nominal;
        $akun_dari = $q->akun_dari;
        $akun_tujuan = $q->akun_tujuan;
        $del = $q->delete();
        if ($del) {
            Jurnal::where('id_transaksi', $kode)->delete();
            // mengembalikan nominal yg di transfers
            // pengurangan saldo akun_dari
            $akun_d = Akun::find($akun_dari);
            $akun_d->ukms_id = Session::get('ukms_id');
            $akun_d->kredit -= $nominal;
            $akun_d->save();

            // penambahan saldo akun_tujuan
            $akun_d = Akun::find($akun_tujuan);
            $akun_d->ukms_id = Session::get('ukms_id');
            $akun_d->debet -= $nominal;
            $akun_d->save();
            return response()->json(['status' => $del, 'message' => 'Hapus Sukses']);
        } else {
            return response()->json(['status' => $del, 'message' => 'Gagal']);
        }
    }

    public function update(Request $req)
    {
        try {
            $q = Transfers::find($req->id);
            $q->akun_dari = $req->akun_dari;
            $q->nominal = $req->nominal;
            $q->akun_tujuan = $req->akun_tujuan;
            $q->ukms_id = Session::get('ukms_id');
            $q->users_id = auth()->user()->id;
            $q->save();
            return response()->json(['status' => true, 'message' => 'Sukses']);
        } catch (\Exception $err) {
            return response()->json(['status' => false, 'message' => $err->getMessage()]);
        }
    }
}
