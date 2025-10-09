<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sucursales extends Model
{
  use HasFactory;

  protected $table = 'sucursales';
  protected $fillable = ['nombre', 'cadenas_farmaceuticas_id'];

  public function cadena()
  {
    return $this->belongsTo(CadenasFarmaceuticas::class, 'cadenas_farmaceuticas_id');
  }

  /**
   * Get the stock records for this branch
   */
  public function stocks()
  {
    return $this->hasMany(MedicamentosSucursales::class, 'sucursal_id');
  }

  /**
   * Get stock for a specific medication
   */
  public function getStockForMedication($medicamentoId)
  {
    $stock = $this->stocks()->where('medicamentos_id', $medicamentoId)->first();
    return $stock ? $stock->stock : 0;
  }
}
