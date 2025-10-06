<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Medicamentos>
 */
class MedicamentosFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    return [
      'nombre_comercial' => $this->faker->unique()->catchPhrase(),
      'sustancia_activa' => $this->faker->optional(0.8)->word(),
      'precio_unitario' => $this->faker->randomFloat(2, 5, 500), // Price between $5.00 and $500.00
    ];
  }
}
