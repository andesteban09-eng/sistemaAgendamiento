<?php

namespace App\Http\Controllers\Paciente;

use App\Http\Controllers\Controller;
use App\Models\Agenda;
use App\Models\Cita;
use App\Models\Paciente;
use App\Models\Servicio;
use App\Models\TipoServicio;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use App\Services\Citas\GestionCitasService;

class CitaController extends Controller
{
    public function index(): View
    {
        $paciente = Auth::user()->paciente;

        $citas = $paciente
            ? $paciente->citas()
            ->with([
                'servicio',
                'tipoServicio',
                'agenda.profesional.user',
                'agenda.sede',
            ])
            ->orderBy('fechacita', 'desc')
            ->get()
            : collect();

        return view(
            'Paciente.citas.index',
            compact('citas')
        );
    }

    public function create(): View
    {
        $tiposServicio = TipoServicio::where(
            'estadotiposervicio',
            'Activo'
        )
            ->with([
                'servicios' => function ($query) {
                    $query->where(
                        'estadoservicio',
                        'Activo'
                    );
                }
            ])
            ->get();

        return view(
            'Paciente.citas.create',
            compact('tiposServicio')
        );
    }

    public function horarios($idservicio): \Illuminate\Http\JsonResponse
    {
        $servicio = Servicio::query()
            ->where('idservicio', $idservicio)
            ->where('estadoservicio', 'Activo')
            ->whereHas('tipoServicio', function ($query) {
                $query->where('estadotiposervicio', 'Activo');
            })
            ->first();

        if (!$servicio) {
            return response()->json([]);
        }
        $ahora = now();
        $segundosActuales =
            ($ahora->hour * 3600) +
            ($ahora->minute * 60) +
            $ahora->second;

        $horarios = Agenda::whereHas(
            'profesional.perfilesServicio',
            function ($query) use ($idservicio) {
                $query->where('idservicio', $idservicio)
                    ->where('estadoperfil', 'Activo');
            }
        )
            ->whereHas('profesional', function ($query) {
                $query->where('estadoprofesionalsalud', 'Activo')
                    ->whereHas('user', function ($query) {
                        $query->where('estado', 'activo');
                    });
            })
            ->whereDoesntHave('cita', function ($query) {
                $query->where('estadocita', '!=', 'Cancelada');
            })
            ->where(function ($query) use ($ahora, $segundosActuales) {

                $query->where(
                    'fecha',
                    '>',
                    $ahora->toDateString()
                )

                    ->orWhere(function ($query) use ($ahora, $segundosActuales) {

                        $query->where(
                            'fecha',
                            $ahora->toDateString()
                        )
                            ->whereRaw(
                                "horainicio > NUMTODSINTERVAL(?, 'SECOND')",
                                [$segundosActuales]
                            );
                    });
            })
            ->with([
                'profesional.user',
                'sede',
            ])
            ->orderBy('fecha')
            ->orderBy('horainicio')
            ->get();

        return response()->json($horarios);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'idtiposervicio' => [
                'required',
                'integer',
            ],

            'idservicio' => [
                'required',
                'integer',
            ],

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

        $paciente = Auth::user()->paciente;

        if (!$paciente) {
            abort(403);
        }


        /*
    |--------------------------------------------------------------------------
    | SERVICIO
    |--------------------------------------------------------------------------
    */

        $servicio = Servicio::query()
            ->where('idservicio', $request->idservicio)
            ->where('estadoservicio', 'Activo')
            ->whereHas('tipoServicio', function ($query) {
                $query->where('estadotiposervicio', 'Activo');
            })
            ->first();

        if (!$servicio) {

            return back()
                ->withInput()
                ->with(
                    'error',
                    'El servicio seleccionado no está disponible.'
                );
        }


        /*
    |--------------------------------------------------------------------------
    | VALIDAR TIPO DE SERVICIO
    |--------------------------------------------------------------------------
    */

        if (
            (int) $servicio->idtiposervicio !==
            (int) $request->idtiposervicio
        ) {

            return back()
                ->withInput()
                ->with(
                    'error',
                    'El servicio no pertenece al tipo de servicio seleccionado.'
                );
        }


        /*
    |--------------------------------------------------------------------------
    | FECHA Y HORA ACTUAL
    |--------------------------------------------------------------------------
    */

        $ahora = now();

        $segundosActuales =
            ($ahora->hour * 3600) +
            ($ahora->minute * 60) +
            $ahora->second;


        /*
    |--------------------------------------------------------------------------
    | CREAR CITA DENTRO DE UNA TRANSACCIÓN
    |--------------------------------------------------------------------------
    */

        try {

            $cita = DB::transaction(function () use (
                $request,
                $paciente,
                $servicio,
                $ahora,
                $segundosActuales
            ) {

                /*
            |--------------------------------------------------------------------------
            | OBTENER Y BLOQUEAR EL HORARIO
            |--------------------------------------------------------------------------
            */
                $agenda = Agenda::where(
                    'idhorariodispo',
                    $request->idhorariodispo
                )
                    ->whereHas('profesional.perfilesServicio', function ($query) use ($servicio) {
                        $query->where('idservicio', $servicio->idservicio)
                            ->where('estadoperfil', 'Activo');
                    })
                    ->whereHas('profesional', function ($query) {
                        $query->where('estadoprofesionalsalud', 'Activo')
                            ->whereHas('user', function ($query) {
                                $query->where('estado', 'activo');
                            });
                    })
                    ->where(function ($query) use (
                        $ahora,
                        $segundosActuales
                    ) {

                        $query->where(
                            'fecha',
                            '>',
                            $ahora->toDateString()
                        )
                            ->orWhere(function ($query) use (
                                $ahora,
                                $segundosActuales
                            ) {

                                $query->where(
                                    'fecha',
                                    $ahora->toDateString()
                                )
                                    ->whereRaw(
                                        "horainicio > NUMTODSINTERVAL(?, 'SECOND')",
                                        [
                                            $segundosActuales
                                        ]
                                    );
                            });
                    })
                    ->lockForUpdate()
                    ->first();

                if (!$agenda) {
                    throw new \RuntimeException(
                        'El horario seleccionado ya no está disponible.'
                    );
                }


                /*
            |--------------------------------------------------------------------------
            | COMPROBAR NUEVAMENTE SI EL HORARIO ESTÁ OCUPADO
            |--------------------------------------------------------------------------
            */

                $horarioOcupado = Cita::where(
                    'idhorariodispo',
                    $agenda->idhorariodispo
                )
                    ->where(
                        'estadocita',
                        '!=',
                        'Cancelada'
                    )
                    ->exists();

                if ($horarioOcupado) {

                    throw new \RuntimeException(
                        'El horario seleccionado ya no está disponible.'
                    );
                }


                /*
            |--------------------------------------------------------------------------
            | OBTENER HORA DEL INTERVAL DE ORACLE
            |--------------------------------------------------------------------------
            */

                $hora = $agenda->getRawOriginal(
                    'horainicio'
                );

                preg_match(
                    '/(\d{2}):(\d{2}):(\d{2})/',
                    $hora,
                    $partes
                );

                if (!$partes) {

                    throw new \RuntimeException(
                        'No fue posible determinar la hora del horario seleccionado.'
                    );
                }


                /*
            |--------------------------------------------------------------------------
            | CONSTRUIR FECHA Y HORA DE LA CITA
            |--------------------------------------------------------------------------
            */

                $fechacita = Carbon::create(
                    $agenda->fecha->year,
                    $agenda->fecha->month,
                    $agenda->fecha->day,
                    (int) $partes[1],
                    (int) $partes[2],
                    (int) $partes[3]
                );


                /*
            |--------------------------------------------------------------------------
            | DETALLE
            |--------------------------------------------------------------------------
            |
            | Oracle considera '' como NULL en VARCHAR2.
            | Como CITA.DETALLE es NOT NULL, usamos un valor neutro
            | cuando el paciente no escribe observaciones.
            |
            */

                $detalle = trim(
                    (string) $request->input(
                        'detalle',
                        ''
                    )
                );

                if ($detalle === '') {
                    $detalle = 'Sin observaciones';
                }


                /*
            |--------------------------------------------------------------------------
            | CREAR CITA
            |--------------------------------------------------------------------------
            */

                return Cita::create([
                    'idpaciente' => $paciente->idpaciente,

                    'idtiposervicio' =>
                    $servicio->idtiposervicio,

                    'idhorariodispo' =>
                    $agenda->idhorariodispo,

                    'idservicio' =>
                    $servicio->idservicio,

                    'fechacita' =>
                    $fechacita,

                    'detalle' =>
                    $detalle,

                    'estadocita' =>
                    'Pendiente',
                ]);
            });
        } catch (\RuntimeException $e) {

            return back()
                ->withInput()
                ->with(
                    'error',
                    $e->getMessage()
                );
        }


        /*
    |--------------------------------------------------------------------------
    | REDIRECCIÓN AL LISTADO
    |--------------------------------------------------------------------------
    */

        return redirect()
            ->route(
                'Paciente.citas.show',
                $cita
            )
            ->with(
                'success',
                'Tu cita fue agendada correctamente.'
            );
    }

