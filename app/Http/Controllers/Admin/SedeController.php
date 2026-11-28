<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sede;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SedeController extends Controller
{
    public function index(Request $request): View
    {
        $filtros = $request->validate([
            'buscar' => ['nullable', 'string', 'max:100'],
            'estado' => ['nullable', 'in:Activo,Inactivo'],
        ]);

        $query = Sede::query();

        $buscar = trim($filtros['buscar'] ?? '');
        $estado = $filtros['estado'] ?? null;

        if ($buscar !== '') {
            $query->where(function ($query) use ($buscar) {
                $query->where('nombre', 'like', "%{$buscar}%")
                    ->orWhere('ciudad', 'like', "%{$buscar}%")
                    ->orWhere('direccion', 'like', "%{$buscar}%");
            });
        }

        if ($estado !== null) {
            $query->where('estadosede', $estado);
        }

        $sedes = $query
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        $totalSedes = Sede::count();

        $sedesActivas = Sede::where(
            'estadosede',
            Sede::ESTADO_ACTIVO
        )->count();

        $sedesInactivas = Sede::where(
            'estadosede',
            Sede::ESTADO_INACTIVO
        )->count();

        return view('admin.sedes.index', compact(
            'sedes',
            'totalSedes',
            'sedesActivas',
            'sedesInactivas'
        ));
    }

    public function show(Sede $sede): View
    {
        $sede->loadCount('agendas');

        return view('admin.sedes.show', compact('sede'));
    }

    public function create(): View
    {
        return view('admin.sedes.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validarDatos($request);

        Sede::create([
            ...$validated,
            'estadosede' => Sede::ESTADO_ACTIVO,
        ]);

        return redirect()
            ->route('admin.sedes.index')
            ->with('success', 'Sede registrada correctamente.');
    }

    public function edit(Sede $sede): View
    {
        return view('admin.sedes.edit', compact('sede'));
    }

    public function update(
        Request $request,
        Sede $sede
    ): RedirectResponse {
        $validated = $this->validarDatos($request);

        $sede->update($validated);

        return redirect()
            ->route('admin.sedes.index')
            ->with('success', 'Sede actualizada correctamente.');
    }

    private function validarDatos(Request $request): array
    {
        return $request->validate([
            'nombre' => ['required', 'string', 'max:80'],
            'ciudad' => ['required', 'string', 'max:100'],
            'direccion' => ['required', 'string', 'max:60'],
            'detalles' => ['required', 'string', 'max:150'],
        ]);
    }
}
