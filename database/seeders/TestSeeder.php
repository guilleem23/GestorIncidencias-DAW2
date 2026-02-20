<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\Incidencia;
use App\Models\Sede;
use App\Models\User;
use Illuminate\Database\Seeder;

class TestSeeder extends Seeder
{
	public function run(): void
	{
		// IMPORTANT:
		// Este seeder asume que ya has ejecutado DatabaseSeeder.
		// No crea sedes/usuarios/categorías nuevas: solo añade 2 incidencias de prueba.

		$bcn = Sede::where('nom', 'Barcelona')->first();
		$clientJoan = User::where('email', 'client@empresa.com')->first();
		$tecnicPepe = User::where('email', 'tecnic@empresa.com')->first();
		$software = Categoria::where('nom', 'Software')->first();
		$subAccesRemot = $software?->subcategorias()->where('nom', 'Accés remot')->first();

		if (!$bcn || !$clientJoan || !$tecnicPepe || !$software || !$subAccesRemot) {
			throw new \RuntimeException('Faltan datos base. Ejecuta primero: php artisan db:seed');
		}

		// Helper to create incidencias with extra (non-fillable) fields
		$createOrUpdateIncidencia = function (array $data) {
			$incidencia = Incidencia::firstOrNew(['titol' => $data['titol']]);
			$incidencia->titol = $data['titol'];
			$incidencia->descripcio = $data['descripcio'];
			$incidencia->estat = $data['estat'];
			$incidencia->sede_id = $data['sede_id'];
			$incidencia->categoria_id = $data['categoria_id'];
			$incidencia->subcategoria_id = $data['subcategoria_id'];
			$incidencia->client_id = $data['client_id'];
			$incidencia->tecnic_id = $data['tecnic_id'] ?? null;

			if (array_key_exists('prioritat', $data)) {
				$incidencia->prioritat = $data['prioritat'];
			}

			if (array_key_exists('data_creacio', $data)) {
				$incidencia->data_creacio = $data['data_creacio'];
				$incidencia->created_at = $data['data_creacio'];
			}

			if (array_key_exists('data_inici_treball', $data)) {
				$incidencia->data_inici_treball = $data['data_inici_treball'];
			}

			if (array_key_exists('data_resolucio', $data)) {
				$incidencia->data_resolucio = $data['data_resolucio'];
			}

			if (array_key_exists('updated_at', $data)) {
				$incidencia->updated_at = $data['updated_at'];
			}

			$incidencia->save();
			return $incidencia;
		};

		// Solo 2 incidencias SIN ASIGNAR (son las que muestra el panel del gestor)
		$createOrUpdateIncidencia([
			'titol' => '[TEST] No puedo entrar por escritorio remoto',
			'descripcio' => 'Al intentar conectarme por escritorio remoto aparece un error de autenticación. Necesito acceso hoy.',
			'estat' => 'Sense assignar',
			'prioritat' => 'alta',
			'sede_id' => $bcn->id,
			'categoria_id' => $software->id,
			'subcategoria_id' => $subAccesRemot->id,
			'client_id' => $clientJoan->id,
			'tecnic_id' => null,
			'data_creacio' => now()->subHours(3),
			'updated_at' => now()->subHours(3),
		]);

		$createOrUpdateIncidencia([
			'titol' => '[TEST] El acceso remoto va muy lento',
			'descripcio' => 'La conexión remota funciona pero con mucha latencia. Se vuelve imposible trabajar con normalidad.',
			'estat' => 'Sense assignar',
			'prioritat' => 'mitjana',
			'sede_id' => $bcn->id,
			'categoria_id' => $software->id,
			'subcategoria_id' => $subAccesRemot->id,
			'client_id' => $clientJoan->id,
			'tecnic_id' => null,
			'data_creacio' => now()->subDays(1),
			'updated_at' => now()->subDays(1),
		]);

	}
}

