<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sede extends Model
{
    protected $table = 'SEDE';

    protected $primaryKey = 'idsede';

    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'ciudad',
        'direccion',
        'detalles',
        'estadosede',
    ];
}
