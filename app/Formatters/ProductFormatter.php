<?php

namespace App\Formatters;

use App\Models\Product\Product;
use App\Models\Product\Category;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
/**
 * Class ProductFormatter
 * 
 * FORMATEADOR DE PRODUCTOS
 * 
 * ¿Qué hace?
 * - Transforma objetos Product en arrays formateados para diferentes contextos
 * - Separa la lógica de presentación del modelo
 * - Centraliza el formato en un solo lugar
 * 
 * PRINCIPIOS APLICADOS:
 * 
 * 1. SRP: Única responsabilidad - Formatear datos para presentación
 * 2. OCP: Podemos agregar nuevos formatos sin modificar los existentes
 * 3. DIP: Puede ser usado por cualquier clase que necesite formateo
 * 
 * VENTAJAS:
 * - Los controladores no tienen lógica de formato
 * - Los modelos no tienen responsabilidades de presentación
 * - Fácil de mantener y modificar
 * - Reutilizable en diferentes contextos (web, API, reportes)
 * 
 * EJEMPLO DE USO:
 * ```php
 * $formatter = new ProductFormatter();
 * $formattedProduct = $formatter->formatForIndex($product);
 * $formattedProducts = $formatter->formatCollection($products, 'forIndex');
 * ```
 */
class ProductFormatter
{
    /**
     * Formatear producto para la lista (index)
     * 
     * Contexto: Tabla de productos, cards de productos, resultados de búsqueda
     * 
     * ¿Qué datos incluye?
     * - Información básica del producto
     * - Datos formateados para mostrar (precio, estado)
     * - URLs para acciones (ver, editar)
     * - Datos de la categoría
     * - Imagen principal
     */
    public function formatForIndex(Product $product): array
    {
        return [
            // Datos básicos
            'id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'sku' => $product->sku,
            'description' => $this->truncate($product->description, 100),
            
            // Precios
            'price' => $product->formatted_price,
            'price_raw' => $product->price,
            'original_price' => $product->formatted_original_price,
            'original_price_raw' => $product->original_price,
            'discount_percentage' => $product->discount_percentage,
            'has_discount' => $product->hasDiscount(),
            
            // Stock
            'stock' => $product->stock,
            'stock_status' => $this->getStockStatus($product->stock),
            'in_stock' => $product->isInStock(),
            
            // Estado
            'status' => $product->status,
            'status_label' => $this->getStatusLabel($product->status),
            'status_color' => $this->getStatusColor($product->status),
            
            // Badge
            'badge' => $product->badge,
            'badge_label' => $product->badge_label,
            'badge_class' => $product->badge_class,
            
            // Categoría
            'category' => $product->category ? [
                'id' => $product->category->id,
                'name' => $product->category->name,
                'slug' => $product->category->slug,
            ] : null,
            
            // Imagen
            'primary_image' => $product->primaryImage ? [
                'url' => $product->primaryImage->image_url,
                'path' => $product->primaryImage->image_path,
                'alt' => $product->name,
            ] : null,
            
            // URLs
            'url' => route('products.show', $product),
            'edit_url' => route('products.edit', $product),
            'delete_url' => route('products.destroy', $product),
            
            // Fechas
            'created_at' => $product->created_at->format('d/m/Y H:i'),
            'created_at_diff' => $product->created_at->diffForHumans(),
            'updated_at' => $product->updated_at->format('d/m/Y H:i'),
            
            // Metadata
            'featured' => $product->featured,
            'views' => $product->views,
        ];
    }

    /**
     * Formatear producto para la vista detallada (show)
     * 
     * Contexto: Página de detalle de producto
     * 
     * ¿Qué datos incluye?
     * - Todos los datos del producto
     * - Todas las imágenes
     * - Datos extendidos de la categoría
     * - Metadatos adicionales
     */
    public function formatForShow(Product $product): array
    {
        return [
            // Datos básicos
            'id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'sku' => $product->sku,
            'description' => $product->description,
            'description_html' => $this->formatDescription($product->description),
            
            // Precios
            'price' => $product->formatted_price,
            'price_raw' => $product->price,
            'original_price' => $product->formatted_original_price,
            'original_price_raw' => $product->original_price,
            'discount_percentage' => $product->discount_percentage,
            'discount_amount' => $this->formatMoney($product->discount_amount),
            'discount_amount_raw' => $product->discount_amount,
            'has_discount' => $product->hasDiscount(),
            
            // Stock
            'stock' => $product->stock,
            'stock_status' => $this->getStockStatus($product->stock),
            'in_stock' => $product->isInStock(),
            'stock_message' => $product->isInStock() ? 'Disponible' : 'Agotado',
            
            // Estado
            'status' => $product->status,
            'status_label' => $this->getStatusLabel($product->status),
            'status_color' => $this->getStatusColor($product->status),
            
            // Badge
            'badge' => $product->badge,
            'badge_label' => $product->badge_label,
            'badge_class' => $product->badge_class,
            
            // Categoría (datos extendidos)
            'category' => $product->category ? [
                'id' => $product->category->id,
                'name' => $product->category->name,
                'slug' => $product->category->slug,
                'description' => $product->category->description,
                'icon' => $product->category->icon,
                'icon_html' => $product->category->icon_html,
            ] : null,
            
            // Imágenes (todas)
            'images' => $product->images->map(function ($image) {
                return [
                    'id' => $image->id,
                    'url' => $image->image_url,
                    'path' => $image->image_path,
                    'is_primary' => $image->is_primary,
                    'order' => $image->order,
                ];
            })->values()->toArray(),
            
            // Imagen principal
            'primary_image' => $product->primaryImage ? [
                'id' => $product->primaryImage->id,
                'url' => $product->primaryImage->image_url,
                'path' => $product->primaryImage->image_path,
            ] : null,
            
            // URLs
            'url' => route('products.show', $product),
            'edit_url' => route('products.edit', $product),
            'category_url' => $product->category ? route('products.index', ['category' => $product->category->id]) : null,
            
            // Fechas
            'created_at' => $product->created_at->format('d/m/Y H:i'),
            'created_at_diff' => $product->created_at->diffForHumans(),
            'updated_at' => $product->updated_at->format('d/m/Y H:i'),
            
            // Metadata
            'featured' => $product->featured,
            'views' => $product->views,
            
            // Datos para SEO
            'seo' => [
                'title' => $product->name . ' - RentSky',
                'description' => $this->truncate($product->description, 160),
                'image' => $product->primaryImage ? $product->primaryImage->image_url : null,
            ],
        ];
    }

