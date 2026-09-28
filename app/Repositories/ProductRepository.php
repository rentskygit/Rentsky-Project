<?php

namespace App\Repositories;

use App\Models\Product\Product;
use App\Repositories\Contracts\Product\ProductRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Class ProductRepository
 * 
 * Implementación del repositorio de productos.
 * 
 * RESPONSABILIDAD (SRP):
 * - SOLO maneja operaciones de acceso a datos (CRUD)
 * - NO tiene lógica de negocio
 * - NO formatea datos para presentación
 * 
 * EJEMPLO DE USO:
 * ```php
 * // En el controlador
 * $productRepository = app(ProductRepositoryInterface::class);
 * $products = $productRepository->getAll(['category' => 1, 'status' => 'active']);
 * ```
 * 
 * VENTAJAS DE ESTA ESTRUCTURA:
 * 1. El controlador no sabe cómo se obtienen los datos (MySQL, API, caché)
 * 2. Podemos cambiar la fuente de datos sin modificar el controlador
 * 3. Es fácil de testear (mock)
 * 4. Código más limpio y organizado
 */
class ProductRepository implements ProductRepositoryInterface
{
    protected $model;

    /**
     * Inyección de dependencia del modelo
     * 
     * ¿Por qué inyectar el modelo en lugar de usarlo directamente?
     * - Facilita pruebas unitarias (podemos mockear el modelo)
     * - Permite cambiar el modelo si es necesario (ej: ProductEloquent, ProductMongoDB)
     */
    public function __construct(Product $model)
    {
        $this->model = $model;
    }

    /**
     * Obtener todos los productos con filtros
     * 
     * Este método demuestra el Principio OCP (Abierto/Cerrado):
     * - Está ABIERTO para extensión (podemos agregar nuevos filtros)
     * - Está CERRADO para modificación (no tocamos el código existente)
     * 
     * ¿Cómo agregar un nuevo filtro?
     * Simplemente agregamos un nuevo case en el switch
     */
    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->with(['category', 'primaryImage']);

        // Filtros dinámicos - Abierto para extensión
        foreach ($filters as $key => $value) {
            if (empty($value)) continue;

            switch ($key) {
                case 'search':
                    $query->where(function($q) use ($value) {
                        $q->where('name', 'LIKE', "%{$value}%")
                          ->orWhere('sku', 'LIKE', "%{$value}%");
                    });
                    break;
                case 'category':
                    $query->where('category_id', $value);
                    break;
                case 'status':
                    $query->where('status', $value);
                    break;
                case 'sort':
                    $this->applySorting($query, $value);
                    break;
                case 'min_price':
                    $query->where('price', '>=', $value);
                    break;
                case 'max_price':
                    $query->where('price', '<=', $value);
                    break;
                // 👇 Aquí puedes agregar nuevos filtros sin modificar el código existente
                case 'featured':
                    $query->where('featured', $value);
                    break;
                case 'badge':
                    $query->where('badge', $value);
                    break;
            }
        }

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * Aplicar ordenamiento
     * 
     * Método privado para mantener SRP (Responsabilidad Única)
     * Este método solo se encarga del ordenamiento
     */
    private function applySorting($query, string $sort): void
    {
        $sortOptions = [
            'price_asc' => ['price', 'asc'],
            'price_desc' => ['price', 'desc'],
            'name_asc' => ['name', 'asc'],
            'name_desc' => ['name', 'desc'],
            'stock_asc' => ['stock', 'asc'],
            'stock_desc' => ['stock', 'desc'],
            'created_at_desc' => ['created_at', 'desc'],
        ];

        if (isset($sortOptions[$sort])) {
            $query->orderBy($sortOptions[$sort][0], $sortOptions[$sort][1]);
        } else {
            $query->orderBy('created_at', 'desc');
        }
    }

