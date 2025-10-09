<?php

namespace App\Repositories;

use App\Models\Sucursales;
use Illuminate\Support\Collection;

class SucursalesRepository
{
  public function getByChain(int $chainId): Collection
  {
    return Sucursales::where('cadenas_farmaceuticas_id', $chainId)
      ->select('id', 'nombre', 'cadenas_farmaceuticas_id')
      ->orderBy('nombre')
      ->get();
  }

  public function getAllWithChain(): Collection
  {
    return Sucursales::with('cadena')
      ->select('sucursales.id', 'sucursales.nombre', 'sucursales.cadenas_farmaceuticas_id')
      ->join('cadenas_farmaceuticas', 'sucursales.cadenas_farmaceuticas_id', '=', 'cadenas_farmaceuticas.id')
      ->orderBy('cadenas_farmaceuticas.nombre')
      ->orderBy('sucursales.nombre')
      ->get();
  }
}
