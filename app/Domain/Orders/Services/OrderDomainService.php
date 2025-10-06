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

  /**
   * Crea un nuevo pedido con validaciones de negocio
   */
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

  /**
   * Valida y agrega un detalle al pedido
   */
  private function validarYAgregarDetalle(Pedido $pedido, array $item): void
  {
    if (!isset($item['id']) || !isset($item['cantidad'])) {
      throw new InvalidArgumentException('Faltan datos del medicamento (id o cantidad)');
    }

    $medicamentoId = (int) $item['id'];
    $cantidadValue = (int) $item['cantidad'];

    // Validar que el medicamento existe
    if (!$this->pedidoRepository->medicamentoExiste($medicamentoId)) {
      throw new InvalidArgumentException('El medicamento ID ' . $medicamentoId . ' no existe');
    }

    // Obtener precio actual del medicamento
    $precio = $this->pedidoRepository->getPrecioMedicamento($medicamentoId);
    if ($precio === null) {
      throw new InvalidArgumentException('No se pudo obtener el precio del medicamento ID ' . $medicamentoId);
    }

    // Crear value objects
    $cantidad = new Cantidad($cantidadValue);
    $precioUnitario = new PrecioUnitario($precio);

    // Validar stock disponible
    $stockDisponible = $this->pedidoRepository->getStockDisponible($medicamentoId, $pedido->getSucursalId());
    if ($stockDisponible < $cantidad->getValue()) {
      throw new InvalidArgumentException(
        "Stock insuficiente para medicamento ID {$medicamentoId}. " .
          "Stock disponible: {$stockDisponible}, solicitado: {$cantidad->getValue()}"
      );
    }

    // Reservar stock
    $stockReservado = $this->pedidoRepository->reservarStock(
      $medicamentoId,
      $pedido->getSucursalId(),
      $cantidad->getValue()
    );

    if (!$stockReservado) {
      throw new InvalidArgumentException(
        "No se pudo reservar el stock para medicamento ID {$medicamentoId}"
      );
    }

    // Crear y agregar detalle
    $detalle = new DetallePedido($medicamentoId, $cantidad, $precioUnitario);
    $pedido->addDetalle($detalle);
  }
}
