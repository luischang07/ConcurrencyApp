<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\CadenasRepository;
use App\Repositories\SucursalesRepository;
use App\Repositories\MedicamentosRepository;
use App\Repositories\PedidosRepository;

class PedidosController extends Controller
{
  public function create(CadenasRepository $cadenasRepository, MedicamentosRepository $medicamentosRepository)
  {
    $cadenas = $cadenasRepository->getAll();
    $medicamentos = $medicamentosRepository->getAll();

    return view('pedidos.create', [
      'cadenasJson' => $cadenas->toJson(),
      'medicamentosJson' => $medicamentos->toJson(),
    ]);
  }

  public function sucursalesByChain(int $chainId, SucursalesRepository $sucursalesRepository)
  {
    $sucursales = $sucursalesRepository->getByChain($chainId);
    return response()->json($sucursales);
  }

  public function medicamentosConStock(int $sucursalId, MedicamentosRepository $medicamentosRepository)
  {
    $medicamentos = $medicamentosRepository->getMedicamentosConStockPorSucursal($sucursalId);
    return response()->json($medicamentos);
  }

  public function index(PedidosRepository $pedidosRepository)
  {
    $pedidos = $pedidosRepository->getPaginatedWithSucursal();
    return view('pedidos.index', ['pedidos' => $pedidos]);
  }

  public function store(Request $request, PedidosRepository $pedidosRepository)
  {
    $data = $request->validate([
      'cadena' => 'required|integer|exists:cadenas_farmaceuticas,id',
      'sucursal' => 'required|integer|exists:sucursales,id',
      'medicamentos' => 'required|json',
    ], [
      'cadena.exists' => 'La cadena seleccionada no es válida.',
      'sucursal.exists' => 'La sucursal seleccionada no es válida para la cadena seleccionada.',
      'medicamentos.required' => 'Debe agregar al menos un medicamento.',
      'medicamentos.json' => 'Formato de medicamentos inválido.',
    ]);

    $items = json_decode($request->input('medicamentos'), true);
    if (!is_array($items) || count($items) === 0) {
      return back()->withErrors(['medicamentos' => 'Debe agregar al menos un medicamento'])->withInput();
    }

    try {
      $pedidosRepository->create($data, $items);
      return redirect()->route('pedidos.index')->with('success', 'Pedido creado correctamente');
    } catch (\Illuminate\Validation\ValidationException $e) {
      return back()->withErrors($e->errors())->withInput();
    }
  }
}
