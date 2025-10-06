<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\CadenasFarmaceuticasSeeder;
use Database\Seeders\MedicamentosSeeder;
use Database\Seeders\SucursalesSeeder;
use Database\Seeders\MedicamentosSucursalesSeeder;

class DatabaseSeeder extends Seeder
{
  /**
   * Seed the application's database.
   */
  public function run(): void
  {

    User::factory()->create([
      'name' => 'Test User',
      'email' => 'test@example.com',
    ]);

    // Seed pharmaceutical chains
    $this->call(CadenasFarmaceuticasSeeder::class);
    // Seed sucursales (depends on cadenas)
    $this->call(SucursalesSeeder::class);
    // Seed medicamentos
    $this->call(MedicamentosSeeder::class);
    // Seed stock for medicamentos per sucursal (depends on both medicamentos and sucursales)
    $this->call(MedicamentosSucursalesSeeder::class);
  }
}
