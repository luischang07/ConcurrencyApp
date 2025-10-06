<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\CadenasFarmaceuticas;

class CadenasFarmaceuticasSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    // Seed a few known pharmaceutical chains
    $known = [
      'Farmacia San Pablo',
      'Farmacia del Ahorro',
      'Farmacias Guadalajara',
      'Farmacia Benavides',
      'Farmacia del Pueblo',
    ];

    foreach ($known as $name) {
      DB::table('cadenas_farmaceuticas')->updateOrInsert([
        'nombre' => $name,
        'created_at' => now(),
        'updated_at' => now(),
      ]);
    }

    // Create some extra random chains using the factory
    CadenasFarmaceuticas::factory()->count(5)->create();
  }
}
