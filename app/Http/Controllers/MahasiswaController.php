<?php

namespace App\Http\Controllers;

use App\AnggotaUkm;
use Illuminate\Http\Request;
use App\Mahasiswa;
use App\Ukm;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\File;
use App\User;

class MahasiswaController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Mahasiswa::all();
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
                    // $btn .= '<a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-danger btn-sm mx-1" id="hapus"><i class="fa fa-trash" aria-hidden="true"></i></a>';
                    $btn .= '<a href="' . route('ukm.mahasiswa', ['id' => $row->id]) . '" data-id="' . $row->id . '" class="btn btn-danger btn-sm mx-1" id="ukm"><i class="fa fa-plus" aria-hidden="true"></i> Ukm</a>';
                    return $btn;
                })
                ->rawColumns(['action', 'foto'])
                ->make(true);
        }
        return view('mahasiswa.index');
    }

    public function ukm(Request $request, $id_mhs = null)
    {
        if ($request->ajax()) {
            $data = AnggotaUkm::with('ukm')->where('users_global', $id_mhs)->get();
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
            'title' => 'List UKM - ' . ucwords(Mahasiswa::find($id_mhs)->name) . '',
            'id_mhs' => $id_mhs,
            'ukm' => Ukm::all(),
        ];
        return view('mahasiswa.ukm', $data);
    }

    public function ukm_post(Request $request)
    {
        $q = AnggotaUkm::where([
            'ukms_id' => $request->ukms_id,
            // 'jabatan' => $request->jabatan,
            'users_global' => $request->users_global,
        ])->exists();
        if($request->jabatan == 'ketua'){
            $q2 = AnggotaUkm::where([
                'ukms_id' => $request->ukms_id,
                'jabatan' => 'ketua',
            ])->exists();
            if ($q) {
                return response()->json(['status' => false, 'message' => 'Mahasiswa sudah memilih ukm!']);
            } else if ($q2) {
                return response()->json(['status' => false, 'message' => 'Jabatan pada UKM sudah terisi oleh mahasiswa lain!']);
            } else {
                $q1 = new AnggotaUkm;
                $q1->ukms_id = $request->ukms_id;
                $q1->jabatan = $request->jabatan;
                $q1->users_global = $request->users_global;
                $q1->users_id = auth()->user()->id;
                $simpan = $q1->save();
                return response()->json(['status' => $simpan, 'message' => 'Sukses']);
            }
        }else{
            $q2 = AnggotaUkm::where('jabatan','bendahara')->count();
            if ($q) {
                return response()->json(['status' => false, 'message' => 'Mahasiswa sudah memilih ukm!']);
            } else if ($q2 >=2) {
                return response()->json(['status' => false, 'message' => 'Jabatan pada UKM sudah terisi oleh mahasiswa lain!']);
            } else {
                $q1 = new AnggotaUkm;
                $q1->ukms_id = $request->ukms_id;
                $q1->jabatan = $request->jabatan;
                $q1->users_global = $request->users_global;
                $q1->users_id = auth()->user()->id;
                $simpan = $q1->save();
                return response()->json(['status' => $simpan, 'message' => 'Sukses']);
            }
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

    public function insert(Request $request)
    {
        $mhs = new Mahasiswa;
        $cek = $mhs->where(['nim' => $request->nim])->exists();
        if ($cek) {
            return response()->json(['status' => false, 'message' => 'NIM sudah digunakan!']);
        } else {
            $mhs->nim = $request->nim;
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
                $user->username = $request->nim;
                $user->password = bcrypt($request->nim);
                $user->role = 'mahasiswa';
                $user->save();
                return response()->json(['status' => $simpan, 'message' => 'Sukses']);
            }
        }
    }

    public function edit(Request $request)
    {
        $q = Mahasiswa::find($request->id);
        return response()->json($q);
    }

    public function delete(Request $request)
    {
        $query = Mahasiswa::find($request->id);
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
            $query = new Mahasiswa;
            $up = $query->find($request->id);
            $up->nim = $request->nim;
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
                $d_users = User::where('users_global', $request->id)->first();
                $user = User::find($d_users->id);
                $user->users_global = $request->id;
                $user->name = $request->name;
                $user->email = $request->email;
                $user->username = $request->nim;
                $user->password = bcrypt($request->nim);
                $user->save();
                return response()->json(['status' => $simpan, 'message' => 'Sukses']);
            }
        } catch (\Exception $err) {
            return response()->json(['status' => false, 'message' => $err->getMessage()]);
        }
    }
}
