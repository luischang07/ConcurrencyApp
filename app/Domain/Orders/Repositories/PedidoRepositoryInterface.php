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
}
