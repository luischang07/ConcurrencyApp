<?php

namespace Tests\Unit\Domain\Orders;

use PHPUnit\Framework\TestCase;
use App\Domain\Orders\Entities\Pedido;
use App\Domain\Orders\Entities\DetallePedido;
use App\Domain\Orders\ValueObjects\Cantidad;
use App\Domain\Orders\ValueObjects\PrecioUnitario;
use DateTime;
use InvalidArgumentException;

class PedidoTest extends TestCase
{
  public function test_puede_crear_pedido_basico()
  {
    $sucursalId = 1;
    $pedido = new Pedido($sucursalId);

    $this->assertEquals($sucursalId, $pedido->getSucursalId());
    $this->assertEquals(Pedido::ESTADO_ENVIADO, $pedido->getEstado());
    $this->assertInstanceOf(DateTime::class, $pedido->getFechaHora());
    $this->assertFalse($pedido->tieneDetalles());
  }

  public function test_puede_agregar_detalle_al_pedido()
  {
    $pedido = new Pedido(1);

    $detalle = new DetallePedido(
      101, // medicamento ID
      new Cantidad(5),
      new PrecioUnitario(25.50)
    );

    $pedido->addDetalle($detalle);

    $this->assertTrue($pedido->tieneDetalles());
    $this->assertEquals(1, $pedido->getCantidadItems());
    $this->assertEquals(127.50, $pedido->getTotal()); // 5 * 25.50
  }

  public function test_no_permite_cantidad_negativa()
  {
    $this->expectException(InvalidArgumentException::class);
    $this->expectExceptionMessage('La cantidad debe ser un número positivo mayor a 0');

    new Cantidad(-1);
  }

  public function test_no_permite_precio_negativo()
  {
    $this->expectException(InvalidArgumentException::class);
    $this->expectExceptionMessage('El precio unitario debe ser mayor a 0');

    new PrecioUnitario(-10.0);
  }

  public function test_no_permite_medicamento_duplicado()
  {
    $pedido = new Pedido(1);

    $detalle1 = new DetallePedido(101, new Cantidad(5), new PrecioUnitario(25.50));
    $detalle2 = new DetallePedido(101, new Cantidad(3), new PrecioUnitario(30.00)); // mismo medicamento

    $pedido->addDetalle($detalle1);

    $this->expectException(InvalidArgumentException::class);
    $this->expectExceptionMessage('Ya existe un detalle para el medicamento ID: 101');

    $pedido->addDetalle($detalle2);
  }

  public function test_calcula_total_correctamente_con_multiples_detalles()
  {
    $pedido = new Pedido(1);

    $detalle1 = new DetallePedido(101, new Cantidad(2), new PrecioUnitario(10.50)); // 21.00
    $detalle2 = new DetallePedido(102, new Cantidad(1), new PrecioUnitario(15.25)); // 15.25
    $detalle3 = new DetallePedido(103, new Cantidad(3), new PrecioUnitario(8.75));  // 26.25

    $pedido->addDetalle($detalle1);
    $pedido->addDetalle($detalle2);
    $pedido->addDetalle($detalle3);

    $this->assertEquals(62.50, $pedido->getTotal()); // 21.00 + 15.25 + 26.25
    $this->assertEquals(3, $pedido->getCantidadItems());
  }

  public function test_puede_cambiar_estado_pedido()
  {
    $pedido = new Pedido(1);

    $pedido->cambiarEstado(Pedido::ESTADO_COMPLETADO);

    $this->assertEquals(Pedido::ESTADO_COMPLETADO, $pedido->getEstado());
  }

  public function test_no_permite_estado_invalido()
  {
    $pedido = new Pedido(1);

    $this->expectException(InvalidArgumentException::class);
    $this->expectExceptionMessage('Estado de pedido inválido: ESTADO_INEXISTENTE');

    $pedido->cambiarEstado('ESTADO_INEXISTENTE');
  }
}
