<?php

namespace App\Http\Controllers;

use App\Services\Contracts\Product\ProductServiceInterface;
use App\Http\Requests\ProductRequest;
use Illuminate\Http\Request;
use App\Repositories\Contracts\Product\CategoryRepositoryInterface;

/**
 * Class ProductController
 * 
 * CONTROLADOR DE PRODUCTOS
 * 
 * ¿Qué hace este controlador?
 * - Recibe peticiones HTTP
 * - Valida los datos (usando Request)
 * - Llama al servicio (ProductService) para la lógica de negocio
 * - Retorna respuestas (vistas, JSON, redirecciones)
 * 
 * PRINCIPIOS APLICADOS:
 * 
 * 1. SRP: Solo maneja peticiones HTTP y respuestas
 *    - NO tiene lógica de negocio
 *    - NO accede a la base de datos directamente
 *    - NO formatea datos
 * 
 * 2. DIP: Depende de ProductServiceInterface, no de ProductService
 *    - Podemos cambiar la implementación sin modificar el controlador
 *    - Fácil de testear (mockear el servicio)
 * 
 * 3. OCP: Abierto para extensión
 *    - Podemos agregar nuevos métodos sin modificar los existentes
 * 
 * 4. LSP: Cualquier implementación de ProductServiceInterface sirve
 * 
 * ¿Por qué esta estructura?
 * 
 * Sin SOLID (Controlador monolítico):
 * ```php
 * public function store(Request $request) {
 *     $product = Product::create($request->all());
 *     // Lógica de imágenes aquí...
 *     // Validaciones aquí...
 *     // Formateo aquí...
 *     // 200 líneas de código mezcladas
 * }
 * ```
 * 
 * Con SOLID (Controlador limpio):
 * ```php
 * public function store(ProductRequest $request) {
 *     $product = $this->productService->createProduct(
 *         $request->validated(),
 *         $request->file('images', [])
 *     );
 *     return redirect()->route('products.index')->with('success', 'Producto creado');
 * }
 * ```
 * 6 líneas vs 200 líneas - ¡Mucho más limpio y mantenible!
 */
class ProductController extends Controller
{
    protected $productService;

    /**
     * Inyección de dependencia del servicio
     * 
     * El controlador DEPENDE de la interfaz, no de la implementación
     * Esto es el corazón del Principio DIP
     */
    public function __construct(ProductServiceInterface $productService)
    {
        $this->productService = $productService;
    }

    /**
     * Lista de productos
     * 
     * GET /products
     */
    public function index(Request $request)
    {
        // 1. Obtener filtros del request
        $filters = [
            'search' => $request->search,
            'category' => $request->category,
            'status' => $request->status,
            'sort' => $request->sort,
            'min_price' => $request->min_price,
            'max_price' => $request->max_price,
        ];

        // 2. Llamar al servicio
        $products = $this->productService->getAllProducts($filters);

        // 3. Obtener categorías para el filtro
        $categories = app(CategoryRepositoryInterface::class)->getAllActive();

        // 4. Retornar vista
        return view('products.index', compact('products', 'categories'));
    }

    /**
     * Formulario para crear producto
     * 
     * GET /products/create
     */
    public function create()
    {
        $categories = app(CategoryRepositoryInterface::class)->getAllActive();
        return view('products.create', compact('categories'));
    }

    /**
     * Almacenar nuevo producto
     * 
     * POST /products
     */
    public function store(ProductRequest $request)
    {
        try {
            // 1. Crear el producto (Servicio)
            $product = $this->productService->createProduct(
                $request->validated(),
                $request->file('images', [])
            );

            // 2. Retornar respuesta
            return redirect()
                ->route('products.index')
                ->with('success', 'Producto "' . $product->name . '" creado exitosamente.');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Error al crear el producto: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar un producto específico
     * 
     * GET /products/{product}
     */
    public function show(string $slug)
    {
        // 1. Obtener producto por slug (Servicio)
        $product = $this->productService->getProductBySlug($slug);

        if (!$product) {
            abort(404, 'Producto no encontrado');
        }

        // 2. Obtener productos relacionados
        $relatedProducts = $this->productService->getRelatedProducts(
            $product['id'],
            $product['category']['id'] ?? 0
        );

        // 3. Retornar vista
        return view('products.show', compact('product', 'relatedProducts'));
    }

    /**
     * Formulario para editar producto
     * 
     * GET /products/{product}/edit
     */
    public function edit(int $id)
    {
        // 1. Obtener producto (Servicio)
        $product = $this->productService->getProductById($id);

        if (!$product) {
            abort(404, 'Producto no encontrado');
        }

        // 2. Obtener categorías
        $categories = app(CategoryRepositoryInterface::class)->getAllActive();

        // 3. Retornar vista
        return view('products.edit', compact('product', 'categories'));
    }

    /**
     * Actualizar producto
     * 
     * PUT/PATCH /products/{product}
     */
    public function update(ProductRequest $request, int $id)
    {
        try {
            // 1. Actualizar producto (Servicio)
            $updated = $this->productService->updateProduct(
                $id,
                $request->validated(),
                $request->file('images', []),
                $request->delete_images ?? []
            );

            if (!$updated) {
                throw new \Exception('No se pudo actualizar el producto');
            }

            // 2. Retornar respuesta
            return redirect()
                ->route('products.index')
                ->with('success', 'Producto actualizado exitosamente.');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Error al actualizar el producto: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar producto
     * 
     * DELETE /products/{product}
     */
    public function destroy(int $id)
    {
        try {
            // 1. Eliminar producto (Servicio)
            $deleted = $this->productService->deleteProduct($id);

            if (!$deleted) {
                throw new \Exception('No se pudo eliminar el producto');
            }

            // 2. Retornar respuesta
            return redirect()
                ->route('products.index')
                ->with('success', 'Producto eliminado exitosamente.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar el producto: ' . $e->getMessage());
        }
    }

    /**
     * Actualizar estado del producto (API)
     * 
     * PATCH /products/{product}/status
     */
    public function updateStatus(Request $request, int $id)
    {
        $request->validate([
            'status' => 'required|in:active,inactive,out_of_stock'
        ]);

        try {
            $updated = $this->productService->updateProductStatus($id, $request->status);

            return response()->json([
                'success' => $updated,
                'message' => $updated ? 'Estado actualizado correctamente.' : 'Error al actualizar estado.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}