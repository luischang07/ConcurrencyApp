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
    Schema::create('medicamentos', function (Blueprint $table) {
      $table->id();
      $table->string('nombre_comercial', 200)->unique();
      $table->string('sustancia_activa', 200)->nullable();
      $table->decimal('precio_unitario', 10, 2)->default(0);
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('medicamentos');
  }
};
