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
    Schema::create('pedidos', function (Blueprint $table) {
      $table->id();
      $table->foreignId('sucursales_id')->constrained('sucursales', 'id');
      $table->timestamp('fecha_hora');
      $table->string('estado', 50)->default('Enviado');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('pedidos');
  }
};
