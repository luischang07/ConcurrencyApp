<?php

namespace App\Domain\Orders\Repositories;

use App\Domain\Orders\Entities\Pedido;
use App\Domain\Orders\Services\Inventario;

interface PedidoRepositoryInterface
{
  /**
   * Guarda un pedido completo con sus detalles y maneja la actualización de stock
   */
  public function save(Pedido $pedido, Inventario $inventario): Pedido;

  /**
   * Encuentra un pedido por su ID, incluye los detalles
   */
  public function findById(int $id): ?Pedido;

  /**
   * Verifica y reserva stock para un medicamento en una sucursal
   * Retorna true si tiene stock suficiente y lo reserva, false en caso contrario
   */
  public function reservarStock(int $medicamentoId, int $sucursalId, int $cantidad): bool;

  /**
   * Obtiene el stock disponible de un medicamento en una sucursal
   */
  public function getStockDisponible(int $medicamentoId, int $sucursalId): int;

  /**
   * Verifica si un medicamento existe
   */
  public function medicamentoExiste(int $medicamentoId): bool;

  /**
   * Obtiene el precio unitario actual de un medicamento
   */
  public function getPrecioMedicamento(int $medicamentoId): ?float;

  public function cargarInventario(array $medicamentoId, int $sucursalId):Inventario;

  public function aplicarInventario(Inventario $inventario): void;

  /**
   * Inicia una transacción (DB transaction)
   */
  public function empezarTransaccion(): void;

  /**
   * Commit de la transacción
   */
  public function commit(): void;

  /**
   * Rollback de la transacción
   */
  public function rollBack(): void;
}

