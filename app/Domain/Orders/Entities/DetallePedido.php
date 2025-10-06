<?php

namespace App\Domain\Orders\Entities;

use App\Domain\Orders\ValueObjects\Cantidad;
use App\Domain\Orders\ValueObjects\PrecioUnitario;

class DetallePedido
{
  private ?int $id;
  private int $medicamentoId;
  private Cantidad $cantidad;
  private PrecioUnitario $precioUnitario;

  public function __construct(
    int $medicamentoId,
    Cantidad $cantidad,
    PrecioUnitario $precioUnitario,
    ?int $id = null
  ) {
    $this->id = $id;
    $this->medicamentoId = $medicamentoId;
    $this->cantidad = $cantidad;
    $this->precioUnitario = $precioUnitario;
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

  public function getPrecioUnitario(): PrecioUnitario
  {
    return $this->precioUnitario;
  }

  public function getSubtotal(): float
  {
    return $this->precioUnitario->multiply($this->cantidad);
  }

  public function toArray(): array
  {
    return [
      'id' => $this->id,
      'medicamento_id' => $this->medicamentoId,
      'cantidad' => $this->cantidad->getValue(),
      'precio_unitario' => $this->precioUnitario->getValue(),
      'subtotal' => $this->getSubtotal(),
    ];
  }
}
