<?php

namespace App\Http\Controllers;

use App\AnggotaUkm;
use Illuminate\Http\Request;
use App\Pembina;
use App\Ukm;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\File;
use App\User;

class PembinaController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Pembina::with('anggota.ukm')->get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('foto', function ($row) {
                    $img = '<div class="text-center"><img src="users/' . $row->foto . '" class="img-fluid rounded-circle" alt="" width="50"></div>';
                    if ($row->foto == null) {
                        $img = '<div class="text-center">-</div>';
                    }
                    return $img;
                })
                ->addColumn('action', function ($row) {
                    $btn = "";
                    $btn .= '<a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-success btn-sm mx-1" id="edit"><i class="fas fa-edit"></i></a>';
                    $btn .= '<a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-danger btn-sm mx-1" id="hapus"><i class="fa fa-trash" aria-hidden="true"></i></a>';
                    $btn .= '<a href="' . route('ukm.pembina', ['id' => $row->id]) . '" data-id="' . $row->id . '" class="btn btn-danger btn-sm mx-1" id="ukm"><i class="fa fa-plus" aria-hidden="true"></i> Ukm</a>';
                    return $btn;
                })
                ->addColumn('ukm', function ($row) {
                    $ukm = [];
                    foreach ($row->anggota as $item) {
                        $ukm[] = '<span class="px-2 py-1 bg-dark">' . $item->ukm->nama . '</span>';
                    }

                    return implode(' ', $ukm);
                })
                ->rawColumns(['action', 'foto', 'ukm'])
                ->make(true);
        }
        return view('pembina.index');
    }

    public function insert(Request $request)
    {
        $mhs = new Pembina;
        $cek = $mhs->where(['email' => $request->email])->exists();
        if ($cek) {
            return response()->json(['status' => false, 'message' => 'Nama atau Email sudah digunakan!']);
        } else {
            $mhs->nip = $request->nip;
            $mhs->name = $request->name;
            $mhs->email = Str::lower($request->email);
            $mhs->alamat = $request->alamat;
            $mhs->tanggalLahir = date('Y-m-d', strtotime($request->tanggalLahir));
            $mhs->noKtp = $request->noKtp;
            $mhs->jk = $request->jk;
            // jika user upload berkas
            if ($request->hasFile('foto')) {
                $file = $request->file('foto');
                $extension = $file->getClientOriginalExtension();
                $fileName = rand(11111, 99999) . '.' . $extension;
                // upload ke folder
                $file->move(public_path() . '/users/', $fileName);
                $mhs->foto = $fileName;
            }
            $mhs->noTlpn = $request->noTlpn;
            $simpan = $mhs->save();
            $last_id = $mhs->id;
            if ($simpan) {
                // user login
                $user = new User;
                $user->users_global = $last_id;
                $user->name = $request->name;
                $user->email = $request->email;
                $user->username = $request->nip;
                $user->password = bcrypt($request->nip);
                $user->role = 'pembina';
                $user->save();
                return response()->json(['status' => $simpan, 'message' => 'Sukses']);
            }
        }
    }

    public function edit(Request $request)
    {
        $q = Pembina::find($request->id);
        return response()->json($q);
    }

    public function delete(Request $request)
    {
        $query = Pembina::find($request->id);
        $foto = $query->foto;
        $del = $query->delete();
        if ($del) {
            if ($foto != "") {
                $filePath = 'users/' . $foto;
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
            return response()->json(['status' => $del, 'message' => 'Hapus Sukses']);
        } else {
            return response()->json(['status' => $del, 'message' => 'Gagal']);
        }
    }

    public function update(Request $request)
    {
        try {
            $query = new Pembina;
            $up = $query->find($request->id);
            $up->nip = $request->nip;
            $up->name = $request->name;
            $up->email = $request->email;
            $up->alamat = $request->alamat;
            $up->jk = $request->jk;
            $up->noTlpn = $request->noTlpn;
            $up->tanggalLahir = $request->tanggalLahir;
            $up->noKtp = $request->noKtp;
            // jika user upload baru
            if ($request->hasFile('foto')) {
                $foto = $up->foto;
                if ($foto != "") {
                    $filePath = 'users/' . $foto;
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                }
                $file = $request->file('foto');
                $extension = $file->getClientOriginalExtension();
                $fileName = $request->nama . '_' . rand(11111, 99999) . '.' . $extension;
                // upload ke folder
                $file->move(public_path() . '/users/', $fileName);
                $up->foto = $fileName;
            }
            $simpan = $up->save();
            if ($simpan) {
                return response()->json(['status' => $simpan, 'message' => 'Sukses']);
            }
        } catch (\Exception $err) {
            return response()->json(['status' => false, 'message' => $err->getMessage()]);
        }
    }

    public function ukm(Request $request, $id_pem = null)
    {
        if ($request->ajax()) {
            $data = AnggotaUkm::with('ukm')->where('users_global', $id_pem)->get();
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
        $data = [
            'title' => 'List UKM - ' . ucwords(Pembina::find($id_pem)->name) . '',
            'id_pem' => $id_pem,
            'ukm' => Ukm::all(),
        ];
        return view('pembina.ukm', $data);
    }

    public function ukm_post(Request $request)
    {
        $q = AnggotaUkm::where([
            'ukms_id' => $request->ukms_id,
            'jabatan' => $request->jabatan,
            'users_global' => $request->users_global,
        ])->exists();
        $q2 = AnggotaUkm::where([
            'ukms_id' => $request->ukms_id,
            'jabatan' => $request->jabatan,
        ])->exists();
        if ($q) {
            return response()->json(['status' => false, 'message' => 'Pembina sudah memilih ukm ini!']);
        } else if ($q2) {
            return response()->json(['status' => false, 'message' => 'Ukm ini sudah memiliki pembina!']);
        } else {
            $q = new AnggotaUkm;
            $q->ukms_id = $request->ukms_id;
            $q->jabatan = $request->jabatan;
            $q->users_global = $request->users_global;
            $q->users_id = auth()->user()->id;
            $simpan = $q->save();
            return response()->json(['status' => $simpan, 'message' => 'Sukses']);
        }
    }

    public function ukm_edit(Request $request)
    {
        $q = AnggotaUkm::find($request->id);
        return response()->json($q);
    }

    public function ukm_update(Request $request)
    {
        $q = AnggotaUkm::find($request->id);
        $q->ukms_id = $request->ukms_id;
        $q->jabatan = $request->jabatan;
        $q->users_global = $request->users_global;
        $q->users_id = auth()->user()->id;
        $simpan = $q->save();
        return response()->json(['status' => $simpan, 'message' => 'Sukses']);
    }

    public function ukm_delete(Request $request)
    {
        try {
            $q = AnggotaUkm::find($request->id);
            $q->delete();
            return response()->json(['status' => true, 'message' => 'Sukses']);
        } catch (\Exception $err) {
            return response()->json(['status' => false, 'message' => $err->getMessage()]);
        }
    }
}
