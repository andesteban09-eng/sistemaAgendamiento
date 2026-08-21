<x-app-layout>

    <x-slot name="header">
        <h2 class="fw-bold">
            Panel de administración
        </h2>
    </x-slot>

    <div class="container py-5">

        <div class="card shadow-sm">
            <div class="card-body">

                <h3>
                    Bienvenido, {{ auth()->user()->name }}
                </h3>

                <p class="text-muted">
                    Desde aquí podrás administrar usuarios,
                    profesionales y la configuración del sistema.
                </p>

            </div>
        </div>

    </div>

</x-app-layout>
