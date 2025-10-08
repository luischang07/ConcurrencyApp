<?php

namespace App\Domain\Inventory\ValueObjects;

use InvalidArgumentException;

class Stock
{
  private int $value;

  public function __construct(int $value)
  {
    if ($value < 0) {
      throw new InvalidArgumentException('El stock no puede ser negativo');
    }

    $this->value = $value;
  }

  public function getValue(): int
  {
    return $this->value;
  }

  public function equals(Stock $other): bool
  {
    return $this->value === $other->value;
  }

  public function add(Stock $other): Stock
  {
    return new Stock($this->value + $other->value);
  }

  public function subtract(Stock $other): Stock
  {
    $newValue = $this->value - $other->value;

    if ($newValue < 0) {
      throw new InvalidArgumentException('La operación resultaría en stock negativo');
    }

    return new Stock($newValue);
  }

  public function isGreaterThan(Stock $other): bool
  {
    return $this->value > $other->value;
  }

  public function isLessThanOrEqual(Stock $other): bool
  {
    return $this->value <= $other->value;
  }

  public function __toString(): string
  {
    return (string) $this->value;
  }
}
