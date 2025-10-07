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
  //GENERAR TRANSACCION DESDE AQUI
  {


    if (empty($items)) {
      throw new InvalidArgumentException('El pedido debe tener al menos un item');
    }
  $this->pedidoRepository->empezarTransaccion();
    try{
    $medicamentoId = array_map(fn($item) => $item['id'], $items);
    $inventario = $this->pedidoRepository->cargarInventario($medicamentoId, $sucursalId);


    $pedido = new Pedido($sucursalId);

    foreach($items as $item) {
      $stockDisponible = $inventario->consultarStock($item['id']);
      if($stockDisponible < $item['cantidad']){
        throw new InvalidArgumentException("Stock insuficiente para surtir los medicamentos");
      }
    }
    foreach($items as $item){
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
    
  } catch(\Exception $e){
    $this->pedidoRepository->rollBack();
    throw $e;
  }
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
    $precioUnitario = new PrecioUnitario($precio);

    $stockDisponible = $this->pedidoRepository->getStockDisponible($medicamentoId, $pedido->getSucursalId());
    if ($stockDisponible < $cantidad->getValue()) {
      throw new InvalidArgumentException(
        "Stock insuficiente para medicamento ID {$medicamentoId}. " .
          "Stock disponible: {$stockDisponible}, solicitado: {$cantidad->getValue()}"
      );
    }

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

    $detalle = new DetallePedido($medicamentoId, $cantidad, $precioUnitario);
    $pedido->addDetalle($detalle);
  }
}
