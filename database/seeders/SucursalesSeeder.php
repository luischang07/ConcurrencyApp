<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Sucursales;
use App\Models\CadenasFarmaceuticas;

class SucursalesSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    // For each existing chain, create 2-3 fixed sucursales
    $chains = CadenasFarmaceuticas::all();

    foreach ($chains as $chain) {
      Sucursales::factory()->count(2)->create([
        'cadenas_farmaceuticas_id' => $chain->id,
      ]);
    }

    // Create some extra random sucursales (will attempt to pick random chain id)
    Sucursales::factory()->count(10)->create();
  }
}
