<?php

namespace App\Domain\Inventory\Repositories;

use App\Domain\Inventory\Entities\Inventario;
use App\Domain\Orders\ValueObjects\Cantidad;

interface InventarioRepositoryInterface
{
  /**
   * Encuentra el inventario de un medicamento en una sucursal específica
   */
  public function findByMedicamentoAndSucursal(int $medicamentoId, int $sucursalId): ?Inventario;

  /**
   * Guarda los cambios en el inventario
   */
  public function save(Inventario $inventario): Inventario;

  /**
   * Reserva stock de forma atómica (con locks)
   */
  public function reservarStockAtomico(int $medicamentoId, int $sucursalId, Cantidad $cantidad): bool;

  /**
   * Obtiene todos los inventarios que necesitan reabastecimiento
   */
  public function getInventariosParaReabastecer(): array;

  /**
   * Obtiene el stock total de un medicamento en todas las sucursales
   */
  public function getStockTotalMedicamento(int $medicamentoId): int;
}
