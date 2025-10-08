<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Domain\Orders\Repositories\PedidoRepositoryInterface;
use App\Domain\Orders\Repositories\EloquentPedidoRepository;
use App\Domain\Inventory\Repositories\InventarioRepositoryInterface;
use App\Domain\Inventory\Repositories\EloquentInventarioRepository;

class AppServiceProvider extends ServiceProvider
{
  /**
   * Register any application services.
   */
  public function register(): void
  {
    // Bind domain repositories to their implementations
    $this->app->bind(
      PedidoRepositoryInterface::class,
      EloquentPedidoRepository::class
    );

    $this->app->bind(
      InventarioRepositoryInterface::class,
      EloquentInventarioRepository::class
    );
  }

  /**
   * Bootstrap any application services.
   */
  public function boot(): void
  {
    //
  }
}
