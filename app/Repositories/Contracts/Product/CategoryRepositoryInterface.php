<?php

namespace App\Repositories\Contracts\Product;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * Interface CategoryRepositoryInterface
 * 
 * Define el contrato para el repositorio de categorías
 * 
 * Beneficios:
 * - Desacopla la lógica de acceso a datos del controlador
 * - Permite múltiples implementaciones (MySQL, Redis, API externa)
 * - Facilita pruebas unitarias
 */
interface CategoryRepositoryInterface
{
    /**
     * Obtener todas las categorías activas
     */
    public function getAllActive(): Collection;

    /**
     * Obtener categorías con conteo de productos
     */
    public function getWithProductCount(int $perPage = 15): LengthAwarePaginator;

    /**
     * Encontrar categoría por ID
     */
    public function findById(int $id): ?object;

    /**
     * Crear una nueva categoría
     */
    public function create(array $data): object;

    /**
     * Actualizar una categoría
     */
    public function update(int $id, array $data): bool;

    /**
     * Eliminar una categoría
     */
    public function delete(int $id): bool;

    /**
     * Verificar si una categoría tiene productos
     */
    public function hasProducts(int $id): bool;
}