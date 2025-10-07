<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CadenasService;
use App\Services\SucursalesService;
use App\Services\MedicamentosService;
use App\Services\PedidosService;

class PedidosController extends Controller
{
  public function create(CadenasService $cadenasService, MedicamentosService $medService)
  {
    $cadenas = $cadenasService->all();
    $medicamentos = $medService->all();

    return view('pedidos.create', [
      'cadenasJson' => $cadenas->toJson(),
      'medicamentosJson' => $medicamentos->toJson(),
    ]);
  }

  public function sucursalesByChain(int $chainId, SucursalesService $sucService)
  {
    $sucursales = $sucService->forChain($chainId);
    return response()->json($sucursales);
  }

  public function medicamentosConStock(int $sucursalId, MedicamentosService $medicamentosService)
  {
    $medicamentos = $medicamentosService->getMedicamentosConStockPorSucursal($sucursalId);
    return response()->json($medicamentos);
  }

  public function index(PedidosService $pedidosService)
  {
    $pedidos = $pedidosService->getPaginatedWithSucursal();
    return view('pedidos.index', ['pedidos' => $pedidos]);
  }

  public function store(Request $request, PedidosService $pedidosService)
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
      //CREAR OBJETO DEL DOMINIO Y PASARLO A LA CAPA
      $pedidosService->create($data, $items);
      return redirect()->route('pedidos.index')->with('success', 'Pedido creado correctamente');
    } catch (\Illuminate\Validation\ValidationException $e) {
      return back()->withErrors($e->errors())->withInput();
    }
  }
}
