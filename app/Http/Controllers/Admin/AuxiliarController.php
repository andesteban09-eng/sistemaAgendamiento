<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuxiliarController extends Controller
{
    public function index()
    {
        $auxiliares = User::where('rol', 'auxiliar')
            ->orderBy('id', 'desc')
            ->get();

        return view('admin.auxiliares.index', compact('auxiliares'));
    }

    public function create()
    {
        return view('admin.auxiliares.create');
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
        ]);

        User::create([
            'name' => $datos['name'],
            'last_name' => $datos['last_name'],
            'email' => $datos['email'],
            'password' => Hash::make($datos['password']),
            'rol' => 'auxiliar',
            'estado' => 'activo',
        ]);

        return redirect()
            ->route('admin.auxiliares.index')
            ->with('success', 'Auxiliar registrado correctamente.');
    }

    public function show(User $auxiliar)
    {
        return view('admin.auxiliares.show', compact('auxiliar'));
    }

    public function edit(User $auxiliar)
    {
        return view('admin.auxiliares.edit', compact('auxiliar'));
    }

    public function update(Request $request, User $auxiliar)
    {
        $datos = $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $auxiliar->id,
            'password' => 'nullable|string|min:8',
        ]);

        $auxiliar->update([
            'name' => $datos['name'],
            'last_name' => $datos['last_name'],
            'email' => $datos['email'],
        ]);

        if (!empty($datos['password'])) {
            $auxiliar->update([
                'password' => Hash::make($datos['password']),
            ]);
        }

        return redirect()
            ->route('admin.auxiliares.index')
            ->with('success', 'Auxiliar actualizado correctamente.');
    }

    public function destroy(User $auxiliar)
    {
        $auxiliar->delete();

        return redirect()
            ->route('admin.auxiliares.index')
            ->with('success', 'Auxiliar eliminado correctamente.');
    }

    public function toggleEstado(User $auxiliar)
    {
        $auxiliar->estado = $auxiliar->estado === 'activo'
            ? 'inactivo'
            : 'activo';

        $auxiliar->save();

        return redirect()
            ->route('admin.auxiliares.index')
            ->with('success', 'Estado del auxiliar actualizado correctamente.');
    }
}
