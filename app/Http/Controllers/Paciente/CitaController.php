<?php

namespace App\Http\Controllers\Paciente;

use App\Http\Controllers\Controller;
use App\Models\Agenda;
use App\Models\Cita;
use App\Models\Servicio;
use App\Models\TipoServicio;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

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
        $ahora = now();

        $horarios = Agenda::whereHas(
            'profesional.perfilesServicio',
            function ($query) use ($idservicio) {

                $query->where('idservicio', $idservicio)
                    ->where('estadoperfil', 'Activo');
            }
        )
            ->whereDoesntHave('cita', function ($query) {
                $query->where('estadocita', '!=', 'Cancelada');
            })
            ->where(function ($query) use ($ahora) {

                $query->where(
                    'fecha',
                    '>',
                    $ahora->toDateString()
                )

                    ->orWhere(function ($query) use ($ahora) {

                        $query->where(
                            'fecha',
                            $ahora->toDateString()
                        )
                            ->whereRaw(
                                "horainicio > NUMTODSINTERVAL(?, 'SECOND')",
                                [
                                    $ahora->diffInSeconds(
                                        $ahora->copy()->startOfDay()
                                    )
                                ]
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

        $servicio = Servicio::where(
            'idservicio',
            $request->idservicio
        )
            ->where(
                'estadoservicio',
                'Activo'
            )
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
                    ->whereHas(
                        'profesional.perfilesServicio',
                        function ($query) use ($request) {

                            $query->where(
                                'idservicio',
                                $request->idservicio
                            )
                                ->where(
                                    'estadoperfil',
                                    'Activo'
                                );
                        }
                    )
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

    public function edit(Cita $cita): View | RedirectResponse
    {
        $paciente = Auth::user()->paciente;

        if (!$paciente || $cita->idpaciente != $paciente->idpaciente) {
            abort(403);
        }

        if ($cita->estadocita !== 'Pendiente') {
            return redirect()
                ->route('Paciente.citas.show', $cita)
                ->with(
                    'error',
                    'Esta cita no puede ser modificada.'
                );
        }

        $cita->load([
            'servicio',
            'tipoServicio',
            'agenda',
        ]);

        $ahora = now();

        $segundosActuales =
            ($ahora->hour * 3600) +
            ($ahora->minute * 60) +
            $ahora->second;

        $horarios = Agenda::whereHas(
            'profesional.perfilesServicio',
            function ($query) use ($cita) {

                $query->where(
                    'idservicio',
                    $cita->idservicio
                )
                    ->where(
                        'estadoperfil',
                        'Activo'
                    );
            }
        )
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
            ->where(function ($query) use ($cita) {

                $query->whereDoesntHave('cita', function ($query) {

                    $query->where(
                        'estadocita',
                        '!=',
                        'Cancelada'
                    );
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

        return view(
            'Paciente.citas.edit',
            compact(
                'cita',
                'horarios'
            )
        );
    }
    public function update(
        Request $request,
        Cita $cita
    ): RedirectResponse {
        $request->validate([
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
            $cita->idpaciente != $paciente->idpaciente
        ) {
            abort(403);
        }

        if ($cita->estadocita !== 'Pendiente') {

            return back()
                ->with(
                    'error',
                    'Esta cita no puede ser modificada.'
                );
        }

        $ahora = now();

        $segundosActuales =
            ($ahora->hour * 3600) +
            ($ahora->minute * 60) +
            $ahora->second;

        try {

            DB::transaction(function () use (
                $request,
                $cita,
                $ahora,
                $segundosActuales
            ) {

                /*
            |--------------------------------------------------------------------------
            | SERVICIO ORIGINAL
            |--------------------------------------------------------------------------
            */

                $servicio = Servicio::where(
                    'idservicio',
                    $cita->idservicio
                )
                    ->where(
                        'estadoservicio',
                        'Activo'
                    )
                    ->first();

                if (!$servicio) {

                    throw new \RuntimeException(
                        'El servicio de la cita ya no está disponible.'
                    );
                }


                /*
            |--------------------------------------------------------------------------
            | OBTENER NUEVO HORARIO
            |--------------------------------------------------------------------------
            */

                $agenda = Agenda::where(
                    'idhorariodispo',
                    $request->idhorariodispo
                )
                    ->whereHas(
                        'profesional.perfilesServicio',
                        function ($query) use ($cita) {

                            $query->where(
                                'idservicio',
                                $cita->idservicio
                            )
                                ->where(
                                    'estadoperfil',
                                    'Activo'
                                );
                        }
                    )
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
            | COMPROBAR SI EL NUEVO HORARIO YA ESTÁ OCUPADO
            |--------------------------------------------------------------------------
            */

                $horarioOcupado = Cita::where(
                    'idhorariodispo',
                    $agenda->idhorariodispo
                )
                    ->where(
                        'idcita',
                        '!=',
                        $cita->idcita
                    )
                    ->where(
                        'estadocita',
                        '!=',
                        'Cancelada'
                    )
                    ->exists();

                if ($horarioOcupado) {

                    throw new \RuntimeException(
                        'El horario seleccionado ya fue reservado por otro paciente.'
                    );
                }


                /*
            |--------------------------------------------------------------------------
            | OBTENER HORA
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
            | CONSTRUIR FECHA
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
            | ACTUALIZAR CITA
            |--------------------------------------------------------------------------
            */

                $cita->update([
                    'idhorariodispo' => $agenda->idhorariodispo,
                    'fechacita' => $fechacita,
                    'detalle' => $detalle,
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


        return redirect()
            ->route(
                'Paciente.citas.show',
                $cita
            )
            ->with(
                'success',
                'La cita fue actualizada correctamente.'
            );
    }

    public function cancelar(Cita $cita): RedirectResponse
    {
        $paciente = Auth::user()->paciente;

        if (!$paciente || $cita->idpaciente != $paciente->idpaciente) {
            abort(403);
        }

        if ($cita->estadocita !== 'Pendiente') {
            return back()
                ->with(
                    'error',
                    'Esta cita no puede ser cancelada.'
                );
        }

        $cita->update([
            'estadocita' => 'Cancelada',
        ]);

        return redirect()
            ->route('Paciente.citas.index')
            ->with(
                'success',
                'La cita fue cancelada correctamente.'
            );
    }
    public function show(Cita $cita): View
    {
        $paciente = Auth::user()->paciente;

        if (!$paciente || $cita->idpaciente != $paciente->idpaciente) {
            abort(403);
        }

        $cita->load([
            'servicio',
            'tipoServicio',
            'agenda.profesional.user',
            'agenda.sede',
        ]);

        return view(
            'Paciente.citas.show',
            compact('cita')
        );
    }
}
