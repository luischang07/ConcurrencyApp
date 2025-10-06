<?php

namespace App\Domain\Orders\ValueObjects;

use InvalidArgumentException;

class PrecioUnitario
{
  private float $value;

  public function __construct(float $value)
  {
    if ($value <= 0) {
      throw new InvalidArgumentException('El precio unitario debe ser mayor a 0');
    }

    $this->value = round($value, 2);
  }

  public function getValue(): float
  {
    return $this->value;
  }

  public function equals(PrecioUnitario $other): bool
  {
    return abs($this->value - $other->value) < 0.01;
  }

  public function multiply(Cantidad $cantidad): float
  {
    return $this->value * $cantidad->getValue();
  }

  public function __toString(): string
  {
    return number_format($this->value, 2);
  }
}
