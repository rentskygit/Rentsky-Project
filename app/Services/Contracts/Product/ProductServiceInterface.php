<?php

namespace App\Services\Contracts\Product;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * Interface ProductServiceInterface
 * 
 * Define el contrato para el servicio de productos (Capa de Negocio)
 * 
 * ¿Por qué esta interfaz?
 * 
 * 1. SEPARACIÓN DE RESPONSABILIDADES (SRP):
 *    - El controlador solo maneja HTTP
 *    - El servicio maneja la lógica de negocio
 *    - El repositorio maneja acceso a datos
 * 
 * 2. INVERSIÓN DE DEPENDENCIAS (DIP):
 *    - El controlador depende de esta interfaz, no de la implementación
 *    - Podemos cambiar la lógica de negocio sin tocar el controlador
 * 
 * 3. PRUEBAS UNITARIAS:
 *    - Podemos crear un servicio falso (mock) para pruebas
 *    - No necesitamos base de datos real para probar el controlador
 * 
 * 4. ESCALABILIDAD:
 *    - Podemos agregar nuevas funcionalidades sin modificar código existente
 *    - Ejemplo: Implementar caché, colas, notificaciones, etc.
 */
interface ProductServiceInterface
{
    /**
     * Obtener todos los productos con filtros aplicados
     */
    public function getAllProducts(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * Obtener datos para la landing page
     * 
     * Retorna un array con:
     * - saleProducts: Productos en oferta
     * - liquidationProducts: Productos en liquidación
     * - carouselItems: Productos destacados para el carrusel
     * - categories: Categorías activas
     * - stats: Estadísticas de productos
     */
    public function getHomePageData(): array;

    /**
     * Buscar productos
     */
    public function searchProducts(string $query, int $perPage = 20): LengthAwarePaginator;

    /**
     * Obtener un producto por su ID
     * 
     * @return array|null Array formateado o null si no existe
     */
    public function getProductById(int $id): ?array;

    /**
     * Obtener un producto por su slug
     * 
     * @return array|null Array formateado o null si no existe
     */
    public function getProductBySlug(string $slug): ?array;

    /**
     * Crear un nuevo producto con sus imágenes
     */
    public function createProduct(array $data, array $images = []): object;

    /**
     * Actualizar un producto con sus imágenes
     */
    public function updateProduct(int $id, array $data, array $images = [], array $deleteImages = []): bool;

    /**
     * Eliminar un producto y sus imágenes
     */
    public function deleteProduct(int $id): bool;

    /**
     * Actualizar el estado de un producto
     */
    public function updateProductStatus(int $id, string $status): bool;

    /**
     * Obtener productos relacionados
     * 
     * @return \Illuminate\Support\Collection Colección de arrays formateados
     */
    public function getRelatedProducts(int $productId, int $categoryId, int $limit = 4): Collection;

    /**
     * Obtener estadísticas de productos
     */
    public function getProductStats(): array;
}