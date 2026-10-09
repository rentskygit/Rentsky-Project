<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\PropertyCategoryController;
use App\Http\Controllers\PublicPropertyController;

/*
|--------------------------------------------------------------------------
| Rutas públicas (guest)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

/*
|--------------------------------------------------------------------------
| Rutas públicas (sin auth)
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('welcome');
Route::get('/welcome', [HomeController::class, 'index']);
Route::get('/search', [HomeController::class, 'search'])->name('search');

// Propiedades públicas
Route::get('/propiedades',        [PublicPropertyController::class, 'index'])->name('public.properties.index');
Route::get('/propiedades/{slug}', [PublicPropertyController::class, 'show'])->name('public.properties.show');

/*
|--------------------------------------------------------------------------
| Rutas protegidas (auth)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::resource('users', UserController::class);
    Route::resource('roles', RoleController::class);
    Route::resource('modules', ModuleController::class);
    Route::resource('products', ProductController::class);

    Route::resource('properties', PropertyController::class);
    Route::resource('property-categories', PropertyCategoryController::class)
        ->parameters(['property-categories' => 'propertyCategory'])
        ->except(['show']);
});