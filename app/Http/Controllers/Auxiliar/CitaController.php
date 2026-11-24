<?php

namespace App\Http\Controllers\Auxiliar;

use App\Http\Controllers\Controller;
use App\Models\Cita;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class CitaController extends Controller
{
    public function index(Request $request): View
    {
        $request->validate([
            'estado' => ['nullable', 'in:Pendiente,Confirmada,Realizada,Cancelada'],
            'fecha' => ['nullable', 'date_format:Y-m-d'],
        ]);

        $citas = Cita::with([
            'paciente.user',
            'servicio',
            'tipoServicio',
            'agenda.profesional.user',
            'agenda.sede',
        ])
            ->when(
                $request->filled('estado'),
                fn ($query) => $query->where('estadocita', $request->estado)
            )
            ->when(
                $request->filled('fecha'),
                fn ($query) => $query->whereDate('fechacita', $request->fecha)
            )
            ->orderBy('fechacita')
            ->paginate(15)
            ->withQueryString();

        return view('auxiliar.citas.index', compact('citas'));
    }

    public function confirmar(Cita $cita): RedirectResponse
    {
        try {
            DB::transaction(function () use ($cita) {
                $citaActual = Cita::where('idcita', $cita->idcita)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($citaActual->estadocita !== 'Pendiente') {
                    throw new \RuntimeException(
                        'Solo se puede confirmar la llegada de una cita pendiente.'
                    );
                }

                $citaActual->update([
                    'estadocita' => 'Confirmada',
                ]);
            });

            return redirect()
                ->route('auxiliar.citas.index')
                ->with('success', 'La llegada del paciente fue confirmada correctamente.');

        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
