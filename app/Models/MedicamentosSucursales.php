<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicamentosSucursales extends Model
{
  use HasFactory;

  protected $table = 'medicamentos_sucursales';

  protected $primaryKey = ['sucursal_id', 'medicamento_id'];
  public $incrementing = false;
  protected $keyType = 'array';

  protected $fillable = [
    'medicamento_id',
    'sucursal_id',
    'stock',
    'stockMinimo',
    'stockMaximo'
  ];

  protected $casts = [
    'stock' => 'integer',
    'stockMinimo' => 'integer',
    'stockMaximo' => 'integer',
  ];

  protected function setKeysForSaveQuery($query)
  {
    $keys = $this->getKeyName();
    if (!is_array($keys)) {
      return parent::setKeysForSaveQuery($query);
    }

    foreach ($keys as $keyName) {
      $query->where($keyName, '=', $this->getKeyForSaveQuery($keyName));
    }

    return $query;
  }

  protected function getKeyForSaveQuery($keyName = null)
  {
    if (is_null($keyName)) {
      $keyName = $this->getKeyName();
    }

    if (isset($this->original[$keyName])) {
      return $this->original[$keyName];
    }

    return $this->getAttribute($keyName);
  }

  public function medicamento()
  {
    return $this->belongsTo(Medicamentos::class, 'medicamento_id');
  }

  public function sucursal()
  {
    return $this->belongsTo(Sucursales::class, 'sucursal_id');
  }

  public static function findByMedicamentoAndSucursal(int $medicamentoId, int $sucursalId): ?self
  {
    return static::where('medicamento_id', $medicamentoId)
      ->where('sucursal_id', $sucursalId)
      ->first();
  }

  public static function updateOrCreateStock(int $medicamentoId, int $sucursalId, array $attributes): self
  {
    return static::updateOrCreate(
      [
        'medicamento_id' => $medicamentoId,
        'sucursal_id' => $sucursalId
      ],
      $attributes
    );
  }
}
