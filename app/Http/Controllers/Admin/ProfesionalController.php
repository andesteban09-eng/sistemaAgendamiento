<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProfesionalSalud;
use App\Models\User;
use App\Models\Servicio;
use App\Models\PerfilServicio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ProfesionalController extends Controller
{
    public function index()
    {
        $profesionales = ProfesionalSalud::with('user')
            ->orderBy('idprofesionalsalud', 'desc')
            ->get();

        return view('admin.profesionales.index', compact('profesionales'));
    }
    public function create()
    {
        $servicios = Servicio::where(
            'estadoservicio',
            'Activo'
        )
            ->with('tipoServicio')
            ->orderBy('nombre')
            ->get();

        return view(
            'admin.profesionales.create',
            compact('servicios')
        );
    }


    public function store(Request $request)
    {
        $datos = $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',

            'email' => 'required|email|max:255|unique:users,email',

            'password' => 'required|string|min:8',

            'tipodoc' => 'required|string|max:40',

            'numdoc' => 'required|string|max:45|unique:profesionalsalud,numdoc',

            'telefono' => 'required|string|max:20',

            'servicios' => 'nullable|array',

            'servicios.*' => [
                'integer',
                'exists:SERVICIO,idservicio',
            ],
        ]);

        DB::transaction(function () use ($datos) {

            /*
        |--------------------------------------------------------------------------
        | Crear usuario
        |--------------------------------------------------------------------------
        */

            $usuario = User::create([
                'name' => $datos['name'],
                'last_name' => $datos['last_name'],
                'email' => $datos['email'],
                'password' => Hash::make($datos['password']),
                'rol' => 'profesional',
                'estado' => 'activo',
            ]);


            /*
        |--------------------------------------------------------------------------
        | Crear profesional
        |--------------------------------------------------------------------------
        */

            $profesional = ProfesionalSalud::create([
                'tipodoc' => $datos['tipodoc'],
                'numdoc' => $datos['numdoc'],
                'telefono' => $datos['telefono'],
                'estadoprofesionalsalud' => 'Activo',
                'idusuario' => $usuario->id,
            ]);


            /*
        |--------------------------------------------------------------------------
        | Asignar servicios
        |--------------------------------------------------------------------------
        */

            $serviciosSeleccionados = $datos['servicios'] ?? [];

            foreach ($serviciosSeleccionados as $idServicio) {

                $servicio = Servicio::find($idServicio);

                if (!$servicio) {
                    continue;
                }

                PerfilServicio::create([
                    'IDPROFESIONALSALUD' =>
                    $profesional->idprofesionalsalud,

                    'IDSERVICIO' =>
                    $servicio->idservicio,

                    'IDTIPOSERVICIO' =>
                    $servicio->idtiposervicio,

                    'FECHAASIGNACION' =>
                    now(),

                    'ESTADOPERFIL' =>
                    'Activo',
                ]);
            }
        });

        return redirect()
            ->route('admin.profesionales.index')
            ->with(
                'success',
                'Profesional registrado correctamente.'
            );
    }


    public function show(ProfesionalSalud $profesional)
    {
        $profesional->load(['user', 'perfilesServicio.servicio.tipoServicio',]);
        return view('admin.profesionales.show', compact('profesional'));
    }

    public function edit(ProfesionalSalud $profesional)
    {
        $profesional->load('user');

        $servicios = Servicio::where(
            'estadoservicio',
            'Activo'
        )
            ->with('tipoServicio')
            ->orderBy('nombre')
            ->get();

        $serviciosAsignados = $profesional->perfilesServicio()
            ->where('ESTADOPERFIL', 'Activo')
            ->pluck('IDSERVICIO')
            ->toArray();

        return view(
            'admin.profesionales.edit',
            compact(
                'profesional',
                'servicios',
                'serviciosAsignados'
            )
        );
    }

    public function update(Request $request, ProfesionalSalud $profesional)
    {
        $datos = $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',

            'email' => 'required|email|max:255|unique:users,email,' . $profesional->idusuario,

            'password' => 'nullable|string|min:8',

            'tipodoc' => 'required|string|max:40',

            'numdoc' => 'required|string|max:45|unique:profesionalsalud,numdoc,' .
                $profesional->idprofesionalsalud . ',idprofesionalsalud',

            'telefono' => 'required|string|max:20',

            'estadoprofesionalsalud' => 'required|in:Activo,Inactivo',

            'servicios' => 'nullable|array',

            'servicios.*' => [
                'integer',
                'exists:SERVICIO,idservicio',
            ],
        ]);

        DB::transaction(function () use ($datos, $profesional) {

            /*
        |--------------------------------------------------------------------------
        | Actualizar usuario
        |--------------------------------------------------------------------------
        */

            $usuario = $profesional->user;

            $usuario->update([
                'name' => $datos['name'],
                'last_name' => $datos['last_name'],
                'email' => $datos['email'],
            ]);

            if (!empty($datos['password'])) {
                $usuario->update([
                    'password' => Hash::make($datos['password']),
                ]);
            }

            /*
        |--------------------------------------------------------------------------
        | Actualizar profesional
        |--------------------------------------------------------------------------
        */

            $profesional->update([
                'tipodoc' => $datos['tipodoc'],
                'numdoc' => $datos['numdoc'],
                'telefono' => $datos['telefono'],
                'estadoprofesionalsalud' =>
                $datos['estadoprofesionalsalud'],
            ]);

            /*
        |--------------------------------------------------------------------------
        | Servicios asignados
        |--------------------------------------------------------------------------
        */

            $serviciosSeleccionados = $datos['servicios'] ?? [];

            /*
        |--------------------------------------------------------------------------
        | Desactivar asignaciones que ya no fueron seleccionadas
        |--------------------------------------------------------------------------
        */

            PerfilServicio::where(
                'IDPROFESIONALSALUD',
                $profesional->idprofesionalsalud
            )
                ->whereNotIn(
                    'IDSERVICIO',
                    $serviciosSeleccionados
                )
                ->update([
                    'ESTADOPERFIL' => 'Inactivo',
                ]);

            /*
        |--------------------------------------------------------------------------
        | Crear o reactivar asignaciones seleccionadas
        |--------------------------------------------------------------------------
        */

            foreach ($serviciosSeleccionados as $idServicio) {

                $servicio = Servicio::find($idServicio);

                if (!$servicio) {
                    continue;
                }

                PerfilServicio::updateOrCreate(
                    [
                        'IDPROFESIONALSALUD' =>
                        $profesional->idprofesionalsalud,

                        'IDSERVICIO' =>
                        $servicio->idservicio,
                    ],
                    [
                        'IDTIPOSERVICIO' =>
                        $servicio->idtiposervicio,

                        'FECHAASIGNACION' =>
                        now(),

                        'ESTADOPERFIL' =>
                        'Activo',
                    ]
                );
            }
        });

        return redirect()
            ->route('admin.profesionales.index')
            ->with(
                'success',
                'Profesional actualizado correctamente.'
            );
    }


    public function destroy(ProfesionalSalud $profesional)
    {
        DB::transaction(function () use ($profesional) {

            $profesional->update([
                'estadoprofesionalsalud' => 'Inactivo',
            ]);

            $profesional->user->update([
                'estado' => 'inactivo',
            ]);
        });

        return redirect()
            ->route('admin.profesionales.index')
            ->with('success', 'Profesional desactivado correctamente.');
    }

    public function toggleEstado(ProfesionalSalud $profesional)
    {
        DB::transaction(function () use ($profesional) {

            $nuevoEstado = $profesional->estadoprofesionalsalud === 'Activo'
                ? 'Inactivo'
                : 'Activo';

            $profesional->update([
                'estadoprofesionalsalud' => $nuevoEstado,
            ]);

            $profesional->user->update([
                'estado' => strtolower($nuevoEstado),
            ]);
        });

        return redirect()
            ->route('admin.profesionales.index')
            ->with(
                'success',
                'Estado del profesional actualizado correctamente.'
            );
    }
}
