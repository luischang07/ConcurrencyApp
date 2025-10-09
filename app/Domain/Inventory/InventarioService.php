<?php

namespace App\Domain\Inventory;

use App\Domain\Inventory\Inventario;
use App\Models\MedicamentosSucursales;
use InvalidArgumentException;
use Illuminate\Support\Facades\DB;

class InventarioService
{
  /**
   * Reserva stock para una reserva de inventario completa
   */
  public function procesarInventario(Inventario $inventario): void
  {
    if (DB::transactionLevel() > 0) {
      $this->aplicarInventario($inventario);
    } else {
      DB::transaction(function () use ($inventario) {
        $this->aplicarInventario($inventario);
      });
    }
  }

  /**
   * Verifica si hay suficiente stock para todos los medicamentos de la reserva
   */
  public function verificarDisponibilidad(Inventario $reserva): bool
  {
    $sucursalId = $reserva->getSucursalId();

    foreach ($reserva->getMedicamentos() as $medicamento) {
      $medicamentoId = $medicamento['medicamentoId'];
      $cantidadSolicitada = $medicamento['cantidad'];

      $stockDisponible = $this->getStockDisponible($medicamentoId, $sucursalId);

      if ($stockDisponible < $cantidadSolicitada) {
        return false;
      }
    }

    return true;
  }

  /**
   * Obtiene el stock disponible de un medicamento en una sucursal
   */
  public function getStockDisponible(int $medicamentoId, int $sucursalId): int
  {
    $medicamentoSucursal = MedicamentosSucursales::findByMedicamentoAndSucursal($medicamentoId, $sucursalId);
    return $medicamentoSucursal ? $medicamentoSucursal->stock : 0;
  }

  /**
   * Obtiene información de stock para todos los medicamentos de la reserva
   */
  public function getInformacionStock(Inventario $reserva): array
  {
    $sucursalId = $reserva->getSucursalId();
    $informacion = [];

    foreach ($reserva->getMedicamentos() as $medicamento) {
      $medicamentoId = $medicamento['medicamentoId'];
      $cantidadSolicitada = $medicamento['cantidad'];
      $stockDisponible = $this->getStockDisponible($medicamentoId, $sucursalId);

      $informacion[] = [
        'medicamentoId' => $medicamentoId,
        'cantidadSolicitada' => $cantidadSolicitada,
        'stockDisponible' => $stockDisponible,
        'suficiente' => $stockDisponible >= $cantidadSolicitada
      ];
    }

    return $informacion;
  }

  private function aplicarInventario(Inventario $inventario): void
  {
    $sucursalId = $inventario->getSucursalId();

    foreach ($inventario->getMedicamentos() as $medicamento) {
      $medicamentoId = $medicamento['medicamentoId'];
      $cantidadSolicitada = $medicamento['cantidad'];

      $rowsAffected = MedicamentosSucursales::where('medicamento_id', $medicamentoId)
        ->where('sucursal_id', $sucursalId)
        ->decrement('stock', $cantidadSolicitada);

      if ($rowsAffected === 0) {
        throw new InvalidArgumentException(
          "No se pudo actualizar el stock para medicamento ID {$medicamentoId} en sucursal ID {$sucursalId}"
        );
      }
    }
  }
}
