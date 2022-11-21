<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ukm extends Model
{
    use HasFactory;
    use \App\Traits\TraitUuid;
    protected $table = 'ukms';

    public function users()
    {
        return $this->belongsTo(User::class, 'users_id', 'id');
    }

    public function anggota()
    {
        return $this->hasMany(AnggotaUkm::class, 'ukms_id', 'id');
    }
}
