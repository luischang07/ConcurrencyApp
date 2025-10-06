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
      ->select('sucursales.id', 'sucursales.nombre', 'sucursales.cadenas_farmaceuticas_id')
      ->join('cadenas_farmaceuticas', 'sucursales.cadenas_farmaceuticas_id', '=', 'cadenas_farmaceuticas.id')
      ->orderBy('cadenas_farmaceuticas.nombre')
      ->orderBy('sucursales.nombre')
      ->get();
  }
}
