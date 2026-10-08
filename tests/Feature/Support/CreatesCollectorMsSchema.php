<?php

namespace Tests\Feature\Support;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;

trait CreatesCollectorMsSchema
{
    protected function prepareCollectorMsSchema(): void
    {
        Config::set('database.default', 'sqlite');
        Config::set('database.connections.sqlite.database', ':memory:');

        Schema::dropAllTables();

        Schema::create('rols', function (Blueprint $table) {
            $table->id();
            $table->string('name');
        });

        Schema::create('states', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('color')->nullable();
        });

        Schema::create('type_identifications', function (Blueprint $table) {
            $table->id();
            $table->string('name');
        });

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('identification')->nullable();
            $table->unsignedBigInteger('type_identification_id')->nullable();
            $table->unsignedBigInteger('rol_id')->nullable();
            $table->string('email')->nullable();
            $table->string('password')->nullable();
            $table->string('image')->nullable();
            $table->unsignedBigInteger('state_id')->nullable();
            $table->date('deleted_at')->nullable();
        });

        Schema::create('collectors', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('identification_document')->nullable();
            $table->string('driving_license_document')->nullable();
            $table->unsignedBigInteger('state_id')->nullable();
            $table->date('deleted_at')->nullable();
        });

        \DB::table('rols')->insert([
            ['name' => 'Administrador'],
            ['name' => 'Usuario'],
            ['name' => 'Recolector'],
        ]);

        \DB::table('states')->insert([
            ['name' => 'Habilitado', 'color' => 'green'],
            ['name' => 'Deshabilitado', 'color' => 'red'],
            ['name' => 'Pendiente de Validar', 'color' => 'yellow'],
            ['name' => 'Rechazado', 'color' => 'orange'],
            ['name' => 'Eliminado', 'color' => 'gray'],
        ]);

        \DB::table('type_identifications')->insert([
            ['name' => 'Cedula'],
        ]);
    }
}
