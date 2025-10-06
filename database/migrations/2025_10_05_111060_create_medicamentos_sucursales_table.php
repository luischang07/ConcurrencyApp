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
      $table->id();
      $table->foreignId('medicamentos_id')->constrained('medicamentos', 'id');
      $table->foreignId('sucursales_id')->constrained('sucursales', 'id');
      $table->unsignedInteger('stock')->default(0);
      $table->unsignedInteger('stockMinimo')->default(0);
      $table->unsignedInteger('stockMaximo')->default(1000);
      $table->timestamps();

      // Ensure unique combination of medicamento and sucursal
      $table->unique(['medicamentos_id', 'sucursales_id']);
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
