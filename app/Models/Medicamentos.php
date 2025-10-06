<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medicamentos extends Model
{
  use HasFactory;

  protected $table = 'medicamentos';
  protected $fillable = ['nombre_comercial', 'sustancia_activa', 'precio_unitario'];

  /**
   * Get the stock records for this medication across all branches
   */
  public function stocks()
  {
    return $this->hasMany(MedicamentosSucursales::class, 'medicamentos_id');
  }

  /**
   * Get stock for a specific branch
   */
  public function getStockForBranch($sucursalId)
  {
    $stock = $this->stocks()->where('sucursales_id', $sucursalId)->first();
    return $stock ? $stock->stock : 0;
  }

  public function scopesWithStockAboveMinimum($query)
  {
    return $query->whereHas('stocks', function ($q) {
      $q->whereColumn('stock', '>', 'stockMinimo');
    });
  }

  public function scopesWithStockBelowMaximum($query)
  {
    return $query->whereHas('stocks', function ($q) {
      $q->whereColumn('stock', '<', 'stockMaximo');
    });
  }
}
