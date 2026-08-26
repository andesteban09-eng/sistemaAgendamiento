<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Servicio extends Model
{
    protected $table = 'SERVICIO';

    protected $primaryKey = 'idservicio';

    public $timestamps = false;

    protected $fillable = [
        'idtiposervicio',
        'nombre',
        'precio',
        'prerequisitos',
        'estadoservicio',
    ];

    public function tipoServicio(): BelongsTo
    {
        return $this->belongsTo(
            TipoServicio::class,
            'idtiposervicio',
            'idtiposervicio'
        );
    }

    public function perfilesServicio(): HasMany
    {
        return $this->hasMany(
            PerfilServicio::class,
            'idservicio',
            'idservicio'
        );
    }
}
