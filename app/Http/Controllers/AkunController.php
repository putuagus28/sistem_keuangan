<?php

namespace App\Http\Controllers;

use App\Akun;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;

class AkunController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Akun::with('users')->get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = "";
                    $btn .= '<a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-success btn-sm mx-1" id="edit"><i class="fas fa-edit"></i></a>';
                    // $btn .= '<a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-danger btn-sm mx-1" id="hapus"><i class="fa fa-trash" aria-hidden="true"></i></a>';
                    return $btn;
                })
                ->addColumn('saldo', function ($row) {
                    return 'Rp ' . number_format($row->saldo_awal, 0, ',', '.');
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
            'title' => 'Data Akun'
        ];
        return view('akun.index', $data);
    }

    public function insert(Request $request)
    {
        try {
            $q = new Akun;
            $cek1 = $q->where(['no_reff' => $request->no_reff]);
            $cek2 = $q->where(['nama_reff' => $request->nama_reff]);
            if ($cek1->exists()) {
                return response()->json(['status' => false, 'message' => 'No Reff sudah ada!']);
            } else if ($request->nama_reff == "Modal" && $cek2->exists()) {
                return response()->json(['status' => false, 'message' => 'Hanya bisa menambah akun modal sekali!']);
            } else {
                $q = new Akun;
                $q->no_reff = $request->no_reff;
                $q->nama_reff = $request->nama_reff;
                $q->keterangan = $request->keterangan;
                // $q->saldo_awal = $request->saldo_awal;
                $q->ukms_id = Session::get('ukms_id');
                $q->users_id = auth()->user()->id;
                $q->save();
                return response()->json(['status' => true, 'message' => 'Tersimpan']);
            }
        } catch (\Exception $err) {
            return response()->json(['status' => false, 'message' => $err->getMessage()]);
        }
    }

    public function edit(Request $request)
    {
        $q = Akun::with('users')->where('id', $request->id)->first();
        return response()->json($q);
    }

    public function delete(Request $request)
    {
        $query = Akun::find($request->id);
        $del = $query->delete();
        if ($del) {
            return response()->json(['status' => $del, 'message' => 'Hapus Sukses']);
        } else {
            return response()->json(['status' => $del, 'message' => 'Gagal']);
        }
    }

    public function update(Request $request)
    {
        try {
            $q = Akun::find($request->id);
            $q->no_reff = $request->no_reff;
            $q->nama_reff = $request->nama_reff;
            $q->keterangan = $request->keterangan;
            // $q->saldo_awal = $request->saldo_awal;
            $q->ukms_id = Session::get('ukms_id');
            $q->save();
            return response()->json(['status' => true, 'message' => 'Sukses']);
        } catch (\Exception $err) {
            return response()->json(['status' => false, 'message' => $err->getMessage()]);
        }
    }
}
