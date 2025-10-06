<?php

namespace App\Domain\Orders\Entities;

use DateTime;
use InvalidArgumentException;

class Pedido
{
  private ?int $id;
  private int $sucursalId;
  private DateTime $fechaHora;
  private string $estado;
  private array $detalles;

  public const ESTADO_ENVIADO = 'ENVIADO';
  public const ESTADO_PENDIENTE = 'PENDIENTE';
  public const ESTADO_COMPLETADO = 'COMPLETADO';
  public const ESTADO_CANCELADO = 'CANCELADO';

  public function __construct(
    int $sucursalId,
    ?DateTime $fechaHora = null,
    string $estado = self::ESTADO_ENVIADO,
    ?int $id = null
  ) {
    $this->id = $id;
    $this->sucursalId = $sucursalId;
    $this->fechaHora = $fechaHora ?? new DateTime();
    $this->estado = $estado;
    $this->detalles = [];

    $this->validateEstado($estado);
  }

  public function getId(): ?int
  {
    return $this->id;
  }

  public function setId(int $id): void
  {
    $this->id = $id;
  }

  public function getSucursalId(): int
  {
    return $this->sucursalId;
  }

  public function getFechaHora(): DateTime
  {
    return $this->fechaHora;
  }

  public function getEstado(): string
  {
    return $this->estado;
  }

  public function cambiarEstado(string $nuevoEstado): void
  {
    $this->validateEstado($nuevoEstado);
    $this->estado = $nuevoEstado;
  }

  public function addDetalle(DetallePedido $detalle): void
  {
    if (empty($this->detalles)) {
      $this->detalles = [];
    }

    // Verificar que no exista ya el mismo medicamento
    foreach ($this->detalles as $detalleExistente) {
      if ($detalleExistente->getMedicamentoId() === $detalle->getMedicamentoId()) {
        throw new InvalidArgumentException(
          'Ya existe un detalle para el medicamento ID: ' . $detalle->getMedicamentoId()
        );
      }
    }

    $this->detalles[] = $detalle;
  }

  public function getDetalles(): array
  {
    return $this->detalles;
  }

  public function getTotal(): float
  {
    $total = 0.0;
    foreach ($this->detalles as $detalle) {
      $total += $detalle->getSubtotal();
    }
    return round($total, 2);
  }

  public function getCantidadItems(): int
  {
    return count($this->detalles);
  }

  public function tieneDetalles(): bool
  {
    return !empty($this->detalles);
  }

  private function validateEstado(string $estado): void
  {
    $estadosValidos = [
      self::ESTADO_ENVIADO,
      self::ESTADO_PENDIENTE,
      self::ESTADO_COMPLETADO,
      self::ESTADO_CANCELADO,
    ];

    if (!in_array($estado, $estadosValidos)) {
      throw new InvalidArgumentException('Estado de pedido inválido: ' . $estado);
    }
  }

  public function toArray(): array
  {
    return [
      'id' => $this->id,
      'sucursal_id' => $this->sucursalId,
      'fecha_hora' => $this->fechaHora->format('Y-m-d H:i:s'),
      'estado' => $this->estado,
      'total' => $this->getTotal(),
      'cantidad_items' => $this->getCantidadItems(),
      'detalles' => array_map(fn($detalle) => $detalle->toArray(), $this->detalles),
    ];
  }
}
