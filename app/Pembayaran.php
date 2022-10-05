<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    use HasFactory;
    use \App\Traits\TraitUuid;
    protected $table = 'pembayarans';

    public function ukm()
    {
        return $this->belongsTo(Ukm::class, 'ukms_id', 'id');
    }

    public function mhs()
    {
        return $this->belongsTo(Mahasiswa::class, 'mahasiswas_id', 'id');
    }

    public function users()
    {
        return $this->belongsTo(User::class, 'users_id', 'id');
    }
}
