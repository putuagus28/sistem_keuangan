<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnggotaUkm extends Model
{
    use HasFactory;
    use \App\Traits\TraitUuid;
    protected $table = 'anggota_ukms';

    public function ukm()
    {
        return $this->belongsTo(Ukm::class, 'ukms_id', 'id');
    }

    public function pembayaran()
    {
        return $this->hasMany(Pembayaran::class, 'mahasiswas_id', 'users_global');
    }

    public function mhs()
    {
        return $this->belongsTo(Mahasiswa::class, 'users_global', 'id');
    }

    public function users()
    {
        return $this->belongsTo(User::class, 'users_global', 'users_global');
    }

    public function pembina()
    {
        return $this->belongsTo(Pembina::class, 'users_global', 'id');
    }
}
