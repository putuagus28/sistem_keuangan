<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengeluaran extends Model
{
    use HasFactory;
    use \App\Traits\TraitUuid;
    protected $table = 'pengeluarans';

    public function ukm()
    {
        return $this->belongsTo(Ukm::class, 'ukms_id', 'id');
    }

    public function users()
    {
        return $this->belongsTo(User::class, 'users_id', 'id');
    }
}
