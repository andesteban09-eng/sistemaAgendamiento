<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfesionalSalud extends Model
{
    protected $table = 'profesionalsalud';

    protected $primaryKey = 'idprofesionalsalud';

    public $timestamps = false;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'iduser', 'id');
    }
}
