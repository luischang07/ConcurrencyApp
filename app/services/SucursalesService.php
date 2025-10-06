<?php

namespace App\Services;

use App\Models\Sucursales;

class SucursalesService
{
  public function forChain(int $chainId)
  {
    return Sucursales::where('cadenas_farmaceuticas_id', $chainId)
      ->select('id', 'nombre', 'cadenas_farmaceuticas_id')
      ->orderBy('nombre')
      ->get();
  }

  public function getAllSucursales()
  {
    return Sucursales::with('cadena')
      ->select('id', 'nombre', 'cadenas_farmaceuticas_id')
      ->orderBy('nombre')
      ->get();
  }
}
