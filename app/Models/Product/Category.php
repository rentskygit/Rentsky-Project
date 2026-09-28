<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Modelo Category
 * 
 * Responsabilidad: Representar la entidad Categoría en la base de datos
 * Principios: SRP - Solo maneja la representación de datos
 */
class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    // ── Relaciones ──
    
    /**
     * Relación con productos (uno a muchos)
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Relación con productos activos (scope)
     */
    public function activeProducts()
    {
        return $this->hasMany(Product::class)->where('status', 'active');
    }

    // ── Scopes ──
    
    /**
     * Scope para categorías activas
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // ── Mutators ──
    
    /**
     * Generar slug automáticamente
     */
    public function setSlugAttribute($value)
    {
        $this->attributes['slug'] = Str::slug($value);
    }

    // ── Accessors ──
    
    /**
     * Obtener conteo de productos
     */
    public function getProductCountAttribute()
    {
        return $this->activeProducts()->count();
    }

    /**
     * Obtener HTML del icono
     */
    public function getIconHtmlAttribute()
    {
        return $this->icon ? '<i class="' . $this->icon . '"></i>' : '';
    }
}