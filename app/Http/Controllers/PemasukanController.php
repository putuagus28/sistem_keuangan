<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Pemasukan;
use App\AnggotaUkm;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use App\User;

class PemasukanController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            if (!empty($request->searchByBulan)) {
                $data = Pemasukan::select('*')
                    ->addSelect(DB::raw('MONTH(tanggal) as bulan', '*'))
                    ->with('ukm', 'users.mhs')
                    ->where('ukms_id', Session::get('ukms_id'))
                    ->whereMonth('tanggal', $request->searchByBulan)
                    ->get();
            } else {
                $data = Pemasukan::select('*')
                    ->addSelect(DB::raw('MONTH(tanggal) as bulan', '*'))
                    ->with('ukm', 'users.mhs')
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
        $data = [];
        return view('transaksi.pemasukan', $data);
    }

    public function insert(Request $request)
    {
        try {
            $pem = new Pemasukan;
            $pem->tanggal = date('Y-m-d', strtotime($request->tanggal));
            $pem->nominal = str_replace(['Rp', '.', ' '], '', trim($request->nominal));
            $pem->metode = $request->metode;
            $pem->keterangan = $request->keterangan;
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
}
