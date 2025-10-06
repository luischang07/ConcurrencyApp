# Implementación de Objetos de Dominio - Pedidos

Esta implementación demuestra cómo usar objetos de dominio siguiendo principios de POO y arquitectura hexagonal en Laravel.

## Estructura Implementada

```
app/Domain/Orders/
├── Entities/
│   ├── Pedido.php              # Entidad principal del dominio
│   └── DetallePedido.php       # Entidad detalle del pedido
├── ValueObjects/
│   ├── Cantidad.php            # Value object para cantidades
│   └── PrecioUnitario.php      # Value object para precios
├── Repositories/
│   ├── PedidoRepositoryInterface.php     # Contrato del repositorio
│   └── EloquentPedidoRepository.php      # Implementación con Eloquent
└── Services/
    └── OrderDomainService.php            # Servicio de dominio
```

## Flujo de Trabajo (POO + Dominio)

### Antes (Eloquent directo)

```php
// En PedidosService::create()
$pedido = Pedidos::create(['sucursales_id' => $data['sucursal']]);
foreach ($items as $item) {
    DetallePedidos::create([...]);
    // Validaciones y lógica mezclada
}
```

### Después (Objetos de Dominio)

```php
// 1. Eloquent hace la consulta inicial (infraestructura)
// 2. Se crean objetos de dominio (lógica de negocio)
$pedidoDominio = $this->orderDomainService->crearPedido($sucursalId, $items);

// 3. Se trabaja con el objeto de dominio
$pedidoDominio->addDetalle($detalle);
$total = $pedidoDominio->getTotal();

// 4. Se persiste al final usando repositorio
$this->pedidoRepository->save($pedidoDominio);
```

## Características Implementadas

### Entidades de Dominio

-   **Pedido**: Entidad raíz del agregado

    -   Valida estados permitidos
    -   Calcula totales automáticamente
    -   Previene medicamentos duplicados
    -   Maneja colección de detalles

-   **DetallePedido**: Entidad valor dentro del agregado
    -   Calcula subtotales
    -   Encapsula medicamento, cantidad y precio

### Value Objects

-   **Cantidad**: Valida que sea > 0
-   **PrecioUnitario**: Valida que sea > 0, maneja redondeo a 2 decimales

### Repositorio

-   **Interface**: Define contrato independiente de infraestructura
-   **Implementación Eloquent**:
    -   Maneja transacciones DB
    -   Implementa locks para concurrencia
    -   Mapea entre Eloquent models ↔ Domain entities

### Servicio de Dominio

-   **OrderDomainService**: Orquesta la creación de pedidos
    -   Valida reglas de negocio
    -   Coordina reserva de stock
    -   Maneja ordenamiento para evitar deadlocks

## Configuración

### 1. Registro en AppServiceProvider

```php
$this->app->bind(
    PedidoRepositoryInterface::class,
    EloquentPedidoRepository::class
);
```

### 2. Inyección en PedidosService

```php
public function __construct(
    OrderDomainService $orderDomainService,
    PedidoRepositoryInterface $pedidoRepository
) {
    $this->orderDomainService = $orderDomainService;
    $this->pedidoRepository = $pedidoRepository;
}
```

## Ventajas de esta Implementación

1. **Separación de responsabilidades**: Lógica de negocio separada de infraestructura
2. **Testabilidad**: Objetos de dominio se pueden testear sin BD
3. **Consistencia**: Reglas de negocio centralizadas en las entidades
4. **Flexibilidad**: Fácil cambiar de Eloquent a otro ORM
5. **Expresividad**: Código más legible y orientado al dominio

## Testing

```bash
# Ejecutar tests unitarios del dominio
php artisan test tests/Unit/Domain/Orders/PedidoTest.php
```

## Uso en Controlador

El controller no cambia, sigue usando PedidosService:

```php
public function store(Request $request, PedidosService $pedidosService)
{
    // ... validaciones request ...

    $pedidosService->create($data, $items); // Usa dominio internamente

    return redirect()->route('pedidos.index');
}
```

## Compatibilidad

-   Mantiene compatibilidad con vistas existentes
-   PedidosService retorna modelos Eloquent para las vistas
-   Transición gradual posible (algunos métodos con dominio, otros sin él)

## Próximos Pasos

1. Migrar otros servicios (MedicamentosService, SucursalesService)
2. Implementar eventos de dominio
3. Agregar más tests de integración
4. Considerar CQRS para consultas complejas
