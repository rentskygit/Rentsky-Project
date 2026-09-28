<?php

namespace App\Http\Controllers;

use App\Services\Contracts\Product\ProductServiceInterface;
use Illuminate\Http\Request;

/**
 * Class HomeController
 * 
 * Controlador de la página de inicio
 */
class HomeController extends Controller
{
    protected $productService;

    public function __construct(ProductServiceInterface $productService)
    {
        $this->productService = $productService;
    }

    /**
     * Página de inicio
     */
    public function index()
    {
        // Obtener todos los datos necesarios del servicio
        $homeData = $this->productService->getHomePageData();

        return view('home', $homeData);
    }

    /**
     * Búsqueda global
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        
        if (empty($query)) {
            return redirect()->route('home');
        }

        $products = $this->productService->searchProducts($query);

        return view('search', compact('products', 'query'));
    }
}