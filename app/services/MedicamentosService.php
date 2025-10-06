<?php

namespace App\Services;

use App\Models\Medicamentos;
use Illuminate\Support\Facades\DB;

class MedicamentosService
{
  public function all()
  {
    return Medicamentos::select('id', 'nombre_comercial', 'sustancia_activa', 'precio_unitario')
      ->orderBy('nombre_comercial')
      ->get();
  }

  public function paginated(int $perPage = 20)
  {
    return Medicamentos::select('id', 'nombre_comercial', 'sustancia_activa', 'precio_unitario')
      ->orderBy('nombre_comercial')
      ->paginate($perPage);
  }

  public function paginatedWithStock(?int $sucursalId = null, int $perPage = 20)
  {
    $query = Medicamentos::select('medicamentos.id', 'medicamentos.nombre_comercial', 'medicamentos.sustancia_activa', 'medicamentos.precio_unitario')
      ->orderBy('medicamentos.nombre_comercial');

    if ($sucursalId) {
      $query->leftJoin('medicamentos_sucursales', function ($join) use ($sucursalId) {
        $join->on('medicamentos.id', '=', 'medicamentos_sucursales.medicamentos_id')
          ->where('medicamentos_sucursales.sucursales_id', '=', $sucursalId);
      })
        ->addSelect('medicamentos_sucursales.stock');
    } else {
      // stock total sumando todas las sucursales
      $query->leftJoin('medicamentos_sucursales', 'medicamentos.id', '=', 'medicamentos_sucursales.medicamentos_id')
        ->addSelect(DB::raw('COALESCE(SUM(medicamentos_sucursales.stock), 0) as stock'))
        ->groupBy('medicamentos.id', 'medicamentos.nombre_comercial', 'medicamentos.sustancia_activa', 'medicamentos.precio_unitario');
    }

    return $query->paginate($perPage);
  }


  public function searchWithStock(string $q, int $sucursalId, int $limit = 10)
  {
    return Medicamentos::where('nombre_comercial', 'like', "%{$q}%")
      ->orWhere('sustancia_activa', 'like', "%{$q}%")
      ->whereHas('stocks', function ($query) use ($sucursalId) {
        $query->where('sucursales_id', $sucursalId)
          ->where('stock', '>', 0);
      })
      ->select('id', 'nombre_comercial', 'sustancia_activa', 'precio_unitario')
      ->limit($limit)
      ->get();
  }

  public function getMedicamentosConStockPorSucursal(int $sucursalId)
  {
    return Medicamentos::select('medicamentos.id', 'medicamentos.nombre_comercial', 'medicamentos.sustancia_activa', 'medicamentos.precio_unitario')
      ->leftJoin('medicamentos_sucursales', function ($join) use ($sucursalId) {
        $join->on('medicamentos.id', '=', 'medicamentos_sucursales.medicamentos_id')
          ->where('medicamentos_sucursales.sucursales_id', '=', $sucursalId);
      })
      ->addSelect(DB::raw('COALESCE(medicamentos_sucursales.stock, 0) as stock'))
      ->orderBy('medicamentos.nombre_comercial')
      ->get();
  }
}
