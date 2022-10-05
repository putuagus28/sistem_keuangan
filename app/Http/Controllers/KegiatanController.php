<?php

namespace App\Http\Controllers;


use App\Kegiatan;
use App\DetailKegiatan;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;

class KegiatanController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            if (!empty($request->searchByBulan)) {
                $data = Kegiatan::with('users', 'detail', 'ukm')
                    ->where('ukms_id', Session::get('ukms_id'))
                    ->whereMonth('tanggal', $request->searchByBulan)
                    ->get();
            } else {
                $data = Kegiatan::with('users', 'detail', 'ukm')
                    ->where('ukms_id', Session::get('ukms_id'))
                    ->get();
            }
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = "";
                    $btn .= '<a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-info btn-sm mx-1" id="detail"><i class="fa fa-eye" aria-hidden="true"></i> detail</a>';
                    if (Session::get('jabatan') == 'bendahara') {
                        $btn .= '<a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-success btn-sm mx-1" id="edit"><i class="fas fa-edit"></i></a>';
                        $btn .= '<a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-danger btn-sm mx-1" id="hapus"><i class="fa fa-trash" aria-hidden="true"></i></a>';
                    }
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('transaksi.kegiatan');
    }

    public function insert(Request $request)
    {
        try {
            $k = new Kegiatan;
            $k->tanggal = date('Y-m-d', strtotime($request->tgl));
            $k->nama_kegiatan = $request->nama_kegiatan;
            $k->ukms_id = Session::get('ukms_id');
            $k->users_id = auth()->user()->id;
            $k->save();
            $id_k = $k->id;

            for ($i = 0; $i < count($request->tanggal); $i++) {
                $fl_arr = [];
                // jika user upload berkas;
                if ($request->hasFile('bukti' . $i)) {
                    foreach ($request->file('bukti' . $i) as $image) {
                        $extension = $image->getClientOriginalExtension();
                        $fileName = rand(11111, 99999) . '.' . $extension;
                        $fl_arr[] = $fileName;
                        $image->move(public_path('bukti'), $fileName);
                    }
                }
                $d = new DetailKegiatan;
                $d->tanggal = date('Y-m-d', strtotime($request->tanggal[$i]));
                $d->nominal = str_replace(['Rp', ' ', '.', ','], '', $request->nominal[$i]);
                $d->status = $request->status[$i];
                $d->keterangan = $request->keterangan[$i];
                $d->kegiatans_id = $id_k;
                $d->bukti = implode(",", $fl_arr);
                $d->save();
            }

            return response()->json(['status' => true, 'message' => 'Tersimpan']);
        } catch (\Exception $err) {
            return response()->json(['status' => false, 'message' => $err->getMessage()]);
        }
    }

    public function edit(Request $request)
    {
        $q = Kegiatan::with('detail')->where('id', $request->id)->first();
        return response()->json($q);
    }

    public function delete(Request $request)
    {
        $d = DetailKegiatan::where('kegiatans_id', $request->id)->get();
        foreach ($d as $v) {
            $foto = $v->bukti;
            $foto = explode(',', $foto);
            foreach ($foto as $f) {
                if (!empty($f)) {
                    $filePath = public_path('bukti/' . $f);
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                }
            }
        }

        $query = Kegiatan::find($request->id);
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
            $k = Kegiatan::find($request->id);
            $k->tanggal = date('Y-m-d', strtotime($request->tgl));
            $k->nama_kegiatan = $request->nama_kegiatan;
            $k->ukms_id = Session::get('ukms_id');
            $k->users_id = auth()->user()->id;
            $k->save();

            for ($i = 0; $i < count($request->tanggal); $i++) {
                $fl_arr = [];

                $d = DetailKegiatan::find($request->id_detail[$i]);
                // jika user upload berkas;
                if ($request->hasFile('bukti' . $i) && $request->status[$i] == "pengeluaran") {
                    // delete file lama
                    $dtl = DetailKegiatan::find($request->id_detail[$i]);
                    $foto = $dtl->bukti;
                    $foto = explode(',', $foto);
                    foreach ($foto as $f) {
                        if (!empty($f)) {
                            $filePath = public_path('bukti/' . $f);
                            if (file_exists($filePath)) {
                                unlink($filePath);
                            }
                        }
                    }
                    // upload file baru
                    foreach ($request->file('bukti' . $i) as $image) {
                        $extension = $image->getClientOriginalExtension();
                        $fileName = rand(11111, 99999) . '.' . $extension;
                        $fl_arr[] = $fileName;
                        $image->move(public_path('bukti'), $fileName);
                    }
                    $d->bukti = implode(",", $fl_arr);
                }
                $d->tanggal = date('Y-m-d', strtotime($request->tanggal[$i]));
                $d->nominal = str_replace(['Rp', ' ', '.', ','], '', $request->nominal[$i]);
                $d->status = $request->status[$i];
                $d->keterangan = $request->keterangan[$i];
                $d->save();
            }
            return response()->json(['status' => true, 'message' => 'Sukses']);
        } catch (\Exception $err) {
            return response()->json(['status' => false, 'message' => $err->getMessage()]);
        }
    }
}
