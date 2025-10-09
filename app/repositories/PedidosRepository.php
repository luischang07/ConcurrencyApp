<?php

namespace App\Repositories;

use App\Models\Pedidos;
use App\Domain\Orders\Services\OrderDomainService;
use App\Domain\Orders\Repositories\PedidoRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;

class PedidosRepository
{
  private OrderDomainService $orderDomainService;
  private PedidoRepositoryInterface $pedidoRepository;

  public function __construct(OrderDomainService $orderDomainService, PedidoRepositoryInterface $pedidoRepository)
  {
    $this->orderDomainService = $orderDomainService;
    $this->pedidoRepository = $pedidoRepository;
  }

  public function getPaginatedWithSucursal(int $perPage = 30): LengthAwarePaginator
  {
    return Pedidos::with('sucursal')->orderBy('fecha_hora', 'desc')->paginate($perPage);
  }

  public function getPaginatedEnviados(int $perPage = 30): LengthAwarePaginator
  {
    return Pedidos::enviados()->with('sucursal')->orderBy('fecha_hora', 'desc')->paginate($perPage);
  }

  public function getPaginatedPendientes(int $perPage = 30): LengthAwarePaginator
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

      return Pedidos::find($pedidoDominio->getId());
    } catch (InvalidArgumentException $e) {
      throw ValidationException::withMessages([
        'medicamentos' => $e->getMessage(),
      ]);
    }
  }

  public function findById(int $id): ?Pedidos
  {
    $pedidoDominio = $this->pedidoRepository->findById($id);

    if (!$pedidoDominio) {
      return null;
    }

    return Pedidos::with('detalles.medicamento', 'sucursal')->find($id);
  }
}
