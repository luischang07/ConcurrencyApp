<?php

namespace App\Domain\Catalog\Repositories;

use Illuminate\Support\Collection;

interface MedicamentoRepositoryInterface
{
  /**
   * Find medicamento by ID
   */
  public function findById(int $medicamentoId): ?object;

  /**
   * Check if medicamento exists
   */
  public function exists(int $medicamentoId): bool;

  /**
   * Get precio unitario for a medicamento
   */
  public function getPrecioUnitario(int $medicamentoId): ?float;

  /**
   * Bloquea y obtiene los stocks disponibles para una lista de medicamentos en una sucursal
   * @param array $medicamentosIds Array de IDs de medicamentos
   * @param int $sucursalId ID de la sucursal
   * @return Collection Colección de stocks indexada por medicamento_id
   */
  public function bloquearStocksPorSucursal(array $medicamentosIds, int $sucursalId): Collection;
}
