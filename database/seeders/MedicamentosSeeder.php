<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Medicamentos;

class MedicamentosSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $fixed = [
      ['nombre_comercial' => 'Paracetamol 500mg', 'sustancia_activa' => 'Paracetamol', 'precio_unitario' => 12.50],
      ['nombre_comercial' => 'Ibuprofeno 400mg', 'sustancia_activa' => 'Ibuprofeno', 'precio_unitario' => 18.00],
      ['nombre_comercial' => 'Loratadina 10mg', 'sustancia_activa' => 'Loratadina', 'precio_unitario' => 25.75],
      ['nombre_comercial' => 'Amoxicilina 500mg', 'sustancia_activa' => 'Amoxicilina', 'precio_unitario' => 35.00],
      ['nombre_comercial' => 'Omeprazol 20mg', 'sustancia_activa' => 'Omeprazol', 'precio_unitario' => 42.80],
    ];

    foreach ($fixed as $med) {
      DB::table('medicamentos')->updateOrInsert([
        'nombre_comercial' => $med['nombre_comercial'],
      ], [
        'nombre_comercial' => $med['nombre_comercial'],
        'sustancia_activa' => $med['sustancia_activa'],
        'precio_unitario' => $med['precio_unitario'],
        'created_at' => now(),
        'updated_at' => now(),
      ]);
    }

    // Create additional random medicamentos
    Medicamentos::factory()->count(20)->create();
  }
}
