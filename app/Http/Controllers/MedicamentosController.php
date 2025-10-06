<?php

namespace App\Http\Controllers;

use App\Services\MedicamentosService;
use App\Services\SucursalesService;
use Illuminate\Http\Request;

class MedicamentosController extends Controller
{
  public function index(Request $request, MedicamentosService $medicamentosService, SucursalesService $sucursalesService)
  {
    $sucursalId = $request->get('sucursal');

    $sucursales = $sucursalesService->getAllSucursales();

    $medicamentos = $medicamentosService->paginatedWithStock($sucursalId, 20);

    return view('medicamentos.index', [
      'medicamentos' => $medicamentos,
      'sucursales' => $sucursales,
      'sucursalSeleccionada' => $sucursalId,
    ]);
  }
}
