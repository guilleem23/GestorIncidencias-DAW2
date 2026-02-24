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
        Schema::create('incidencias', function (Blueprint $table) {
            $table->id();
            $table->string('titol');
            $table->text('descripcio');
            
            // Relaciones con Usuarios y Sedes
            $table->foreignId('client_id')->constrained('usuarios'); // El cliente que informa 
            $table->foreignId('tecnic_id')->nullable()->constrained('usuarios'); // El técnico asignado 
            $table->foreignId('sede_id')->constrained('sedes'); // Sede de la incidencia
            
            // Categorización
            $table->foreignId('categoria_id')->constrained('categorias');
            $table->foreignId('subcategoria_id')->constrained('subcategorias');
            
            // Estados y Prioridades
            $table->enum('estat', [
                'Sense assignar', 
                'Assignada', 
                'En treball', 
                'Resolta', 
                'Tancada'
            ])->default('Sense assignar');
            
            $table->enum('prioritat', ['alta', 'mitjana', 'baixa'])->nullable();
            
            // Fechas de control
            $table->timestamp('data_creacio')->useCurrent();
            $table->timestamp('data_inici_treball')->nullable(); // Para cuando el técnico empieza
            $table->timestamp('data_resolucio')->nullable(); // Cuando el técnico termina
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incidencias');
    }
};
