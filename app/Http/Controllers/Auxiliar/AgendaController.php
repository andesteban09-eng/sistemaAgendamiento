<?php

namespace App\Http\Controllers\Auxiliar;

use App\Http\Controllers\Controller;
use App\Models\Agenda;
use App\Models\ProfesionalSalud;
use App\Models\Sede;
use App\Models\Cita;
use App\Models\PerfilServicio;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AgendaController extends Controller
{
    public function index(): View
    {
        $agendas = Agenda::with([
            'profesional.user',
            'sede'
        ])
            ->orderBy('FECHA')
            ->orderBy('HORAINICIO')
            ->get();

        return view('auxiliar.agenda.index', compact('agendas'));
    }

    public function create(): View
    {
        $profesionales = ProfesionalSalud::with([
            'user',
            'perfilesServicio.servicio',
            'perfilesServicio.tipoServicio',
        ])
            ->where('estadoprofesionalsalud', 'Activo')
            ->whereHas('perfilesServicio', function ($query) {
                $query->where('estadoperfil', 'Activo')
                    ->whereHas('servicio', function ($query) {
                        $query->where('estadoservicio', 'Activo');
                    });
            })
            ->get();

        $sedes = Sede::where('ESTADOSEDE', 'Activo')
            ->orderBy('NOMBRE')
            ->get();

        return view('auxiliar.agenda.create', compact(
            'profesionales',
            'sedes'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'IDPROFESIONALSALUD' => [
                'required',
                'exists:PROFESIONALSALUD,IDPROFESIONALSALUD',
            ],

            'IDSEDE' => [
                'required',
                'exists:SEDE,IDSEDE',
            ],

            'FECHA' => [
                'required',
                'date',
                'after_or_equal:today',
            ],

            'HORAINICIO' => [
                'required',
                'date_format:H:i',
            ],

            'CONSULTORIO' => [
                'required',
                'string',
                'max:45',
            ],
        ]);

        /*
     * Verificar profesional
     */

        $profesional = ProfesionalSalud::find(
            $validated['IDPROFESIONALSALUD']
        );

        if (!$profesional || $profesional->estadoprofesionalsalud !== 'Activo') {
            return back()
                ->withErrors([
                    'IDPROFESIONALSALUD' =>
                    'El profesional seleccionado no está activo.'
                ])
                ->withInput();
        }

        /*
     * Verificar que el profesional tenga
     * al menos un servicio activo asignado
     */

        $tieneServicio = $profesional->perfilesServicio()
            ->where('estadoperfil', 'Activo')
            ->whereHas('servicio', function ($query) {
                $query->where('estadoservicio', 'Activo');
            })
            ->exists();

        if (!$tieneServicio) {
            return back()
                ->withErrors([
                    'IDPROFESIONALSALUD' =>
                    'El profesional seleccionado no tiene servicios activos asignados.'
                ])
                ->withInput();
        }

        /*
     * Verificar sede
     */

        $sede = Sede::find($validated['IDSEDE']);

        if (!$sede || $sede->estadosede !== 'Activo') {
            return back()
                ->withErrors([
                    'IDSEDE' =>
                    'La sede seleccionada no está activa.'
                ])
                ->withInput();
        }

        /*
     * Evitar duplicidad de horario
     */

        $hora = $validated['HORAINICIO'];

        $minutos = ((int) substr($hora, 0, 2) * 60)
            + (int) substr($hora, 3, 2);

        $existe = Agenda::where(
            'IDPROFESIONALSALUD',
            $validated['IDPROFESIONALSALUD']
        )
            ->whereDate('FECHA', $validated['FECHA'])
            ->whereRaw(
                "HORAINICIO = NUMTODSINTERVAL(?, 'MINUTE')",
                [$minutos]
            )
            ->exists();

        if ($existe) {
            return back()
                ->withErrors([
                    'HORAINICIO' =>
                    'El profesional ya tiene una disponibilidad registrada para esa fecha y hora.'
                ])
                ->withInput();
        }

        /*
     * Preparar hora para Oracle
     */

        $validated['HORAINICIO'] =
            '0 ' . $validated['HORAINICIO'] . ':00';

        /*
     * Preparar datos para Eloquent
     */

        $datos = [
            'idprofesionalsalud' => $validated['IDPROFESIONALSALUD'],
            'idsede' => $validated['IDSEDE'],
            'fecha' => $validated['FECHA'],
            'horainicio' => $validated['HORAINICIO'],
            'consultorio' => $validated['CONSULTORIO'],
        ];

        /*
     * Registrar disponibilidad
     */

        Agenda::create($datos);

        return redirect()
            ->route('auxiliar.agenda.index')
            ->with(
                'success',
                'Disponibilidad registrada correctamente.'
            );
    }

    public function edit(Agenda $agenda): View
    {
        $profesionales = ProfesionalSalud::with([
            'user',
            'perfilesServicio.servicio',
            'perfilesServicio.tipoServicio',
        ])
            ->where('estadoprofesionalsalud', 'Activo')
            ->whereHas('perfilesServicio', function ($query) {
                $query->where('estadoperfil', 'Activo')
                    ->whereHas('servicio', function ($query) {
                        $query->where('estadoservicio', 'Activo');
                    });
            })
            ->get();

        $sedes = Sede::where('ESTADOSEDE', 'Activo')
            ->orderBy('NOMBRE')
            ->get();

        return view('auxiliar.agenda.edit', compact(
            'agenda',
            'profesionales',
            'sedes'
        ));
    }

    public function update(Request $request, Agenda $agenda): RedirectResponse
    {
        $validated = $request->validate([
            'IDPROFESIONALSALUD' => [
                'required',
                'exists:PROFESIONALSALUD,IDPROFESIONALSALUD',
            ],

            'IDSEDE' => [
                'required',
                'exists:SEDE,IDSEDE',
            ],

            'FECHA' => [
                'required',
                'date',
                'after_or_equal:today',
            ],

            'HORAINICIO' => [
                'required',
                'date_format:H:i',
            ],

            'CONSULTORIO' => [
                'required',
                'string',
                'max:45',
            ],
        ]);

        /*
     * Verificar profesional
     */

        $profesional = ProfesionalSalud::find(
            $validated['IDPROFESIONALSALUD']
        );

        if (!$profesional || $profesional->estadoprofesionalsalud !== 'Activo') {
            return back()
                ->withErrors([
                    'IDPROFESIONALSALUD' =>
                    'El profesional seleccionado no está activo.'
                ])
                ->withInput();
        }

        /*
     * Verificar que el profesional tenga
     * al menos un servicio activo asignado
     */

        $tieneServicio = $profesional->perfilesServicio()
            ->where('estadoperfil', 'Activo')
            ->whereHas('servicio', function ($query) {
                $query->where('estadoservicio', 'Activo');
            })
            ->exists();

        if (!$tieneServicio) {
            return back()
                ->withErrors([
                    'IDPROFESIONALSALUD' =>
                    'El profesional seleccionado no tiene servicios activos asignados.'
                ])
                ->withInput();
        }

        /*
     * Verificar sede
     */

        $sede = Sede::find($validated['IDSEDE']);

        if (!$sede || $sede->estadosede !== 'Activo') {
            return back()
                ->withErrors([
                    'IDSEDE' =>
                    'La sede seleccionada no está activa.'
                ])
                ->withInput();
        }

        /*
     * Evitar duplicidad de horario
     */

        $hora = $validated['HORAINICIO'];

        $minutos = ((int) substr($hora, 0, 2) * 60)
            + (int) substr($hora, 3, 2);

        $existe = Agenda::where(
            'IDPROFESIONALSALUD',
            $validated['IDPROFESIONALSALUD']
        )
            ->whereDate('FECHA', $validated['FECHA'])
            ->whereRaw(
                "HORAINICIO = NUMTODSINTERVAL(?, 'MINUTE')",
                [$minutos]
            )
            ->where(
                'IDHORARIODISPO',
                '!=',
                $agenda->IDHORARIODISPO
            )
            ->exists();

        if ($existe) {
            return back()
                ->withErrors([
                    'HORAINICIO' =>
                    'El profesional ya tiene una disponibilidad registrada para esa fecha y hora.'
                ])
                ->withInput();
        }

        /*
     * Preparar hora para Oracle
     */

        $validated['HORAINICIO'] =
            '0 ' . $validated['HORAINICIO'] . ':00';

        /*
     * Preparar datos para Eloquent
     */

        $datos = [
            'idprofesionalsalud' => $validated['IDPROFESIONALSALUD'],
            'idsede' => $validated['IDSEDE'],
            'fecha' => $validated['FECHA'],
            'horainicio' => $validated['HORAINICIO'],
            'consultorio' => $validated['CONSULTORIO'],
        ];

        /*
     * Actualizar disponibilidad
     */

        $agenda->update($datos);

        return redirect()
            ->route('auxiliar.agenda.index')
            ->with(
                'success',
                'Disponibilidad actualizada correctamente.'
            );
    }
    public function destroy(Agenda $agenda): RedirectResponse
    {
        $tieneCita = Cita::where(
            'IDHORARIODISPO',
            $agenda->IDHORARIODISPO
        )->exists();

        if ($tieneCita) {
            return redirect()
                ->route('auxiliar.agenda.index')
                ->with('error', 'No se puede eliminar esta disponibilidad porque ya tiene una cita asociada.');
        }

        $agenda->delete();

        return redirect()
            ->route('auxiliar.agenda.index')
            ->with('success', 'Disponibilidad eliminada correctamente.');
    }
}
