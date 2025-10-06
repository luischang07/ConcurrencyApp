<?php

namespace App\Domain\Orders\ValueObjects;

use InvalidArgumentException;

class Cantidad
{
  private int $value;

  public function __construct(int $value)
  {
    if ($value < 1) {
      throw new InvalidArgumentException('La cantidad debe ser un número positivo mayor a 0');
    }

    $this->value = $value;
  }

  public function getValue(): int
  {
    return $this->value;
  }

  public function equals(Cantidad $other): bool
  {
    return $this->value === $other->value;
  }

  public function __toString(): string
  {
    return (string) $this->value;
  }
}
