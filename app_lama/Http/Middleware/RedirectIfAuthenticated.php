<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $akses = array("mahasiswa", "pembina", "kemahasiswaan");

        if (Auth::guard('user')->check()) {

            if (in_array(Auth::guard('user')->user()->role, $akses)) {
                session()->flash('info', '<strong>Oppss</strong>, Silahkan logout terlebih dahulu !');
                return redirect()->route('dashboard');
            }
        }

        return $next($request);
    }
}
