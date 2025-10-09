<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::create('medicamentos_sucursales', function (Blueprint $table) {
      $table->foreignId('sucursal_id')->constrained('sucursales', 'id');
      $table->foreignId('medicamento_id')->constrained('medicamentos', 'id');
      $table->unsignedInteger('stock')->default(0);
      $table->unsignedInteger('stockMinimo')->default(0);
      $table->unsignedInteger('stockMaximo')->default(1000);
      $table->timestamps();

      // Definir llave primaria compuesta
      $table->primary(['sucursal_id', 'medicamento_id']);

      // Índices para mejorar performance en consultas
      $table->index('sucursal_id');
      $table->index('medicamento_id');
      $table->index('stock');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('medicamentos_sucursales');
  }
};
