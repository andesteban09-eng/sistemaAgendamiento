<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cita extends Model
{
    protected $table = 'CITA'; // Oracle suele guardar en mayúsculas si no se citó al crear la tabla

    protected $primaryKey = 'IDCITA';

    public $timestamps = false;

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class, 'IDPACIENTE', 'IDPACIENTE');
    }

    public function profesional(): BelongsTo
    {
        return $this->belongsTo(ProfesionalSalud::class, 'IDPROFESIONALSALUD', 'IDPROFESIONALSALUD');
    }

    public function sede(): BelongsTo
    {
        // según tu SQL, la sede está enlazada vía "agenda" (idhorariodispo), no directo en cita
        return $this->belongsTo(Sede::class, 'IDSEDE', 'IDSEDE');
    }
}