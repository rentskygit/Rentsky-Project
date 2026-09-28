<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Modelo Product
 * 
 * Responsabilidad: Representar la entidad Producto en la base de datos
 * Principios: SRP - Solo maneja la representación de datos
 */
class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'price',
        'original_price',
        'stock',
        'sku',
        'status',
        'badge',
        'featured',
        'views'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'original_price' => 'decimal:2',
        'featured' => 'boolean',
        'views' => 'integer'
    ];

    // ── Relaciones ──
    
    /**
     * Relación con categoría (pertenece a)
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Relación con imágenes (uno a muchos)
     */
    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('order');
    }

    /**
     * Relación con imagen principal
     */
    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    // ── Scopes (Filtros reutilizables) ──
    
    /**
     * Scope: Productos activos
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope: Productos con stock
     */
    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    /**
     * Scope: Productos en oferta
     */
    public function scopeOnSale($query)
    {
        return $query->where('badge', 'sale')
                    ->whereColumn('original_price', '>', 'price');
    }

    /**
     * Scope: Productos en liquidación
     */
    public function scopeOnLiquidation($query)
    {
        return $query->where('badge', 'liquidation');
    }

    /**
     * Scope: Productos destacados
     */
    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    // ── Mutators ──
    
    /**
     * Generar slug automáticamente
     */
    public function setSlugAttribute($value)
    {
        $this->attributes['slug'] = Str::slug($value);
    }

    // ── Accessors (Atributos calculados) ──
    
    /**
     * Precio formateado
     */
    public function getFormattedPriceAttribute()
    {
        return '$' . number_format($this->price, 2);
    }

    /**
     * Precio original formateado
     */
    public function getFormattedOriginalPriceAttribute()
    {
        return $this->original_price ? '$' . number_format($this->original_price, 2) : null;
    }

    /**
     * Porcentaje de descuento
     */
    public function getDiscountPercentageAttribute()
    {
        if ($this->original_price && $this->original_price > 0) {
            return round((($this->original_price - $this->price) / $this->original_price) * 100);
        }
        return 0;
    }
    public function getDiscountAmountAttribute()
    {
        if ($this->original_price && $this->hasDiscount()) {
            return $this->original_price - $this->price;
        }
        return 0;
    }

    /**
     * Clase CSS para la etiqueta (badge)
     */
    public function getBadgeClassAttribute()
    {
        return match($this->badge) {
            'sale' => 'sale',
            'liquidation' => 'liquidation',
            'new' => 'new',
            default => ''
        };
    }

    /**
     * Etiqueta legible para el badge
     */
    public function getBadgeLabelAttribute()
    {
        return match($this->badge) {
            'sale' => 'Oferta',
            'liquidation' => 'Liquidación',
            'new' => 'Nuevo',
            default => ''
        };
    }

    // ── Métodos de utilidad ──
    
    /**
     * Verificar si tiene stock
     */
    public function isInStock()
    {
        return $this->stock > 0;
    }

    /**
     * Verificar si tiene descuento
     */
    public function hasDiscount()
    {
        return $this->original_price && $this->price < $this->original_price;
    }
}