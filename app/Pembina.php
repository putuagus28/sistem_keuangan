<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembina extends Model
{
    use HasFactory;
    use \App\Traits\TraitUuid;
    protected $table = 'pembinas';

    public function anggota()
    {
        return $this->hasMany(AnggotaUkm::class,'users_global','id');
    }
}
