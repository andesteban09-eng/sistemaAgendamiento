<?php

namespace App\Services\Profile;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class ProfileService
{
    /**
     * Consultar el perfil del usuario.
     */
    public function getProfile(User $user): User
    {
        $user = User::query()
            ->findOrFail($user->getKey());

        abort_unless(
            $user->estado === 'activo' &&
                $user->hasVerifiedEmail(),
            403
        );

        return match ($user->rol) {
            'paciente' => $user->load('paciente'),
            'profesional' => $user->load('profesional'),
            default => $user,
        };
    }

    /**
     * Actualizar la información personal.
     */
    public function update(
        User $user,
        array $data
    ): User {
        return DB::transaction(function () use ($user, $data) {

            $user = User::query()
                ->lockForUpdate()
                ->findOrFail($user->getKey());

            abort_unless(
                $user->estado === 'activo' &&
                    $user->hasVerifiedEmail(),
                403
            );

            $rules = [
                'name' => ['required', 'string', 'max:255'],
                'last_name' => ['required', 'string', 'max:255'],
            ];

            if (in_array(
                $user->rol,
                ['paciente', 'profesional'],
                true
            )) {
                $rules['telefono'] = [
                    'required',
                    'string',
                    'max:20',
                ];
            }

            if ($user->rol === 'paciente') {
                $rules['direccion'] = [
                    'required',
                    'string',
                    'max:150',
                ];

                $rules['ciudad'] = [
                    'required',
                    'string',
                    'max:60',
                ];
            }

            $validated = Validator::make(
                $data,
                $rules
            )->validate();

            $user->name = $validated['name'];
            $user->last_name = $validated['last_name'];

            if (!$user->save()) {
                throw new RuntimeException(
                    'No fue posible actualizar el usuario.'
                );
            }

            if ($user->rol === 'paciente') {

                $paciente = $user->paciente()
                    ->lockForUpdate()
                    ->first();

                if ($paciente === null) {
                    throw ValidationException::withMessages([
                        'profile' => 'Debes completar primero tu registro como paciente.',
                    ]);
                }

                $paciente->telefono = $validated['telefono'];
                $paciente->direccion = $validated['direccion'];
                $paciente->ciudad = $validated['ciudad'];

                if (!$paciente->save()) {
                    throw new RuntimeException(
                        'No fue posible actualizar el paciente.'
                    );
                }
            }

            if ($user->rol === 'profesional') {

                $profesional = $user->profesional()
                    ->lockForUpdate()
                    ->first();

                if ($profesional === null) {
                    throw ValidationException::withMessages([
                        'profile' => 'No existe un perfil profesional asociado.',
                    ]);
                }

                $profesional->telefono = $validated['telefono'];

                if (!$profesional->save()) {
                    throw new RuntimeException(
                        'No fue posible actualizar el profesional.'
                    );
                }
            }

            return $this->getProfile($user);
        });
    }
}
