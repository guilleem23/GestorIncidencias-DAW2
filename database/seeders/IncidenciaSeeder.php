<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Incidencia;
use App\Models\User;
use App\Models\Categoria;
use App\Models\Subcategoria;
use App\Models\Sede;
use Carbon\Carbon;

class IncidenciaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpiamos las incidencias existentes si queremos empezar de cero (opcional)
        // Incidencia::truncate();

        // Obtenemos todas los clientes, técnicos y categorías
        $clientes = User::where('rol', 'client')->get();
        $categorias = Categoria::with('subcategorias')->get();
        $prioridades = ['alta', 'mitjana', 'baixa'];

        // Casos realistas de incidencias IT
        $titulos = [
            'El ratón ha dejado de funcionar repentinamente',
            'No puedo iniciar sesión en el portal del empleado',
            'El proyector de la sala de reuniones 3 no detecta mi portátil',
            'La impresora de la planta 2 se ha comido el papel',
            'No tengo acceso a la carpeta compartida de Recursos Humanos',
            'El ordenador va extremadamente lento desde la última actualización',
            'He olvidado mi contraseña del sistema',
            'Pantalla en negro al intentar encender el equipo',
            'El software de contabilidad lanza un error 404',
            'La red WiFi de invitados no funciona',
            'El navegador no abre las páginas web correctamente',
            'Tengo problemas para conectar a la VPN corporativa',
            'El software de compresión no comprime archivos',
            'La actualización de Windows no se completa',
            'El portátil se reinicia sin previo aviso'
        ];

        $descripciones = [
            'Estaba trabajando normalmente y de repente el ratón dejó de responder. He probado a cambiarle las pilas y a conectarlo en otro puerto USB pero sigue sin funcionar.',
            'Intento entrar con mi usuario y contraseña de siempre pero me dice que las credenciales son inválidas. Necesito acceso urgente para subir las nóminas.',
            'Tenemos una presentación importante en 10 minutos y el proyector muestra "No Signal". Hemos revisado el cable HDMI pero parece estar bien.',
            'He mandado a imprimir un documento de 50 páginas y se ha atascado a la mitad. Ahora marca una luz roja intermitente de error 50.',
            'Al intentar entrar en la unidad compartida Z: me aparece una alerta de "Acceso Denegado". Ayer mismo podía entrar sin problemas.',
            'Desde que acepté la actualización de Windows esta mañana, tarda casi 5 minutos en abrir un simple documento de Word.',
            'Volví de las vacaciones y no recuerdo la contraseña correcta para entrar al CRM. ¿Pueden restablecérmela, por favor?',
            'Al darle al botón de encendido hace un pitido largo y dos cortos, pero la pantalla se queda completamente en negro.',
            'Cada vez que intento generar un informe trimestral en la aplicación, el programa se cierra de golpe sin guardar nada.',
            'Varios clientes están intentando conectarse a la red "Empresa-Guest" pero les marca "Sin conexión a internet".',
            'Firefox no carga ninguna página pero Edge funciona correctamente.',
            'Al conectar a la VPN me pide contraseña pero no acepta ninguna.',
            'El programa de compresión abre pero no permite seleccionar archivos.',
            'Windows me pide reiniciar pero la actualización nunca se completa.',
            'El portátil se queda bloqueado durante unos segundos y luego se reinicia.'
        ];

        // Distribución de incidencias por cliente:
        // - 3 abiertas (sin asignar/sin técnico)
        // - 3 cerradas (resueltas/tancadas)
        // - 4 asignadas (con técnico)
        // Total: 10 incidencias por cliente

        foreach ($clientes as $cliente) {
            // Obtener técnicos de la misma sede
            $tecnicosSede = User::where('rol', 'tecnic')
                ->where('sede_id', $cliente->sede_id)
                ->get();

            if ($tecnicosSede->isEmpty() || $categorias->isEmpty()) {
                continue; // Saltar si no hay técnicos o categorías
            }

            $indicesTitulos = [];
            
            // 3 INCIDENCIAS ABIERTAS SIN ASIGNAR
            for ($i = 0; $i < 3; $i++) {
                $indicesTitulos[] = $i;
                $categoria = $categorias->random();
                $subcategoria = $categoria->subcategorias->count() > 0 
                    ? $categoria->subcategorias->random() 
                    : null;

                if (!$subcategoria) continue;

                $diasAtras = rand(1, 30);
                $fechaCreacion = Carbon::now()->subDays($diasAtras)->subHours(rand(1, 10));

                Incidencia::create([
                    'titol' => $titulos[$i],
                    'descripcio' => $descripciones[$i],
                    'client_id' => $cliente->id,
                    'tecnic_id' => null,  // Sin asignar
                    'sede_id' => $cliente->sede_id,
                    'categoria_id' => $categoria->id,
                    'subcategoria_id' => $subcategoria->id,
                    'estat' => 'Sense assignar',
                    'prioritat' => $prioridades[array_rand($prioridades)],
                    'data_creacio' => $fechaCreacion,
                    'data_inici_treball' => null,
                    'data_resolucio' => null,
                    'created_at' => $fechaCreacion,
                    'updated_at' => $fechaCreacion
                ]);
            }

            // 3 INCIDENCIAS CERRADAS
            $estadosCerrados = ['Resolta', 'Tancada', 'Resolta'];
            for ($i = 0; $i < 3; $i++) {
                $indicesTitulos[] = 3 + $i;
                $categoria = $categorias->random();
                $subcategoria = $categoria->subcategorias->count() > 0 
                    ? $categoria->subcategorias->random() 
                    : null;

                if (!$subcategoria) continue;

                $diasAtras = rand(5, 45);
                $fechaCreacion = Carbon::now()->subDays($diasAtras)->subHours(rand(1, 10));
                $fechaInicioTrabajo = (clone $fechaCreacion)->addHours(rand(1, 24));
                $fechaResolucion = (clone $fechaInicioTrabajo)->addHours(rand(2, 48));

                Incidencia::create([
                    'titol' => $titulos[3 + $i],
                    'descripcio' => $descripciones[3 + $i],
                    'client_id' => $cliente->id,
                    'tecnic_id' => $tecnicosSede->random()->id,  // Con técnico
                    'sede_id' => $cliente->sede_id,
                    'categoria_id' => $categoria->id,
                    'subcategoria_id' => $subcategoria->id,
                    'estat' => $estadosCerrados[$i],
                    'prioritat' => $prioridades[array_rand($prioridades)],
                    'data_creacio' => $fechaCreacion,
                    'data_inici_treball' => $fechaInicioTrabajo,
                    'data_resolucio' => $fechaResolucion,
                    'created_at' => $fechaCreacion,
                    'updated_at' => $fechaResolucion
                ]);
            }

            // 4 INCIDENCIAS ASIGNADAS (Con técnico)
            $estadosAsignados = ['Assignada', 'En treball', 'Assignada', 'En treball'];
            for ($i = 0; $i < 4; $i++) {
                $indicesTitulos[] = 6 + $i;
                $categoria = $categorias->random();
                $subcategoria = $categoria->subcategorias->count() > 0 
                    ? $categoria->subcategorias->random() 
                    : null;

                if (!$subcategoria) continue;

                $diasAtras = rand(1, 20);
                $fechaCreacion = Carbon::now()->subDays($diasAtras)->subHours(rand(1, 10));
                $fechaInicioTrabajo = $estadosAsignados[$i] === 'En treball' 
                    ? (clone $fechaCreacion)->addHours(rand(1, 12))
                    : null;

                Incidencia::create([
                    'titol' => $titulos[6 + $i],
                    'descripcio' => $descripciones[6 + $i],
                    'client_id' => $cliente->id,
                    'tecnic_id' => $tecnicosSede->random()->id,  // Con técnico asignado
                    'sede_id' => $cliente->sede_id,
                    'categoria_id' => $categoria->id,
                    'subcategoria_id' => $subcategoria->id,
                    'estat' => $estadosAsignados[$i],
                    'prioritat' => $prioridades[array_rand($prioridades)],
                    'data_creacio' => $fechaCreacion,
                    'data_inici_treball' => $fechaInicioTrabajo,
                    'data_resolucio' => null,
                    'created_at' => $fechaCreacion,
                    'updated_at' => $fechaInicioTrabajo ?? $fechaCreacion
                ]);
            }
        }
    }
}
