<?php

namespace App\Domain\Inventory;

use InvalidArgumentException;

class Inventario
{
  private int $sucursalId;
  private array $medicamentos;

  /**
   * @param int $sucursalId
   * @param array $medicamentos Array con formato: [['medicamentoId' => int, 'cantidad' => int], ...]
   */
  public function __construct(int $sucursalId, array $medicamentos)
  {
    if ($sucursalId <= 0) {
      throw new InvalidArgumentException('El ID de sucursal debe ser mayor a 0');
    }

    if (empty($medicamentos)) {
      throw new InvalidArgumentException('Debe especificar al menos un medicamento para reservar');
    }

    $this->sucursalId = $sucursalId;
    $this->medicamentos = $this->validarMedicamentos($medicamentos);
  }

  public function getSucursalId(): int
  {
    return $this->sucursalId;
  }

  public function getMedicamentos(): array
  {
    return $this->medicamentos;
  }

  public function getTotalMedicamentos(): int
  {
    return count($this->medicamentos);
  }

  public function getTotalUnidades(): int
  {
    return array_sum(array_column($this->medicamentos, 'cantidad'));
  }

  public function contieneMedicamento(int $medicamentoId): bool
  {
    foreach ($this->medicamentos as $medicamento) {
      if ($medicamento['medicamentoId'] === $medicamentoId) {
        return true;
      }
    }
    return false;
  }

  public function getCantidadMedicamento(int $medicamentoId): ?int
  {
    foreach ($this->medicamentos as $medicamento) {
      if ($medicamento['medicamentoId'] === $medicamentoId) {
        return $medicamento['cantidad'];
      }
    }
    return null;
  }

  private function validarMedicamentos(array $medicamentos): array
  {
    $medicamentosValidados = [];
    $medicamentosIds = [];

    foreach ($medicamentos as $item) {
      if (!isset($item['medicamentoId']) || !isset($item['cantidad'])) {
        throw new InvalidArgumentException('Cada medicamento debe tener medicamentoId y cantidad');
      }

      $medicamentoId = (int) $item['medicamentoId'];
      $cantidad = (int) $item['cantidad'];

      if ($medicamentoId <= 0) {
        throw new InvalidArgumentException('El ID del medicamento debe ser mayor a 0');
      }

      if ($cantidad <= 0) {
        throw new InvalidArgumentException('La cantidad debe ser mayor a 0');
      }

      if (in_array($medicamentoId, $medicamentosIds)) {
        throw new InvalidArgumentException("El medicamento ID {$medicamentoId} está duplicado");
      }

      $medicamentosIds[] = $medicamentoId;
      $medicamentosValidados[] = [
        'medicamentoId' => $medicamentoId,
        'cantidad' => $cantidad
      ];
    }

    // Ordenar por ID
    usort($medicamentosValidados, fn($a, $b) => $a['medicamentoId'] <=> $b['medicamentoId']);

    return $medicamentosValidados;
  }
}