    public function show(Cita $cita)
    {
        $paciente = Paciente::where('idusuario', Auth::id())
            ->firstOrFail();

        abort_unless(
            (string) $cita->idpaciente === (string) $paciente->idpaciente,
            403
        );

        return view('Paciente.citas.show', compact('cita'));
    }


    public function edit(Cita $cita): View | RedirectResponse
    {
        $paciente = Auth::user()->paciente;

        if (
            !$paciente ||
            (string) $cita->idpaciente !== (string) $paciente->idpaciente
        ) {
            abort(403);
        }

        if ($cita->estadocita !== 'Pendiente') {
            return redirect()
                ->route('Paciente.citas.show', $cita)
                ->with('error', 'Esta cita no puede ser modificada.');
        }

        if (!$this->tienePlazoParaModificar($cita)) {
            return redirect()
                ->route('Paciente.citas.show', $cita)
                ->with(
                    'error',
                    'Solo puedes modificar tu cita hasta 24 horas antes de su inicio.'
                );
        }

        $cita->load([
            'servicio',
            'tipoServicio',
            'agenda',
        ]);

        // Después de tres reprogramaciones, se permite
        // editar el detalle, pero no cambiar el horario.
        $puedeReprogramar = (int) $cita->numreprogramaciones < 3;

        $horarios = collect();

        if ($puedeReprogramar) {
            $limite = now()->addHours(24);

            $segundosLimite =
                ($limite->hour * 3600) +
                ($limite->minute * 60) +
                $limite->second;

            $horarios = Agenda::whereHas(
                'profesional.perfilesServicio',
                function ($query) use ($cita) {
                    $query->where('idservicio', $cita->idservicio)
                        ->where('estadoperfil', 'Activo');
                }
            )
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
            'Paciente.citas.edit',
            compact('cita', 'horarios', 'puedeReprogramar')
        );
    }

