<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Domain\Orders\Repositories\PedidoRepositoryInterface;
use App\Domain\Orders\Repositories\EloquentPedidoRepository;

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
  }

  /**
   * Bootstrap any application services.
   */
  public function boot(): void
  {
    //
  }
}
