<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Sede;
use App\Models\Categoria;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
public function run(): void
{
    // Crear las Sedes (Barcelona y Montreal)
    $bcn = Sede::create(['nom' => 'Barcelona']); 
    $mtl = Sede::create(['nom' => 'Montreal']); 
    $berlin = Sede::create(['nom' => 'Berlin']);

    // Crear Usuarios de Prueba (uno por cada rol)
    User::create([
        'name' => 'Admin Max',
        'email' => 'admin@empresa.com',
        'password' => bcrypt('password'),
        'rol' => 'administrador', 
        'sede_id' => $bcn->id,
        'actiu' => true 
    ]);

    User::create([
        'name' => 'Client Joan',
        'email' => 'client@empresa.com',
        'password' => bcrypt('password'),
        'rol' => 'client', 
        'sede_id' => $bcn->id,
        'actiu' => true
    ]);

    User::create([
        'name' => 'Gestor BCN',
        'email' => 'gestor@empresa.com',
        'password' => bcrypt('password'),
        'rol' => 'gestor', 
        'sede_id' => $bcn->id,
        'actiu' => true
    ]);

    User::create([
        'name' => 'Tecnic Pepe',
        'email' => 'tecnic@empresa.com',
        'password' => bcrypt('password'),
        'rol' => 'tecnic', 
        'sede_id' => $bcn->id,
        'actiu' => true
    ]);

    // Crear Categorías y Subcategorías iniciales
    $sw = Categoria::create(['nom' => 'Software']); 
    $sw->subcategorias()->create(['nom' => 'Accés remot']); 
}
}