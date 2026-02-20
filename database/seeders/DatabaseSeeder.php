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
    // Crear las Sedes (Barcelona, Montreal y Berlin)
    $bcn = Sede::create(['nom' => 'Barcelona']); 
    $mtl = Sede::create(['nom' => 'Montreal']); 
    $berlin = Sede::create(['nom' => 'Berlin']);

    // ========== ADMINISTRADORES ==========
    User::create([
        'name' => 'Admin Max',
        'email' => 'admin@empresa.com',
        'password' => bcrypt('password'),
        'rol' => 'administrador', 
        'sede_id' => $bcn->id,
        'actiu' => true 
    ]);

    User::create([
        'name' => 'Maria Rodriguez',
        'email' => 'maria.admin@empresa.com',
        'password' => bcrypt('password'),
        'rol' => 'administrador', 
        'sede_id' => $mtl->id,
        'actiu' => true 
    ]);

    // ========== GESTORES (uno por sede) ==========
    User::create([
        'name' => 'Gestor BCN - Carlos Pérez',
        'email' => 'gestor.bcn@empresa.com',
        'password' => bcrypt('password'),
        'rol' => 'gestor', 
        'sede_id' => $bcn->id,
        'actiu' => true
    ]);

    User::create([
        'name' => 'Gestor Montreal - Sophie Martin',
        'email' => 'gestor.mtl@empresa.com',
        'password' => bcrypt('password'),
        'rol' => 'gestor', 
        'sede_id' => $mtl->id,
        'actiu' => true
    ]);

    User::create([
        'name' => 'Gestor Berlin - Hans Mueller',
        'email' => 'gestor.berlin@empresa.com',
        'password' => bcrypt('password'),
        'rol' => 'gestor', 
        'sede_id' => $berlin->id,
        'actiu' => true
    ]);

    // ========== TÉCNICOS ==========
    // Barcelona
    User::create([
        'name' => 'Tecnic BCN - Pepe García',
        'email' => 'pepe.tecnic@empresa.com',
        'password' => bcrypt('password'),
        'rol' => 'tecnic', 
        'sede_id' => $bcn->id,
        'actiu' => true
    ]);

    User::create([
        'name' => 'Tecnic BCN - Laura Martínez',
        'email' => 'laura.tecnic@empresa.com',
        'password' => bcrypt('password'),
        'rol' => 'tecnic', 
        'sede_id' => $bcn->id,
        'actiu' => true
    ]);

    // Montreal
    User::create([
        'name' => 'Tecnic MTL - Jean Dupont',
        'email' => 'jean.tecnic@empresa.com',
        'password' => bcrypt('password'),
        'rol' => 'tecnic', 
        'sede_id' => $mtl->id,
        'actiu' => true
    ]);

    User::create([
        'name' => 'Tecnic MTL - Marie Leblanc',
        'email' => 'marie.tecnic@empresa.com',
        'password' => bcrypt('password'),
        'rol' => 'tecnic', 
        'sede_id' => $mtl->id,
        'actiu' => true
    ]);

    // Berlin
    User::create([
        'name' => 'Tecnic Berlin - Klaus Schmidt',
        'email' => 'klaus.tecnic@empresa.com',
        'password' => bcrypt('password'),
        'rol' => 'tecnic', 
        'sede_id' => $berlin->id,
        'actiu' => true
    ]);

    User::create([
        'name' => 'Tecnic Berlin - Anna Weber',
        'email' => 'anna.tecnic@empresa.com',
        'password' => bcrypt('password'),
        'rol' => 'tecnic', 
        'sede_id' => $berlin->id,
        'actiu' => true
    ]);

    // ========== CLIENTES ==========
    // Barcelona
    User::create([
        'name' => 'Client BCN - Joan López',
        'email' => 'joan.client@empresa.com',
        'password' => bcrypt('password'),
        'rol' => 'client', 
        'sede_id' => $bcn->id,
        'actiu' => true
    ]);

    User::create([
        'name' => 'Client BCN - Carmen Sánchez',
        'email' => 'carmen.client@empresa.com',
        'password' => bcrypt('password'),
        'rol' => 'client', 
        'sede_id' => $bcn->id,
        'actiu' => true
    ]);

    User::create([
        'name' => 'Client BCN - David Ruiz',
        'email' => 'david.client@empresa.com',
        'password' => bcrypt('password'),
        'rol' => 'client', 
        'sede_id' => $bcn->id,
        'actiu' => true
    ]);

    User::create([
        'name' => 'Client BCN - Ana Torres',
        'email' => 'ana.client@empresa.com',
        'password' => bcrypt('password'),
        'rol' => 'client', 
        'sede_id' => $bcn->id,
        'actiu' => true
    ]);

    // Montreal
    User::create([
        'name' => 'Client MTL - Pierre Tremblay',
        'email' => 'pierre.client@empresa.com',
        'password' => bcrypt('password'),
        'rol' => 'client', 
        'sede_id' => $mtl->id,
        'actiu' => true
    ]);

    User::create([
        'name' => 'Client MTL - Emma Gagnon',
        'email' => 'emma.client@empresa.com',
        'password' => bcrypt('password'),
        'rol' => 'client', 
        'sede_id' => $mtl->id,
        'actiu' => true
    ]);

    User::create([
        'name' => 'Client MTL - Luc Bouchard',
        'email' => 'luc.client@empresa.com',
        'password' => bcrypt('password'),
        'rol' => 'client', 
        'sede_id' => $mtl->id,
        'actiu' => true
    ]);

    // Berlin
    User::create([
        'name' => 'Client Berlin - Thomas Fischer',
        'email' => 'thomas.client@empresa.com',
        'password' => bcrypt('password'),
        'rol' => 'client', 
        'sede_id' => $berlin->id,
        'actiu' => true
    ]);

    User::create([
        'name' => 'Client Berlin - Julia Becker',
        'email' => 'julia.client@empresa.com',
        'password' => bcrypt('password'),
        'rol' => 'client', 
        'sede_id' => $berlin->id,
        'actiu' => true
    ]);

    User::create([
        'name' => 'Client Berlin - Stefan Wagner',
        'email' => 'stefan.client@empresa.com',
        'password' => bcrypt('password'),
        'rol' => 'client', 
        'sede_id' => $berlin->id,
        'actiu' => true
    ]);

    // ========== USUARIOS INACTIVOS (para probar filtros) ==========
    User::create([
        'name' => 'Client Inactivo - Marco Polo',
        'email' => 'marco.inactivo@empresa.com',
        'password' => bcrypt('password'),
        'rol' => 'client', 
        'sede_id' => $bcn->id,
        'actiu' => false  // ⚠️ Inactivo
    ]);

    User::create([
        'name' => 'Tecnic Inactivo - Luis Moreno',
        'email' => 'luis.inactivo@empresa.com',
        'password' => bcrypt('password'),
        'rol' => 'tecnic', 
        'sede_id' => $mtl->id,
        'actiu' => false  // ⚠️ Inactivo
    ]);

    // ========== CATEGORÍAS Y SUBCATEGORÍAS ==========
    // Software
    $software = Categoria::create(['nom' => 'Software']); 
    $software->subcategorias()->createMany([
        ['nom' => 'Aplicació gestió administrativa'],
        ['nom' => 'Accés remot'],
        ['nom' => 'Aplicació de videoconferència'],
        ['nom' => 'Sistema operatiu'],
    ]);

    // Hardware
    $hardware = Categoria::create(['nom' => 'Hardware']); 
    $hardware->subcategorias()->createMany([
        ['nom' => 'Problema amb el teclat'],
        ['nom' => 'El ratolí no funciona'],
        ['nom' => 'Monitor no s\'encén'],
        ['nom' => 'Imatge de projector defectuosa'],
        ['nom' => 'Ordinador no arranca'],
    ]);

    // Xarxa (Red)
    $xarxa = Categoria::create(['nom' => 'Xarxa']); 
    $xarxa->subcategorias()->createMany([
        ['nom' => 'Sense connexió a Internet'],
        ['nom' => 'WiFi lenta'],
        ['nom' => 'No puc accedir a carpetes compartides'],
    ]);

    // Impressores
    $impressores = Categoria::create(['nom' => 'Impressores']); 
    $impressores->subcategorias()->createMany([
        ['nom' => 'No imprimeix'],
        ['nom' => 'Paper encallat'],
        ['nom' => 'Qualitat d\'impressió dolenta'],
    ]);
}
}