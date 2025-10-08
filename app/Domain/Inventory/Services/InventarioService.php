<?php

namespace App\Domain\Inventory\Services;

use App\Domain\Inventory\Repositories\InventarioRepositoryInterface;
use App\Domain\Orders\ValueObjects\Cantidad;
use InvalidArgumentException;

class InventarioService
{
  private InventarioRepositoryInterface $inventarioRepository;

  public function __construct(InventarioRepositoryInterface $inventarioRepository)
  {
    $this->inventarioRepository = $inventarioRepository;
  }

  public function reservarStock(int $medicamentoId, int $sucursalId, Cantidad $cantidad): void
  {
    $inventario = $this->inventarioRepository->findByMedicamentoAndSucursal($medicamentoId, $sucursalId);

    if (!$inventario) {
      throw new InvalidArgumentException(
        "No existe inventario para medicamento ID {$medicamentoId} en sucursal ID {$sucursalId}"
      );
    }

    if (!$inventario->puedeReservar($cantidad)) {
      throw new InvalidArgumentException(
        "Stock insuficiente para medicamento ID {$medicamentoId}. " .
          "Stock disponible: {$inventario->getStockActual()->getValue()}, " .
          "Solicitado: {$cantidad->getValue()}"
      );
    }

    $inventario->reservarStock($cantidad);
    $this->inventarioRepository->save($inventario);
  }

  public function verificarDisponibilidad(int $medicamentoId, int $sucursalId, Cantidad $cantidad): bool
  {
    $inventario = $this->inventarioRepository->findByMedicamentoAndSucursal($medicamentoId, $sucursalId);

    return $inventario && $inventario->puedeReservar($cantidad);
  }

  public function getStockDisponible(int $medicamentoId, int $sucursalId): int
  {
    $inventario = $this->inventarioRepository->findByMedicamentoAndSucursal($medicamentoId, $sucursalId);

    return $inventario ? $inventario->getStockActual()->getValue() : 0;
  }

  public function getInventariosParaReabastecer(): array
  {
    return $this->inventarioRepository->getInventariosParaReabastecer();
  }
}
