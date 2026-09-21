<?php

use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\BulkOfferController;
use App\Http\Controllers\Admin\ExportController;
use App\Http\Controllers\Admin\GalleryImageController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CustomerDashboardController;
use App\Http\Controllers\CheckoutConfirmationController;
use App\Http\Controllers\Admin\WhatsAppSettingsController;
use App\Http\Controllers\CustomerProfileController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\GiftDesignController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LaserWorkController;
use App\Http\Controllers\ManagerStaffController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\StaffDashboardController;
use App\Models\Customer;
use Illuminate\Support\Facades\Route;

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

    // Gift & Design cart checkout. The cart itself remains in the browser so it
    // can be kept while the customer signs in and navigates to this page.
    Route::get('/checkout', function () {
        $customer = Customer::where('email', auth()->user()->email)->first();

        return view('checkout', compact('customer'));
    })->name('checkout');

    Route::post('/checkout', [OrderController::class, 'storeCartOrder'])->name('checkout.store');
    Route::get('/orders/{order}/confirmation', [CheckoutConfirmationController::class, 'show'])->name('checkout.confirmation');
    Route::get('/orders/{id}/pdf', [OrderController::class, 'downloadPdf'])->name('orders.pdf');
    Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
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

/*
 | Operational workspace.  This is intentionally separate from /admin:
 | staff can fulfil orders and maintain the catalogue/gallery, but cannot
 | reach customer or staff administration routes.
 */
