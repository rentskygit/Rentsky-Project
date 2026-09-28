<?php

namespace App\Services;

use App\Models\Product\ProductImage;
use App\Repositories\Contracts\Product\ProductRepositoryInterface;
use App\Repositories\Contracts\Product\CategoryRepositoryInterface;
use App\Services\Contracts\Product\ProductServiceInterface;
use App\Services\Contracts\Product\ImageStorageInterface;
use App\Formatters\ProductFormatter;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class ProductService
 * 
 * SERVICIO DE PRODUCTOS - Capa de Negocio
 * 
 * ¿Qué hace este servicio?
 * - Orquesta las operaciones entre repositorios, formateadores y almacenamiento
 * - Contiene TODA la lógica de negocio
 * - No sabe nada sobre HTTP (no recibe Request, no devuelve respuestas HTTP)
 * - Puede ser reutilizado en diferentes contextos (API, CLI, Jobs)
 * 
 * PRINCIPIOS APLICADOS:
 * 
 * 1. SRP - Responsabilidad Única
 *    - Solo maneja lógica de negocio de productos
 *    - No sabe cómo se almacenan las imágenes (usa ImageStorageInterface)
 *    - No sabe cómo se formatean los datos (usa ProductFormatter)
 * 
 * 2. DIP - Inversión de Dependencias
 *    - Depende de interfaces, no de implementaciones concretas
 *    - ProductRepositoryInterface, CategoryRepositoryInterface, ImageStorageInterface
 * 
 * 3. OCP - Abierto/Cerrado
 *    - Podemos agregar nuevos métodos sin modificar los existentes
 *    - Podemos cambiar implementaciones (ej: nuevo sistema de caché)
 * 
 * 4. LSP - Sustitución de Liskov
 *    - Cualquier implementación de ProductServiceInterface puede sustituir a esta
 */
class ProductService implements ProductServiceInterface
{
    protected $productRepository;
    protected $categoryRepository;
    protected $imageStorage;
    protected $formatter;

    /**
     * Constructor con inyección de dependencias
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        CategoryRepositoryInterface $categoryRepository,
        ImageStorageInterface $imageStorage,
        ProductFormatter $formatter
    ) {
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
        $this->imageStorage = $imageStorage;
        $this->formatter = $formatter;
    }

    /**
     * Obtener todos los productos con filtros
     */
    public function getAllProducts(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        // 1. Obtener datos (Repositorio)
        $products = $this->productRepository->getAll($filters, $perPage);

        // 2. Formatear datos (Formatter)
        $products->getCollection()->transform(function ($product) {
            return $this->formatter->formatForIndex($product);
        });

        // 3. Retornar
        return $products;
    }

    /**
     * Obtener datos para la landing page
     */
    public function getHomePageData(): array
    {
        // 1. Obtener datos de diferentes fuentes
        $saleProducts = $this->productRepository->getOnSale(8);
        $liquidationProducts = $this->productRepository->getOnLiquidation(8);
        $featuredProducts = $this->productRepository->getFeatured(5);
        $categories = $this->categoryRepository->getAllActive();
        $stats = $this->getProductStats();

        // 2. Formatear cada conjunto de datos
        return [
            'saleProducts' => $this->formatter->formatCollection($saleProducts, 'formatForIndex'),
            'liquidationProducts' => $this->formatter->formatCollection($liquidationProducts, 'formatForIndex'),
            'carouselItems' => $this->formatter->formatCollection($featuredProducts, 'formatForCarousel'),
            'categories' => $categories->map(function ($category) {
                return $this->formatter->formatCategoryForHome($category);
            }),
            'stats' => $stats,
        ];
    }

    /**
     * Buscar productos
     */
    public function searchProducts(string $query, int $perPage = 20): LengthAwarePaginator
    {
        $products = $this->productRepository->search($query, $perPage);

        $products->getCollection()->transform(function ($product) {
            return $this->formatter->formatForIndex($product);
        });

        return $products;
    }

    /**
     * Obtener producto por ID (formateado como array)
     */
    public function getProductById(int $id): ?array
    {
        $product = $this->productRepository->findById($id);
        return $product ? $this->formatter->formatForShow($product) : null;
    }

    /**
     * Obtener producto por slug (formateado como array)
     * 
     * Incrementa las vistas automáticamente (lógica de negocio)
     */
    public function getProductBySlug(string $slug): ?array
    {
        $product = $this->productRepository->findBySlug($slug);
        if ($product) {
            // Lógica de negocio: Incrementar vistas
            $product->increment('views');
            return $this->formatter->formatForShow($product);
        }
        return null;
    }

