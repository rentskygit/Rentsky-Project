<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class ProductRequest
 * 
 * REQUEST DE PRODUCTOS
 * 
 * ¿Qué hace?
 * - Valida los datos enviados al crear/actualizar un producto
 * - Centraliza las reglas de validación
 * - Separa la validación del controlador
 * 
 * PRINCIPIOS APLICADOS:
 * 
 * 1. SRP: Solo se encarga de validar datos
 * 2. Separación de responsabilidades: El controlador no valida
 * 
 * VENTAJAS:
 * - Reglas de validación reutilizables
 * - Mensajes de error centralizados
 * - Fácil de mantener
 * - El controlador queda más limpio
 */
class ProductRequest extends FormRequest
{
    /**
     * Determinar si el usuario está autorizado
     */
    public function authorize(): bool
    {
        // Aquí puedes agregar lógica de autorización
        // Ejemplo: return auth()->user()->can('create products');
        return true;
    }

    /**
     * Reglas de validación
     * 
     * Las reglas se aplican según si es creación o actualización
     */
    public function rules(): array
    {
        $productParam = $this->route('product');
        $productId = is_object($productParam) ? $productParam->id : $productParam;

        // Regla base para unique (sin ID a excluir)
        $uniqueSlug = 'unique:products,slug';
        $uniqueSku = 'unique:products,sku';

        // Si estamos actualizando, agregar el ID a excluir
        if ($productId) {
            $uniqueSlug .= ',' . $productId;
            $uniqueSku .= ',' . $productId;
        }

        return [
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:200',
            'slug' => 'nullable|string|max:200|' . $uniqueSlug,
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0|max:999999.99',
            'original_price' => 'nullable|numeric|min:0|max:999999.99|gt:price',
            'stock' => 'required|integer|min:0|max:999999',
            'sku' => 'required|string|max:50|' . $uniqueSku,
            'status' => ['required', Rule::in(['active', 'inactive', 'out_of_stock'])],
            'badge' => ['required', Rule::in(['sale', 'liquidation', 'new', 'none'])],
            'featured' => 'nullable|boolean',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'delete_images' => 'nullable|array',
            'delete_images.*' => 'exists:product_images,id',
        ];
    }

    /**
     * Mensajes de error personalizados
     */
    public function messages(): array
    {
        return [
            'category_id.required' => 'Debes seleccionar una categoría.',
            'category_id.exists' => 'La categoría seleccionada no existe.',
            
            'name.required' => 'El nombre del producto es obligatorio.',
            'name.max' => 'El nombre no debe superar los 200 caracteres.',
            
            'price.required' => 'El precio del producto es obligatorio.',
            'price.numeric' => 'El precio debe ser un número válido.',
            'price.min' => 'El precio debe ser mayor o igual a 0.',
            
            'original_price.gt' => 'El precio original debe ser mayor que el precio de venta.',
            
            'stock.required' => 'El stock del producto es obligatorio.',
            'stock.integer' => 'El stock debe ser un número entero.',
            'stock.min' => 'El stock debe ser mayor o igual a 0.',
            
            'sku.required' => 'El SKU del producto es obligatorio.',
            'sku.unique' => 'Este SKU ya está en uso por otro producto.',
            
            'status.required' => 'El estado del producto es obligatorio.',
            'status.in' => 'El estado seleccionado no es válido.',
            
            'badge.required' => 'La etiqueta del producto es obligatoria.',
            'badge.in' => 'La etiqueta seleccionada no es válida.',
            
            'images.*.image' => 'El archivo debe ser una imagen válida.',
            'images.*.max' => 'La imagen no debe superar los 2MB.',
            'images.*.mimes' => 'La imagen debe ser de tipo: jpeg, png, jpg, gif, webp.',
            
            'delete_images.*.exists' => 'Una de las imágenes seleccionadas no existe.',
        ];
    }

    /**
     * Preparar los datos para la validación
     * 
     * Se ejecuta antes de la validación
     */
    protected function prepareForValidation(): void
    {
        // Si no se envía featured, asignar false
        if (!$this->has('featured')) {
            $this->merge(['featured' => false]);
        }

        // Si no se envía badge, asignar 'none'
        if (!$this->has('badge')) {
            $this->merge(['badge' => 'none']);
        }

        // Generar slug si no se proporciona
        if (!$this->filled('slug') && $this->filled('name')) {
            $this->merge(['slug' => \Illuminate\Support\Str::slug($this->name)]);
        }
    }
}