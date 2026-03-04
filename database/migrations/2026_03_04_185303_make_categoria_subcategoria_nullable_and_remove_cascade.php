<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Remove cascade from subcategorias.categoria_id
        Schema::table('subcategorias', function (Blueprint $table) {
            $table->dropForeign(['categoria_id']);
            $table->foreign('categoria_id')->references('id')->on('categorias');
        });

        // 2. Make categoria_id and subcategoria_id nullable in incidencias
        Schema::table('incidencias', function (Blueprint $table) {
            $table->foreignId('categoria_id')->nullable()->change();
            $table->foreignId('subcategoria_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('subcategorias', function (Blueprint $table) {
            $table->dropForeign(['categoria_id']);
            $table->foreign('categoria_id')->references('id')->on('categorias')->onDelete('cascade');
        });

        Schema::table('incidencias', function (Blueprint $table) {
            $table->foreignId('categoria_id')->nullable(false)->change();
            $table->foreignId('subcategoria_id')->nullable(false)->change();
        });
    }
};
