<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mahasiswa extends Model
{
    use HasFactory;
    use \App\Traits\TraitUuid;
    protected $table = 'mahasiswas';

    public function anggota()
    {
        return $this->hasMany(AnggotaUkm::class, 'users_global', 'id');
    }

    public function pembayaran()
    {
        return $this->hasMany(Pembayaran::class, 'mahasiswas_id', 'id');
    }

    // public static function boot()
    // {
    //     parent::boot();
    //     static::deleting(function ($delete) { // before delete() method call this
    //         $delete->anggota()->delete();
    //         $delete->pembayaran()->delete();
    //     });
    // }
}
