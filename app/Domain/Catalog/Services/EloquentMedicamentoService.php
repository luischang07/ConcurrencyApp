<?php

namespace App\Domain\Catalog\Services;

use App\Models\Medicamentos;

class EloquentMedicamentoService implements MedicamentoServiceInterface
{
  public function existe(int $medicamentoId): bool
  {
    return Medicamentos::where('id', $medicamentoId)->exists();
  }

  public function getPrecio(int $medicamentoId): ?float
  {
    $medicamento = Medicamentos::find($medicamentoId);
    return $medicamento ? $medicamento->precio_unitario : null;
  }

  public function findById(int $medicamentoId): ?array
  {
    $medicamento = Medicamentos::find($medicamentoId);
    
    if (!$medicamento) {
      return null;
    }

    return [
      'id' => $medicamento->id,
      'nombre_comercial' => $medicamento->nombre_comercial,
      'sustancia_activa' => $medicamento->sustancia_activa,
      'precio_unitario' => $medicamento->precio_unitario,
    ];
  }
}