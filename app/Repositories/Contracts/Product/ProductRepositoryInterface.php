<?php

namespace App\Repositories\Contracts\Product;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * Interface ProductRepositoryInterface
 * 
 * ¿Qué es?
 * Define el CONTRATO que deben cumplir todos los repositorios de productos.
 * 
 * ¿Por qué una interfaz? (Principio DIP)
 * - El controlador depende de esta interfaz, no de la implementación
 * - Podemos cambiar la implementación sin modificar el controlador
 * - Facilita el testing (podemos crear mocks)
 * 
 * ¿Qué logra? (Principio ISP)
 * - Solo tiene métodos relacionados con productos
 * - No tiene métodos genéricos que no se usen
 * 
 * ¿Cuándo usar?
 * - Cuando quieras cambiar de base de datos (MySQL a MongoDB)
 * - Cuando quieras implementar caché sin modificar el controlador
 * - Para pruebas unitarias (crear un repositorio falso)
 */
interface ProductRepositoryInterface
{
    /**
     * Obtener todos los productos con filtros
     * 
     * @param array $filters Filtros aplicables (search, category, status, sort, min_price, max_price)
     * @param int $perPage Cantidad por página
     * @return LengthAwarePaginator
     */
    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * Obtener productos activos con stock
     */
    public function getActiveWithStock(array $filters = [], int $limit = null): Collection;

    /**
     * Obtener productos en oferta
     */
    public function getOnSale(int $limit = 8): Collection;

    /**
     * Obtener productos en liquidación
     */
    public function getOnLiquidation(int $limit = 8): Collection;

    /**
     * Obtener productos destacados
     */
    public function getFeatured(int $limit = 5): Collection;

    /**
     * Buscar productos por término
     */
    public function search(string $query, int $perPage = 20): LengthAwarePaginator;

    /**
     * Encontrar producto por ID
     */
    public function findById(int $id): ?object;

    /**
     * Encontrar producto por slug
     */
    public function findBySlug(string $slug): ?object;

    /**
     * Crear un nuevo producto
     */
    public function create(array $data): object;

    /**
     * Actualizar un producto
     */
    public function update(int $id, array $data): bool;

    /**
     * Eliminar un producto
     */
    public function delete(int $id): bool;

    /**
     * Actualizar estado del producto
     */
    public function updateStatus(int $id, string $status): bool;

    /**
     * Obtener productos relacionados
     */
    public function getRelated(int $productId, int $categoryId, int $limit = 4): Collection;

    /**
     * Obtener estadísticas de productos
     */
    public function getStats(): array;
}