<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\PropertyCategoryController;
use App\Http\Controllers\PublicPropertyController;
use App\Http\Controllers\QualificationController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\ApartmentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ShowingController;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::get('/', [HomeController::class, 'index'])->name('welcome');
Route::get('/welcome', [HomeController::class, 'index']);

Route::get('/apartments', [ApartmentController::class, 'index'])->name('apartments.index');
Route::get('/apartments/{slug}', [ApartmentController::class, 'show'])->name('apartments.show');

Route::get('/propiedades', [PublicPropertyController::class, 'index'])->name('public.properties.index');
Route::get('/propiedades/{slug}', [PublicPropertyController::class, 'show'])->name('public.properties.show');

Route::post('/chatbot/message', [ChatbotController::class, 'message'])
    ->middleware('throttle:40,1')
    ->name('chatbot.message');

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    Route::prefix('showings')->name('showings.')->group(function () {
        Route::get('/', [ShowingController::class, 'index'])->name('index');
        Route::get('/eligibility/{property}', [ShowingController::class, 'eligibility'])->name('eligibility');
        Route::post('/', [ShowingController::class, 'store'])->name('store');
        Route::post('/quick-store', [ShowingController::class, 'quickStore'])->name('quick-store');
        Route::get('/user/{user}', [ShowingController::class, 'showUser'])->name('user');
        Route::post('/user/{user}', [ShowingController::class, 'storeForUser'])->name('user.store');
        Route::get('/user/{user}/full-dates', [ShowingController::class, 'userFullDates'])->name('user.full-dates');
        Route::put('/{showing}', [ShowingController::class, 'update'])->name('update');
        Route::patch('/{showing}/status', [ShowingController::class, 'updateStatus'])->name('status');
        Route::delete('/{showing}', [ShowingController::class, 'destroy'])->name('destroy');
    });
});

Route::middleware(['auth', 'module.access'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::resource('users', UserController::class);
    Route::resource('roles', RoleController::class);
    Route::resource('modules', ModuleController::class);

    Route::resource('properties', PropertyController::class);
    Route::resource('property-categories', PropertyCategoryController::class)
        ->parameters(['property-categories' => 'propertyCategory'])
        ->except(['show']);

    Route::get('qualifications/check-user/{user}', [QualificationController::class, 'checkUser'])
        ->name('qualifications.check-user');

    Route::resource('qualifications', QualificationController::class)
        ->except(['destroy']);

    Route::get('qualifications/{qualification}/pdf', [QualificationController::class, 'pdf'])
        ->name('qualifications.pdf');

    Route::get('applications/check-user/{user}', [ApplicationController::class, 'checkUser'])
        ->name('applications.check-user');

    Route::resource('applications', ApplicationController::class)
        ->except(['destroy']);

    Route::get('applications/{application}/pdf', [ApplicationController::class, 'pdf'])
        ->name('applications.pdf');

    Route::get('clients', [ClientController::class, 'index'])->name('clients.index');
    Route::put('clients/{user}/assign', [ClientController::class, 'assign'])->name('clients.assign');
});