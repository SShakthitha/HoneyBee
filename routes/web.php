<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\GiftDesignController;
use App\Http\Controllers\LaserWorkController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\CustomerDashboardController;
use App\Http\Controllers\CustomerProfileController;
use App\Http\Controllers\Admin\GalleryImageController;

// Home
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::view('/about', 'about')->name('about');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
Route::post('/contact', [HomeController::class, 'sendContact'])->name('contact.send');

// Services
Route::get('/services', [ServiceController::class, 'index'])->name('services');
Route::get('/services/{id}', [ServiceController::class, 'show'])->name('services.show');

// Gift & Design (frontend)
Route::get('/gift-design', [GiftDesignController::class, 'index'])->name('gift.design');
Route::get('/gift-and-design', [GiftDesignController::class, 'index'])->name('gift.and.design');
Route::get('/gift', [GiftDesignController::class, 'index'])->name('gift');

// Laser Work (frontend)
Route::get('/laser-work', [LaserWorkController::class, 'index'])->name('laser.work');
Route::get('/lesar-work', [LaserWorkController::class, 'index'])->name('lesar.work');
Route::get('/laser', [LaserWorkController::class, 'index'])->name('laser');

// Events (frontend)
Route::get('/events', [EventController::class, 'index'])->name('events');
Route::get('/event', [EventController::class, 'index'])->name('event');

// Orders (frontend)
Route::middleware('auth')->group(function () {
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/create/{service}', [OrderController::class, 'create'])->name('orders.create');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
});

// Dashboard
Route::get('/dashboard', [CustomerDashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {

    Route::get('/customer/dashboard', [CustomerDashboardController::class, 'index'])->name('customer.dashboard');

    Route::get('/profile/edit', [CustomerProfileController::class, 'edit'])->name('customer.profile.edit');

    Route::put('/profile/update', [CustomerProfileController::class, 'update'])->name('customer.profile.update');

});

// Profile
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';


Route::prefix('admin')->group(function () {

   // Dashboard
   Route::get('/', [AdminController::class, 'index'])->name('admin.dashboard');

   //Gallery
   Route::get('/gallery', [GalleryImageController::class, 'index'])->name('admin.gallery.index');
   Route::get('/gallery/create', [GalleryImageController::class, 'create'])->name('admin.gallery.create');
   Route::post('/gallery', [GalleryImageController::class, 'store'])->name('admin.gallery.store');
   Route::delete('/gallery/{id}', [GalleryImageController::class, 'destroy'])->name('admin.gallery.delete');
   // Businesses
   Route::get('/businesses', [AdminController::class, 'businesses'])->name('admin.businesses');
   Route::get('/businesses/create', [AdminController::class, 'createBusiness'])->name('admin.businesses.create');
   Route::get('/businesses/data', [AdminController::class, 'businessesData'])->name('admin.businesses.data');
   Route::post('/businesses/store', [AdminController::class, 'storeBusiness'])->name('admin.businesses.store');
   Route::get('/businesses/{id}/edit', [AdminController::class, 'editBusiness'])->name('admin.businesses.edit');
   Route::put('/businesses/{id}', [AdminController::class, 'updateBusiness'])->name('admin.businesses.update');
   Route::delete('/businesses/{id}', [AdminController::class, 'deleteBusiness'])->name('admin.businesses.delete');

    // Services
    Route::get('/services', [AdminController::class, 'services'])->name('admin.services');
    Route::get('/gift/create', [AdminController::class, 'createGift'])->name('admin.gift.create');
    Route::get('/laser/create', [AdminController::class, 'createLaser'])->name('admin.laser.create');
    Route::get('/event/create', [AdminController::class, 'createEvent'])->name('admin.event.create');

    // Orders
    Route::get('/orders', [AdminController::class, 'orders'])->name('admin.orders');
    Route::put('/orders/{id}/status', [AdminController::class, 'updateOrderStatus'])->name('admin.orders.status');

   // Customers
   Route::get('/customers', [CustomerController::class, 'index'])->name('admin.customers.index');
   Route::get('/customers/data', [CustomerController::class, 'data'])->name('admin.customers.data');
   Route::post('/customers', [CustomerController::class, 'store'])->name('admin.customers.store');
   Route::put('/customers/{id}', [CustomerController::class, 'update'])->name('admin.customers.update');
   Route::delete('/customers/{id}', [CustomerController::class, 'destroy'])->name('admin.customers.delete');
   Route::get('/customers/{id}/edit', [CustomerController::class, 'edit'])->name('admin.customers.edit');

    // Gift
    Route::post('/gift/store', [AdminController::class, 'storeGift'])->name('admin.gift.store');
    Route::get('/gift/{id}/edit', [AdminController::class, 'editGift'])->name('admin.gift.edit');
    Route::put('/gift/{id}', [AdminController::class, 'updateGift'])->name('admin.gift.update');
    Route::delete('/gift/{id}', [AdminController::class, 'destroyGift'])->name('admin.gift.destroy');

    // Laser
    Route::post('/laser/store', [AdminController::class, 'storeLaser'])->name('admin.laser.store');
    Route::get('/laser/{id}/edit', [AdminController::class, 'editLaser'])->name('admin.laser.edit');
    Route::put('/laser/{id}', [AdminController::class, 'updateLaser'])->name('admin.laser.update');
    Route::delete('/laser/{id}', [AdminController::class, 'destroyLaser'])->name('admin.laser.destroy');

    // Events
    Route::post('/event/store', [AdminController::class, 'storeEvent'])->name('admin.event.store');
    Route::get('/event/{id}/edit', [AdminController::class, 'editEvent'])->name('admin.event.edit');
    Route::put('/event/{id}', [AdminController::class, 'updateEvent'])->name('admin.event.update');
    Route::delete('/event/{id}', [AdminController::class, 'destroyEvent'])->name('admin.event.destroy');
    
   // Staff
    Route::get('/staff', [StaffController::class, 'index'])->name('admin.staff');
    Route::get('/staff/data', [StaffController::class, 'data'])->name('admin.staff.data');
    Route::post('/staff', [StaffController::class, 'store'])->name('admin.staff.store');
    Route::get('/staff/{id}/edit', [StaffController::class, 'edit'])->name('admin.staff.edit');
    Route::put('/staff/{id}', [StaffController::class, 'update'])->name('admin.staff.update');
    Route::delete('/staff/{id}', [StaffController::class, 'destroy'])->name('admin.staff.delete');
});
