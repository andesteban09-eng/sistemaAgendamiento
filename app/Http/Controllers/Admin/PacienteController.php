<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Paciente;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PacienteController extends Controller
{
    public function index()
    {
        $pacientes = Paciente::with('user')
            ->orderBy('idpaciente', 'asc')
            ->get();

        return view('admin.pacientes.index', compact('pacientes'));
    }

    public function create()
    {
        return view('admin.pacientes.create');
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',

            'tipodoc' => 'required|string|max:40',
            'numdoc' => 'required|string|max:45|unique:paciente,numdoc',
            'telefono' => 'required|string|max:20',
            'direccion' => 'required|string|max:150',
            'ciudad' => 'required|string|max:60',
        ]);

        DB::transaction(function () use ($datos) {

            $usuario = User::create([
                'name' => $datos['name'],
                'last_name' => $datos['last_name'],
                'email' => $datos['email'],
                'password' => Hash::make($datos['password']),
                'rol' => 'paciente',
                'estado' => 'activo',
            ]);

            Paciente::create([
                'tipodoc' => $datos['tipodoc'],
                'numdoc' => $datos['numdoc'],
                'telefono' => $datos['telefono'],
                'direccion' => $datos['direccion'],
                'ciudad' => $datos['ciudad'],
                'fecharegistro' => now(),
                'estadopaciente' => 'Activo',
                'idusuario' => $usuario->id,
            ]);
        });

        return redirect()
            ->route('admin.pacientes.index')
            ->with('success', 'Paciente registrado correctamente.');
    }

    public function show(Paciente $paciente)
    {
        $paciente->load('user');

        return view('admin.pacientes.show', compact('paciente'));
    }

    public function edit(Paciente $paciente)
    {
        $paciente->load('user');

        return view('admin.pacientes.edit', compact('paciente'));
    }
    public function update(Request $request, Paciente $paciente)
    {
        $datos = $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $paciente->idusuario,

            'tipodoc' => 'required|string|max:40',
            'numdoc' => 'required|string|max:45|unique:paciente,numdoc,' . $paciente->idpaciente . ',idpaciente',
            'telefono' => 'required|string|max:20',
            'direccion' => 'required|string|max:150',
            'ciudad' => 'required|string|max:60',
            'estadopaciente' => 'required|in:Activo,Inactivo',
        ]);

        DB::transaction(function () use ($datos, $paciente) {

            $paciente->update([
                'tipodoc' => $datos['tipodoc'],
                'numdoc' => $datos['numdoc'],
                'telefono' => $datos['telefono'],
                'direccion' => $datos['direccion'],
                'ciudad' => $datos['ciudad'],
                'estadopaciente' => $datos['estadopaciente'],
            ]);

            $paciente->user->update([
                'name' => $datos['name'],
                'last_name' => $datos['last_name'],
                'email' => $datos['email'],
            ]);
        });

        return redirect()
            ->route('admin.pacientes.index')
            ->with('success', 'Paciente actualizado correctamente.');
    }

    public function destroy(Paciente $paciente)
    {
        DB::transaction(function () use ($paciente) {

            $paciente->update([
                'estadopaciente' => 'Inactivo',
            ]);

            $paciente->user->update([
                'estado' => 'inactivo',
            ]);
        });

        return redirect()
            ->route('admin.pacientes.index')
            ->with('success', 'Paciente desactivado correctamente.');
    }

    public function toggleEstado(Paciente $paciente)
    {
        DB::transaction(function () use ($paciente) {

            if ($paciente->estadopaciente === 'Activo') {

                $paciente->update([
                    'estadopaciente' => 'Inactivo',
                ]);

                $paciente->user->update([
                    'estado' => 'inactivo',
                ]);
            } else {

                $paciente->update([
                    'estadopaciente' => 'Activo',
                ]);

                $paciente->user->update([
                    'estado' => 'activo',
                ]);
            }
        });

        return redirect()
            ->route('admin.pacientes.index')
            ->with(
                'success',
                $paciente->estadopaciente === 'Activo'
                    ? 'Paciente activado correctamente.'
                    : 'Paciente desactivado correctamente.'
            );
    }
}
