<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailKegiatan extends Model
{
    use HasFactory;
    use \App\Traits\TraitUuid;
    protected $table = 'detail_kegiatans';

    public function kegiatan()
    {
        return $this->belongsTo(Kegiatan::class, 'kegiatans_id', 'id');
    }
}