    /**
     * Formatear producto para el carrusel
     * 
     * Contexto: Carrusel de la página de inicio
     * Datos más ligeros y enfocados en presentación
     */
    public function formatForCarousel(Product $product): array
    {
        return [
            'id' => $product->id,
            'title' => $product->name,
            'description' => $this->truncate($product->description, 80),
            'price' => $product->formatted_price,
            'tag' => $product->badge === 'sale' ? '🔥 Hasta ' . $product->discount_percentage . '% OFF' : 
                      ($product->badge === 'liquidation' ? '💥 Liquidación' : 
                      ($product->badge === 'new' ? '✨ Nuevo' : '')),
            'icon' => $this->getProductIcon($product->category?->name ?? 'producto'),
            'image' => $product->primaryImage ? $product->primaryImage->image_url : null,
            'url' => route('products.show', $product),
        ];
    }

    /**
     * Formatear categoría para la página de inicio
     */
    public function formatCategoryForHome(Category $category): array
    {
        return [
            'id' => $category->id,
            'name' => $category->name,
            'slug' => $category->slug,
            'icon' => $category->icon,
            'icon_html' => $category->icon_html,
            'count' => $category->products_count ?? $category->products()->count(),
            'url' => route('products.index', ['category' => $category->id]),
        ];
    }

    /**
     * Formatear una colección de productos
     * 
     * Método utilitario para formatear múltiples productos
     * 
     * @param Collection $products Colección de productos
     * @param string $method Método de formato a usar (forIndex, forShow, forCarousel)
     * @return Collection Colección formateada
     */
    public function formatCollection(EloquentCollection $products, string $method): Collection
    {
        if (!str_starts_with($method, 'format')) {
            $method = 'format' . ucfirst($method);
        }

        if (!method_exists($this, $method)) {
            throw new \InvalidArgumentException(
                "El método '{$method}' no existe en " . static::class
            );
        }

        return $products->map(function ($product) use ($method) {
            return $this->$method($product);
        });
    }

    // ── Métodos auxiliares privados ──
    
    /**
     * Obtener estado del stock
     */
    private function getStockStatus(int $stock): string
    {
        if ($stock <= 0) return 'Sin stock';
        if ($stock <= 5) return 'Pocas unidades';
        if ($stock <= 20) return 'Stock medio';
        return 'Stock disponible';
    }

    /**
     * Obtener etiqueta del estado
     */
    private function getStatusLabel(string $status): string
    {
        return match($status) {
            'active' => 'Activo',
            'inactive' => 'Inactivo',
            'out_of_stock' => 'Sin Stock',
            default => $status
        };
    }

    /**
     * Obtener color del estado
     */
    private function getStatusColor(string $status): string
    {
        return match($status) {
            'active' => '#16a34a',
            'inactive' => '#991b1b',
            'out_of_stock' => '#92400e',
            default => '#6b7280'
        };
    }

    /**
     * Obtener icono según categoría
     */
    private function getProductIcon(string $category): string
    {
        $icons = [
            'electronica' => 'bi bi-laptop',
            'ropa' => 'bi bi-hanger',
            'hogar' => 'bi bi-house-heart',
            'accesorios' => 'bi bi-watch',
            'muebles' => 'bi bi-sofa',
            'jardin' => 'bi bi-tree',
        ];

        $categoryLower = strtolower($category);
        return $icons[$categoryLower] ?? 'bi bi-box';
    }

    /**
     * Truncar texto
     */
    private function truncate(?string $text, int $length): string
    {
        if (!$text) return '';
        if (strlen($text) <= $length) return $text;
        return substr($text, 0, $length) . '...';
    }

    /**
     * Formatear descripción (convertir saltos de línea a HTML)
     */
    private function formatDescription(?string $description): string
    {
        if (!$description) return '';
        return nl2br(e($description));
    }

    /**
     * Formatear dinero
     */
    private function formatMoney(float $amount): string
    {
        return '$' . number_format($amount, 2);
    }
}