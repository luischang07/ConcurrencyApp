<?php

namespace App\Services;

use App\Models\Pedidos;
use App\Domain\Orders\Services\OrderDomainService;
use App\Domain\Orders\Repositories\PedidoRepositoryInterface;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;

class PedidosService
{
  private OrderDomainService $orderDomainService;
  private PedidoRepositoryInterface $pedidoRepository;

  public function __construct(OrderDomainService $orderDomainService, PedidoRepositoryInterface $pedidoRepository)
  {
    $this->orderDomainService = $orderDomainService;
    $this->pedidoRepository = $pedidoRepository;
  }

  public function getPaginatedWithSucursal(int $perPage = 30)
  {
    return Pedidos::with('sucursal')->orderBy('fecha_hora', 'desc')->paginate($perPage);
  }

  public function getPaginatedEnviados(int $perPage = 30)
  {
    return Pedidos::enviados()->with('sucursal')->orderBy('fecha_hora', 'desc')->paginate($perPage);
  }

  public function getPaginatedPendientes(int $perPage = 30)
  {
    return Pedidos::pendientes()->with('sucursal')->orderBy('fecha_hora', 'desc')->paginate($perPage);
  }

  public function create(array $data, array $items): Pedidos
  {
    try {
      $pedidoDominio = $this->orderDomainService->crearPedido(
        $data['sucursal'],
        $items
      );

      if (!$pedidoDominio) {
        throw new InvalidArgumentException('No se pudo crear el pedido');
      }

      return Pedidos::with('detalles.medicamento', 'sucursal')->find($pedidoDominio->getId());
    } catch (InvalidArgumentException $e) {
      throw ValidationException::withMessages([
        'medicamentos' => $e->getMessage(),
      ]);
    }
  }

  public function findById(int $id)
  {
    $pedidoDominio = $this->pedidoRepository->findById($id);

    if (!$pedidoDominio) {
      return null;
    }

    return Pedidos::with('detalles.medicamento', 'sucursal')->find($id);
  }
}
