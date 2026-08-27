<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PerfilServicio extends Model
{
    protected $table = 'perfilservicio';

    protected $primaryKey = 'idperfilservicio';

    public $timestamps = false;

    protected $fillable = [
        'idprofesionalsalud',
        'idservicio',
        'idtiposervicio',
        'fechaasignacion',
        'estadoperfil',
    ];

    public function profesional(): BelongsTo
    {
        return $this->belongsTo(
            ProfesionalSalud::class,
            'idprofesionalsalud',
            'idprofesionalsalud'
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
