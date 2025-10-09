<?php

namespace App\Domain\Catalog\Services;

use App\Domain\Catalog\Repositories\MedicamentoRepositoryInterface;
use Illuminate\Support\Collection;

class EloquentMedicamentoService implements MedicamentoServiceInterface
{
  private MedicamentoRepositoryInterface $medicamentoRepository;

  public function __construct(MedicamentoRepositoryInterface $medicamentoRepository)
  {
    $this->medicamentoRepository = $medicamentoRepository;
  }

  public function existe(int $medicamentoId): bool
  {
    return $this->medicamentoRepository->exists($medicamentoId);
  }

  public function getPrecio(int $medicamentoId): ?float
  {
    return $this->medicamentoRepository->getPrecioUnitario($medicamentoId);
  }

  public function findById(int $medicamentoId): ?array
  {
    $medicamento = $this->medicamentoRepository->findById($medicamentoId);

    if (!$medicamento) {
      return null;
    }

    return [
      'id' => $medicamento->id,
      'nombre_comercial' => $medicamento->nombre_comercial,
      'sustancia_activa' => $medicamento->sustancia_activa,
      'precio_unitario' => $medicamento->precio_unitario,
    ];
  }

  public function bloquearStocksPorSucursal(array $medicamentosIds, int $sucursalId): Collection
  {
    return $this->medicamentoRepository->bloquearStocksPorSucursal($medicamentosIds, $sucursalId);
  }
}
