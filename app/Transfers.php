<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transfers extends Model
{
    use HasFactory;
    use \App\Traits\TraitUuid;
    protected $table = 'transfers';

    public function ukm()
    {
        return $this->belongsTo(Ukm::class, 'ukms_id', 'id');
    }

    public function akun_dari()
    {
        return $this->belongsTo(Akun::class, 'akun_dari', 'id');
    }

    public function akun_tujuan()
    {
        return $this->belongsTo(Akun::class, 'akun_tujuan', 'id');
    }

    public function users()
    {
        return $this->belongsTo(User::class, 'users_id', 'id');
    }
}
