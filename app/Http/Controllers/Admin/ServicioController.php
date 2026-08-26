<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Servicio;
use App\Models\TipoServicio;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ServicioController extends Controller
{
    public function index(): View
    {
        $servicios = Servicio::with('tipoServicio')
            ->orderBy('NOMBRE')
            ->get();

        return view(
            'admin.servicios.index',
            compact('servicios')
        );
    }

    public function create(): View
    {
        $tiposServicio = TipoServicio::where(
            'ESTADOTIPOSERVICIO',
            'Activo'
        )
            ->orderBy('NOMBRE')
            ->get();

        return view(
            'admin.servicios.create',
            compact('tiposServicio')
        );
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'idtiposervicio' => [
                'required',
                'exists:TIPOSERVICIO,IDTIPOSERVICIO',
            ],

            'nombre' => [
                'required',
                'string',
                'max:80',
            ],

            'precio' => [
                'required',
                'numeric',
                'min:0',
            ],

            'prerequisitos' => [
                'nullable',
                'string',
            ],
        ]);

        $tipoServicio = TipoServicio::find(
            $validated['idtiposervicio']
        );

        if (
            !$tipoServicio ||
            $tipoServicio->estadotiposervicio !== 'Activo'
        ) {
            return back()
                ->withErrors([
                    'idtiposervicio' =>
                    'El tipo de servicio seleccionado no está activo.'
                ])
                ->withInput();
        }

        $validated['estadoservicio'] = 'Activo';

        Servicio::create($validated);

        return redirect()
            ->route('admin.servicios.index')
            ->with(
                'success',
                'Servicio creado correctamente.'
            );
    }

    public function edit(Servicio $servicio): View
    {
        $tiposServicio = TipoServicio::where(
            'ESTADOTIPOSERVICIO',
            'Activo'
        )
            ->orderBy('NOMBRE')
            ->get();

        return view(
            'admin.servicios.edit',
            compact(
                'servicio',
                'tiposServicio'
            )
        );
    }

    public function update(
        Request $request,
        Servicio $servicio
    ): RedirectResponse {
        $validated = $request->validate([
            'idtiposervicio' => [
                'required',
                'exists:TIPOSERVICIO,IDTIPOSERVICIO',
            ],

            'nombre' => [
                'required',
                'string',
                'max:80',
            ],

            'precio' => [
                'required',
                'numeric',
                'min:0',
            ],

            'prerequisitos' => [
                'nullable',
                'string',
            ],
        ]);

        $tipoServicio = TipoServicio::find(
            $validated['idtiposervicio']
        );

        if (
            !$tipoServicio ||
            $tipoServicio->estadotiposervicio !== 'Activo'
        ) {
            return back()
                ->withErrors([
                    'idtiposervicio' =>
                    'El tipo de servicio seleccionado no está activo.'
                ])
                ->withInput();
        }

        $servicio->update($validated);

        return redirect()
            ->route('admin.servicios.index')
            ->with(
                'success',
                'Servicio actualizado correctamente.'
            );
    }

    public function toggleEstado(
        Servicio $servicio
    ): RedirectResponse {
        $nuevoEstado = $servicio->estadoservicio === 'Activo'
            ? 'Inactivo'
            : 'Activo';

        $servicio->update([
            'estadoservicio' => $nuevoEstado,
        ]);

        return redirect()
            ->route('admin.servicios.index')
            ->with(
                'success',
                'Estado del servicio actualizado correctamente.'
            );
    }
}
