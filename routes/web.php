<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\VehicleBrandController;
use App\Http\Controllers\Admin\VehicleModelController;
use App\Http\Controllers\Admin\VehicleVariantController;
use App\Http\Controllers\Admin\VehicleColorController;
use App\Http\Controllers\Admin\HsnSacMasterController;
use App\Http\Controllers\Admin\SparePartCategoryController;
use App\Http\Controllers\Admin\SparePartController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\CustomerController;

use App\Http\Controllers\Admin\PurchaseOrderController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ServiceCategoryController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\JobCardController;
use App\Http\Controllers\Admin\ServiceReminderController;
use App\Http\Controllers\Admin\SaleController;
use App\Http\Controllers\Admin\SpareSaleController;
use App\Http\Controllers\Admin\VehiclePurchaseOrderController;


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

        Route::resource('vehicle-brands', VehicleBrandController::class)->except(['show']);
        Route::post('vehicle-brands/{vehicle_brand}/toggle-status', [VehicleBrandController::class, 'toggleStatus'])->name('vehicle-brands.toggle-status');
        Route::resource('vehicle-models', VehicleModelController::class)->except(['show']);
        Route::post('vehicle-models/{vehicle_model}/toggle-status', [VehicleModelController::class, 'toggleStatus'])->name('vehicle-models.toggle-status');
        Route::resource('vehicle-variants', VehicleVariantController::class)->except(['show']);
        Route::post('vehicle-variants/{vehicle_variant}/toggle-status', [VehicleVariantController::class, 'toggleStatus'])->name('vehicle-variants.toggle-status');
        Route::resource('vehicle-colors', VehicleColorController::class)->except(['show']);
        Route::post('vehicle-colors/{vehicle_color}/toggle-status', [VehicleColorController::class, 'toggleStatus'])->name('vehicle-colors.toggle-status');
        Route::resource('hsn-sac-master', HsnSacMasterController::class)->except(['show']);
        Route::post('hsn-sac-master/{hsn_sac_master}/toggle-status', [HsnSacMasterController::class, 'toggleStatus'])->name('hsn-sac-master.toggle-status');
        Route::resource('spare-part-categories', SparePartCategoryController::class)->except(['show']);
        Route::post('spare-part-categories/{spare_part_category}/toggle-status', [SparePartCategoryController::class, 'toggleStatus'])->name('spare-part-categories.toggle-status');
        Route::resource('spare-parts', SparePartController::class)->except(['show']);
        Route::post('spare-parts/{spare_part}/toggle-status', [SparePartController::class, 'toggleStatus'])->name('spare-parts.toggle-status');
        Route::resource('suppliers', SupplierController::class)->except(['show']);
        Route::post('suppliers/{supplier}/toggle-status', [SupplierController::class, 'toggleStatus'])->name('suppliers.toggle-status');
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

        Route::get('invoices', [InvoiceController::class, 'index'])->name('invoices.index');
        Route::get('invoices/create-vehicle', [InvoiceController::class, 'createVehicle'])->name('invoices.create-vehicle');
        Route::post('invoices/create-vehicle', [InvoiceController::class, 'storeVehicle'])->name('invoices.store-vehicle');
        Route::get('invoices/create-parts', [InvoiceController::class, 'createParts'])->name('invoices.create-parts');
        Route::post('invoices/create-parts', [InvoiceController::class, 'storeParts'])->name('invoices.store-parts');
        Route::get('invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
        Route::post('invoices/{invoice}/destroy', [InvoiceController::class, 'destroy'])->name('invoices.destroy');

        Route::resource('payments', PaymentController::class)->except(['edit', 'update', 'show']);

        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('ledger', [ReportController::class, 'ledger'])->name('ledger');
            Route::get('gstr1', [ReportController::class, 'gstr1'])->name('gstr1');
            Route::post('gstr1-export', [ReportController::class, 'gstr1Export'])->name('gstr1-export');
        });

        // Service Module
        Route::resource('service-categories', ServiceCategoryController::class)->except(['show']);
        Route::post('service-categories/{service_category}/toggle-status', [ServiceCategoryController::class, 'toggleStatus'])->name('service-categories.toggle-status');
        Route::resource('services', ServiceController::class)->except(['show']);
        Route::post('services/{service}/toggle-status', [ServiceController::class, 'toggleStatus'])->name('services.toggle-status');

        Route::get('job-cards', [JobCardController::class, 'index'])->name('job-cards.index');
        Route::get('job-cards/create', [JobCardController::class, 'create'])->name('job-cards.create');
        Route::post('job-cards', [JobCardController::class, 'store'])->name('job-cards.store');
        Route::get('job-cards/{job_card}/edit', [JobCardController::class, 'edit'])->name('job-cards.edit');
        Route::put('job-cards/{job_card}', [JobCardController::class, 'update'])->name('job-cards.update');
        Route::get('job-cards/{job_card}', [JobCardController::class, 'show'])->name('job-cards.show');
        Route::post('job-cards/{job_card}/destroy', [JobCardController::class, 'destroy'])->name('job-cards.destroy');
        Route::post('job-cards/{job_card}/toggle-status', [JobCardController::class, 'toggleStatus'])->name('job-cards.toggle-status');
        Route::post('job-cards/{job_card}/update-status', [JobCardController::class, 'updateStatus'])->name('job-cards.update-status');
        Route::post('job-cards/{job_card}/calculate-billing', [JobCardController::class, 'calculateBilling'])->name('job-cards.calculate-billing');
        Route::get('job-cards/{job_card}/print', [JobCardController::class, 'print'])->name('job-cards.print');

        Route::resource('service-reminders', ServiceReminderController::class)->except(['show']);
        Route::post('service-reminders/{service_reminder}/toggle-status', [ServiceReminderController::class, 'toggleStatus'])->name('service-reminders.toggle-status');
        Route::post('service-reminders/{service_reminder}/update-status', [ServiceReminderController::class, 'updateStatus'])->name('service-reminders.update-status');

        // Sales Module (Vehicle Lifecycle)
        Route::get('sales', [SaleController::class, 'index'])->name('sales.index');
        Route::get('sales/create', [SaleController::class, 'create'])->name('sales.create');
        Route::post('sales', [SaleController::class, 'store'])->name('sales.store');
        Route::get('sales/{sale}/edit', [SaleController::class, 'edit'])->name('sales.edit');
        Route::put('sales/{sale}', [SaleController::class, 'update'])->name('sales.update');
        Route::get('sales/{sale}', [SaleController::class, 'show'])->name('sales.show');
        Route::post('sales/{sale}/destroy', [SaleController::class, 'destroy'])->name('sales.destroy');
        Route::post('sales/{sale}/toggle-status', [SaleController::class, 'toggleStatus'])->name('sales.toggle-status');
        Route::post('sales/{sale}/update-status', [SaleController::class, 'updateStatus'])->name('sales.update-status');
        Route::get('sales/{sale}/generate-invoice', [SaleController::class, 'generateInvoice'])->name('sales.generate-invoice');

        // Spare Sale Module (Counter Sales)
        Route::get('spare-sales', [SpareSaleController::class, 'index'])->name('spare-sales.index');
        Route::get('spare-sales/create', [SpareSaleController::class, 'create'])->name('spare-sales.create');
        Route::post('spare-sales', [SpareSaleController::class, 'store'])->name('spare-sales.store');
        Route::get('spare-sales/{spare_sale}', [SpareSaleController::class, 'show'])->name('spare-sales.show');
        Route::get('spare-sales/{spare_sale}/print', [SpareSaleController::class, 'print'])->name('spare-sales.print');
        Route::post('spare-sales/{spare_sale}/destroy', [SpareSaleController::class, 'destroy'])->name('spare-sales.destroy');

});



});

Route::middleware(['auth'])->group(function () {

});



