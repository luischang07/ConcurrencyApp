<?php

namespace App\Domain\Inventory;

use App\Domain\Inventory\ReservaInventario;
use App\Models\MedicamentosSucursales;
use InvalidArgumentException;
use Illuminate\Support\Facades\DB;

class InventarioService
{
  /**
   * Reserva stock para una reserva de inventario completa
   */
  public function reservarStock(ReservaInventario $reserva): void
  {
    if (DB::transactionLevel() > 0) {
      $this->ejecutarReserva($reserva);
    } else {
      DB::transaction(function () use ($reserva) {
        $this->ejecutarReserva($reserva);
      });
    }
  }

  /**
   * Verifica si hay suficiente stock para todos los medicamentos de la reserva
   */
  public function verificarDisponibilidad(ReservaInventario $reserva): bool
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
  public function getInformacionStock(ReservaInventario $reserva): array
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

  /**
   * Ejecuta la reserva de stock - asume que ya está en transacción y con locks
   */
  private function ejecutarReserva(ReservaInventario $reserva): void
  {
    $sucursalId = $reserva->getSucursalId();

    // Solo decrementar el stock - las validaciones y locks ya se hicieron
    foreach ($reserva->getMedicamentos() as $medicamento) {
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
