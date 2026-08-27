<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Paciente extends Model
{
    protected $table = 'paciente';

    protected $primaryKey = 'idpaciente';

    public $timestamps = false;

    protected $fillable = [
        'tipodoc',
        'numdoc',
        'telefono',
        'direccion',
        'ciudad',
        'fecharegistro',
        'estadopaciente',
        'idusuario',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'idusuario',
            'id'
        );
    }

    public function citas(): HasMany
    {
        return $this->hasMany(
            Cita::class,
            'idpaciente',
            'idpaciente'
        );
    }
}
