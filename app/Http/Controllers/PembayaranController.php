<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Pembayaran;
use App\AnggotaUkm;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use App\User;

class PembayaranController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            if (!empty($request->searchByBulan)) {
                $data = Pembayaran::select('*')
                    ->addSelect(DB::raw('MONTH(tanggal) as bulan', '*'))
                    ->with('mhs', 'ukm', 'users.mhs')
                    ->where('ukms_id', Session::get('ukms_id'))
                    ->whereMonth('tanggal', $request->searchByBulan)
                    ->get();
            } else {
                $data = Pembayaran::select('*')
                    ->addSelect(DB::raw('MONTH(tanggal) as bulan', '*'))
                    ->with('mhs', 'ukm', 'users.mhs')
                    ->where('ukms_id', Session::get('ukms_id'))
                    ->whereMonth('tanggal', date('m'))
                    ->get();
            }
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('tanggal', function ($row) {
                    return date('d-m-Y', strtotime($row->tanggal));
                })
                ->addColumn('nominal', function ($row) {
                    return 'Rp ' . number_format($row->nominal, 0, ',', '.');
                })
                ->addColumn('action', function ($row) {
                    $btn = "";
                    if (Session::get('jabatan') == 'bendahara') {
                        if (!in_array($row->jabatan, ['ketua', 'bendahara'])) {
                            $btn .= '<a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-success btn-sm mx-1" id="edit"><i class="fas fa-edit"></i></a>';
                            // $btn .= '<a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-danger btn-sm mx-1" id="hapus"><i class="fa fa-trash" aria-hidden="true"></i></a>';
                        }
                    }
                    return $btn;
                })
                ->rawColumns(['action', 'tanggal', 'nominal'])
                ->make(true);
        }
        $data = [
            'mhs' => AnggotaUkm::with('mhs')->where('ukms_id', Session::get('ukms_id'))
                ->where('jabatan', '!=', 'pembina')
                ->get(),
        ];
        return view('transaksi.pembayaran', $data);
    }

    public function belum_bayar(Request $request)
    {
        if ($request->ajax()) {
            if (!empty($request->searchByBulan)) {
                $data = AnggotaUkm::with('mhs', 'pembayaran')
                    ->where('ukms_id', Session::get('ukms_id'))
                    ->where('jabatan', '!=', 'pembina')
                    ->whereNotIn('users_global', function ($query) use ($request) {
                        $query->select('mahasiswas_id')
                            ->from('pembayarans')
                            ->where('ukms_id', Session::get('ukms_id'))
                            ->whereMonth('tanggal', $request->searchByBulan);
                    })
                    ->get();
            } else {
                $data = AnggotaUkm::with('mhs', 'pembayaran')
                    ->where('ukms_id', Session::get('ukms_id'))
                    ->where('jabatan', '!=', 'pembina')
                    ->whereNotIn('users_global', function ($query) {
                        $query->select('mahasiswas_id')
                            ->from('pembayarans')
                            ->where('ukms_id', Session::get('ukms_id'))
                            ->whereMonth('tanggal', date('m'));
                    })
                    ->get();
            }

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('tanggal', function ($row) {
                    return date('d-m-Y', strtotime($row->tanggal));
                })
                ->addColumn('nominal', function ($row) {
                    return 'Rp' . number_format($row->nominal, 0, ',', '.');
                })
                ->rawColumns(['tanggal', 'nominal'])
                ->make(true);
        }
    }

    public function insert(Request $request)
    {
        try {
            $pem = new Pembayaran;
            $pem->tanggal = date('Y-m-d', strtotime($request->tanggal));
            $pem->nominal = str_replace(['Rp', '.', ' '], '', trim($request->nominal));
            $pem->metode = $request->metode;
            $pem->keterangan = $request->keterangan;
            $pem->mahasiswas_id = $request->mahasiswas_id;
            $pem->ukms_id = Session::get('ukms_id');
            $pem->users_id = auth()->user()->id;
            $simpan = $pem->save();
            return response()->json(['status' => $simpan, 'message' => 'Sukses']);
        } catch (\Exception $err) {
            return response()->json(['status' => false, 'message' => $err->getMessage()]);
        }
    }

    public function edit(Request $request)
    {
        $q = Pembayaran::find($request->id);
        return response()->json($q);
    }

    public function update(Request $request)
    {
        try {
            $pem = Pembayaran::find($request->id);
            $pem->tanggal = date('Y-m-d', strtotime($request->tanggal));
            $pem->nominal = str_replace(['Rp', '.', ' '], '', trim($request->nominal));
            $pem->metode = $request->metode;
            $pem->keterangan = $request->keterangan;
            $pem->mahasiswas_id = $request->mahasiswas_id;
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
}
