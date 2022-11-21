<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Akun extends Model
{
    use HasFactory;
    use \App\Traits\TraitUuid;
    protected $table = 'akuns';

    public function ukm()
    {
        return $this->belongsTo(Ukm::class, 'ukms_id', 'id');
    }

    public function mhs()
    {
        return $this->belongsTo(Mahasiswa::class, 'users_global', 'id');
    }

    public function users()
    {
        return $this->belongsTo(User::class, 'users_id', 'id');
    }
}
