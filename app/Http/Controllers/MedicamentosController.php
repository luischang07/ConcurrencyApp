<?php

namespace App\Http\Controllers;

use App\Repositories\MedicamentosRepository;
use App\Repositories\SucursalesRepository;
use Illuminate\Http\Request;

class MedicamentosController extends Controller
{
  public function index(Request $request, MedicamentosRepository $medicamentosRepository, SucursalesRepository $sucursalesRepository)
  {
    $sucursalId = $request->get('sucursal');

    $sucursales = $sucursalesRepository->getAllWithChain();

    $medicamentos = $medicamentosRepository->getPaginatedWithStock($sucursalId, 20);

    return view('medicamentos.index', [
      'medicamentos' => $medicamentos,
      'sucursales' => $sucursales,
      'sucursalSeleccionada' => $sucursalId,
    ]);
  }
}
