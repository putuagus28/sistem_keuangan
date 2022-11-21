<?php

namespace App\Http\Controllers;

use App\AnggotaUkm;
use App\Nasabah;
use App\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;

class LoginController extends Controller
{
    /** halaman akun */
    public function index()
    {
        $data['user'] = User::all();
        return view('profile.index', $data);
    }

    /** fungsi crud data akun */
    public function get()
    {
        if (Auth::guard('pegawai')->check()) {
            $data['profile'] = Pegawai::find(auth()->guard('pegawai')->user()->id);
        } elseif (Auth::guard('user')->check()) {
            $data['profile'] = User::find(auth()->guard('user')->user()->id);
        } elseif (Auth::guard('nasabah')->check()) {
            $data['profile'] = Nasabah::find(auth()->guard('nasabah')->user()->id);
        }
        return response()->json($data);
    }

    public function updateData(Request $request)
    {
        if (Auth::guard('pegawai')->check()) {
            $guard = new Pegawai;
        } elseif (Auth::guard('user')->check()) {
            $guard = new User;
        } elseif (Auth::guard('nasabah')->check()) {
            $guard = new Nasabah;
        }
        $update = $guard->find($request->id);
        $update->name = $request->name;
        $update->email = Str::lower($request->email);
        if (!empty($request->password)) {
            $update->password = Hash::make($request->password);
        }
        $update->alamat = $request->alamat;
        $update->tanggalLahir = date('Y-m-d', strtotime($request->tanggalLahir));
        $update->noKtp = $request->noKtp;
        // jika user upload berkas
        if ($request->hasFile('foto')) {
            $fotolama = $guard->find($request->id)->foto;
            $file = $request->file('foto');
            if ($fotolama != null) {
                $image_path = public_path() . '/users/' . $fotolama;
                if (File::exists($image_path)) {
                    unlink($image_path);
                }
            }
            $extension = $file->getClientOriginalExtension();
            $fileName = rand(11111, 99999) . '.' . $extension;
            // upload ke folder
            $file->move(public_path() . '/users/', $fileName);
            $update->foto = $fileName;
        }
        $update->noTlpn = $request->noTlpn;
        $update->save();
        return response()->json(['status' => $update, 'message' => 'Sukses Update']);
    }

    /** fungsi login */
    public function getLogin()
    {
        return view('login');
    }

    public function postLogin(Request $request)
    {
        //LAKUKAN PENGECEKAN, JIKA INPUTAN DARI USERNAME FORMATNYA ADALAH EMAIL, MAKA KITA AKAN MELAKUKAN PROSES AUTHENTICATION MENGGUNAKAN EMAIL, SELAIN ITU, AKAN MENGGUNAKAN USERNAME
        $loginType = filter_var($request->username, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        //TAMPUNG INFORMASI LOGINNYA, DIMANA KOLOM TYPE PERTAMA BERSIFAT DINAMIS BERDASARKAN VALUE DARI PENGECEKAN DIATAS
        $login = [
            $loginType => $request->username,
            'password' => $request->password
        ];
        if (Auth::guard('user')->attempt($login)) {
            if (empty(auth()->guard('user')->user()->anggota_ukm)) {
                return response()->json([
                    'success' => true,
                    'role' => auth()->guard('user')->user()->role,
                    'message' => 'Login Sukses!'
                ]);
            } else {
                if (auth()->guard('user')->user()->anggota_ukm->jabatan == "anggota_biasa") {
                    Auth::guard('user')->logout();
                    Session::flush();
                    return response()->json([
                        'success' => false,
                        'message' => 'Anda tidak punya hak akses untuk login'
                    ]);
                } else {
                    return response()->json([
                        'success' => true,
                        'role' => auth()->guard('user')->user()->role,
                        'message' => 'Login Sukses!'
                    ]);
                }
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Username atau password tidak terdaftar!'
            ]);
        }
    }

    public function setprivilege(Request $request)
    {
        if ($request->has('ukms_id')) {
            if($request->jabatan!="anggota_biasa"){
                Session::put('ukms_id', $request->ukms_id);
                Session::put('jabatan', $request->jabatan);
                return redirect()->route('dashboard');
            }else{
                return redirect()->back()->with(['info' => 'Anggota biasa tidak memiliki akses ke halaman ini']);
            }
        }
        $data['ukm'] = AnggotaUkm::with('ukm')->where('users_global', auth()->user()->users_global)->get();
        return view('setprivilege', $data);
    }

    public function logout()
    {
        if (Auth::guard('user')->check()) {
            Auth::guard('user')->logout();
            Session::flush();
            return redirect()->route('login');
        }
    }
}
