<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;
    use \App\Traits\TraitUuid;
    protected $table = 'carts';

    public function ukm()
    {
        return $this->belongsTo(Ukm::class, 'ukms_id', 'id');
    }

    public function akun()
    {
        return $this->belongsTo(Akun::class, 'akuns_id', 'id');
    }

    public function users()
    {
        return $this->belongsTo(User::class, 'users_id', 'id');
    }
}
