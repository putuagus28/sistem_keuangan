<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CekLevel
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, ...$roles)
    {
        $akses = array("mahasiswa", "pembina", "kemahasiswaan");
        if (Auth::guard('user')->check()) {
            if (in_array($request->user()->role, $roles)) {
                return $next($request);
            }

            if (in_array(Auth::guard('user')->user()->role, $akses)) {
                session()->flash('info', '<strong>Oppss</strong>, Anda tidak memiliki akses ke halaman itu!');
                return redirect()->route('dashboard');
            }
        }
    }
}
