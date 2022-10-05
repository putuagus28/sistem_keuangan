<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kegiatan extends Model
{
    use HasFactory;
    use \App\Traits\TraitUuid;
    protected $table = 'kegiatans';

    public function detail()
    {
        return $this->hasMany(DetailKegiatan::class, 'kegiatans_id', 'id');
    }

    public function ukm()
    {
        return $this->belongsTo(Ukm::class, 'ukms_id', 'id');
    }

    public function users()
    {
        return $this->belongsTo(User::class, 'users_id', 'id');
    }
}
