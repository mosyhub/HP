<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\SearchController; // Added the SearchController import

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ✅ Landing page with login and register
Route::get('/', function () {
    return view('auth.login');
});

// ✅ Authentication routes
Auth::routes();

// ✅ Normal user routes
Route::middleware(['auth'])->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // Search route - moved outside admin group since search should be available to all users
    Route::get('/search', [SearchController::class, 'search'])->name('search');

    // Cart
    Route::prefix('cart')->group(function () {
        Route::get('/', [CartController::class, 'index'])->name('cart.index');
        Route::get('/add/{product_id}', [CartController::class, 'addToCart'])->name('cart.add');
        Route::patch('/update/{product_id}', [CartController::class, 'update'])->name('cart.update');
        Route::get('/checkout', [CartController::class, 'checkout'])->name('cart.checkout');
        Route::post('/process-payment', [CartController::class, 'processPayment'])->name('cart.process-payment');
        Route::delete('/remove/{product_id}', [CartController::class, 'remove'])->name('cart.remove');
        Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    });

    // Order
    Route::prefix('orders')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::patch('/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    });

    // Profile
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])->name('profile.show');
        Route::get('/edit', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/update', [ProfileController::class, 'update'])->name('profile.update');
        Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    });
});

// ✅ Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');

    // ✅ User Management (using AdminController)
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [AdminController::class, 'usersIndex'])->name('index');
        Route::get('/data', [AdminController::class, 'getUsersData'])->name('data');
        Route::get('/{user}/edit', [AdminController::class, 'editUser'])->name('edit');
        Route::put('/{user}', [AdminController::class, 'updateUser'])->name('update');
        Route::delete('/{user}', [AdminController::class, 'destroyUser'])->name('destroy');

        // ✅ Role and Status updates
        Route::post('/{id}/update-role', [AdminController::class, 'updateUserRole'])->name('updateRole');
        Route::post('/{id}/update-status', [AdminController::class, 'updateUserStatus'])->name('updateStatus');
    });
    
    // ✅ Product Management (fixed version)
    Route::resource('products', ProductController::class)->names([
        'index' => 'products.index',
        'create' => 'products.create',
        'store' => 'products.store',
        'show' => 'products.show',
        'edit' => 'products.edit',
        'update' => 'products.update',
        'destroy' => 'products.destroy'
    ]);

    // Orders
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [AdminOrderController::class, 'index'])->name('index');
        Route::patch('/{order}/update-status', [AdminOrderController::class, 'updateStatus'])
            ->name('update-status');
    });

    // Reviews
    Route::prefix('reviews')->name('reviews.')->group(function () {
        Route::get('/', [ReviewController::class, 'index'])->name('index');
    });
});