<?php

namespace Tests\Support;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

final class SigacTestSchema
{
    private const TIPOS_DOCUMENTO = [
        'Cedula Ciudadania',
        'Tarjeta Identidad',
        'Cedula Extranjeria',
        'Pasaporte',
        'Registro Civil',
        'Permiso Proteccion Temporal',
        'Otro',
    ];

    public static function create(): void
    {
        self::ensureTestingEnvironment();

        self::createPacienteTable();
        self::createProfesionalTable();
    }

    private static function ensureTestingEnvironment(): void
    {
        if (
            !app()->environment('testing')
            || config('database.default') !== 'sqlite'
            || DB::connection('sqlite')->getDatabaseName() !== ':memory:'
        ) {
            throw new RuntimeException(
                'El esquema de pruebas solo puede utilizarse '
                . 'sobre SQLite en memoria.'
            );
        }
    }

    private static function createPacienteTable(): void
    {
        $schema = Schema::connection('sqlite');

        if ($schema->hasTable('paciente')) {
            return;
        }

        $schema->create('paciente', function (Blueprint $table) {
            $table->id('idpaciente');

            $table->enum('tipodoc', self::TIPOS_DOCUMENTO);
            $table->string('numdoc', 45)->unique();
            $table->string('telefono', 20);
            $table->string('direccion', 150);
            $table->string('ciudad', 60);

            $table->timestamp('fecharegistro')
                ->nullable()
                ->useCurrent();

            $table->enum('estadopaciente', [
                'Activo',
                'Inactivo',
            ])->default('Activo');

            $table->foreignId('idusuario')
                ->unique()
                ->constrained('users', 'id');
        });
    }

    private static function createProfesionalTable(): void
    {
        $schema = Schema::connection('sqlite');

        if ($schema->hasTable('profesionalsalud')) {
            return;
        }

        $schema->create('profesionalsalud', function (Blueprint $table) {
            $table->id('idprofesionalsalud');

            $table->enum('tipodoc', self::TIPOS_DOCUMENTO);
            $table->string('numdoc', 45)->unique();
            $table->string('telefono', 20);

            $table->enum('estadoprofesionalsalud', [
                'Activo',
                'Inactivo',
            ])->default('Activo');

            $table->foreignId('idusuario')
                ->unique()
                ->constrained('users', 'id');
        });
    }
}
