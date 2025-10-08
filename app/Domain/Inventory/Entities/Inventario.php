<?php

namespace App\Domain\Inventory\Entities;

use App\Domain\Inventory\ValueObjects\Stock;
use App\Domain\Orders\ValueObjects\Cantidad;
use InvalidArgumentException;

class Inventario
{
  private int $medicamentoId;
  private int $sucursalId;
  private Stock $stockActual;
  private Stock $stockMinimo;
  private Stock $stockMaximo;

  public function __construct(
    int $medicamentoId,
    int $sucursalId,
    Stock $stockActual,
    Stock $stockMinimo,
    Stock $stockMaximo
  ) {
    $this->medicamentoId = $medicamentoId;
    $this->sucursalId = $sucursalId;
    $this->stockActual = $stockActual;
    $this->stockMinimo = $stockMinimo;
    $this->stockMaximo = $stockMaximo;

    $this->validarCoherenciaStock();
  }

  public function puedeReservar(Cantidad $cantidad): bool
  {
    return $this->stockActual->getValue() >= $cantidad->getValue();
  }

  public function reservarStock(Cantidad $cantidad): void
  {
    if (!$this->puedeReservar($cantidad)) {
      throw new InvalidArgumentException(
        "Stock insuficiente. Disponible: {$this->stockActual->getValue()}, Solicitado: {$cantidad->getValue()}"
      );
    }

    $nuevoStock = new Stock($this->stockActual->getValue() - $cantidad->getValue());
    $this->stockActual = $nuevoStock;
  }

  public function necesitaReabastecimiento(): bool
  {
    return $this->stockActual->getValue() <= $this->stockMinimo->getValue();
  }

  public function estaCompleto(): bool
  {
    return $this->stockActual->getValue() >= $this->stockMaximo->getValue();
  }

  // Getters
  public function getMedicamentoId(): int
  {
    return $this->medicamentoId;
  }

  public function getSucursalId(): int
  {
    return $this->sucursalId;
  }

  public function getStockActual(): Stock
  {
    return $this->stockActual;
  }

  public function getStockMinimo(): Stock
  {
    return $this->stockMinimo;
  }

  public function getStockMaximo(): Stock
  {
    return $this->stockMaximo;
  }

  private function validarCoherenciaStock(): void
  {
    if ($this->stockMinimo->getValue() > $this->stockMaximo->getValue()) {
      throw new InvalidArgumentException('El stock mínimo no puede ser mayor al stock máximo');
    }
  }
}
