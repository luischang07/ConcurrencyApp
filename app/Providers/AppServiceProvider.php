<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Domain\Orders\Repositories\PedidoRepositoryInterface;
use App\Domain\Orders\Repositories\EloquentPedidoRepository;
use App\Domain\Catalog\Repositories\MedicamentoRepositoryInterface;
use App\Domain\Catalog\Repositories\EloquentMedicamentoRepository;
use App\Domain\Catalog\Services\MedicamentoServiceInterface;
use App\Domain\Catalog\Services\EloquentMedicamentoService;

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
      MedicamentoRepositoryInterface::class,
      EloquentMedicamentoRepository::class
    );

    $this->app->bind(
      MedicamentoServiceInterface::class,
      EloquentMedicamentoService::class
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
