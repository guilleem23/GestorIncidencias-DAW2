<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategoriaSubcategoriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categorias = [
            [
                'nom' => 'Hardware',
                'descripcion' => 'Incidencias relacionadas con equipos físicos, componentes, periféricos y cableado.',
                'subcategorias' => [
                    ['nom' => 'Ordenadores y Portátiles', 'descripcion' => 'Fallos de encendido, pantallas rotas, lentitud del sistema o problemas de batería.'],
                    ['nom' => 'Periféricos', 'descripcion' => 'Problemas con ratones, teclados, monitores externos o cámaras web.'],
                    ['nom' => 'Impresoras y Escáneres', 'descripcion' => 'Atascos de papel, fallos de conexión, falta de tóner/tinta o mala calidad de impresión.'],
                    ['nom' => 'Redes y Comunicaciones', 'descripcion' => 'Problemas con routers, switches, cableado de red o puntos de acceso Wi-Fi.']
                ]
            ],
            [
                'nom' => 'Software',
                'descripcion' => 'Problemas con programas, sistemas operativos, licencias y aplicaciones corporativas.',
                'subcategorias' => [
                    ['nom' => 'Sistema Operativo', 'descripcion' => 'Errores de Windows/macOS/Linux, pantallazos azules o problemas de actualización.'],
                    ['nom' => 'Ofimática', 'descripcion' => 'Problemas con Microsoft Office, correos electrónicos, Teams o herramientas de edición.'],
                    ['nom' => 'Software Corporativo', 'descripcion' => 'Errores en ERPs, CRMs o herramientas de gestión interna de la empresa.'],
                    ['nom' => 'Licencias y Accesos', 'descripcion' => 'Renovación de licencias caducadas o solicitud de permisos especiales.']
                ]
            ],
            [
                'nom' => 'Cuentas y Accesos',
                'descripcion' => 'Gestión de credenciales, permisos de usuario y accesos a plataformas.',
                'subcategorias' => [
                    ['nom' => 'Restablecimiento de Contraseñas', 'descripcion' => 'Recuperación de contraseñas olvidadas o bloqueadas del correo o sistema.'],
                    ['nom' => 'Alta de Usuarios', 'descripcion' => 'Creación de nuevas cuentas de correo, acceso a VPN y asignación de equipos.'],
                    ['nom' => 'Baja de Usuarios', 'descripcion' => 'Deshabilitar accesos, copias de seguridad de datos y retirada de equipos.'],
                ]
            ],
            [
                'nom' => 'Infraestructura General',
                'descripcion' => 'Mantenimiento del edificio, mobiliario, clima e instalaciones compartidas.',
                'subcategorias' => [
                    ['nom' => 'Climatización y Lumínica', 'descripcion' => 'Problemas con el aire acondicionado, calefacción o luces fundidas.'],
                    ['nom' => 'Mobiliario', 'descripcion' => 'Sillas rotas, mesas inestables, armarios atascados o pizarras defectuosas.'],
                    ['nom' => 'Limpieza y Suministros', 'descripcion' => 'Falta de material en baños, salas de reuniones sucias o papeleras llenas.']
                ]
            ]
        ];

        foreach ($categorias as $catData) {
            $categoria = \App\Models\Categoria::create([
                'nom' => $catData['nom'],
                'descripcion' => $catData['descripcion']
            ]);

            foreach ($catData['subcategorias'] as $subData) {
                \App\Models\Subcategoria::create([
                    'nom' => $subData['nom'],
                    'descripcion' => $subData['descripcion'],
                    'categoria_id' => $categoria->id
                ]);
            }
        }
    }
}
