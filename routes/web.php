<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\IngredientController;
use App\Http\Controllers\ProductAddonController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductVariantController;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\KitchenController;
use App\Http\Controllers\CashDrawerController;
use App\Http\Controllers\RefundController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

// === Public QR Menu Routes ===
Route::get('/', function () {
    return redirect()->route('public.menu');
});
Route::get('/menu', [\App\Http\Controllers\PublicMenuController::class, 'index'])->name('public.menu');
Route::get('/cart', [\App\Http\Controllers\PublicMenuController::class, 'cart'])->name('public.cart');
Route::post('/cart/add', [\App\Http\Controllers\PublicMenuController::class, 'addToCart'])->name('public.addToCart');
Route::post('/cart/update', [\App\Http\Controllers\PublicMenuController::class, 'updateCart'])->name('public.updateCart');
Route::post('/cart/remove', [\App\Http\Controllers\PublicMenuController::class, 'removeFromCart'])->name('public.removeFromCart');
Route::get('/checkout', [\App\Http\Controllers\PublicMenuController::class, 'checkout'])->name('public.checkout');
Route::post('/checkout/process', [\App\Http\Controllers\CheckoutController::class, 'process'])->name('public.checkout.process');
Route::get('/order/{transaction}', [\App\Http\Controllers\CheckoutController::class, 'orderStatus'])->name('public.order-status');

/*
|--------------------------------------------------------------------------
| Guest Routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

/*
|--------------------------------------------------------------------------
| Public Routes (No Auth Required)
|--------------------------------------------------------------------------
*/
Route::get('/absencetemp', [AttendanceController::class, 'index'])->name('attendance.temp');
Route::post('/absencetemp/checkin', [AttendanceController::class, 'checkIn'])->name('attendance.checkin');
Route::post('/absencetemp/checkout', [AttendanceController::class, 'checkOut'])->name('attendance.checkout');

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
// Logout is outside attendance middleware to avoid circular blocks
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});

Route::middleware(['auth', 'attendance'])->group(function () {

    // Dashboard (Owner & Manager)
    Route::middleware('role:owner,manager')->group(function () {
        Route::get('/dashboard', function () {
            return view('dashboard.index');
        })->name('dashboard');

        // Master Data Management
        Route::resource('categories', CategoryController::class)->except(['show']);
        Route::resource('products', ProductController::class);
        Route::post('products/{product}/variants', [ProductVariantController::class, 'store'])->name('products.variants.store');
        Route::delete('products/{product}/variants/{variant}', [ProductVariantController::class, 'destroy'])->name('products.variants.destroy');
        Route::post('products/{product}/addons', [ProductAddonController::class, 'store'])->name('products.addons.store');
        Route::delete('products/{product}/addons/{addon}', [ProductAddonController::class, 'destroy'])->name('products.addons.destroy');
        Route::get('products/{product}/recipe', [RecipeController::class, 'edit'])->name('products.recipe.edit');
        Route::put('products/{product}/recipe', [RecipeController::class, 'update'])->name('products.recipe.update');

        // Recipes
        Route::get('recipes', [RecipeController::class, 'index'])->name('recipes.index');

        // Inventory
        Route::resource('ingredients', IngredientController::class)->except(['destroy']);
        Route::post('ingredients/{ingredient}/batches', [IngredientController::class, 'addBatch'])->name('ingredients.batches.store');

        // Tables, Discounts, Settings
        Route::resource('tables', TableController::class)->except(['show', 'create', 'edit']);
        Route::resource('discounts', DiscountController::class)->except(['show', 'create', 'edit']);
        Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
        Route::put('settings', [SettingController::class, 'update'])->name('settings.update');
    });
    // Cash Drawer (Cashier+)
    Route::middleware('role:owner,manager,cashier')->prefix('cash-drawer')->group(function () {
        Route::get('/shift-sales', [CashDrawerController::class, 'shiftSales'])->name('cash-drawer.shift-sales');
        Route::get('/', [CashDrawerController::class, 'index'])->name('cash-drawer.index');
        Route::post('/open', [CashDrawerController::class, 'open'])->name('cash-drawer.open');
        Route::get('/{cashDrawer}', [CashDrawerController::class, 'show'])->name('cash-drawer.show');
        Route::post('/{cashDrawer}/close', [CashDrawerController::class, 'close'])->name('cash-drawer.close');
    });

    // Refund (Owner, Manager & Cashier per SRS FR-05)
    Route::middleware('role:owner,manager,cashier')->group(function () {
        Route::get('/refunds', [RefundController::class, 'index'])->name('refunds.index');
        Route::get('/refunds/create/{transaction}', [RefundController::class, 'create'])->name('refunds.create');
        Route::post('/refunds/{transaction}', [RefundController::class, 'store'])->name('refunds.store');
    });

    // Reports (Owner & Manager)
    Route::middleware('role:owner,manager')->prefix('reports')->group(function () {
        Route::get('/daily', [ReportController::class, 'dailySummary'])->name('reports.daily');
        Route::get('/best-selling', [ReportController::class, 'bestSelling'])->name('reports.best-selling');
    });

    // POS (Cashier)
    Route::middleware('role:owner,manager,cashier')->prefix('pos')->group(function () {
        Route::get('/', [PosController::class, 'index'])->name('pos.index');
        Route::post('/bill', [PosController::class, 'createBill'])->name('pos.createBill');
        Route::get('/bill/{transaction}', [PosController::class, 'showBill'])->name('pos.bill');
        Route::post('/bill/{transaction}/item', [PosController::class, 'addItem'])->name('pos.addItem');
        Route::delete('/bill/{transaction}/item/{detail}', [PosController::class, 'removeItem'])->name('pos.removeItem');
        Route::post('/bill/{transaction}/void', [PosController::class, 'voidBill'])->name('pos.voidBill');
        Route::post('/bill/{transaction}/checkout', [PosController::class, 'checkout'])->name('pos.checkout');
        Route::get('/receipt/{transaction}', [PosController::class, 'receipt'])->name('pos.receipt');
        
        // Bookings
        Route::get('/bookings', [PosController::class, 'bookings'])->name('pos.bookings');
        Route::patch('/bookings/{booking}/status', [PosController::class, 'updateBookingStatus'])->name('pos.updateBookingStatus');

        // Tables Management (Clear Dine-In Tables)
        Route::get('/tables', [PosController::class, 'tables'])->name('pos.tables');
        Route::patch('/tables/{table}/clear', [PosController::class, 'clearTable'])->name('pos.clearTable');
    });

    // Kitchen (Kitchen Staff)
    Route::middleware('role:owner,manager,kitchen_staff')->prefix('kitchen')->group(function () {
        Route::get('/', [KitchenController::class, 'index'])->name('kitchen.index');
        Route::patch('/item/{detail}/status', [KitchenController::class, 'updateStatus'])->name('kitchen.updateStatus');
        Route::post('/order/{transaction}/done', [KitchenController::class, 'markAllDone'])->name('kitchen.markAllDone');
    });
});
