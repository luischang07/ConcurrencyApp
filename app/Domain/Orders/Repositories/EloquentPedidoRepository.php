<?php

namespace App\Domain\Orders\Repositories;

use App\Domain\Orders\Entities\Pedido;
use App\Domain\Orders\Entities\DetallePedido;
use App\Domain\Orders\ValueObjects\Cantidad;
use App\Models\Pedidos;
use App\Models\DetallePedidos;
use Illuminate\Support\Facades\DB;
use DateTime;

class EloquentPedidoRepository implements PedidoRepositoryInterface
{
  public function save(Pedido $pedido): Pedido
  {
    $pedidoModel = null;
    if ($pedido->getId()) {
      $pedidoModel = Pedidos::find($pedido->getId());
      $pedidoModel->update([
        'sucursales_id' => $pedido->getSucursalId(),
        'fecha_hora' => $pedido->getFechaHora()->format('Y-m-d H:i:s'),
        'estado' => $pedido->getEstado(),
      ]);
    } else {
      $pedidoModel = Pedidos::create([
        'sucursales_id' => $pedido->getSucursalId(),
        'fecha_hora' => $pedido->getFechaHora()->format('Y-m-d H:i:s'),
        'estado' => $pedido->getEstado(),
      ]);
      $pedido->setId($pedidoModel->id);
    }

    foreach ($pedido->getDetalles() as $detalle) {
      $this->saveDetalle($pedidoModel->id, $detalle);
    }

    return $pedido;
  }

  public function findById(int $id): ?Pedido
  {
    $pedidoModel = Pedidos::with('detalles')->find($id);
    if (!$pedidoModel) {
      return null;
    }

    return $this->mapToDomain($pedidoModel);
  }

  private function saveDetalle(int $pedidoId, DetallePedido $detalle): void
  {
    if ($detalle->getId()) {
      DetallePedidos::where('id', $detalle->getId())->update([
        'medicamentos_id' => $detalle->getMedicamentoId(),
        'cantidad' => $detalle->getCantidad()->getValue(),
        'precio_unitario' => $detalle->getPrecioUnitario(),
      ]);
    } else {
      $detalleModel = DetallePedidos::create([
        'pedidos_id' => $pedidoId,
        'medicamentos_id' => $detalle->getMedicamentoId(),
        'cantidad' => $detalle->getCantidad()->getValue(),
        'precio_unitario' => $detalle->getPrecioUnitario(),
      ]);
      $detalle->setId($detalleModel->id);
    }
  }

  private function mapToDomain(Pedidos $pedidoModel): Pedido
  {
    $fechaHora = new DateTime($pedidoModel->fecha_hora);

    $pedido = new Pedido(
      $pedidoModel->sucursales_id,
      $fechaHora,
      $pedidoModel->estado,
      $pedidoModel->id
    );

    foreach ($pedidoModel->detalles as $detalleModel) {
      $detalle = new DetallePedido(
        $detalleModel->medicamentos_id,
        new Cantidad($detalleModel->cantidad),
        $detalleModel->precio_unitario,
        $detalleModel->id
      );
      $pedido->addDetalle($detalle);
    }

    return $pedido;
  }
}
