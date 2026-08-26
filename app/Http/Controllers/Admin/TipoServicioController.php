<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TipoServicio;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TipoServicioController extends Controller
{
    public function index(): View
    {
        $tiposServicio = TipoServicio::orderBy('nombre')->get();

        return view(
            'admin.tiposervicios.index',
            compact('tiposServicio')
        );
    }

    public function create(): View
    {
        return view('admin.tiposervicios.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:80',
            ],

            'descripcion' => [
                'nullable',
                'string',
            ],
        ]);

        $validated['estadotiposervicio'] = 'Activo';

        TipoServicio::create($validated);

        return redirect()
            ->route('admin.tiposervicios.index')
            ->with(
                'success',
                'Tipo de servicio creado correctamente.'
            );
    }

    public function edit(TipoServicio $tipoServicio): View
    {
        return view(
            'admin.tiposervicios.edit',
            compact('tipoServicio')
        );
    }

    public function update(
        Request $request,
        TipoServicio $tipoServicio
    ): RedirectResponse {
        $validated = $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:80',
            ],

            'descripcion' => [
                'nullable',
                'string',
            ],
        ]);

        $tipoServicio->update($validated);

        return redirect()
            ->route('admin.tiposervicios.index')
            ->with(
                'success',
                'Tipo de servicio actualizado correctamente.'
            );
    }

    public function toggleEstado(
        TipoServicio $tipoServicio
    ): RedirectResponse {
        $nuevoEstado = $tipoServicio->estadotiposervicio === 'Activo'
            ? 'Inactivo'
            : 'Activo';

        $tipoServicio->update([
            'estadotiposervicio' => $nuevoEstado,
        ]);

        return redirect()
            ->route('admin.tiposervicios.index')
            ->with(
                'success',
                'Estado del tipo de servicio actualizado correctamente.'
            );
    }
}
