<?php

namespace App\Domain\Orders\Entities;

use App\Domain\Orders\ValueObjects\Cantidad;

class DetallePedido
{
  private ?int $id;
  private int $medicamentoId;
  private Cantidad $cantidad;
  private float $precioUnitario;

  public function __construct(
    int $medicamentoId,
    Cantidad $cantidad,
    float $precioUnitario,
    ?int $id = null
  ) {
    $this->id = $id;
    $this->medicamentoId = $medicamentoId;
    $this->cantidad = $cantidad;
    $this->precioUnitario = round($precioUnitario, 2);
  }

  public function getId(): ?int
  {
    return $this->id;
  }

  public function setId(int $id): void
  {
    $this->id = $id;
  }

  public function getMedicamentoId(): int
  {
    return $this->medicamentoId;
  }

  public function getCantidad(): Cantidad
  {
    return $this->cantidad;
  }

  public function getPrecioUnitario(): float
  {
    return $this->precioUnitario;
  }

  public function getSubtotal(): float
  {
    return $this->precioUnitario * $this->cantidad->getValue();
  }

  public function equals(DetallePedido $other): bool
  {
    return $this->medicamentoId === $other->medicamentoId &&
      $this->cantidad->equals($other->cantidad) &&
      abs($this->precioUnitario - $other->precioUnitario) < 0.01;
  }

  public function toArray(): array
  {
    return [
      'id' => $this->id,
      'medicamento_id' => $this->medicamentoId,
      'cantidad' => $this->cantidad->getValue(),
      'precio_unitario' => $this->precioUnitario,
      'subtotal' => $this->getSubtotal(),
    ];
  }
}
