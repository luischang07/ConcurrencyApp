<?php

namespace Database\Factories;

use App\Models\Medicamentos;
use App\Models\Sucursales;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MedicamentosSucursales>
 */
class MedicamentosSucursalesFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    return [
      'sucursal_id' => Sucursales::factory(),
      'medicamento_id' => Medicamentos::factory(),
      'stock' => $this->faker->numberBetween(0, 500),
      'stockMinimo' => $this->faker->numberBetween(5, 20),
      'stockMaximo' => $this->faker->numberBetween(100, 1000),
    ];
  }
}
