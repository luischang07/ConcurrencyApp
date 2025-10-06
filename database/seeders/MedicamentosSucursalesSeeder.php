<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Medicamentos;
use App\Models\Sucursales;

class MedicamentosSucursalesSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    // Get all medicamentos and sucursales
    $medicamentos = Medicamentos::all();
    $sucursales = Sucursales::all();

    // Create stock records for each medication in each branch
    foreach ($medicamentos as $medicamento) {
      foreach ($sucursales as $sucursal) {
        // Generate random stock between 0 and 200 for each medication per branch
        $stock = rand(0, 200);

        DB::table('medicamentos_sucursales')->updateOrInsert([
          'medicamentos_id' => $medicamento->id,
          'sucursales_id' => $sucursal->id,
        ], [
          'medicamentos_id' => $medicamento->id,
          'sucursales_id' => $sucursal->id,
          'stock' => $stock,
          'created_at' => now(),
          'updated_at' => now(),
        ]);
      }
    }
  }
}
