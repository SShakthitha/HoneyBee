<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\GiftDesignController;
use App\Http\Controllers\LaserWorkController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;

// Home
Route::get('/', [HomeController::class, 'index'])->name('home');

// Services
Route::get('/services', [ServiceController::class, 'index'])->name('services');
Route::get('/services/{id}', [ServiceController::class, 'show'])->name('services.show');

// Gift & Design
Route::get('/gift-design', [GiftDesignController::class, 'index'])->name('gift.design');
Route::get('/gift-design/{id}', [GiftDesignController::class, 'show'])->name('gift.design.show');

// Laser Work
Route::get('/laser-work', [LaserWorkController::class, 'index'])->name('laser.work');
Route::get('/laser-work/{id}', [LaserWorkController::class, 'show'])->name('laser.work.show');

// Events
Route::get('/events', [EventController::class, 'index'])->name('events');
Route::get('/events/{id}', [EventController::class, 'show'])->name('events.show');

// Orders
Route::get('/orders', [OrderController::class, 'index'])->name('orders');
Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');

// Dashboard
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Profile
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

// Admin
Route::prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/services', [AdminController::class, 'services'])->name('admin.services');
    Route::get('/orders', [AdminController::class, 'orders'])->name('admin.orders');
    Route::get('/customers', [AdminController::class, 'customers'])->name('admin.customers');
    Route::get('/staff', [AdminController::class, 'staff'])->name('admin.staff');

    // Gift
    Route::get('/gift/create', [AdminController::class, 'createGift'])->name('admin.gift.create');
    Route::post('/gift/store', [AdminController::class, 'storeGift'])->name('admin.gift.store');
    Route::get('/gift/{id}/edit', [AdminController::class, 'editGift'])->name('admin.gift.edit');
    Route::put('/gift/{id}', [AdminController::class, 'updateGift'])->name('admin.gift.update');
    Route::delete('/gift/{id}', [AdminController::class, 'destroyGift'])->name('admin.gift.destroy');

    // Laser
    Route::get('/laser/create', [AdminController::class, 'createLaser'])->name('admin.laser.create');
    Route::post('/laser/store', [AdminController::class, 'storeLaser'])->name('admin.laser.store');
    Route::get('/laser/{id}/edit', [AdminController::class, 'editLaser'])->name('admin.laser.edit');
    Route::put('/laser/{id}', [AdminController::class, 'updateLaser'])->name('admin.laser.update');
    Route::delete('/laser/{id}', [AdminController::class, 'destroyLaser'])->name('admin.laser.destroy');

    // Events
    Route::get('/event/create', [AdminController::class, 'createEvent'])->name('admin.event.create');
    Route::post('/event/store', [AdminController::class, 'storeEvent'])->name('admin.event.store');
    Route::get('/event/{id}/edit', [AdminController::class, 'editEvent'])->name('admin.event.edit');
    Route::put('/event/{id}', [AdminController::class, 'updateEvent'])->name('admin.event.update');
    Route::delete('/event/{id}', [AdminController::class, 'destroyEvent'])->name('admin.event.destroy');
});