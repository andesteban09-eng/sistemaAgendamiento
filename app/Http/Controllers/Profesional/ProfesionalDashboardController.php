<?php

namespace App\Http\Controllers\Profesional;

use App\Http\Controllers\Controller;
use App\Models\Cita;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProfesionalDashboardController extends Controller
{
    public function index(): View
    {
        $usuario = Auth::user();
        $profesional = $usuario->profesional;

        abort_unless($usuario->estado === 'activo', 403);

        $perfilActivo =
            $profesional?->estadoprofesionalsalud === 'Activo';

        $ahora = now();

        // Inicializar siempre la colección.
        $citasRaw = collect();

        if ($perfilActivo) {
            $citasRaw = Cita::whereHas('agenda', function ($query) use ($profesional) {
                $query->where(
                    'idprofesionalsalud',
                    $profesional->idprofesionalsalud
                );
            })
                ->with([
                    'servicio',
                    'paciente.user',
                    'agenda.sede',
                ])
                ->orderBy('fechacita')
                ->orderBy('idcita')
                ->get();
        }

        // Indicadores de la jornada.

        $citasJornada = $citasRaw->filter(
            fn ($cita) => $cita->fechacita?->isSameDay($ahora)
        );

        $citasHoy = $citasJornada->count();

        $citasPendientes = $citasJornada
            ->where('estadocita', 'Pendiente')
            ->count();

        $citasConfirmadas = $citasJornada
            ->where('estadocita', 'Confirmada')
            ->count();

        $citasRealizadas = $citasJornada
            ->whereIn('estadocita', ['Realizada', 'Atendida'])
            ->count();

        $citasCanceladas = $citasJornada
            ->where('estadocita', 'Cancelada')
            ->count();

        // Eventos de FullCalendar.

        $citas = $citasRaw->map(function ($cita) {

            $color = match ($cita->estadocita) {
                'Pendiente' => '#a16207',
                'Confirmada' => '#087e98',
                'Realizada', 'Atendida' => '#15803d',
                'Cancelada' => '#b91c1c',
                default => '#64748b',
            };

            $usuarioPaciente = $cita->paciente?->user;

            $pacienteNombre = trim(
                ($usuarioPaciente?->name ?? '') . ' ' .
                ($usuarioPaciente?->last_name ?? '')
            );

            $pacienteNombre =
                $pacienteNombre ?: 'Paciente no asignado';

            $servicioNombre =
                $cita->servicio?->nombre ?? 'Sin servicio';

            $puedeRealizar =
                $cita->estadocita === 'Confirmada';

            return [
                'id' => (string) $cita->idcita,

                'title' => "{$pacienteNombre} - {$servicioNombre}",

                'start' => $cita->fechacita?->format('Y-m-d\TH:i:s'),

                'color' => $color,

                'paciente' => $pacienteNombre,

                'servicio' => $servicioNombre,

                'estadoCita' => $cita->estadocita,

                'observacion' => $cita->detalle,

                'fecha' => $cita->fechacita?->format('d/m/Y'),

                'hora' => $cita->fechacita?->format('g:i A'),

                'sede' => $cita->agenda?->sede?->nombre,

                'consultorio' => $cita->agenda?->consultorio,

                'puedeRealizar' => $puedeRealizar,

                'urlRealizar' => $puedeRealizar
                    ? route('profesional.citas.realizar', $cita)
                    : null,
            ];

        })->values();

        // Próxima cita programada.

        $proximaCitaRaw = $citasRaw->first(
            fn ($cita) =>
                $cita->fechacita &&
                $cita->fechacita->gte($ahora) &&
                in_array(
                    $cita->estadocita,
                    ['Pendiente', 'Confirmada'],
                    true
                )
        );

        $proximaCita = $proximaCitaRaw
            ? $citas->firstWhere(
                'id',
                (string) $proximaCitaRaw->idcita
            )
            : null;

        // Citas confirmadas correspondientes al día de hoy.

        $citasPorAtender = $citas->filter(
            fn ($cita) =>
                $cita['estadoCita'] === 'Confirmada' &&
                $cita['fecha'] === $ahora->format('d/m/Y')
        )->values();

        // Todas las variables se entregan siempre a Blade.

        return view('profesional.dashboardProfesional', compact(
            'perfilActivo',
            'citas',
            'citasHoy',
            'citasPendientes',
            'citasConfirmadas',
            'citasRealizadas',
            'citasCanceladas',
            'proximaCita',
            'citasPorAtender'
        ));
    }

    public function realizar(Cita $cita): RedirectResponse
    {
        $usuario = Auth::user();
        $profesional = $usuario->profesional;

        abort_unless(
            $usuario->rol === 'profesional' &&
            $usuario->estado === 'activo' &&
            $profesional?->estadoprofesionalsalud === 'Activo',
            403
        );

        $realizada = DB::transaction(function () use ($cita, $profesional) {

            $citaActual = Cita::where('idcita', $cita->idcita)
                ->whereHas('agenda', function ($query) use ($profesional) {
                    $query->where(
                        'idprofesionalsalud',
                        $profesional->idprofesionalsalud
                    );
                })
                ->lockForUpdate()
                ->firstOrFail();

            $agenda = $citaActual->agenda()
                ->lockForUpdate()
                ->firstOrFail();

            abort_unless(
                (string) $agenda->idprofesionalsalud ===
                (string) $profesional->idprofesionalsalud,
                403
            );

            if ($citaActual->estadocita !== 'Confirmada') {
                return false;
            }

            return $citaActual->update([
                'estadocita' => 'Realizada',
            ]);

        }, 3);

        if (!$realizada) {
            return redirect()
                ->route('dashboard')
                ->with(
                    'error',
                    'Solo se pueden finalizar citas confirmadas.'
                );
        }

        return redirect()
            ->route('dashboard')
            ->with(
                'success',
                'La atención fue registrada correctamente.'
            );
    }
}
