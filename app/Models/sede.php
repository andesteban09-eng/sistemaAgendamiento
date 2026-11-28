<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sede extends Model
{
    protected $table = 'sede';

    protected $primaryKey = 'idsede';

    public $timestamps = false;

    public $incrementing = true;

    protected $keyType = 'int';

    public const ESTADO_ACTIVO = 'Activo';

    public const ESTADO_INACTIVO = 'Inactivo';

    protected $fillable = [
        'nombre',
        'ciudad',
        'direccion',
        'detalles',
        'estadosede',
    ];

    public function agendas(): HasMany
    {
        return $this->hasMany(
            Agenda::class,
            'idsede',
            'idsede'
        );
    }
}
