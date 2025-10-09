<?php

namespace App\Domain\Orders\Services;

use App\Domain\Orders\Entities\Pedido;
use App\Domain\Orders\Entities\DetallePedido;
use App\Domain\Orders\ValueObjects\Cantidad;
use App\Domain\Orders\Repositories\PedidoRepositoryInterface;
use App\Domain\Inventory\InventarioService;
use App\Domain\Inventory\ReservaInventario;
use App\Domain\Catalog\Services\MedicamentoServiceInterface;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class OrderDomainService
{
  private PedidoRepositoryInterface $pedidoRepository;
  private InventarioService $inventarioService;
  private MedicamentoServiceInterface $medicamentoService;

  public function __construct(
    PedidoRepositoryInterface $pedidoRepository,
    InventarioService $inventarioService,
    MedicamentoServiceInterface $medicamentoService
  ) {
    $this->pedidoRepository = $pedidoRepository;
    $this->inventarioService = $inventarioService;
    $this->medicamentoService = $medicamentoService;
  }

  public function crearPedido(int $sucursalId, array $items): Pedido
  {
    if (empty($items)) {
      throw new InvalidArgumentException('El pedido debe tener al menos un item');
    }

    return DB::transaction(function () use ($sucursalId, $items) {
      $medicamentosParaReserva = [];
      $detallesPedido = [];

      // Ordenar por ID
      usort($items, fn($a, $b) => $a['id'] <=> $b['id']);

      $medicamentosIds = array_map(fn($item) => (int) $item['id'], $items);

      $stocksDisponibles = $this->medicamentoService->bloquearStocksPorSucursal($medicamentosIds, $sucursalId);

      foreach ($items as $item) {
        $detalleDatos = $this->validarItem($item);
        $medicamentoId = $detalleDatos['medicamentoId'];
        $cantidadSolicitada = $detalleDatos['cantidad']->getValue();

        if (!$stocksDisponibles->has($medicamentoId)) {
          throw new InvalidArgumentException(
            "No existe inventario para medicamento ID {$medicamentoId} en sucursal ID {$sucursalId}"
          );
        }

        $stockDisponible = $stocksDisponibles->get($medicamentoId)->stock;
        if ($stockDisponible < $cantidadSolicitada) {
          throw new InvalidArgumentException(
            "Stock insuficiente para medicamento ID {$medicamentoId}. " .
              "Stock disponible: {$stockDisponible}, " .
              "Solicitado: {$cantidadSolicitada}"
          );
        }

        $medicamentosParaReserva[] = [
          'medicamentoId' => $medicamentoId,
          'cantidad' => $cantidadSolicitada
        ];

        $detallesPedido[] = $detalleDatos;
      }

      $reservaInventario = new ReservaInventario($sucursalId, $medicamentosParaReserva);
      $this->inventarioService->reservarStock($reservaInventario);

      $pedido = new Pedido($sucursalId);

      foreach ($detallesPedido as $detalleDatos) {
        $detalle = new DetallePedido(
          $detalleDatos['medicamentoId'],
          $detalleDatos['cantidad'],
          $detalleDatos['precio']
        );
        $pedido->addDetalle($detalle);
      }

      return $this->pedidoRepository->save($pedido);
    });
  }

  private function validarItem(array $item): array
  {
    if (!isset($item['id']) || !isset($item['cantidad'])) {
      throw new InvalidArgumentException('Faltan datos del medicamento (id o cantidad)');
    }

    $medicamentoId = (int) $item['id'];
    $cantidadValue = (int) $item['cantidad'];

    if (!$this->medicamentoService->existe($medicamentoId)) {
      throw new InvalidArgumentException('El medicamento ID ' . $medicamentoId . ' no existe');
    }

    $precio = $this->medicamentoService->getPrecio($medicamentoId);
    if ($precio === null) {
      throw new InvalidArgumentException('No se pudo obtener el precio del medicamento ID ' . $medicamentoId);
    }

    $cantidad = new Cantidad($cantidadValue);

    return [
      'medicamentoId' => $medicamentoId,
      'cantidad' => $cantidad,
      'precio' => $precio
    ];
  }
}
