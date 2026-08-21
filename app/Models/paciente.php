<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Paciente extends Model
{
    protected $table = 'paciente';

    protected $primaryKey = 'idpaciente';

    public $timestamps = false;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'idusuario', 'id');
    }
}
