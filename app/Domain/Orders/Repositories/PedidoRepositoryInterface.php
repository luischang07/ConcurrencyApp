<?php

namespace App\Domain\Orders\Repositories;

use App\Domain\Orders\Entities\Pedido;

interface PedidoRepositoryInterface
{
  /**
   * Guarda un pedido completo con sus detalles
   */
  public function save(Pedido $pedido): Pedido;

  /**
   * Encuentra un pedido por su ID, incluye los detalles
   */
  public function findById(int $id): ?Pedido;

  /**
   * Verifica si un medicamento existe
   */
  public function medicamentoExiste(int $medicamentoId): bool;

  /**
   * Obtiene el precio unitario actual de un medicamento
   */
  public function getPrecioMedicamento(int $medicamentoId): ?float;
}
