<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\Product\ProductRepositoryInterface;
use App\Repositories\ProductRepository;
use App\Repositories\Contracts\Product\CategoryRepositoryInterface;
use App\Repositories\CategoryRepository;
use App\Services\Contracts\Product\ProductServiceInterface;
use App\Services\ProductService;
use App\Services\Contracts\Product\ImageStorageInterface;
use App\Services\ImageStorageService;

/**
 * Class RepositoryServiceProvider
 * 
 * PROVEEDOR DE SERVICIOS PARA REPOSITORIOS
 * 
 * ¿Qué hace?
 * - Registra las dependencias en el contenedor de Laravel
 * - Configura qué implementación usar para cada interfaz
 * 
 * PRINCIPIO DIP (Inversión de Dependencias):
 * - Aquí es donde decimos: "Cuando alguien pida ProductRepositoryInterface,
 *   dale ProductRepository"
 * 
 * VENTAJAS:
 * - Centraliza la configuración de dependencias
 * - Fácil de cambiar implementaciones
 * - Un solo lugar para modificar si cambiamos de sistema
 * 
 * EJEMPLO DE EXTENSIÓN (OCP):
 * 
 * Para cambiar a Cloudinary:
 * 1. Crear CloudinaryStorageService implementando ImageStorageInterface
 * 2. Cambiar el binding aquí:
 *    $this->app->bind(ImageStorageInterface::class, CloudinaryStorageService::class);
 * 3. ¡Todo el código existente sigue funcionando sin modificaciones!
 */
class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Registrar servicios en el contenedor
     */
    public function register(): void
    {
        // ── Repositorios ──
        
        // Cuando alguien pida ProductRepositoryInterface, dale ProductRepository
        $this->app->bind(
            ProductRepositoryInterface::class,
            ProductRepository::class
        );

        // Cuando alguien pida CategoryRepositoryInterface, dale CategoryRepository
        $this->app->bind(
            CategoryRepositoryInterface::class,
            CategoryRepository::class
        );

        // ── Servicios ──
        
        // Cuando alguien pida ProductServiceInterface, dale ProductService
        $this->app->bind(
            ProductServiceInterface::class,
            ProductService::class
        );

        // ── Almacenamiento ──
        
        // Cuando alguien pida ImageStorageInterface, dale ImageStorageService
        $this->app->bind(
            ImageStorageInterface::class,
            ImageStorageService::class
        );

        // ── Formatters ──
        
        // Registrar formatter como singleton para reutilizar
        $this->app->singleton(
            \App\Formatters\ProductFormatter::class,
            \App\Formatters\ProductFormatter::class
        );
    }

    /**
     * Boot: Configuración adicional después de registrar todos los servicios
     */
    public function boot(): void
    {
        // Aquí puedes agregar configuraciones adicionales
        // Ejemplo: Configurar el disco de almacenamiento
        // config(['filesystems.default' => 'public']);
    }
}