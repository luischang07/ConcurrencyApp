<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pedidos extends Model
{
  protected $table = 'pedidos';
  protected $fillable = ['sucursales_id', 'fecha_hora', 'estado'];

  const ESTADO_ENVIADO = 'ENVIADO';
  const ESTADO_PENDIENTE = 'PENDIENTE';
  const ESTADO_COMPLETADO = 'COMPLETADO';
  const ESTADO_CANCELADO = 'CANCELADO';


  public function sucursal()
  {
    return $this->belongsTo(Sucursales::class, 'sucursales_id');
  }

  public function detalles()
  {
    return $this->hasMany(DetallePedidos::class, 'pedidos_id');
  }

  //scopes
  public function scopeEnviados($query)
  {
    return $query->where('estado', self::ESTADO_ENVIADO);
  }

  public function scopePendientes($query)
  {
    return $query->where('estado', self::ESTADO_PENDIENTE);
  }

  public function scopeCompletados($query)
  {
    return $query->where('estado', self::ESTADO_COMPLETADO);
  }

  public function scopeCancelados($query)
  {
    return $query->where('estado', self::ESTADO_CANCELADO);
  }
}
