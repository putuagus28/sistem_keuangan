<?php

namespace App\Http\Controllers;

use App\AnggotaUkm;
use App\Ukm;
use App\Mahasiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\File;
use App\User;
use Illuminate\Support\Facades\Session;

class AnggotaController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = AnggotaUkm::with('ukm', 'mhs', 'pembayaran')
                ->where('ukms_id', Session::get('ukms_id'))
                ->where('jabatan', '!=', 'pembina')
                ->get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = "";
                    if (Session::get('jabatan') == 'bendahara') {
                        if (!in_array($row->jabatan, ['ketua', 'bendahara'])) {
                            $btn .= '<a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-danger btn-sm mx-1" id="hapus"><i class="fa fa-trash" aria-hidden="true"></i></a>';
                        }
                        $btn .= '<a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-success btn-sm mx-1" id="edit"><i class="fas fa-edit"></i></a>';
                    }
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        $data = [
            'ukm' => Ukm::find(Session::get('ukms_id')),
            'pembina' => AnggotaUkm::with('ukm', 'pembina')
                ->where('ukms_id', Session::get('ukms_id'))
                ->where('jabatan', 'pembina')
                ->first(),
        ];
        return view('ukm.anggota', $data);
    }

    public function insert(Request $request)
    {
        $ukms_id = Session::get('ukms_id');
        $mhs = new Mahasiswa;
        $cek_mhs = $mhs->where(['nim' => $request->nim]);
        $row_mhs = $cek_mhs->first();
        if ($cek_mhs->exists()) {
            // langsung insert ke anggota ukm
            $anggota = new AnggotaUkm;
            $cek = $anggota->where([
                'users_global' => $row_mhs->id,
                'ukms_id' => $ukms_id,
            ])->exists();
            if ($cek) {
                return response()->json(['status' => false, 'message' => 'Mahasiswa sudah terdaftar di anggota ukm ini!']);
            } else {
                $q = new AnggotaUkm;
                $q->ukms_id = $ukms_id;
                $q->jabatan = 'anggota_biasa';
                $q->users_global = $row_mhs->id;
                $q->users_id = auth()->user()->id;
                $simpan = $q->save();
                return response()->json(['status' => false, 'message' => 'Sukses']);
            }
        } else {
        try {
            $last_id = '';
            $mhs = new Mahasiswa;
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
            // user login
            $user = new User;
            $user->users_global = $last_id;
            $user->name = $request->name;
            $user->email = $request->email;
            $user->username = $request->nim;
            $user->password = bcrypt($request->nim);
            $user->role = 'mahasiswa';
            $user->save();
            // anggota baru
            $q = new AnggotaUkm;
            $q->ukms_id = $ukms_id;
            $q->jabatan = 'anggota_biasa';
            $q->users_global = $last_id;
            $q->users_id = auth()->user()->id;
            $simpan = $q->save();
            return response()->json(['status' => $simpan, 'message' => 'Sukses']);
        } catch (\Exception $err) {
            return response()->json(['status' => false, 'message' => $err->getMessage()]);
        }
        }
    }

    public function edit(Request $request)
    {
        $q = AnggotaUkm::with('ukm', 'mhs', 'pembayaran')->where('id', $request->id)->first();
        return response()->json($q);
    }

    public function delete(Request $request)
    {
        $query  = AnggotaUkm::find($request->id);
        $mhs    = Mahasiswa::find($query->users_global);
        $foto   = $mhs->foto;
        $query->pembayaran()->delete();
        $query->users()->delete();
        $query->mhs()->delete();
        $del    = $query->delete();
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
            $ukm  = AnggotaUkm::find($request->id);
            $query = new Mahasiswa;
            $up = $query->find($ukm->users_global);
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
                $d_users = User::where('users_global', $ukm->users_global)->first();
                $user = User::find($d_users->id);
                $user->users_global = $request->id;
                $user->name = $request->name;
                $user->email = $request->email;
                $user->username = $request->nim;
                $user->password = bcrypt($request->nim);
                $user->save();
                return response()->json(['status' => $simpan, 'message' => 'Tersimpan']);
            }
        } catch (\Exception $err) {
            return response()->json(['status' => false, 'message' => $err->getMessage()]);
        }
    }
}
