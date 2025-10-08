<?php

namespace App\Domain\Catalog\Services;

interface MedicamentoServiceInterface
{
  /**
   * Verifica si un medicamento existe
   */
  public function existe(int $medicamentoId): bool;

  /**
   * Obtiene el precio unitario de un medicamento
   */
  public function getPrecio(int $medicamentoId): ?float;

  /**
   * Obtiene información básica de un medicamento
   */
  public function findById(int $medicamentoId): ?array;
}
