<?php

namespace App\Services;

use App\Models\Pedidos;
use App\Models\DetallePedidos;
use App\Models\Medicamentos;
use App\Models\MedicamentosSucursales;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

use function Psy\debug;

class PedidosService
{
  public function getPaginatedWithSucursal(int $perPage = 30)
  {
    return Pedidos::with('sucursal')->orderBy('fecha_hora', 'desc')->paginate($perPage);
  }

  public function getPaginatedEnviados(int $perPage = 30)
  {
    return Pedidos::enviados()->with('sucursal')->orderBy('fecha_hora', 'desc')->paginate($perPage);
  }

  public function getPaginatedPendientes(int $perPage = 30)
  {
    return Pedidos::pendientes()->with('sucursal')->orderBy('fecha_hora', 'desc')->paginate($perPage);
  }

  public function create(array $data, array $items): Pedidos
  {
    return DB::transaction(function () use ($data, $items) {
      $pedido = Pedidos::create([
        'sucursales_id' => $data['sucursal'],
        'fecha_hora' => now(),
        'estado' => Pedidos::ESTADO_ENVIADO,
      ]);

      usort($items, function ($a, $b) {
        return $a['id'] <=> $b['id'];
      });

      foreach ($items as $item) {
        $this->createDetalleItem($pedido->id, $item);
      }

      return $pedido;
    });
  }

  private function createDetalleItem(int $pedidoId, array $item): void
  {
    if (!isset($item['id']) || !isset($item['cantidad'])) {
      throw ValidationException::withMessages([
        'medicamentos' => 'Faltan datos del medicamento (id o cantidad).',
      ]);
    }

    $medicamento = Medicamentos::lockForUpdate()->find($item['id']);
    if (!$medicamento) {
      throw ValidationException::withMessages([
        'medicamentos' => 'Alguno de los medicamentos no existe.',
      ]);
    }

    $cantidad = (int) $item['cantidad'];
    if ($cantidad < 1) {
      throw ValidationException::withMessages([
        'medicamentos' => 'La cantidad debe ser un entero positivo.',
      ]);
    }

    $pedido = Pedidos::find($pedidoId);
    $sucursalId = $pedido->sucursales_id;

    $this->validateAndUpdateStock($medicamento, $sucursalId, $cantidad);

    DetallePedidos::create([
      'pedidos_id' => $pedidoId,
      'medicamentos_id' => $medicamento->id,
      'cantidad' => $cantidad,
      'precio_unitario' => $medicamento->precio_unitario,
    ]);
  }

  private function validateAndUpdateStock(Medicamentos $medicamento, int $sucursalId, int $cantidad): void
  {
    $stockRecord = MedicamentosSucursales::lockForUpdate()
      ->where('medicamentos_id', $medicamento->id)
      ->where('sucursales_id', $sucursalId)
      ->first();

    $currentStock = $stockRecord ? $stockRecord->stock : 0;

    if ($currentStock < $cantidad) {
      throw ValidationException::withMessages([
        'medicamentos' => "Stock insuficiente para {$medicamento->nombre_comercial} en esta sucursal. Stock disponible: {$currentStock}.",
      ]);
    }

    // Decrement stock
    if ($stockRecord) {
      $stockRecord->stock = $currentStock - $cantidad;
      $stockRecord->save();
    }
  }
}