Route::prefix('staff')->middleware(['auth', 'staff'])->name('staff.')->group(function () {
    Route::get('/dashboard', [StaffDashboardController::class, 'dashboard'])->name('dashboard');

    Route::get('/orders', [StaffDashboardController::class, 'orders'])->name('orders.index');
    Route::get('/orders/{id}', [StaffDashboardController::class, 'showOrder'])->name('orders.show');
    Route::put('/orders/{id}/status', [StaffDashboardController::class, 'updateOrderStatus'])->name('orders.status');

    Route::get('/products', [StaffDashboardController::class, 'products'])->name('products.index');
    Route::get('/products/{type}/create', [StaffDashboardController::class, 'productForm'])->name('products.create');
    Route::post('/products/{type}', [StaffDashboardController::class, 'saveProduct'])->name('products.store');
    Route::get('/products/{type}/{id}/edit', [StaffDashboardController::class, 'productForm'])->name('products.edit');
    Route::put('/products/{type}/{id}', [StaffDashboardController::class, 'saveProduct'])->name('products.update');
    Route::delete('/products/{type}/{id}', [StaffDashboardController::class, 'deleteProduct'])->name('products.destroy');

    Route::get('/gallery', [StaffDashboardController::class, 'gallery'])->name('gallery.index');
    Route::get('/gallery/create', [StaffDashboardController::class, 'galleryForm'])->name('gallery.create');
    Route::post('/gallery', [StaffDashboardController::class, 'saveGallery'])->name('gallery.store');
    Route::get('/gallery/{id}/edit', [StaffDashboardController::class, 'galleryForm'])->name('gallery.edit');
    Route::put('/gallery/{id}', [StaffDashboardController::class, 'saveGallery'])->name('gallery.update');
    Route::delete('/gallery/{id}', [StaffDashboardController::class, 'deleteGallery'])->name('gallery.destroy');

    Route::get('/promotions', [PromotionController::class, 'index'])->name('promotions.index');
    Route::get('/promotions/create', [PromotionController::class, 'create'])->name('promotions.create');
    Route::post('/promotions', [PromotionController::class, 'store'])->name('promotions.store');
    Route::get('/promotions/{promotion}/edit', [PromotionController::class, 'edit'])->name('promotions.edit');
    Route::put('/promotions/{promotion}', [PromotionController::class, 'update'])->name('promotions.update');
    Route::delete('/promotions/{promotion}', [PromotionController::class, 'destroy'])->name('promotions.destroy');

    Route::get('/profile', [StaffDashboardController::class, 'profile'])->name('profile');
    Route::put('/profile', [StaffDashboardController::class, 'updateProfile'])->name('profile.update');

    Route::middleware('manager')->prefix('staff')->name('staff.')->group(function () {
        Route::get('/', [ManagerStaffController::class, 'index'])->name('index');
        Route::get('/create', [ManagerStaffController::class, 'create'])->name('create');
        Route::post('/', [ManagerStaffController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [ManagerStaffController::class, 'edit'])->name('edit');
        Route::put('/{id}', [ManagerStaffController::class, 'update'])->name('update');
        Route::delete('/{id}', [ManagerStaffController::class, 'destroy'])->name('destroy');
    });
});

Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {

   Route::get('/settings/whatsapp', [WhatsAppSettingsController::class, 'edit'])->name('admin.settings.whatsapp.edit');
   Route::put('/settings/whatsapp', [WhatsAppSettingsController::class, 'update'])->name('admin.settings.whatsapp.update');

   Route::get('/exports/customers', [ExportController::class, 'customers'])->name('admin.exports.customers');
   Route::get('/exports/products', [ExportController::class, 'products'])->name('admin.exports.products');
   Route::get('/exports/orders', [ExportController::class, 'orders'])->name('admin.exports.orders');
   Route::get('/exports/staff', [ExportController::class, 'staff'])->name('admin.exports.staff');
   Route::post('/bulk-offers', [BulkOfferController::class, 'apply'])->name('admin.bulk-offers.apply');
   Route::delete('/bulk-offers', [BulkOfferController::class, 'remove'])->name('admin.bulk-offers.remove');

    // Dashboard
    Route::get('/', [AdminController::class, 'index'])->name('admin.dashboard');

    // Gallery
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
    Route::get('/gallery/{id}/edit', [GalleryImageController::class, 'edit'])->name('admin.gallery.edit');
    Route::put('/gallery/{id}', [GalleryImageController::class, 'update'])->name('admin.gallery.update');
    Route::delete('/businesses/{id}', [AdminController::class, 'deleteBusiness'])->name('admin.businesses.delete');

    // Promotions
    Route::get('/promotions', [PromotionController::class, 'index'])->name('admin.promotions.index');
    Route::get('/promotions/create', [PromotionController::class, 'create'])->name('admin.promotions.create');
    Route::post('/promotions', [PromotionController::class, 'store'])->name('admin.promotions.store');
    Route::get('/promotions/{promotion}/edit', [PromotionController::class, 'edit'])->name('admin.promotions.edit');
    Route::put('/promotions/{promotion}', [PromotionController::class, 'update'])->name('admin.promotions.update');
    Route::delete('/promotions/{promotion}', [PromotionController::class, 'destroy'])->name('admin.promotions.destroy');

    // Services
    Route::get('/services', [AdminController::class, 'services'])->name('admin.services');
    Route::get('/services/gift-design', [AdminController::class, 'serviceSection'])->defaults('section', 'gift-design')->name('admin.services.gift');
    Route::get('/services/events', [AdminController::class, 'serviceSection'])->defaults('section', 'events')->name('admin.services.events');
    Route::get('/services/laser-work', [AdminController::class, 'serviceSection'])->defaults('section', 'laser-work')->name('admin.services.laser');
    Route::get('/gift/create', [AdminController::class, 'createGift'])->name('admin.gift.create');
    Route::get('/laser/create', [AdminController::class, 'createLaser'])->name('admin.laser.create');
    Route::get('/event/create', [AdminController::class, 'createEvent'])->name('admin.event.create');

    // Orders
    Route::get('/orders', [AdminController::class, 'orders'])->name('admin.orders');
    Route::get('/orders/data', [AdminController::class, 'ordersData'])->name('admin.orders.data');
    Route::put('/orders/{id}/status', [AdminController::class, 'updateOrderStatus'])->name('admin.orders.status');
    Route::get('/orders/{id}/pdf', [AdminController::class, 'downloadOrderPdf'])->name('admin.orders.pdf');
    Route::get('/orders/{id}', [AdminController::class, 'showOrder'])
        ->name('admin.orders.show');

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
