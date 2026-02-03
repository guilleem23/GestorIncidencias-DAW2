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
            $table->string('titol'); // [cite: 54]
            $table->text('descripcio'); // [cite: 54, 69]
            
            // Relaciones con Usuarios y Sedes
            $table->foreignId('client_id')->constrained('usuarios'); // El cliente que informa 
            $table->foreignId('tecnic_id')->nullable()->constrained('usuarios'); // El técnico asignado 
            $table->foreignId('sede_id')->constrained('sedes'); // Sede de la incidencia [cite: 16, 35]
            
            // Categorización
            $table->foreignId('categoria_id')->constrained('categorias'); // [cite: 65]
            $table->foreignId('subcategoria_id')->constrained('subcategorias'); // [cite: 65]
            
            // Estados y Prioridades
            $table->enum('estat', [
                'Sense assignar', 
                'Assignada', 
                'En treball', 
                'Resolta', 
                'Tancada'
            ])->default('Sense assignar'); // [cite: 36, 58, 59, 64]
            
            $table->enum('prioritat', ['alta', 'mitjana', 'baixa'])->nullable(); // [cite: 42, 67]
            
            // Fechas de control
            $table->timestamp('data_creacio')->useCurrent(); // [cite: 56]
            $table->timestamp('data_inici_treball')->nullable(); // Para cuando el técnico empieza [cite: 51, 62]
            $table->timestamp('data_resolucio')->nullable(); // Cuando el técnico termina [cite: 52, 57]
            
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
