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

        // Obtenemos todas las sedes, técnicos y clientes
        $sedes = Sede::all();
        $categorias = Categoria::with('subcategorias')->get();

        $estados = ['Sense assignar', 'Assignada', 'En treball', 'Resolta', 'Tancada'];
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
            'La red WiFi de invitados no funciona'
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
            'Varios clientes están intentando conectarse a la red "Empresa-Guest" pero les marca "Sin conexión a internet".'
        ];

        foreach ($sedes as $sede) {
            // Obtener clientes y técnicos específicos para esta sede
            $clientesSede = User::where('rol', 'client')->where('sede_id', $sede->id)->get();
            $tecnicosSede = User::where('rol', 'tecnic')->where('sede_id', $sede->id)->get();

            if ($clientesSede->isEmpty() || $tecnicosSede->isEmpty()) {
                continue; // Saltar si la sede no tiene suficientes usuarios
            }

            // Generamos entre 5 y 8 incidencias por Sede
            $numIncidencias = rand(5, 8);

            for ($i = 0; $i < $numIncidencias; $i++) {
                $cliente = $clientesSede->random();
                
                // Seleccionamos un estado aleatorio
                $estado = $estados[array_rand($estados)];
                
                // Si está 'Sense assignar', no tiene técnico. Si no, cogemos uno.
                $tecnico = ($estado === 'Sense assignar') ? null : $tecnicosSede->random();
                
                // Prioridad
                $prioridad = $prioridades[array_rand($prioridades)];
                
                // Categoría y Subcategoría (nos aseguramos de que coincidan)
                $categoria = $categorias->random();
                $subcategoria = $categoria->subcategorias->count() > 0 ? $categoria->subcategorias->random() : null;
                
                // Si por algún motivo la categoría no tiene subcategorías, la saltamos o pillamos otra
                if (!$subcategoria) {
                    continue; 
                }

                $indiceDatos = array_rand($titulos);

                // Fechas
                $diasAtras = rand(1, 45); // Hace 1 a 45 días
                $fechaCreacion = Carbon::now()->subDays($diasAtras)->subHours(rand(1, 10));
                
                $fechaInicioTrabajo = null;
                $fechaResolucion = null;

                if (in_array($estado, ['En treball', 'Resolta', 'Tancada'])) {
                    $fechaInicioTrabajo = (clone $fechaCreacion)->addHours(rand(1, 24));
                }

                if (in_array($estado, ['Resolta', 'Tancada'])) {
                    $fechaResolucion = (clone $fechaInicioTrabajo)->addHours(rand(2, 48));
                }

                Incidencia::create([
                    'titol' => $titulos[$indiceDatos],
                    'descripcio' => $descripciones[$indiceDatos],
                    'client_id' => $cliente->id,
                    'tecnic_id' => $tecnico ? $tecnico->id : null,
                    'sede_id' => $sede->id,
                    'categoria_id' => $categoria->id,
                    'subcategoria_id' => $subcategoria->id,
                    'estat' => $estado,
                    'prioritat' => $prioridad,
                    'data_creacio' => $fechaCreacion,
                    'data_inici_treball' => $fechaInicioTrabajo,
                    'data_resolucio' => $fechaResolucion,
                    'created_at' => $fechaCreacion,
                    'updated_at' => $fechaResolucion ?? ($fechaInicioTrabajo ?? $fechaCreacion)
                ]);
            }
        }
    }
}
