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
        $stockMinimo = rand(5, 20);
        $stockMaximo = rand(100, 500);

        DB::table('medicamentos_sucursales')->updateOrInsert([
          'sucursal_id' => $sucursal->id,
          'medicamento_id' => $medicamento->id,
        ], [
          'medicamento_id' => $medicamento->id,
          'sucursal_id' => $sucursal->id,
          'stock' => $stock,
          'stockMinimo' => $stockMinimo,
          'stockMaximo' => $stockMaximo,
          'created_at' => now(),
          'updated_at' => now(),
        ]);
      }
    }
  }
}
