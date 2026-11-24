<?php

namespace App\Services\Citas;

use App\Models\Cita;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class GestionCitasService
{
    public function cancelar(Cita $cita, ?int $idpaciente = null): void
    {
        DB::transaction(function () use ($cita, $idpaciente) {

            $citaActual = Cita::where('idcita', $cita->idcita)
                ->lockForUpdate()
                ->firstOrFail();

            if (
                $idpaciente !== null &&
                (string) $citaActual->idpaciente !== (string) $idpaciente
            ) {
                abort(403);
            }

            if ($citaActual->estadocita !== 'Pendiente') {
                throw new \RuntimeException(
                    'Esta cita ya no puede ser cancelada.'
                );
            }

            if (! $this->tienePlazoParaModificar($citaActual)) {
                throw new \RuntimeException(
                    'Solo se puede cancelar una cita hasta 24 horas antes de su inicio.'
                );
            }

            $citaActual->update([
                'estadocita' => 'Cancelada',
            ]);
        });
    }

    public function tienePlazoParaModificar(Cita $cita): bool
    {
        $inicio = Carbon::parse(
            $cita->fechacita,
            'America/Bogota'
        );

        return now()->lte(
            $inicio->copy()->subHours(24)
        );
    }
}
