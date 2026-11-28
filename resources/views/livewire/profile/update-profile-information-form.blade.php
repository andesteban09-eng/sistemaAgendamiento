<?php

use App\Models\User;
use App\Services\Profile\ProfileService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Volt\Component;

new class extends Component {
    public string $name = '';
    public string $last_name = '';
    public string $telefono = '';
    public string $direccion = '';
    public string $ciudad = '';

    #[Computed]
    public function profile(): User
    {
        return app(ProfileService::class)->getProfile(Auth::user());
    }

    public function mount(): void
    {
        $this->loadInformation();
    }

    private function loadInformation(): void
    {
        $user = $this->profile;

        $this->name = $user->name;
        $this->last_name = $user->last_name;

        if ($user->rol === 'paciente') {
            $this->telefono = $user->paciente?->telefono ?? '';
            $this->direccion = $user->paciente?->direccion ?? '';
            $this->ciudad = $user->paciente?->ciudad ?? '';
        }

        if ($user->rol === 'profesional') {
            $this->telefono = $user->profesional?->telefono ?? '';
        }
    }

    public function updateProfileInformation(): void
    {
        $this->resetValidation();

        app(ProfileService::class)->update(Auth::user(), [
            'name' => $this->name,
            'last_name' => $this->last_name,
            'telefono' => $this->telefono,
            'direccion' => $this->direccion,
            'ciudad' => $this->ciudad,
        ]);

        unset($this->profile);

        $this->loadInformation();

        $this->dispatch('profile-updated', name: $this->name);

        session()->flash('profile_status', 'Tu información personal se actualizó correctamente.');
    }
};

?>

<section class="sigac-profile-section">

    @php
        $user = $this->profile;

        $details = match ($user->rol) {
            'paciente' => $user->paciente,
            'profesional' => $user->profesional,
            default => null,
        };
    @endphp

    <header class="sigac-profile-section-header">
        <i class="bi bi-person-vcard"></i>

        <div>
            <h5>Información personal</h5>
            <p>Consulta y actualiza tus datos personales.</p>
        </div>
    </header>

    @if (session('profile_status'))
        <div class="alert alert-success">
            {{ session('profile_status') }}
        </div>
    @endif

    @error('profile')
        <div class="alert alert-warning">
            {{ $message }}
        </div>
    @enderror

    <form wire:submit="updateProfileInformation">

        <div class="sigac-profile-grid">

            <div class="sigac-profile-field">
                <label for="profile_name">Nombres</label>

                <input id="profile_name" wire:model="name" type="text" maxlength="255"
                    class="form-control @error('name') is-invalid @enderror" required>

                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="sigac-profile-field">
                <label for="profile_last_name">Apellidos</label>

                <input id="profile_last_name" wire:model="last_name" type="text" maxlength="255"
                    class="form-control @error('last_name') is-invalid @enderror" required>

                @error('last_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            @if ($details)
                <div class="sigac-profile-field">
                    <label>Tipo de documento</label>

                    <div class="sigac-profile-readonly">
                        {{ $details->tipodoc }}
                    </div>
                </div>

                <div class="sigac-profile-field">
                    <label>Número de documento</label>

                    <div class="sigac-profile-readonly">
                        {{ $details->numdoc }}
                    </div>
                </div>
            @endif

        </div>

        @if ($details)

            <h6 class="sigac-profile-subtitle">
                <i class="bi bi-telephone me-2"></i>
                Información de contacto
            </h6>

            <div class="sigac-profile-grid">

                <div class="sigac-profile-field">
                    <label for="profile_telefono">Teléfono</label>

                    <input id="profile_telefono" wire:model="telefono" type="tel" maxlength="20"
                        class="form-control @error('telefono') is-invalid @enderror" required>

                    @error('telefono')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                @if ($user->rol === 'paciente')
                    <div class="sigac-profile-field">
                        <label for="profile_ciudad">Ciudad</label>

                        <input id="profile_ciudad" wire:model="ciudad" type="text" maxlength="60"
                            class="form-control @error('ciudad') is-invalid @enderror" required>

                        @error('ciudad')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="sigac-profile-field sigac-profile-full">
                        <label for="profile_direccion">Dirección</label>

                        <input id="profile_direccion" wire:model="direccion" type="text" maxlength="150"
                            class="form-control @error('direccion') is-invalid @enderror" required>

                        @error('direccion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                @endif

            </div>

        @endif

        <h6 class="sigac-profile-subtitle">
            <i class="bi bi-info-circle me-2"></i>
            Información de la cuenta
        </h6>

        <div class="sigac-profile-grid">

            <div class="sigac-profile-field">
                <label>Rol</label>
                <div class="sigac-profile-readonly">
                    {{ ucfirst($user->rol) }}
                </div>
            </div>

            <div class="sigac-profile-field">
                <label>Estado de la cuenta</label>
                <div class="sigac-profile-readonly">
                    {{ ucfirst($user->estado) }}
                </div>
            </div>

            @if ($user->rol === 'paciente' && $details)
                <div class="sigac-profile-field">
                    <label>Fecha de registro del paciente</label>

                    <div class="sigac-profile-readonly">
                        {{ $details->fecharegistro ?? 'No registrada' }}
                    </div>
                </div>

                <div class="sigac-profile-field">
                    <label>Estado del paciente</label>

                    <div class="sigac-profile-readonly">
                        {{ $details->estadopaciente }}
                    </div>
                </div>
            @elseif ($user->rol === 'profesional' && $details)
                <div class="sigac-profile-field">
                    <label>Estado del profesional</label>

                    <div class="sigac-profile-readonly">
                        {{ $details->estadoprofesionalsalud }}
                    </div>
                </div>
            @endif

        </div>

        @if (in_array($user->rol, ['paciente', 'profesional'], true) && !$details)
            <div class="alert alert-warning mt-3">
                Tu cuenta todavía no tiene un perfil asociado.
            </div>
        @else
            <div class="sigac-profile-actions">

                <button type="submit" class="sigac-profile-btn" wire:loading.attr="disabled">
                    <i class="bi bi-floppy me-2"></i>

                    <span wire:loading.remove wire:target="updateProfileInformation">
                        Guardar cambios
                    </span>

                    <span wire:loading wire:target="updateProfileInformation">
                        Guardando...
                    </span>
                </button>

            </div>
        @endif

    </form>

    <h6 class="sigac-profile-subtitle">
        <i class="bi bi-envelope me-2"></i>
        Correo electrónico
    </h6>

    <div class="sigac-profile-email">

        <div>
            <span>{{ $user->email }}</span>

            @if ($user->hasVerifiedEmail())
                <small class="text-success d-block">
                    <i class="bi bi-patch-check-fill"></i>
                    Correo verificado
                </small>
            @endif
        </div>

    </div>

    <p class="sigac-profile-hint">
        El cambio de correo electrónico requiere un
        procedimiento de seguridad independiente.
    </p>

</section>