    /**
     * Crear un nuevo producto
     * 
     * Orquesta:
     * 1. Crear el producto en la base de datos
     * 2. Procesar y guardar las imágenes
     * 3. Manejar errores con transacciones
     * 4. Registrar logs para auditoría
     */
    public function createProduct(array $data, array $images = []): object
    {
        try {
            DB::beginTransaction();

            // 1. Crear el producto (Repositorio)
            $product = $this->productRepository->create($data);

            // 2. Procesar imágenes si existen (Imagen Storage)
            if (!empty($images)) {
                $this->processProductImages($product->id, $images);
            }

            // 3. Confirmar transacción
            DB::commit();

            // 4. Registrar log (auditoría)
            Log::info('Producto creado exitosamente', [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'user_id' => auth()->id() ?? 'guest'
            ]);

            return $product;

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error al crear producto', [
                'error' => $e->getMessage(),
                'data' => $data,
                'trace' => $e->getTraceAsString()
            ]);

            throw $e;
        }
    }

    /**
     * Actualizar un producto
     */
    public function updateProduct(int $id, array $data, array $images = [], array $deleteImages = []): bool
    {
        try {
            DB::beginTransaction();

            // 1. Actualizar el producto
            $updated = $this->productRepository->update($id, $data);

            if (!$updated) {
                throw new \Exception('No se pudo actualizar el producto');
            }

            // 2. Eliminar imágenes seleccionadas
            if (!empty($deleteImages)) {
                $this->deleteProductImages($deleteImages);
            }

            // 3. Procesar nuevas imágenes
            if (!empty($images)) {
                $this->processProductImages($id, $images);
            }

            DB::commit();

            Log::info('Producto actualizado exitosamente', ['product_id' => $id]);

            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar producto', [
                'product_id' => $id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Eliminar un producto
     */
    public function deleteProduct(int $id): bool
    {
        try {
            // Obtener el producto para eliminar sus imágenes
            $product = $this->productRepository->findById($id);
            if (!$product) {
                throw new \Exception('Producto no encontrado');
            }

            // 1. Eliminar imágenes físicas (Image Storage)
            foreach ($product->images as $image) {
                $this->imageStorage->delete($image->image_path);
            }

            // 2. Eliminar el producto (Repositorio)
            $deleted = $this->productRepository->delete($id);

            Log::info('Producto eliminado exitosamente', ['product_id' => $id]);

            return $deleted;

        } catch (\Exception $e) {
            Log::error('Error al eliminar producto', [
                'product_id' => $id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Actualizar estado del producto
     */
    public function updateProductStatus(int $id, string $status): bool
    {
        return $this->productRepository->updateStatus($id, $status);
    }

    /**
     * Obtener productos relacionados
     * 
     * @return \Illuminate\Support\Collection Colección de arrays formateados
     */
    public function getRelatedProducts(int $productId, int $categoryId, int $limit = 4): Collection
    {
        $products = $this->productRepository->getRelated($productId, $categoryId, $limit);
        return $this->formatter->formatCollection($products, 'formatForIndex');
    }

    /**
     * Obtener estadísticas de productos
     */
    public function getProductStats(): array
    {
        return $this->productRepository->getStats();
    }

    /**
     * PROCESAR IMÁGENES
     * 
     * Método privado (encapsulación) para mantener SRP
     * Solo es llamado desde dentro de la clase
     * 
     * ¿Qué hace?
     * 1. Guarda las imágenes usando ImageStorageService
     * 2. Crea registros en la base de datos
     * 3. Establece la primera imagen como principal
     */
    private function processProductImages(int $productId, array $images): void
    {
        $product = $this->productRepository->findById($productId);
        $currentCount = $product->images()->count();

        foreach ($images as $index => $image) {
            // Guardar imagen (Image Storage Service)
            $path = $this->imageStorage->store($image, 'products');

            // Crear registro en BD
            $product->images()->create([
                'image_path' => $path,
                'is_primary' => $currentCount === 0 && $index === 0,
                'order' => $currentCount + $index,
            ]);
        }
    }

    /**
     * ELIMINAR IMÁGENES
     * 
     * Método privado para eliminar imágenes seleccionadas
     */
    private function deleteProductImages(array $imageIds): void
    {
        $images = ProductImage::whereIn('id', $imageIds)->get();

        foreach ($images as $image) {
            // Eliminar archivo físico
            $this->imageStorage->delete($image->image_path);

            // Eliminar registro de BD
            $image->delete();
        }
    }
}