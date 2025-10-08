<?php

namespace App\Domain\Orders\Services;

use App\Domain\Orders\Entities\Pedido;
use App\Domain\Orders\Entities\DetallePedido;
use App\Domain\Orders\ValueObjects\Cantidad;
use App\Domain\Orders\Repositories\PedidoRepositoryInterface;
use App\Domain\Inventory\Services\InventarioService;
use InvalidArgumentException;

class OrderDomainService
{
  private PedidoRepositoryInterface $pedidoRepository;
  private InventarioService $inventarioService;

  public function __construct(
    PedidoRepositoryInterface $pedidoRepository,
    InventarioService $inventarioService
  ) {
    $this->pedidoRepository = $pedidoRepository;
    $this->inventarioService = $inventarioService;
  }

  public function crearPedido(int $sucursalId, array $items): Pedido
  {
    if (empty($items)) {
      throw new InvalidArgumentException('El pedido debe tener al menos un item');
    }

    $pedido = new Pedido($sucursalId);

    // Ordenar por ID
    usort($items, fn($a, $b) => $a['id'] <=> $b['id']);

    foreach ($items as $item) {
      $this->validarYAgregarDetalle($pedido, $item);
    }

    return $this->pedidoRepository->save($pedido);
  }

  private function validarYAgregarDetalle(Pedido $pedido, array $item): void
  {
    if (!isset($item['id']) || !isset($item['cantidad'])) {
      throw new InvalidArgumentException('Faltan datos del medicamento (id o cantidad)');
    }

    $medicamentoId = (int) $item['id'];
    $cantidadValue = (int) $item['cantidad'];

    if (!$this->pedidoRepository->medicamentoExiste($medicamentoId)) {
      throw new InvalidArgumentException('El medicamento ID ' . $medicamentoId . ' no existe');
    }

    $precio = $this->pedidoRepository->getPrecioMedicamento($medicamentoId);
    if ($precio === null) {
      throw new InvalidArgumentException('No se pudo obtener el precio del medicamento ID ' . $medicamentoId);
    }

    $cantidad = new Cantidad($cantidadValue);

    // Usar InventarioService para verificar y reservar stock
    $this->inventarioService->reservarStock($medicamentoId, $pedido->getSucursalId(), $cantidad);

    $detalle = new DetallePedido($medicamentoId, $cantidad, $precio);
    $pedido->addDetalle($detalle);
  }
}
