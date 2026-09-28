<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modelo ProductImage
 * 
 * Responsabilidad: Representar imágenes de productos
 * Principios: SRP - Solo maneja la representación de imágenes
 */
class ProductImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'image_path',
        'is_primary',
        'order'
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'order' => 'integer'
    ];

    // ── Relaciones ──
    
    /**
     * Relación con producto (pertenece a)
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // ── Accessors ──
    
    /**
     * Obtener URL completa de la imagen
     */
    public function getImageUrlAttribute()
    {
        return $this->image_path ? asset('storage/' . $this->image_path) : null;
    }
}