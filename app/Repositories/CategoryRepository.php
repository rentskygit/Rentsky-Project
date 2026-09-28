<?php

namespace App\Repositories;

use App\Models\Product\Category;
use App\Repositories\Contracts\Product\CategoryRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class CategoryRepository
 * 
 * Implementación del repositorio de categorías
 * 
 * RESPONSABILIDAD (SRP):
 * - Solo maneja operaciones de acceso a datos de categorías
 * - No tiene lógica de negocio
 * - No tiene validaciones (eso lo hace el Request)
 */
class CategoryRepository implements CategoryRepositoryInterface
{
    protected $model;

    public function __construct(Category $model)
    {
        $this->model = $model;
    }

    public function getAllActive(): Collection
    {
        return $this->model->active()->orderBy('name')->get();
    }

    public function getWithProductCount(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->withCount('products')
            ->orderBy('name')
            ->paginate($perPage);
    }

    public function findById(int $id): ?object
    {
        return $this->model->find($id);
    }

    public function create(array $data): object
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): bool
    {
        $category = $this->findById($id);
        if (!$category) {
            return false;
        }
        return $category->update($data);
    }

    public function delete(int $id): bool
    {
        $category = $this->findById($id);
        if (!$category) {
            return false;
        }
        return $category->delete();
    }

    public function hasProducts(int $id): bool
    {
        return $this->model->where('id', $id)->has('products')->exists();
    }
}