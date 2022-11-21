<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;
    use \App\Traits\TraitUuid;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'role', 'username', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function mhs()
    {
        return $this->belongsTo(Mahasiswa::class, 'users_global', 'id');
    }

    public function pembina()
    {
        return $this->belongsTo(Pembina::class, 'users_global', 'id');
    }

    public function anggota_ukm()
    {
        return $this->belongsTo(AnggotaUkm::class, 'users_global', 'users_global');
    }

    public function kemahasiswaan()
    {
        return $this->belongsTo(Kemahasiswaan::class, 'users_global', 'id');
    }
}