    /**
     * Obtener productos activos con stock
     */
    public function getActiveWithStock(array $filters = [], int $limit = null): Collection
    {
        $query = $this->model->active()
            ->inStock()
            ->with(['category', 'primaryImage']);

        if (isset($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (isset($filters['featured'])) {
            $query->where('featured', $filters['featured']);
        }

        if ($limit) {
            $query->limit($limit);
        }

        return $query->get();
    }

    /**
     * Obtener productos en oferta
     */
    public function getOnSale(int $limit = 8): Collection
    {
        return $this->model->active()
            ->inStock()
            ->onSale()
            ->with(['category', 'primaryImage'])
            ->orderByRaw('(original_price - price) DESC NULLS LAST')
            ->limit($limit)
            ->get();
    }

    /**
     * Obtener productos en liquidación
     */
    public function getOnLiquidation(int $limit = 8): Collection
    {
        return $this->model->active()
            ->inStock()
            ->onLiquidation()
            ->with(['category', 'primaryImage'])
            ->orderByRaw('(original_price - price) DESC NULLS LAST')
            ->limit($limit)
            ->get();
    }

    /**
     * Obtener productos destacados
     */
    public function getFeatured(int $limit = 5): Collection
    {
        return $this->model->active()
            ->inStock()
            ->featured()
            ->with(['category', 'primaryImage'])
            ->orderBy('views', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Buscar productos
     */
    public function search(string $query, int $perPage = 20): LengthAwarePaginator
    {
        return $this->model->active()
            ->inStock()
            ->where(function($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('description', 'LIKE', "%{$query}%")
                  ->orWhere('sku', 'LIKE', "%{$query}%");
            })
            ->with(['category', 'primaryImage'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Encontrar producto por ID
     */
    public function findById(int $id): ?object
    {
        return $this->model->with(['category', 'images', 'primaryImage'])->find($id);
    }

    /**
     * Encontrar producto por slug
     */
    public function findBySlug(string $slug): ?object
    {
        return $this->model->with(['category', 'images', 'primaryImage'])
            ->where('slug', $slug)
            ->first();
    }

    /**
     * Crear un nuevo producto
     * 
     * Uso de transacción para garantizar integridad de datos
     * Si algo falla, todo se revierte
     */
    public function create(array $data): object
    {
        return DB::transaction(function () use ($data) {
            return $this->model->create($data);
        });
    }

    /**
     * Actualizar un producto
     */
    public function update(int $id, array $data): bool
    {
        return DB::transaction(function () use ($id, $data) {
            $product = $this->findById($id);
            if (!$product) {
                return false;
            }
            return $product->update($data);
        });
    }

    /**
     * Eliminar un producto
     */
    public function delete(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $product = $this->findById($id);
            if (!$product) {
                return false;
            }
            
            // Eliminar imágenes asociadas (cascada)
            foreach ($product->images as $image) {
                $image->delete();
            }
            
            return $product->delete();
        });
    }

    /**
     * Actualizar estado del producto
     */
    public function updateStatus(int $id, string $status): bool
    {
        return $this->update($id, ['status' => $status]);
    }

    /**
     * Obtener productos relacionados
     */
    public function getRelated(int $productId, int $categoryId, int $limit = 4): Collection
    {
        return $this->model->active()
            ->inStock()
            ->where('category_id', $categoryId)
            ->where('id', '!=', $productId)
            ->with(['category', 'primaryImage'])
            ->limit($limit)
            ->get();
    }

    /**
     * Obtener estadísticas de productos
     */
    public function getStats(): array
    {
        return [
            'total' => $this->model->count(),
            'active' => $this->model->active()->count(),
            'inactive' => $this->model->where('status', 'inactive')->count(),
            'out_of_stock' => $this->model->where('status', 'out_of_stock')->count(),
            'on_sale' => $this->model->onSale()->count(),
            'on_liquidation' => $this->model->onLiquidation()->count(),
            'featured' => $this->model->featured()->count(),
        ];
    }
}