<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\SparePartController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\CustomerController;

use App\Http\Controllers\Admin\PurchaseOrderController;
use App\Http\Controllers\Admin\VehiclePurchaseOrderController;
use App\Http\Controllers\Admin\SparePartStockController;
use App\Http\Controllers\Admin\VehicleMasterController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::get('/', [HomeController::class, 'index'])->name('/');
Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::name('admin.')->prefix('admin')->group(function () {
    Route::get('/', [AdminAuthController::class, 'index']);

    Route::get('login', [AdminAuthController::class, 'login'])->name('login');

    Route::post('login', [AdminAuthController::class, 'postLogin'])->name('login.post');

    Route::get('forget-password', [AdminAuthController::class, 'showForgetPasswordForm'])->name('forget.password.get');

    Route::post('forget-password', [AdminAuthController::class, 'submitForgetPasswordForm'])->name('forget.password.post');

    Route::get('reset-password/{token}', [AdminAuthController::class, 'showResetPasswordForm'])->name('reset.password.get');

    Route::post('reset-password', [AdminAuthController::class, 'submitResetPasswordForm'])->name('reset.password.post');

    Route::middleware(['admin'])->group(function () {
    	Route::get('dashboard', [AdminAuthController::class, 'adminDashboard'])->name('dashboard');

        Route::get('change-password', [AdminAuthController::class, 'changePassword'])->name('change.password');

        Route::post('update-password', [AdminAuthController::class, 'updatePassword'])->name('update.password');

        Route::get('logout', [AdminAuthController::class, 'logout'])->name('logout');

        Route::get('profile', [AdminAuthController::class, 'adminProfile'])->name('profile');

        Route::post('profile', [AdminAuthController::class, 'updateAdminProfile'])->name('update.profile');

        Route::get('vehicle-masters/import-template', [VehicleMasterController::class, 'downloadTemplate'])->name('vehicle-masters.import-template');
        Route::post('vehicle-masters/import', [VehicleMasterController::class, 'import'])->name('vehicle-masters.import');
        Route::resource('vehicle-masters', VehicleMasterController::class)->except(['show']);
        Route::post('vehicle-masters/{vehicle_master}/toggle-status', [VehicleMasterController::class, 'toggleStatus'])->name('vehicle-masters.toggle-status');

        Route::get('spare-parts/import-template', [SparePartController::class, 'downloadTemplate'])->name('spare-parts.import-template');
        Route::post('spare-parts/import', [SparePartController::class, 'import'])->name('spare-parts.import');
        Route::resource('spare-parts', SparePartController::class)->except(['show']);
        Route::post('spare-parts/{spare_part}/toggle-status', [SparePartController::class, 'toggleStatus'])->name('spare-parts.toggle-status');

        Route::get('suppliers/import-template', [SupplierController::class, 'downloadTemplate'])->name('suppliers.import-template');
        Route::post('suppliers/import', [SupplierController::class, 'import'])->name('suppliers.import');
        Route::resource('suppliers', SupplierController::class)->except(['show']);
        Route::post('suppliers/{supplier}/toggle-status', [SupplierController::class, 'toggleStatus'])->name('suppliers.toggle-status');

        Route::get('customers/import-template', [CustomerController::class, 'downloadTemplate'])->name('customers.import-template');
        Route::post('customers/import', [CustomerController::class, 'import'])->name('customers.import');
        Route::resource('customers', CustomerController::class);
        Route::post('customers/{customer}/toggle-status', [CustomerController::class, 'toggleStatus'])->name('customers.toggle-status');

        Route::resource('purchase-orders', PurchaseOrderController::class);
        Route::post('purchase-orders/{purchase_order}/toggle-status', [PurchaseOrderController::class, 'toggleStatus'])->name('purchase-orders.toggle-status');
        Route::get('purchase-orders/{purchase_order}/receive', [PurchaseOrderController::class, 'receive'])->name('purchase-orders.receive');
        Route::post('purchase-orders/{purchase_order}/receive-store', [PurchaseOrderController::class, 'receiveStore'])->name('purchase-orders.receive-store');

        Route::resource('vehicle-purchase-orders', VehiclePurchaseOrderController::class);
        Route::post('vehicle-purchase-orders/{vehicle_purchase_order}/toggle-status', [VehiclePurchaseOrderController::class, 'toggleStatus'])->name('vehicle-purchase-orders.toggle-status');
        Route::get('vehicle-purchase-orders/{vehicle_purchase_order}/receive', [VehiclePurchaseOrderController::class, 'receive'])->name('vehicle-purchase-orders.receive');
        Route::post('vehicle-purchase-orders/{vehicle_purchase_order}/receive-store', [VehiclePurchaseOrderController::class, 'receiveStore'])->name('vehicle-purchase-orders.receive-store');
        Route::get('vehicle-inventories', [VehiclePurchaseOrderController::class, 'inventory'])->name('vehicle-inventories.index');
        Route::get('spare-part-stocks', [SparePartStockController::class, 'index'])->name('spare-part-stocks.index');
        Route::post('spare-part-stocks/{spare_part_stock}/toggle-status', [SparePartStockController::class, 'toggleStatus'])->name('spare-part-stocks.toggle-status');
        Route::post('spare-part-stocks/{spare_part_stock}/destroy', [SparePartStockController::class, 'destroy'])->name('spare-part-stocks.destroy');



});



});

Route::middleware(['auth'])->group(function () {

});



