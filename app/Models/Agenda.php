<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Agenda extends Model
{
    protected $table = 'AGENDA';

    protected $primaryKey = 'idhorariodispo';

    public $timestamps = false;

    protected $fillable = [
        'idprofesionalsalud',
        'idsede',
        'fecha',
        'horainicio',
        'consultorio',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    public function profesional(): BelongsTo
    {
        return $this->belongsTo(
            ProfesionalSalud::class,
            'idprofesionalsalud',
            'idprofesionalsalud'
        );
    }

    public function sede(): BelongsTo
    {
        return $this->belongsTo(
            Sede::class,
            'idsede',
            'idsede'
        );
    }
}
