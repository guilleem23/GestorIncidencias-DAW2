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
        'username' => 'adminmax',
        'email' => 'admin@empresa.com',
        'password' => bcrypt('password'),
        'rol' => 'administrador', 
        'sede_id' => $bcn->id,
        'actiu' => true 
    ]);

    User::create([
        'name' => 'Gestor BCN',
        'username' => 'gestorbcn',
        'email' => 'gestor@empresa.com',
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
        'username' => 'tecnicpepe',
        'password' => bcrypt('password'),
        'rol' => 'tecnic', 
        'sede_id' => $bcn->id,
        'actiu' => true
    ]);

    // Montreal
    User::create([
        'name' => 'Tecnic MTL - Jean Dupont',
        'email' => 'jean.tecnic@empresa.com',
        'username' => 'tecnicjean',
        'password' => bcrypt('password'),
        'rol' => 'tecnic', 
        'sede_id' => $mtl->id,
        'actiu' => true
    ]);

    User::create([
        'name' => 'Tecnic MTL - Marie Leblanc',
        'email' => 'marie.tecnic@empresa.com',
        'username' => 'tecnicmarie',
        'password' => bcrypt('password'),
        'rol' => 'tecnic', 
        'sede_id' => $mtl->id,
        'actiu' => true
    ]);

    // Berlin
    User::create([
        'name' => 'Tecnic Berlin - Klaus Schmidt',
        'email' => 'klaus.tecnic@empresa.com',
        'username' => 'tecnicklaus',
        'password' => bcrypt('password'),
        'rol' => 'tecnic', 
        'sede_id' => $berlin->id,
        'actiu' => true
    ]);

    User::create([
        'name' => 'Tecnic Berlin - Anna Weber',
        'email' => 'anna.tecnic@empresa.com',
        'username' => 'tecnicanna',
        'password' => bcrypt('password'),
        'rol' => 'tecnic', 
        'sede_id' => $berlin->id,
        'actiu' => true
    ]);

    // ========== CLIENTES ==========
    // Barcelona
    User::create([
        'name' => 'Client BCN - Joan López',
        'username' => 'clientjoan',
        'email' => 'joan.client@empresa.com',
        'password' => bcrypt('password'),
        'rol' => 'client', 
        'sede_id' => $bcn->id,
        'actiu' => true
    ]);

    User::create([
        'name' => 'Client BCN - Carmen Sánchez',
        'username' => 'clientcarmen',
        'email' => 'carmen.client@empresa.com',
        'password' => bcrypt('password'),
        'rol' => 'client', 
        'sede_id' => $bcn->id,
        'actiu' => true
    ]);

    User::create([
        'name' => 'Client BCN - David Ruiz',
        'username' => 'clientdavid',
        'email' => 'david.client@empresa.com',
        'password' => bcrypt('password'),
        'rol' => 'client', 
        'sede_id' => $bcn->id,
        'actiu' => true
    ]);

    User::create([
        'name' => 'Client BCN - Ana Torres',
        'username' => 'clientana',
        'email' => 'ana.client@empresa.com',
        'password' => bcrypt('password'),
        'rol' => 'client', 
        'sede_id' => $bcn->id,
        'actiu' => true
    ]);

    // Montreal
    User::create([
        'name' => 'Client MTL - Pierre Tremblay',
        'username' => 'clientpierre',
        'email' => 'pierre.client@empresa.com',
        'password' => bcrypt('password'),
        'rol' => 'client', 
        'sede_id' => $mtl->id,
        'actiu' => true
    ]);

    User::create([
        'name' => 'Client MTL - Emma Gagnon',
        'username' => 'clientemma',
        'email' => 'emma.client@empresa.com',
        'password' => bcrypt('password'),
        'rol' => 'client', 
        'sede_id' => $mtl->id,
        'actiu' => true
    ]);

    User::create([
        'name' => 'Client MTL - Luc Bouchard',
        'username' => 'clientluc',
        'email' => 'luc.client@empresa.com',
        'password' => bcrypt('password'),
        'rol' => 'client', 
        'sede_id' => $mtl->id,
        'actiu' => true
    ]);

    // Berlin
    User::create([
        'name' => 'Client Berlin - Thomas Fischer',
        'username' => 'clientthomas',
        'email' => 'thomas.client@empresa.com',
        'password' => bcrypt('password'),
        'rol' => 'client', 
        'sede_id' => $berlin->id,
        'actiu' => true
    ]);

    User::create([
        'name' => 'Client Berlin - Julia Becker',
        'username' => 'clientjulia',
        'email' => 'julia.client@empresa.com',
        'password' => bcrypt('password'),
        'rol' => 'client', 
        'sede_id' => $berlin->id,
        'actiu' => true
    ]);

    User::create([
        'name' => 'Client Berlin - Stefan Wagner',
        'username' => 'clientstefan',
        'email' => 'stefan.client@empresa.com',
        'password' => bcrypt('password'),
        'rol' => 'client', 
        'sede_id' => $berlin->id,
        'actiu' => true
    ]);

    // ========== USUARIOS INACTIVOS (para probar filtros) ==========
    User::create([
        'name' => 'Client Inactivo - Marco Polo',
        'username' => 'clientmarco',
        'email' => 'marco.inactivo@empresa.com',
        'password' => bcrypt('password'),
        'rol' => 'client', 
        'sede_id' => $bcn->id,
        'actiu' => false  // ⚠️ Inactivo
    ]);

    User::create([
        'name' => 'Tecnic Inactivo - Luis Moreno',
        'username' => 'tecnicluis',
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

    // Ejecutar el seeder de Incidencias realista
    $this->call([
        IncidenciaSeeder::class,
    ]);
}
}