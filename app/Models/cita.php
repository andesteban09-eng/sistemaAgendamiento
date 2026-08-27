<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cita extends Model
{
    protected $table = 'cita';

    protected $primaryKey = 'idcita';

    public $timestamps = false;

    protected $fillable = [
        'idpaciente',
        'idtiposervicio',
        'idhorariodispo',
        'idservicio',
        'fechacita',
        'detalle',
        'estadocita',
    ];

    protected $casts = [
        'fechacita' => 'datetime',
    ];

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(
            Paciente::class,
            'idpaciente',
            'idpaciente'
        );
    }

    public function agenda(): BelongsTo
    {
        return $this->belongsTo(
            Agenda::class,
            'idhorariodispo',
            'idhorariodispo'
        );
    }

    public function servicio(): BelongsTo
    {
        return $this->belongsTo(
            Servicio::class,
            'idservicio',
            'idservicio'
        );
    }

    public function tipoServicio(): BelongsTo
    {
        return $this->belongsTo(
            TipoServicio::class,
            'idtiposervicio',
            'idtiposervicio'
        );
    }
}
