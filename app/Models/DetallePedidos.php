<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetallePedidos extends Model
{
  protected $table = 'detalle_pedidos';
  protected $fillable = ['pedidos_id', 'medicamentos_id', 'cantidad', 'precio_unitario', 'subtotal'];

  // Eventos del modelo
  protected static function boot()
  {
    parent::boot();

    // Calcular subtotal automáticamente antes de guardar
    static::saving(function ($detalle) {
      if ($detalle->precio_unitario && $detalle->cantidad) {
        $detalle->subtotal = $detalle->precio_unitario * $detalle->cantidad;
      }
    });
  }

  public function pedido()
  {
    return $this->belongsTo(Pedidos::class);
  }

  public function medicamento()
  {
    return $this->belongsTo(Medicamentos::class);
  }
}
