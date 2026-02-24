<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categorias', function (Blueprint $table) {
            $table->text('descripcion')->nullable()->after('nom');
        });

        Schema::table('subcategorias', function (Blueprint $table) {
            $table->text('descripcion')->nullable()->after('nom');
        });
    }

    public function down(): void
    {
        Schema::table('categorias', function (Blueprint $table) {
            $table->dropColumn('descripcion');
        });

        Schema::table('subcategorias', function (Blueprint $table) {
            $table->dropColumn('descripcion');
        });
    }
};
