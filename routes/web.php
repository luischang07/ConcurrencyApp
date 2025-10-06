<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PedidosController;
use App\Http\Controllers\MedicamentosController;

Route::get('/', [PedidosController::class, 'index'])->name('home');
// Pedidos
Route::get('/pedidos', [PedidosController::class, 'index'])->name('pedidos.index');
Route::get('/pedidos/create', [PedidosController::class, 'create'])->name('pedidos.create');
Route::post('/pedidos', [PedidosController::class, 'store'])->name('pedidos.store');
// API-ish endpoint for sucursales by cadena
Route::get('/api/sucursales/{chainId}', [PedidosController::class, 'sucursalesByChain'])->name('api.sucursales.byChain');
// API-ish endpoint for medicamentos with stock by sucursal
Route::get('/api/medicamentos-stock/{sucursalId}', [PedidosController::class, 'medicamentosConStock'])->name('api.medicamentos.stock');

// Medicamentos
Route::get('/medicamentos', [MedicamentosController::class, 'index'])->name('medicamentos.index');
