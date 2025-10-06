# ConcurrencyApp - Sistema de Concurrencia Farmacéutica

Una aplicación Laravel para gestionar la concurrencia entre cadenas farmacéuticas, medicamentos, sucursales y pedidos.

## Características

-   Gestión de cadenas farmacéuticas
-   Control de inventario de medicamentos por sucursal
-   Sistema de pedidos con control de concurrencia
-   Interfaz web responsiva con Tailwind CSS

## Requisitos del Sistema

-   PHP >= 8.1
-   Composer
-   Node.js >= 16
-   MySQL/MariaDB
-   XAMPP (recomendado para desarrollo local)

## Instalación

### 1. Clonar o descargar el proyecto

```bash
# Si usas Git
git clone <repository-url> ConcurrencyApp
cd ConcurrencyApp
```

### 2. Instalar dependencias de PHP

```bash
composer install
```

### 3. Configurar el archivo de entorno

```bash
# Copiar el archivo de configuración
cp .env.example .env

# Generar la clave de aplicación
php artisan key:generate
```

### 4. Configurar la base de datos

Edita el archivo `.env` con tus credenciales de base de datos:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=concurrency_app
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Crear la base de datos

```sql
CREATE DATABASE concurrency_app;
```

### 6. Ejecutar migraciones y seeders

```bash
# Ejecutar migraciones
php artisan migrate

# Ejecutar seeders para datos de prueba
php artisan db:seed
```

### 7. Instalar dependencias de Node.js

```bash
npm install
```

### 8. Compilar assets

```bash
# Para desarrollo
npm run dev

# Para producción
npm run build
```

### 9. Iniciar el servidor

```bash
php artisan serve
```

La aplicación estará disponible en `http://localhost:8000`

## Uso con XAMPP

Si utilizas XAMPP:

1. Coloca el proyecto en `C:\xampp\htdocs\laravel\Concurrencia\ConcurrencyApp`
2. Inicia Apache y MySQL desde el panel de control de XAMPP
3. Accede a `http://localhost/laravel/Concurrencia/ConcurrencyApp/public`

## Comandos Útiles

```bash
# Limpiar cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Ver rutas disponibles
php artisan route:list
```

## Estructura del Proyecto

-   `app/Models/` - Modelos Eloquent
-   `app/Http/Controllers/` - Controladores
-   `app/services/` - Servicios de lógica de negocio
-   `database/migrations/` - Migraciones de base de datos
-   `database/seeders/` - Seeders para datos de prueba
-   `resources/views/` - Vistas Blade
-   `resources/js/` - JavaScript
-   `resources/css/` - Estilos CSS

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

-   [Simple, fast routing engine](https://laravel.com/docs/routing).
-   [Powerful dependency injection container](https://laravel.com/docs/container).
-   Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
-   Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
-   Database agnostic [schema migrations](https://laravel.com/docs/migrations).
-   [Robust background job processing](https://laravel.com/docs/queues).
-   [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

-   **[Vehikl](https://vehikl.com)**
-   **[Tighten Co.](https://tighten.co)**
-   **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
-   **[64 Robots](https://64robots.com)**
-   **[Curotec](https://www.curotec.com/services/technologies/laravel)**
-   **[DevSquad](https://devsquad.com/hire-laravel-developers)**
-   **[Redberry](https://redberry.international/laravel-development)**
-   **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
