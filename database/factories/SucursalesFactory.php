<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sucursales>
 */
class SucursalesFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    return [
      'nombre' => $this->faker->company() . ' Sucursal',
      // Attempt to pick an existing chain id; if none, null (you can set one when calling the factory)
      'cadenas_farmaceuticas_id' => \App\Models\CadenasFarmaceuticas::inRandomOrder()->value('id') ?? null,
    ];
  }
}
