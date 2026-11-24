<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cita;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Agenda;
use App\Models\Servicio;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use App\Services\Citas\GestionCitasService;

class CitaController extends Controller
{
    public function index(Request $request): View
    {
        $datos = $request->validate([
            'estado' => [
                'nullable',
                'in:Pendiente,Realizada,Cancelada',
            ],
            'fecha' => [
                'nullable',
                'date_format:Y-m-d',
            ],
        ]);

        $citas = Cita::query()
            ->with([
                'paciente.user',
                'servicio',
                'tipoServicio',
                'agenda.profesional.user',
                'agenda.sede',
            ])
            ->when(
                $datos['estado'] ?? null,
                fn($query, $estado) =>
                $query->where('estadocita', $estado)
            )
            ->when(
                $datos['fecha'] ?? null,
                fn($query, $fecha) =>
                $query->whereDate('fechacita', $fecha)
            )
            ->orderBy('fechacita', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('admin.citas.index', compact('citas'));
    }

    public function show(Cita $cita): View
    {
        $cita->load([
            'paciente.user',
            'servicio',
            'tipoServicio',
            'agenda.profesional.user',
            'agenda.sede',
        ]);

        return view('admin.citas.show', compact('cita'));
    }
    public function edit(
        Cita $cita,
        GestionCitasService $gestionCitas
    ): View|RedirectResponse {
        if ($cita->estadocita !== 'Pendiente') {
            return redirect()
                ->route('admin.citas.show', $cita)
                ->with('error', 'Esta cita no puede ser modificada.');
        }

        if (! $gestionCitas->tienePlazoParaModificar($cita)) {
            return redirect()
                ->route('admin.citas.show', $cita)
                ->with('error', 'La cita debe estar al menos a 24 horas de su inicio.');
        }

        $cita->load([
            'paciente.user',
            'servicio',
            'tipoServicio',
            'agenda.profesional.user',
            'agenda.sede',
        ]);

        $puedeReprogramar = (int) $cita->numreprogramaciones < 3;

        $horarios = collect();

        if ($puedeReprogramar) {
            $limite = now()->addHours(24);

            $segundosLimite =
                ($limite->hour * 3600) +
                ($limite->minute * 60) +
                $limite->second;

            $horarios = Agenda::query()
                ->whereHas('profesional.perfilesServicio', function ($query) use ($cita) {
                    $query->where('idservicio', $cita->idservicio)
                        ->where('estadoperfil', 'Activo');
                })
                ->whereHas('profesional', function ($query) {
                    $query->where('estadoprofesionalsalud', 'Activo')
                        ->whereHas('user', function ($query) {
                            $query->where('estado', 'activo');
                        });
                })
                ->where(function ($query) use ($limite, $segundosLimite) {
                    $query->where('fecha', '>', $limite->toDateString())
                        ->orWhere(function ($query) use ($limite, $segundosLimite) {
                            $query->where('fecha', $limite->toDateString())
                                ->whereRaw(
                                    "horainicio >= NUMTODSINTERVAL(?, 'SECOND')",
                                    [$segundosLimite]
                                );
                        });
                })
                ->where(function ($query) use ($cita) {
                    $query->whereDoesntHave('cita', function ($query) {
                        $query->where('estadocita', '!=', 'Cancelada');
                    })
                        ->orWhere(
                            'idhorariodispo',
                            $cita->idhorariodispo
                        );
                })
                ->with([
                    'profesional.user',
                    'sede',
                ])
                ->orderBy('fecha')
                ->orderBy('horainicio')
                ->get();
        }

        return view(
            'admin.citas.edit',
            compact('cita', 'horarios', 'puedeReprogramar')
        );
    }

    public function update(
        Request $request,
        Cita $cita,
        GestionCitasService $gestionCitas
    ): RedirectResponse {
        $datos = $request->validate([
            'idhorariodispo' => [
                'required',
                'integer',
            ],
            'detalle' => [
                'nullable',
                'string',
                'max:100',
            ],
        ]);

        try {
            DB::transaction(function () use ($datos, $cita, $gestionCitas) {

                // Bloquear la cita para evitar modificaciones simultáneas.
                $citaActual = Cita::where('idcita', $cita->idcita)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($citaActual->estadocita !== 'Pendiente') {
                    throw new \RuntimeException(
                        'Esta cita ya no puede ser modificada.'
                    );
                }

                if (! $gestionCitas->tienePlazoParaModificar($citaActual)) {
                    throw new \RuntimeException(
                        'La cita debe estar al menos a 24 horas de su inicio.'
                    );
                }

                $nuevoIdHorario = (int) $datos['idhorariodispo'];

                $reprogramar = $nuevoIdHorario !==
                    (int) $citaActual->idhorariodispo;

                // Oracle interpreta las cadenas vacías como NULL.
                $detalle = trim((string) ($datos['detalle'] ?? ''));

                if ($detalle === '') {
                    $detalle = 'Sin observaciones';
                }

                // Cambiar observaciones no consume una reprogramación.
                if (! $reprogramar) {
                    $citaActual->update([
                        'detalle' => $detalle,
                    ]);

                    return;
                }

                if ((int) $citaActual->numreprogramaciones >= 3) {
                    throw new \RuntimeException(
                        'La cita alcanzó el máximo de 3 reprogramaciones.'
                    );
                }

                // Verificar que el servicio sigue activo.
                $servicio = Servicio::where(
                    'idservicio',
                    $citaActual->idservicio
                )
                    ->where('estadoservicio', 'Activo')
                    ->whereHas('tipoServicio', function ($query) {
                        $query->where('estadotiposervicio', 'Activo');
                    })
                    ->first();

                if (! $servicio) {
                    throw new \RuntimeException(
                        'El servicio de esta cita ya no está disponible.'
                    );
                }

                $limite = now('America/Bogota')->addHours(24);

                $segundosLimite =
                    ($limite->hour * 3600) +
                    ($limite->minute * 60) +
                    $limite->second;

                // Obtener y bloquear el horario solicitado.
                $agenda = Agenda::where(
                    'idhorariodispo',
                    $nuevoIdHorario
                )
                    ->whereHas(
                        'profesional.perfilesServicio',
                        function ($query) use ($citaActual) {
                            $query->where(
                                'idservicio',
                                $citaActual->idservicio
                            )
                                ->where('estadoperfil', 'Activo');
                        }
                    )
                    ->whereHas('profesional', function ($query) {
                        $query->where(
                            'estadoprofesionalsalud',
                            'Activo'
                        )
                            ->whereHas('user', function ($query) {
                                $query->where('estado', 'activo');
                            });
                    })
                    ->where(function ($query) use (
                        $limite,
                        $segundosLimite
                    ) {
                        $query->where(
                            'fecha',
                            '>',
                            $limite->toDateString()
                        )
                            ->orWhere(function ($query) use (
                                $limite,
                                $segundosLimite
                            ) {
                                $query->where(
                                    'fecha',
                                    $limite->toDateString()
                                )
                                    ->whereRaw(
                                        "horainicio >= NUMTODSINTERVAL(?, 'SECOND')",
                                        [$segundosLimite]
                                    );
                            });
                    })
                    ->lockForUpdate()
                    ->first();

                if (! $agenda) {
                    throw new \RuntimeException(
                        'El horario seleccionado ya no está disponible.'
                    );
                }

                // Comprobar que no exista otra cita activa en el horario.
                $horarioOcupado = Cita::where(
                    'idhorariodispo',
                    $agenda->idhorariodispo
                )
                    ->where('idcita', '!=', $citaActual->idcita)
                    ->where('estadocita', '!=', 'Cancelada')
                    ->exists();

                if ($horarioOcupado) {
                    throw new \RuntimeException(
                        'El horario ya está reservado por otro paciente.'
                    );
                }

                // Extraer la hora del INTERVAL DAY TO SECOND de Oracle.
                $hora = $agenda->getRawOriginal('horainicio');

                if (! preg_match(
                    '/(\d{2}):(\d{2}):(\d{2})/',
                    (string) $hora,
                    $partes
                )) {
                    throw new \RuntimeException(
                        'No fue posible determinar la hora del horario.'
                    );
                }

                // Construir la fecha y hora de la nueva cita.
                $fecha = Carbon::parse(
                    $agenda->fecha,
                    'America/Bogota'
                );

                $fechacita = Carbon::create(
                    $fecha->year,
                    $fecha->month,
                    $fecha->day,
                    (int) $partes[1],
                    (int) $partes[2],
                    (int) $partes[3],
                    'America/Bogota'
                );

                // Validación final de anticipación.
                if ($fechacita->lt(
                    now('America/Bogota')->addHours(24)
                )) {
                    throw new \RuntimeException(
                        'El nuevo horario debe estar al menos a 24 horas de distancia.'
                    );
                }

                // Guardar la reprogramación.
                $citaActual->update([
                    'idhorariodispo' => $agenda->idhorariodispo,
                    'fechacita' => $fechacita,
                    'detalle' => $detalle,
                    'numreprogramaciones' =>
                    (int) $citaActual->numreprogramaciones + 1,
                ]);
            });
        } catch (\RuntimeException $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }

        return redirect()
            ->route('admin.citas.show', $cita)
            ->with('success', 'La cita fue actualizada correctamente.');
    }

    public function cancelar(
        Cita $cita,
        GestionCitasService $gestionCitas
    ): RedirectResponse {
        try {
            $gestionCitas->cancelar($cita);
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()
            ->route('admin.citas.show', $cita)
            ->with('success', 'La cita fue cancelada correctamente.');
    }
}
