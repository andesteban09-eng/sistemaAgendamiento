<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class TipoServicio extends Model
{
    protected $table = 'TIPOSERVICIO';

    protected $primaryKey = 'IDTIPOSERVICIO';

    public $timestamps = false;

    protected $fillable = [
        'NOMBRE',
        'DESCRIPCION',
        'ESTADOTIPOSERVICIO',
    ];

    public function servicios(): HasMany
    {
        return $this->hasMany(
            Servicio::class,
            'IDTIPOSERVICIO',
            'IDTIPOSERVICIO'
        );
    }
}
