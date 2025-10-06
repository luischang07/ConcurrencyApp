<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicamentosSucursales extends Model
{
  use HasFactory;

  protected $table = 'medicamentos_sucursales';
  protected $fillable = ['medicamentos_id', 'sucursales_id', 'stock'];

  /**
   * Get the medication for this stock record
   */
  public function medicamento()
  {
    return $this->belongsTo(Medicamentos::class, 'medicamentos_id');
  }

  /**
   * Get the branch for this stock record
   */
  public function sucursal()
  {
    return $this->belongsTo(Sucursales::class, 'sucursales_id');
  }
}
