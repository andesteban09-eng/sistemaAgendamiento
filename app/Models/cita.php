<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cita extends Model
{
    protected $table = 'CITA';

    protected $primaryKey = 'IDCITA';

    public $timestamps = false;

    protected $fillable = [
        'IDPACIENTE',
        'IDTIPOSERVICIO',
        'IDHORARIODISPO',
        'IDSERVICIO',
        'FECHACITA',
        'DETALLE',
        'ESTADOCITA',
    ];

    protected $casts = [
        'FECHACITA' => 'datetime',
    ];

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(
            Paciente::class,
            'IDPACIENTE',
            'IDPACIENTE'
        );
    }

    public function agenda(): BelongsTo
    {
        return $this->belongsTo(
            Agenda::class,
            'IDHORARIODISPO',
            'IDHORARIODISPO'
        );
    }

    public function servicio(): BelongsTo
    {
        return $this->belongsTo(
            Servicio::class,
            'IDSERVICIO',
            'idservicio'
        );
    }

    public function tipoServicio(): BelongsTo
    {
        return $this->belongsTo(
            TipoServicio::class,
            'IDTIPOSERVICIO',
            'IDTIPOSERVICIO'
        );
    }
}