    private function tienePlazoParaModificar(Cita $cita): bool
    {
        $inicio = Carbon::parse(
            $cita->fechacita,
            'America/Bogota'
        );

        return now()->lte(
            $inicio->copy()->subHours(24)
        );
    }


    public function update(Request $request, Cita $cita): RedirectResponse
    {
        $datosValidados = $request->validate([
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

        $paciente = Auth::user()->paciente;

        if (
            !$paciente ||
            (string) $cita->idpaciente !== (string) $paciente->idpaciente
        ) {
            abort(403);
        }

        try {
            DB::transaction(function () use (
                $datosValidados,
                $cita,
                $paciente
            ) {
                // Bloquear la cita para evitar modificaciones simultáneas.
                $citaActual = Cita::where('idcita', $cita->idcita)
                    ->lockForUpdate()
                    ->firstOrFail();

                // Comprobar nuevamente el propietario.
                if (
                    (string) $citaActual->idpaciente !==
                    (string) $paciente->idpaciente
                ) {
                    abort(403);
                }

                if ($citaActual->estadocita !== 'Pendiente') {
                    throw new \RuntimeException(
                        'Esta cita ya no puede ser modificada.'
                    );
                }

                // La cita original debe estar al menos a 24 horas.
                if (!$this->tienePlazoParaModificar($citaActual)) {
                    throw new \RuntimeException(
                        'Solo puedes modificar tu cita hasta 24 horas antes de su inicio.'
                    );
                }

                $nuevoIdHorario = (int) $datosValidados['idhorariodispo'];

                $reprogramar = $nuevoIdHorario !==
                    (int) $citaActual->idhorariodispo;

                // Normalizar el detalle para Oracle.
                $detalle = trim(
                    (string) ($datosValidados['detalle'] ?? '')
                );

                if ($detalle === '') {
                    $detalle = 'Sin observaciones';
                }

                // Si no cambia el horario, solo se actualiza el detalle.
                // Esto no consume ninguna reprogramación.
                if (!$reprogramar) {
                    $citaActual->update([
                        'detalle' => $detalle,
                    ]);

                    return;
                }

                // Comprobar el máximo de tres reprogramaciones.
                if ((int) $citaActual->numreprogramaciones >= 3) {
                    throw new \RuntimeException(
                        'Has alcanzado el máximo de 3 reprogramaciones.'
                    );
                }

                // Verificar que el servicio original sigue activo.
                $servicio = Servicio::where(
                    'idservicio',
                    $citaActual->idservicio
                )
                    ->where('estadoservicio', 'Activo')
                    ->whereHas('tipoServicio', function ($query) {
                        $query->where('estadotiposervicio', 'Activo');
                    })
                    ->first();

                if (!$servicio) {
                    throw new \RuntimeException(
                        'El servicio de la cita ya no está disponible.'
                    );
                }

                // Calcular el límite para el nuevo horario.
                $limite = now()->addHours(24);

                $segundosLimite =
                    ($limite->hour * 3600) +
                    ($limite->minute * 60) +
                    $limite->second;

                // Obtener y bloquear el nuevo horario.
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
                    ->lockForUpdate()
                    ->first();

                if (!$agenda) {
                    throw new \RuntimeException(
                        'El horario seleccionado ya no está disponible.'
                    );
                }

                // Verificar que ninguna otra cita activa ocupe el horario.
                $horarioOcupado = Cita::where(
                    'idhorariodispo',
                    $agenda->idhorariodispo
                )
                    ->where('idcita', '!=', $citaActual->idcita)
                    ->where('estadocita', '!=', 'Cancelada')
                    ->exists();

                if ($horarioOcupado) {
                    throw new \RuntimeException(
                        'El horario seleccionado ya fue reservado por otro paciente.'
                    );
                }

                // Obtener la hora del INTERVAL DAY TO SECOND de Oracle.
                $hora = $agenda->getRawOriginal('horainicio');

                if (!preg_match(
                    '/(\d{2}):(\d{2}):(\d{2})/',
                    (string) $hora,
                    $partes
                )) {
                    throw new \RuntimeException(
                        'No fue posible determinar la hora del horario seleccionado.'
                    );
                }

                // Construir la nueva fecha y hora de la cita.
                $fechacita = Carbon::create(
                    $agenda->fecha->year,
                    $agenda->fecha->month,
                    $agenda->fecha->day,
                    (int) $partes[1],
                    (int) $partes[2],
                    (int) $partes[3],
                    'America/Bogota'
                );

                // Validación final de las 24 horas.
                if ($fechacita->lt(now()->addHours(24))) {
                    throw new \RuntimeException(
                        'El nuevo horario debe estar al menos a 24 horas de distancia.'
                    );
                }

                // Actualizar la cita y aumentar el contador
                // dentro de la misma transacción.
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
            ->route('Paciente.citas.show', $cita)
            ->with(
                'success',
                'La cita fue actualizada correctamente.'
            );
    }

    public function cancelar(
        Cita $cita,
        GestionCitasService $gestionCitas
    ): RedirectResponse {
        $paciente = Auth::user()->paciente;

        if (
            ! $paciente ||
            (string) $cita->idpaciente !== (string) $paciente->idpaciente
        ) {
            abort(403);
        }

        try {
            $gestionCitas->cancelar($cita, (int) $paciente->idpaciente);
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with(
            'success',
            'La cita fue cancelada correctamente.'
        );
    }
}
