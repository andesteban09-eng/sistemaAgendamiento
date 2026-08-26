<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoServicio extends Model
{
    protected $table = 'TIPOSERVICIO';

    protected $primaryKey = 'idtiposervicio';

    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'descripcion',
        'estadotiposervicio',
    ];

    public function servicios(): HasMany
    {
        return $this->hasMany(
            Servicio::class,
            'idtiposervicio',
            'idtiposervicio'
        );
    }
}
