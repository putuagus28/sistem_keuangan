<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\File;
use App\Ukm;

class UkmController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Ukm::with('users')->get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = "";
                    $btn .= '<a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-success btn-sm mx-1" id="edit"><i class="fas fa-edit"></i></a>';
                    $btn .= '<a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-danger btn-sm mx-1" id="hapus"><i class="fa fa-trash" aria-hidden="true"></i></a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('ukm.index');
    }

    public function insert(Request $request)
    {
        try {
            $ukm = new Ukm;
            $cek_ukm = $ukm->where(['nama' => $request->nama]);
            if ($cek_ukm->exists()) {
                return response()->json(['status' => false, 'message' => 'Ukm sudah ada!']);
            }else{
                $user = new Ukm;
                $user->nama = $request->nama;
                $user->users_id = auth()->user()->id;
                $user->save();
                return response()->json(['status' => true, 'message' => 'Tersimpan']);
            }
        } catch (\Exception $err) {
            return response()->json(['status' => false, 'message' => $err->getMessage()]);
        }
    }

    public function edit(Request $request)
    {
        $q = Ukm::find($request->id);
        return response()->json($q);
    }

    public function delete(Request $request)
    {
        $query = Ukm::find($request->id);
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
            $user = Ukm::find($request->id);
            $user->nama = $request->nama;
            $user->save();
            return response()->json(['status' => true, 'message' => 'Sukses']);
        } catch (\Exception $err) {
            return response()->json(['status' => false, 'message' => $err->getMessage()]);
        }
    }
}
