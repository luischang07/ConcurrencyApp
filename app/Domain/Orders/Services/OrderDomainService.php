<?php

namespace App\Domain\Orders\Services;

use App\Domain\Orders\Entities\Pedido;
use App\Domain\Orders\Entities\DetallePedido;
use App\Domain\Orders\ValueObjects\Cantidad;
use App\Domain\Orders\ValueObjects\PrecioUnitario;
use App\Domain\Orders\Repositories\PedidoRepositoryInterface;
use InvalidArgumentException;

class OrderDomainService
{
  private PedidoRepositoryInterface $pedidoRepository;

  public function __construct(PedidoRepositoryInterface $pedidoRepository)
  {
    $this->pedidoRepository = $pedidoRepository;
  }

  public function crearPedido(int $sucursalId, array $items): Pedido
  {
    if (empty($items)) {
      throw new InvalidArgumentException('El pedido debe tener al menos un item');
    }
    $this->pedidoRepository->empezarTransaccion();
    try {
      usort($items, fn($a, $b) => $a['id'] <=> $b['id']);

      $medicamentoId = array_map(fn($item) => $item['id'], $items);
      $inventario = $this->pedidoRepository->cargarInventario($medicamentoId, $sucursalId);

      $pedido = new Pedido($sucursalId);

      foreach ($items as $item) {
        $stockDisponible = $inventario->consultarStock($item['id']);
        if ($stockDisponible < $item['cantidad']) {
          throw new InvalidArgumentException("Stock insuficiente para surtir los medicamentos");
        }
      }
      foreach ($items as $item) {
        $inventario->descontarStock($item['id'], $item['cantidad']);
        $precio = $this->pedidoRepository->getPrecioMedicamento($item['id']);
        $detalle = new DetallePedido(
          $item['id'],
          new Cantidad($item['cantidad']),
          new PrecioUnitario($precio)
        );
        $pedido->addDetalle($detalle);
      }

      $pedido = $this->pedidoRepository->save($pedido, $inventario);
      $this->pedidoRepository->commit();
      return $pedido;
    } catch (\Exception $e) {
      $this->pedidoRepository->rollBack();
      throw $e;
    }
  }
}
