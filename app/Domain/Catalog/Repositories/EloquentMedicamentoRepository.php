<?php

namespace App\Domain\Catalog\Repositories;

use App\Models\Medicamentos;
use App\Models\MedicamentosSucursales;
use Illuminate\Support\Collection;

class EloquentMedicamentoRepository implements MedicamentoRepositoryInterface
{
  public function findById(int $medicamentoId): ?object
  {
    return Medicamentos::find($medicamentoId);
  }

  public function exists(int $medicamentoId): bool
  {
    return Medicamentos::where('id', $medicamentoId)->exists();
  }

  public function getPrecioUnitario(int $medicamentoId): ?float
  {
    $medicamento = $this->findById($medicamentoId);
    return $medicamento ? $medicamento->precio_unitario : null;
  }

  public function bloquearStocksPorSucursal(array $medicamentosIds, int $sucursalId): Collection
  {
    return MedicamentosSucursales::whereIn('medicamento_id', $medicamentosIds)
      ->where('sucursal_id', $sucursalId)
      ->lockForUpdate()
      ->get()
      ->keyBy('medicamento_id');
  }
}
