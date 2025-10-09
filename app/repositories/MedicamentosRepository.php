<?php

namespace App\Repositories;

use App\Models\Medicamentos;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class MedicamentosRepository
{
  public function getAll(): Collection
  {
    return Medicamentos::select('id', 'nombre_comercial', 'sustancia_activa', 'precio_unitario')
      ->orderBy('nombre_comercial')
      ->get();
  }

  public function getPaginated(int $perPage = 20): LengthAwarePaginator
  {
    return Medicamentos::select('id', 'nombre_comercial', 'sustancia_activa', 'precio_unitario')
      ->orderBy('nombre_comercial')
      ->paginate($perPage);
  }

  public function getPaginatedWithStock(?int $sucursalId = null, int $perPage = 20): LengthAwarePaginator
  {
    $query = Medicamentos::select('medicamentos.id', 'medicamentos.nombre_comercial', 'medicamentos.sustancia_activa', 'medicamentos.precio_unitario')
      ->orderBy('medicamentos.nombre_comercial');

    if ($sucursalId) {
      $query->leftJoin('medicamentos_sucursales', function ($join) use ($sucursalId) {
        $join->on('medicamentos.id', '=', 'medicamentos_sucursales.medicamento_id')
          ->where('medicamentos_sucursales.sucursal_id', '=', $sucursalId);
      })
        ->addSelect('medicamentos_sucursales.stock');
    } else {
      $query->leftJoin('medicamentos_sucursales', 'medicamentos.id', '=', 'medicamentos_sucursales.medicamento_id')
        ->addSelect(DB::raw('COALESCE(SUM(medicamentos_sucursales.stock), 0) as stock'))
        ->groupBy('medicamentos.id', 'medicamentos.nombre_comercial', 'medicamentos.sustancia_activa', 'medicamentos.precio_unitario');
    }

    return $query->paginate($perPage);
  }

  public function searchWithStock(string $query, int $sucursalId, int $limit = 10): Collection
  {
    return Medicamentos::where('nombre_comercial', 'like', "%{$query}%")
      ->orWhere('sustancia_activa', 'like', "%{$query}%")
      ->whereHas('stocks', function ($stockQuery) use ($sucursalId) {
        $stockQuery->where('sucursal_id', $sucursalId)
          ->where('stock', '>', 0);
      })
      ->select('id', 'nombre_comercial', 'sustancia_activa', 'precio_unitario')
      ->limit($limit)
      ->get();
  }

  public function getByBranch(int $sucursalId): Collection
  {
    return Medicamentos::select('medicamentos.id', 'medicamentos.nombre_comercial', 'medicamentos.sustancia_activa', 'medicamentos.precio_unitario')
      ->leftJoin('medicamentos_sucursales', function ($join) use ($sucursalId) {
        $join->on('medicamentos.id', '=', 'medicamentos_sucursales.medicamento_id')
          ->where('medicamentos_sucursales.sucursal_id', '=', $sucursalId);
      })
      ->addSelect(DB::raw('COALESCE(medicamentos_sucursales.stock, 0) as stock'))
      ->orderBy('medicamentos.nombre_comercial')
      ->get();
  }

  public function getMedicamentosConStockPorSucursal(int $sucursalId): Collection
  {
    return $this->getByBranch($sucursalId);
  }
}
