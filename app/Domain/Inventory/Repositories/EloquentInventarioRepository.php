<?php

namespace App\Domain\Inventory\Repositories;

use App\Domain\Inventory\Entities\Inventario;
use App\Domain\Inventory\ValueObjects\Stock;
use App\Domain\Orders\ValueObjects\Cantidad;
use App\Models\MedicamentosSucursales;
use Illuminate\Support\Facades\DB;

class EloquentInventarioRepository implements InventarioRepositoryInterface
{
  public function findByMedicamentoAndSucursal(int $medicamentoId, int $sucursalId): ?Inventario
  {
    $stockRecord = MedicamentosSucursales::where('medicamentos_id', $medicamentoId)
      ->where('sucursales_id', $sucursalId)
      ->first();

    if (!$stockRecord) {
      return null;
    }

    return new Inventario(
      $medicamentoId,
      $sucursalId,
      new Stock($stockRecord->stock),
      new Stock($stockRecord->stockMinimo),
      new Stock($stockRecord->stockMaximo)
    );
  }

  public function save(Inventario $inventario): Inventario
  {
    $stockRecord = MedicamentosSucursales::where('medicamentos_id', $inventario->getMedicamentoId())
      ->where('sucursales_id', $inventario->getSucursalId())
      ->first();

    if ($stockRecord) {
      $stockRecord->update([
        'stock' => $inventario->getStockActual()->getValue(),
        'stockMinimo' => $inventario->getStockMinimo()->getValue(),
        'stockMaximo' => $inventario->getStockMaximo()->getValue(),
      ]);
    } else {
      MedicamentosSucursales::create([
        'medicamentos_id' => $inventario->getMedicamentoId(),
        'sucursales_id' => $inventario->getSucursalId(),
        'stock' => $inventario->getStockActual()->getValue(),
        'stockMinimo' => $inventario->getStockMinimo()->getValue(),
        'stockMaximo' => $inventario->getStockMaximo()->getValue(),
      ]);
    }

    return $inventario;
  }

  public function reservarStockAtomico(int $medicamentoId, int $sucursalId, Cantidad $cantidad): bool
  {
    return DB::transaction(function () use ($medicamentoId, $sucursalId, $cantidad) {
      $stockRecord = MedicamentosSucursales::lockForUpdate()
        ->where('medicamentos_id', $medicamentoId)
        ->where('sucursales_id', $sucursalId)
        ->first();

      if (!$stockRecord || $stockRecord->stock < $cantidad->getValue()) {
        return false;
      }

      $stockRecord->stock -= $cantidad->getValue();
      $stockRecord->save();

      return true;
    });
  }

  public function getInventariosParaReabastecer(): array
  {
    $stockRecords = MedicamentosSucursales::whereColumn('stock', '<=', 'stockMinimo')->get();

    $inventarios = [];
    foreach ($stockRecords as $record) {
      $inventarios[] = new Inventario(
        $record->medicamentos_id,
        $record->sucursales_id,
        new Stock($record->stock),
        new Stock($record->stockMinimo),
        new Stock($record->stockMaximo)
      );
    }

    return $inventarios;
  }

  public function getStockTotalMedicamento(int $medicamentoId): int
  {
    return MedicamentosSucursales::where('medicamentos_id', $medicamentoId)
      ->sum('stock');
  }
}
