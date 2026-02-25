<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->string('password');
            // Roles mínimos: administrador, client, gestor, tecnic
            $table->enum('rol', ['administrador', 'client', 'gestor', 'tecnic']); 
            $table->foreignId('sede_id')->constrained('sedes'); // Cada usuario pertenece a una sede
            $table->boolean('actiu')->default(true); // Para bajas de RRHH
            $table->rememberToken();
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};
