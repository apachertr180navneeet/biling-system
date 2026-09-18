<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\BookingController as WebBookingController;
use App\Models\Company;
use App\Models\Currency;

// Auth
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\Auth\ForgotPasswordController;
use App\Http\Controllers\Admin\Auth\ResetPasswordController;
use App\Http\Controllers\Admin\Auth\ChangePasswordController;
use App\Http\Controllers\Admin\Auth\ProfileController;

// Core
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;

// Company Setup
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\TimeZoneController;
use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\FinancialYearController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\DesignationController;

// Masters
use App\Http\Controllers\Admin\Master\CurrencyController;
use App\Http\Controllers\Admin\Master\TaxController;
use App\Http\Controllers\Admin\Master\TaxGroupController;
use App\Http\Controllers\Admin\Master\NumberSeriesController;

// Property
use App\Http\Controllers\Admin\Property\HotelController;
use App\Http\Controllers\Admin\Property\BuildingController;
use App\Http\Controllers\Admin\Property\FloorController;
use App\Http\Controllers\Admin\Property\WingController;
use App\Http\Controllers\Admin\Property\RoomTypeController;
use App\Http\Controllers\Admin\Property\AmenityController;
use App\Http\Controllers\Admin\Property\BedTypeController;
use App\Http\Controllers\Admin\Property\RoomStatusController;
use App\Http\Controllers\Admin\Property\RoomController;

// Reservation
use App\Http\Controllers\Admin\Reservation\GuestController;
use App\Http\Controllers\Admin\Reservation\BookingController;
use App\Http\Controllers\Admin\Reservation\CalendarController;
use App\Http\Controllers\Admin\Reservation\GroupBookingController;
use App\Http\Controllers\Admin\Reservation\RatePlanController;
use App\Http\Controllers\Admin\Reservation\PaymentController;

// Front Office
use App\Http\Controllers\Admin\FrontOffice\CheckInController;
use App\Http\Controllers\Admin\FrontOffice\CheckOutController;
use App\Http\Controllers\Admin\FrontOffice\NightAuditController;

// Guest CRM
use App\Http\Controllers\Admin\Crm\GuestProfileController;
use App\Http\Controllers\Admin\Crm\LoyaltyController;
use App\Http\Controllers\Admin\Crm\CampaignController;

// Housekeeping
use App\Http\Controllers\Admin\Housekeeping\CleaningScheduleController;
use App\Http\Controllers\Admin\Housekeeping\LaundryItemController;
use App\Http\Controllers\Admin\Housekeeping\LaundryOrderController;

// Restaurant POS
use App\Http\Controllers\Admin\Restaurant\RestaurantTableController;
use App\Http\Controllers\Admin\Restaurant\MenuItemController;
use App\Http\Controllers\Admin\Restaurant\OrderController;
use App\Http\Controllers\Admin\Restaurant\RoomServiceController;

// Inventory
use App\Http\Controllers\Admin\Inventory\CategoryController;
use App\Http\Controllers\Admin\Inventory\UnitController;
use App\Http\Controllers\Admin\Inventory\ItemController;
use App\Http\Controllers\Admin\Inventory\SupplierController;
use App\Http\Controllers\Admin\Inventory\PurchaseOrderController;
use App\Http\Controllers\Admin\Inventory\GrnController;
use App\Http\Controllers\Admin\Inventory\StockController;
use App\Http\Controllers\Admin\Inventory\StockTransferController;
use App\Http\Controllers\Admin\Inventory\StockAdjustmentController;

// Procurement
use App\Http\Controllers\Admin\Procurement\VendorCategoryController;
use App\Http\Controllers\Admin\Procurement\VendorController;

// Banquet
use App\Http\Controllers\Admin\Banquet\HallController;
use App\Http\Controllers\Admin\Banquet\EventController;

// Spa
use App\Http\Controllers\Admin\Spa\SpaServiceController;
use App\Http\Controllers\Admin\Spa\SpaAppointmentController;

// Travel Desk
use App\Http\Controllers\Admin\TravelDesk\TransportTypeController;
use App\Http\Controllers\Admin\TravelDesk\TransportBookingController;

// Finance
use App\Http\Controllers\Admin\Finance\ChartOfAccountsController;
use App\Http\Controllers\Admin\Finance\JournalEntryController;
use App\Http\Controllers\Admin\Finance\AccountsPayableController;
use App\Http\Controllers\Admin\Finance\AccountsReceivableController;
use App\Http\Controllers\Admin\Finance\GstController;
use App\Http\Controllers\Admin\Master\HsnSacCodeController;
use App\Http\Controllers\Admin\Finance\ExpenseController;
use App\Http\Controllers\Admin\Finance\ReportController;
use App\Http\Controllers\Admin\Finance\FinanceReportController;

// Payment Gateways
use App\Http\Controllers\Admin\PaymentGatewayController;

// HR
use App\Http\Controllers\Admin\Hr\EmployeeController;
use App\Http\Controllers\Admin\Hr\AttendanceController;
use App\Http\Controllers\Admin\Hr\PayrollController as HrPayrollController;

// Maintenance
use App\Http\Controllers\Admin\Maintenance\AssetController as MaintenanceAssetController;
use App\Http\Controllers\Admin\Maintenance\AmcController as MaintenanceAmcController;
use App\Http\Controllers\Admin\Maintenance\WorkOrderController as MaintenanceWorkOrderController;

// Channel Manager
use App\Http\Controllers\Admin\ChannelManager\OtaChannelController;
use App\Http\Controllers\Admin\ChannelManager\ChannelMappingController;
use App\Http\Controllers\Admin\ChannelManager\SyncLogController;
use App\Http\Controllers\Admin\ChannelManager\SyncController;
use App\Http\Controllers\Admin\ChannelManager\TestApiController;

// Integrations - Devices
use App\Http\Controllers\Admin\Integrations\DeviceController;

// Foundation Modules
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\ApprovalController;
use App\Http\Controllers\Admin\CommunicationController;
use App\Http\Controllers\Admin\LanguageController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('/');
Route::get('/home', [HomeController::class, 'index'])->name('home');

// Public website routes
Route::get('/hotels', [HomeController::class, 'hotels'])->name('web.hotels');
Route::get('/hotels/{hotel:slug}', [HomeController::class, 'hotelShow'])->name('web.hotel.show');
Route::get('/about', [HomeController::class, 'about'])->name('web.about');
Route::get('/contact', [HomeController::class, 'contact'])->name('web.contact');

// Booking Engine
Route::prefix('booking')->name('web.booking')->group(function () {
    Route::get('/', [WebBookingController::class, 'index'])->name('');
    Route::post('/availability', [WebBookingController::class, 'availability'])->name('.availability');
    Route::get('/checkout', [WebBookingController::class, 'checkout'])->name('.checkout');
    Route::post('/confirm', [WebBookingController::class, 'confirm'])->name('.confirm');
});

// Guest Web App
Route::prefix('guest')->name('guest.')->group(function () {
    Route::get('login', [\App\Http\Controllers\Guest\GuestAppController::class, 'showLogin'])->name('login');
    Route::post('login', [\App\Http\Controllers\Guest\GuestAppController::class, 'login'])->name('login.post');
    Route::get('logout', [\App\Http\Controllers\Guest\GuestAppController::class, 'logout'])->name('logout');
    Route::get('dashboard', [\App\Http\Controllers\Guest\GuestAppController::class, 'dashboard'])->name('dashboard');
    Route::get('my-booking', [\App\Http\Controllers\Guest\GuestAppController::class, 'myBooking'])->name('my-booking');
    Route::get('service-request/create', [\App\Http\Controllers\Guest\GuestAppController::class, 'createServiceRequest'])->name('service-request.create');
    Route::post('service-request/store', [\App\Http\Controllers\Guest\GuestAppController::class, 'storeServiceRequest'])->name('service-request.store');
    Route::get('service-request/{serviceRequest}', [\App\Http\Controllers\Guest\GuestAppController::class, 'serviceRequestDetail'])->name('service-request.detail');
});

// Staff Web App
Route::prefix('staff')->name('staff.')->group(function () {
    Route::get('login', [\App\Http\Controllers\Staff\StaffLoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [\App\Http\Controllers\Staff\StaffLoginController::class, 'login'])->name('login.post');
    Route::get('logout', [\App\Http\Controllers\Staff\StaffLoginController::class, 'logout'])->name('logout');
});

Route::prefix('staff')->name('staff.')->middleware(['staff'])->group(function () {
    Route::get('dashboard', [\App\Http\Controllers\Staff\StaffAppController::class, 'dashboard'])->name('dashboard');
    Route::get('my-tasks', [\App\Http\Controllers\Staff\StaffAppController::class, 'myTasks'])->name('my-tasks');
    Route::get('unassigned-tasks', [\App\Http\Controllers\Staff\StaffAppController::class, 'unassignedTasks'])->name('unassigned-tasks');
    Route::post('claim-task/{task}', [\App\Http\Controllers\Staff\StaffAppController::class, 'claimTask'])->name('claim-task');
    Route::post('update-status/{task}', [\App\Http\Controllers\Staff\StaffAppController::class, 'updateStatus'])->name('update-status');
    Route::get('housekeeping', [\App\Http\Controllers\Staff\StaffAppController::class, 'housekeeping'])->name('housekeeping');
    Route::get('maintenance', [\App\Http\Controllers\Staff\StaffAppController::class, 'maintenance'])->name('maintenance');
    Route::get('task/{task}', [\App\Http\Controllers\Staff\StaffAppController::class, 'taskDetail'])->name('task-detail');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::name('admin.')->prefix('admin')->group(function () {

    // Guest Auth Routes
    Route::get('/', [LoginController::class, 'index']);
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login'])->name('login.post');
    Route::get('logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('forget-password', [ForgotPasswordController::class, 'showForm'])->name('forget.password.get');
    Route::post('forget-password', [ForgotPasswordController::class, 'submit'])->name('forget.password.post');
    Route::get('reset-password/{token}', [ResetPasswordController::class, 'showForm'])->name('reset.password.get');
    Route::post('reset-password', [ResetPasswordController::class, 'submit'])->name('reset.password.post');

    // Authenticated Admin Routes
    Route::middleware(['admin'])->group(function () {

        // Dashboard
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Profile
        Route::get('profile', [ProfileController::class, 'show'])->name('profile');
        Route::post('profile', [ProfileController::class, 'update'])->name('profile.update');

        // Change Password
        Route::get('change-password', [ChangePasswordController::class, 'showForm'])->name('change.password');
        Route::post('update-password', [ChangePasswordController::class, 'update'])->name('update.password');

        // Property Switcher
        Route::post('switch-hotel', [\App\Http\Controllers\Admin\SwitchHotelController::class, 'switch'])->name('switch-hotel');

        // Language Switcher
        Route::post('switch-language', function () { return redirect()->back(); })->name('switch-language');

        // Company Setup
        Route::get('company/data', [CompanyController::class, 'data'])->name('company.data');
        Route::resource('company', CompanyController::class)->except(['show'])->middleware('permission:company.view');
        Route::patch('company/{company}/status', [CompanyController::class, 'status'])->name('company.status')->middleware('permission:company.edit');

        // Timezones
        Route::get('timezones/data', [TimeZoneController::class, 'data'])->name('timezones.data');
        Route::resource('timezones', TimeZoneController::class)->except(['show'])->middleware('permission:company.view');

        // Data routes (Yajra DataTables AJAX) - MUST be before resource routes
        // to prevent wildcard params like {user} from catching "data" as an ID
        Route::get('users/data', [UserController::class, 'data'])->name('users.data');
        Route::get('roles/data', [RoleController::class, 'data'])->name('roles.data');
        Route::get('branches/data', [BranchController::class, 'data'])->name('branches.data');
        Route::get('departments/data', [DepartmentController::class, 'data'])->name('departments.data');
        Route::get('designations/data', [DesignationController::class, 'data'])->name('designations.data');
        Route::get('financial-years/data', [FinancialYearController::class, 'data'])->name('financial-years.data');
        Route::get('masters/currencies/data', [CurrencyController::class, 'data'])->name('masters.currencies.data');
        Route::get('masters/taxes/data', [TaxController::class, 'data'])->name('masters.taxes.data');
        Route::get('masters/tax-groups/data', [TaxGroupController::class, 'data'])->name('masters.tax-groups.data');
        Route::get('masters/number-series/data', [NumberSeriesController::class, 'data'])->name('masters.number-series.data');

        // User Management
        Route::get('users/data', [UserController::class, 'data'])->name('users.data');
        Route::resource('users', UserController::class)->except(['show'])->middleware('permission:users.view');
        Route::patch('users/{user}/status', [UserController::class, 'status'])->name('users.status')->middleware('permission:users.edit');
        Route::get('users/{user}', [UserController::class, 'show'])->name('users.show')->middleware('permission:users.view');

        // Role Management
        Route::get('roles/data', [RoleController::class, 'data'])->name('roles.data');
        Route::resource('roles', RoleController::class)->except(['show'])->middleware('permission:roles.view');

        // Branches
        Route::get('branches/data', [BranchController::class, 'data'])->name('branches.data');
        Route::resource('branches', BranchController::class)->except(['show'])->middleware('permission:branches.view');
        Route::patch('branches/{branch}/status', [BranchController::class, 'status'])->name('branches.status')->middleware('permission:branches.edit');

        // Departments
        Route::get('departments/data', [DepartmentController::class, 'data'])->name('departments.data');
        Route::resource('departments', DepartmentController::class)->except(['show'])->middleware('permission:departments.view');
        Route::patch('departments/{department}/status', [DepartmentController::class, 'status'])->name('departments.status')->middleware('permission:departments.edit');

        // Designations
        Route::get('designations/data', [DesignationController::class, 'data'])->name('designations.data');
        Route::resource('designations', DesignationController::class)->except(['show'])->middleware('permission:designations.view');
        Route::patch('designations/{designation}/status', [DesignationController::class, 'status'])->name('designations.status')->middleware('permission:designations.edit');

        // Financial Years
        Route::get('financial-years/data', [FinancialYearController::class, 'data'])->name('financial-years.data');
        Route::resource('financial-years', FinancialYearController::class)->except(['show'])->middleware('permission:financial-years.view');
        Route::patch('financial-years/{financialYear}/set-active', [FinancialYearController::class, 'setActive'])->name('financial-years.set-active')->middleware('permission:financial-years.edit');

        // Masters - Currencies
        Route::get('masters/currencies/data', [CurrencyController::class, 'data'])->name('masters.currencies.data');
        Route::resource('masters/currencies', CurrencyController::class)->except(['show'])->names('masters.currencies')->middleware('permission:currencies.view');
        Route::patch('masters/currencies/{currency}/status', [CurrencyController::class, 'status'])->name('masters.currencies.status')->middleware('permission:currencies.edit');

        // Masters - Taxes
        Route::get('masters/taxes/data', [TaxController::class, 'data'])->name('masters.taxes.data');
        Route::resource('masters/taxes', TaxController::class)->except(['show'])->names('masters.taxes')->middleware('permission:taxes.view');
        Route::patch('masters/taxes/{tax}/status', [TaxController::class, 'status'])->name('masters.taxes.status')->middleware('permission:taxes.edit');

        // Masters - Tax Groups
        Route::get('masters/tax-groups/data', [TaxGroupController::class, 'data'])->name('masters.tax-groups.data');
        Route::resource('masters/tax-groups', TaxGroupController::class)->except(['show'])->names('masters.tax-groups')->middleware('permission:taxes.view');

        // Masters - Number Series
        Route::get('masters/number-series/data', [NumberSeriesController::class, 'data'])->name('masters.number-series.data');
        Route::resource('masters/number-series', NumberSeriesController::class)->except(['show'])->names('masters.number-series')->middleware('permission:number-series.view');

        // Property - Hotels
        Route::get('property/hotels/data', [HotelController::class, 'data'])->name('property.hotels.data');
        Route::resource('property/hotels', HotelController::class)->except(['show'])->names('property.hotels')->middleware('permission:hotels.view');
        Route::patch('property/hotels/{hotel}/status', [HotelController::class, 'status'])->name('property.hotels.status')->middleware('permission:hotels.edit');

        // Property - Buildings
        Route::get('property/buildings/data', [BuildingController::class, 'data'])->name('property.buildings.data');
        Route::resource('property/buildings', BuildingController::class)->except(['show'])->names('property.buildings')->middleware('permission:buildings.view');
        Route::patch('property/buildings/{building}/status', [BuildingController::class, 'status'])->name('property.buildings.status')->middleware('permission:buildings.edit');

        // Property - Floors
        Route::get('property/floors/data', [FloorController::class, 'data'])->name('property.floors.data');
        Route::resource('property/floors', FloorController::class)->except(['show'])->names('property.floors')->middleware('permission:floors.view');
        Route::patch('property/floors/{floor}/status', [FloorController::class, 'status'])->name('property.floors.status')->middleware('permission:floors.edit');

        // Property - Wings
        Route::get('property/wings/data', [WingController::class, 'data'])->name('property.wings.data');
        Route::resource('property/wings', WingController::class)->except(['show'])->names('property.wings')->middleware('permission:wings.view');
        Route::patch('property/wings/{wing}/status', [WingController::class, 'status'])->name('property.wings.status')->middleware('permission:wings.edit');

        // Property - Room Types
        Route::get('property/room-types/data', [RoomTypeController::class, 'data'])->name('property.room-types.data');
        Route::resource('property/room-types', RoomTypeController::class)->except(['show'])->names('property.room-types')->middleware('permission:room-types.view');
        Route::patch('property/room-types/{roomType}/status', [RoomTypeController::class, 'status'])->name('property.room-types.status')->middleware('permission:room-types.edit');

        // Property - Amenities
        Route::get('property/amenities/data', [AmenityController::class, 'data'])->name('property.amenities.data');
        Route::resource('property/amenities', AmenityController::class)->except(['show'])->names('property.amenities')->middleware('permission:amenities.view');
        Route::patch('property/amenities/{amenity}/status', [AmenityController::class, 'status'])->name('property.amenities.status')->middleware('permission:amenities.edit');

        // Property - Bed Types
        Route::get('property/bed-types/data', [BedTypeController::class, 'data'])->name('property.bed-types.data');
        Route::resource('property/bed-types', BedTypeController::class)->except(['show'])->names('property.bed-types')->middleware('permission:bed-types.view');
        Route::patch('property/bed-types/{bedType}/status', [BedTypeController::class, 'status'])->name('property.bed-types.status')->middleware('permission:bed-types.edit');

        // Property - Room Status
        Route::get('property/room-status/data', [RoomStatusController::class, 'data'])->name('property.room-status.data');
        Route::resource('property/room-status', RoomStatusController::class)->except(['show'])->names('property.room-status')->middleware('permission:room-status.view');
        Route::patch('property/room-status/{roomStatus}/status', [RoomStatusController::class, 'status'])->name('property.room-status.status')->middleware('permission:room-status.edit');

        // Property - Rooms
        Route::get('property/rooms/data', [RoomController::class, 'data'])->name('property.rooms.data');
        Route::resource('property/rooms', RoomController::class)->except(['show'])->names('property.rooms')->middleware('permission:rooms.view');
        Route::patch('property/rooms/{room}/status', [RoomController::class, 'status'])->name('property.rooms.status')->middleware('permission:rooms.edit');

        // Reservation - Data routes (MUST be before resource routes)
        Route::get('reservation/guests/data', [GuestController::class, 'data'])->name('reservation.guests.data');
        Route::get('reservation/bookings/data', [BookingController::class, 'data'])->name('reservation.bookings.data');
        Route::get('reservation/group-bookings/data', [GroupBookingController::class, 'data'])->name('reservation.group-bookings.data');
        Route::get('reservation/rate-plans/data', [RatePlanController::class, 'data'])->name('reservation.rate-plans.data');

        // Reservation - Guests
        Route::post('reservation/guests/quick-store', [GuestController::class, 'quickStore'])->name('reservation.guests.quick-store')->middleware('permission:guests.create');
        Route::get('reservation/guests/data', [GuestController::class, 'data'])->name('reservation.guests.data');
        Route::resource('reservation/guests', GuestController::class)->except(['show'])->names('reservation.guests')->middleware('permission:guests.view');
        Route::patch('reservation/guests/{guest}/status', [GuestController::class, 'status'])->name('reservation.guests.status')->middleware('permission:guests.edit');

        // Reservation - Bookings
        Route::get('reservation/bookings/data', [BookingController::class, 'data'])->name('reservation.bookings.data');
        Route::resource('reservation/bookings', BookingController::class)->except(['show'])->names('reservation.bookings')->parameters([
            'bookings' => 'reservation'
        ])->middleware('permission:bookings.view');
        Route::patch('reservation/bookings/{reservation}/status', [BookingController::class, 'status'])->name('reservation.bookings.status')->middleware('permission:bookings.edit');
        Route::patch('reservation/bookings/{reservation}/cancel', [BookingController::class, 'cancel'])->name('reservation.bookings.cancel')->middleware('permission:bookings.edit');

        // Reservation - Calendar
        Route::get('reservation/calendar', [CalendarController::class, 'index'])->name('reservation.calendar')->middleware('permission:bookings.view');
        Route::get('reservation/calendar/events', [CalendarController::class, 'events'])->name('reservation.calendar.events')->middleware('permission:bookings.view');
        Route::get('reservation/calendar/availability', [CalendarController::class, 'availability'])->name('reservation.calendar.availability')->middleware('permission:bookings.view');
        Route::get('reservation/calendar/rates', [CalendarController::class, 'rates'])->name('reservation.calendar.rates')->middleware('permission:bookings.view');

        // Reservation - Group Bookings
        Route::get('reservation/group-bookings/data', [GroupBookingController::class, 'data'])->name('reservation.group-bookings.data');
        Route::resource('reservation/group-bookings', GroupBookingController::class)->except(['show'])->names('reservation.group-bookings')->parameters([
            'group-bookings' => 'reservation'
        ])->middleware('permission:bookings.view');
        Route::patch('reservation/group-bookings/{reservation}/status', [GroupBookingController::class, 'status'])->name('reservation.group-bookings.status')->middleware('permission:bookings.edit');

        // Reservation - Rate Plans
        Route::get('reservation/rate-plans/data', [RatePlanController::class, 'data'])->name('reservation.rate-plans.data');
        Route::resource('reservation/rate-plans', RatePlanController::class)->except(['show'])->names('reservation.rate-plans')->middleware('permission:rate-plans.view');
        Route::patch('reservation/rate-plans/{ratePlan}/status', [RatePlanController::class, 'status'])->name('reservation.rate-plans.status')->middleware('permission:rate-plans.edit');

        // Reservation - Payments
        Route::get('reservation/{reservation}/payments', [PaymentController::class, 'index'])->name('reservation.payments.index')->middleware('permission:bookings.view');
        Route::post('reservation/{reservation}/payments', [PaymentController::class, 'store'])->name('reservation.payments.store')->middleware('permission:bookings.create');
        Route::delete('reservation/{reservation}/payments/{payment}', [PaymentController::class, 'destroy'])->name('reservation.payments.destroy')->middleware('permission:bookings.delete');

        // Front Office - Check In
        Route::get('front-office/check-ins/data', [CheckInController::class, 'data'])->name('front-office.check-ins.data');
        Route::resource('front-office/check-ins', CheckInController::class)->except(['show'])->names('front-office.check-ins')->middleware('permission:check-ins.view');
        Route::patch('front-office/check-ins/{checkIn}/status', [CheckInController::class, 'status'])->name('front-office.check-ins.status')->middleware('permission:check-ins.edit');

        // Front Office - Check Out
        Route::get('front-office/check-outs/data', [CheckOutController::class, 'data'])->name('front-office.check-outs.data');
        Route::resource('front-office/check-outs', CheckOutController::class)->except(['show'])->names('front-office.check-outs')->middleware('permission:check-outs.view');
        Route::patch('front-office/check-outs/{checkOut}/status', [CheckOutController::class, 'status'])->name('front-office.check-outs.status')->middleware('permission:check-outs.edit');
        Route::get('front-office/check-outs/{checkOut}/invoice', [CheckOutController::class, 'invoice'])->name('front-office.check-outs.invoice')->middleware('permission:check-outs.view');

        // Front Office - Night Audit
        Route::get('front-office/night-audits/data', [NightAuditController::class, 'data'])->name('front-office.night-audits.data');
        Route::resource('front-office/night-audits', NightAuditController::class)->except(['show'])->names('front-office.night-audits')->middleware('permission:night-audit.view');
        Route::patch('front-office/night-audits/{nightAudit}/status', [NightAuditController::class, 'status'])->name('front-office.night-audits.status')->middleware('permission:night-audit.edit');

        // Guest CRM - Guest Profiles
        Route::get('crm/guest-profiles/data', [GuestProfileController::class, 'data'])->name('crm.guest-profiles.data');
        Route::resource('crm/guest-profiles', GuestProfileController::class)->except(['show'])->names('crm.guest-profiles')->middleware('permission:guest-profiles.view');
        Route::patch('crm/guest-profiles/{guestProfile}/status', [GuestProfileController::class, 'status'])->name('crm.guest-profiles.status')->middleware('permission:guest-profiles.edit');

        // Guest CRM - Loyalty Members
        Route::get('crm/loyalty/data', [LoyaltyController::class, 'data'])->name('crm.loyalty.data');
        Route::resource('crm/loyalty', LoyaltyController::class)->except(['show'])->names('crm.loyalty')->parameters([
            'loyalty' => 'member'
        ])->middleware('permission:loyalty.view');
        Route::patch('crm/loyalty/{member}/status', [LoyaltyController::class, 'status'])->name('crm.loyalty.status')->middleware('permission:loyalty.edit');
        Route::get('crm/loyalty/{member}/transactions', [LoyaltyController::class, 'transactions'])->name('crm.loyalty.transactions')->middleware('permission:loyalty.view');
        Route::post('crm/loyalty/{member}/transactions', [LoyaltyController::class, 'storeTransaction'])->name('crm.loyalty.transactions.store')->middleware('permission:loyalty.edit');

        // Guest CRM - Loyalty Tiers
        Route::get('crm/loyalty/tiers', [LoyaltyController::class, 'tiers'])->name('crm.loyalty.tiers.index')->middleware('permission:loyalty.view');
        Route::get('crm/loyalty/tiers/data', [LoyaltyController::class, 'tiersData'])->name('crm.loyalty.tiers.data')->middleware('permission:loyalty.view');
        Route::get('crm/loyalty/tiers/create', [LoyaltyController::class, 'tierCreate'])->name('crm.loyalty.tiers.create')->middleware('permission:loyalty.create');
        Route::post('crm/loyalty/tiers', [LoyaltyController::class, 'tierStore'])->name('crm.loyalty.tiers.store')->middleware('permission:loyalty.create');
        Route::get('crm/loyalty/tiers/{tier}/edit', [LoyaltyController::class, 'tierEdit'])->name('crm.loyalty.tiers.edit')->middleware('permission:loyalty.edit');
        Route::put('crm/loyalty/tiers/{tier}', [LoyaltyController::class, 'tierUpdate'])->name('crm.loyalty.tiers.update')->middleware('permission:loyalty.edit');
        Route::delete('crm/loyalty/tiers/{tier}', [LoyaltyController::class, 'tierDestroy'])->name('crm.loyalty.tiers.destroy')->middleware('permission:loyalty.delete');
        Route::patch('crm/loyalty/tiers/{tier}/status', [LoyaltyController::class, 'tierStatus'])->name('crm.loyalty.tiers.status')->middleware('permission:loyalty.edit');

        // Guest CRM - Marketing Campaigns
        Route::get('crm/campaigns/data', [CampaignController::class, 'data'])->name('crm.campaigns.data');
        Route::get('crm/campaigns/{campaign}/send', [CampaignController::class, 'send'])->name('crm.campaigns.send')->middleware('permission:marketing_campaigns.edit');
        Route::resource('crm/campaigns', CampaignController::class)->names('crm.campaigns')->middleware('permission:marketing_campaigns.view');

        // Housekeeping - Cleaning Schedules
        Route::get('housekeeping/cleaning-schedules/data', [CleaningScheduleController::class, 'data'])->name('housekeeping.cleaning-schedules.data');
        Route::resource('housekeeping/cleaning-schedules', CleaningScheduleController::class)->except(['show'])->names('housekeeping.cleaning-schedules')->middleware('permission:cleaning_schedules.view');
        Route::patch('housekeeping/cleaning-schedules/{cleaningSchedule}/status', [CleaningScheduleController::class, 'status'])->name('housekeeping.cleaning-schedules.status')->middleware('permission:cleaning_schedules.edit');

        // Housekeeping - Laundry Items
        Route::get('housekeeping/laundry-items/data', [LaundryItemController::class, 'data'])->name('housekeeping.laundry-items.data');
        Route::resource('housekeeping/laundry-items', LaundryItemController::class)->except(['show'])->names('housekeeping.laundry-items')->middleware('permission:laundry_items.view');
        Route::patch('housekeeping/laundry-items/{laundryItem}/status', [LaundryItemController::class, 'status'])->name('housekeeping.laundry-items.status')->middleware('permission:laundry_items.edit');

        // Housekeeping - Laundry Orders
        Route::get('housekeeping/laundry-orders/data', [LaundryOrderController::class, 'data'])->name('housekeeping.laundry-orders.data');
        Route::resource('housekeeping/laundry-orders', LaundryOrderController::class)->except(['show'])->names('housekeeping.laundry-orders')->middleware('permission:laundry_orders.view');
        Route::patch('housekeeping/laundry-orders/{laundryOrder}/status', [LaundryOrderController::class, 'status'])->name('housekeeping.laundry-orders.status')->middleware('permission:laundry_orders.edit');

        // Restaurant - Tables
        Route::get('restaurant/tables/data', [RestaurantTableController::class, 'data'])->name('restaurant.tables.data');
        Route::resource('restaurant/tables', RestaurantTableController::class)->except(['show'])->names('restaurant.tables')->parameters([
            'tables' => 'restaurantTable'
        ])->middleware('permission:restaurant_tables.view');
        Route::patch('restaurant/tables/{restaurantTable}/status', [RestaurantTableController::class, 'status'])->name('restaurant.tables.status')->middleware('permission:restaurant_tables.edit');

        // Restaurant - Menu Items
        Route::get('restaurant/menu-items/data', [MenuItemController::class, 'data'])->name('restaurant.menu-items.data');
        Route::resource('restaurant/menu-items', MenuItemController::class)->except(['show'])->names('restaurant.menu-items')->middleware('permission:menu_items.view');
        Route::patch('restaurant/menu-items/{menuItem}/status', [MenuItemController::class, 'status'])->name('restaurant.menu-items.status')->middleware('permission:menu_items.edit');

        // Restaurant - Orders
        Route::get('restaurant/orders/data', [OrderController::class, 'data'])->name('restaurant.orders.data');
        Route::get('restaurant/orders/menu-items', [OrderController::class, 'menuItems'])->name('restaurant.orders.menu-items');
        Route::resource('restaurant/orders', OrderController::class)->except(['show'])->names('restaurant.orders')->middleware('permission:restaurant_orders.view');
        Route::patch('restaurant/orders/{order}/status', [OrderController::class, 'status'])->name('restaurant.orders.status')->middleware('permission:restaurant_orders.edit');

        // Restaurant - Room Service
        Route::get('restaurant/room-service/data', [RoomServiceController::class, 'data'])->name('restaurant.room-service.data');
        Route::get('restaurant/room-service/orders-by-reservation', [RoomServiceController::class, 'ordersByReservation'])->name('restaurant.room-service.orders-by-reservation');
        Route::resource('restaurant/room-service', RoomServiceController::class)->except(['show'])->names('restaurant.room-service')->parameters([
            'room-service' => 'charge'
        ])->middleware('permission:room_service.view');
        Route::patch('restaurant/room-service/{charge}/status', [RoomServiceController::class, 'status'])->name('restaurant.room-service.status')->middleware('permission:room_service.edit');

        // Inventory - Item Master
        Route::get('inventory/categories/data', [CategoryController::class, 'data'])->name('inventory.categories.data');
        Route::resource('inventory/categories', CategoryController::class)->except(['show'])->names('inventory.categories')->middleware('permission:inventory_categories.view');
        Route::patch('inventory/categories/{category}/status', [CategoryController::class, 'status'])->name('inventory.categories.status')->middleware('permission:inventory_categories.edit');

        Route::get('inventory/units/data', [UnitController::class, 'data'])->name('inventory.units.data');
        Route::resource('inventory/units', UnitController::class)->except(['show'])->names('inventory.units')->middleware('permission:inventory_units.view');
        Route::patch('inventory/units/{unit}/status', [UnitController::class, 'status'])->name('inventory.units.status')->middleware('permission:inventory_units.edit');

        Route::get('inventory/items/data', [ItemController::class, 'data'])->name('inventory.items.data');
        Route::resource('inventory/items', ItemController::class)->except(['show'])->names('inventory.items')->middleware('permission:inventory_items.view');
        Route::patch('inventory/items/{item}/status', [ItemController::class, 'status'])->name('inventory.items.status')->middleware('permission:inventory_items.edit');

        // Inventory - Suppliers
        Route::get('inventory/suppliers/data', [SupplierController::class, 'data'])->name('inventory.suppliers.data');
        Route::resource('inventory/suppliers', SupplierController::class)->except(['show'])->names('inventory.suppliers')->middleware('permission:inventory_suppliers.view');
        Route::patch('inventory/suppliers/{supplier}/status', [SupplierController::class, 'status'])->name('inventory.suppliers.status')->middleware('permission:inventory_suppliers.edit');

        // Inventory - Purchase Orders
        Route::get('inventory/purchase-orders/data', [PurchaseOrderController::class, 'data'])->name('inventory.purchase-orders.data');
        Route::resource('inventory/purchase-orders', PurchaseOrderController::class)->except(['show'])->names('inventory.purchase-orders')->middleware('permission:inventory_purchase_orders.view');
        Route::patch('inventory/purchase-orders/{purchaseOrder}/status', [PurchaseOrderController::class, 'status'])->name('inventory.purchase-orders.status')->middleware('permission:inventory_purchase_orders.edit');

        // Inventory - GRN
        Route::get('inventory/grns/data', [GrnController::class, 'data'])->name('inventory.grns.data');
        Route::resource('inventory/grns', GrnController::class)->except(['show'])->names('inventory.grns')->middleware('permission:inventory_grn.view');
        Route::patch('inventory/grns/{grn}/status', [GrnController::class, 'status'])->name('inventory.grns.status')->middleware('permission:inventory_grn.edit');

        // Inventory - Stock
        Route::get('inventory/stocks/data', [StockController::class, 'data'])->name('inventory.stocks.data');
        Route::get('inventory/stocks', [StockController::class, 'index'])->name('inventory.stocks.index')->middleware('permission:inventory_stocks.view');

        // Inventory - Stock Transfers
        Route::get('inventory/stock-transfers/data', [StockTransferController::class, 'data'])->name('inventory.stock-transfers.data');
        Route::resource('inventory/stock-transfers', StockTransferController::class)->except(['show'])->names('inventory.stock-transfers')->middleware('permission:inventory_stock_transfers.view');
        Route::patch('inventory/stock-transfers/{stockTransfer}/status', [StockTransferController::class, 'status'])->name('inventory.stock-transfers.status')->middleware('permission:inventory_stock_transfers.edit');

        // Inventory - Stock Adjustments
        Route::get('inventory/stock-adjustments/data', [StockAdjustmentController::class, 'data'])->name('inventory.stock-adjustments.data');
        Route::resource('inventory/stock-adjustments', StockAdjustmentController::class)->except(['show'])->names('inventory.stock-adjustments')->middleware('permission:inventory_stock_adjustments.view');
        Route::patch('inventory/stock-adjustments/{stockAdjustment}/status', [StockAdjustmentController::class, 'status'])->name('inventory.stock-adjustments.status')->middleware('permission:inventory_stock_adjustments.edit');

        // Procurement - Vendor Categories
        Route::get('procurement/vendor-categories/data', [VendorCategoryController::class, 'data'])->name('procurement.vendor-categories.data');
        Route::resource('procurement/vendor-categories', VendorCategoryController::class)->except(['show'])->names('procurement.vendor-categories')->parameters([
            'vendor-categories' => 'category'
        ])->middleware('permission:vendor_categories.view');
        Route::patch('procurement/vendor-categories/{category}/status', [VendorCategoryController::class, 'status'])->name('procurement.vendor-categories.status')->middleware('permission:vendor_categories.edit');

        // Procurement - Vendors
        Route::get('procurement/vendors/data', [VendorController::class, 'data'])->name('procurement.vendors.data');
        Route::resource('procurement/vendors', VendorController::class)->names('procurement.vendors')->middleware('permission:vendors.view');
        Route::patch('procurement/vendors/{vendor}/status', [VendorController::class, 'status'])->name('procurement.vendors.status')->middleware('permission:vendors.edit');

        // Banquet - Halls
        Route::get('banquet/halls/data', [HallController::class, 'data'])->name('banquet.halls.data');
        Route::resource('banquet/halls', HallController::class)->except(['show'])->names('banquet.halls')->middleware('permission:halls.view');
        Route::patch('banquet/halls/{hall}/status', [HallController::class, 'status'])->name('banquet.halls.status')->middleware('permission:halls.edit');

        // Banquet - Events
        Route::get('banquet/events/data', [EventController::class, 'data'])->name('banquet.events.data');
        Route::resource('banquet/events', EventController::class)->names('banquet.events')->middleware('permission:events.view');
        Route::patch('banquet/events/{event}/status', [EventController::class, 'status'])->name('banquet.events.status')->middleware('permission:events.edit');

        // Spa - Services
        Route::get('spa/services/data', [SpaServiceController::class, 'data'])->name('spa.services.data');
        Route::resource('spa/services', SpaServiceController::class)->except(['show'])->names('spa.services')->middleware('permission:spa_services.view');
        Route::patch('spa/services/{service}/status', [SpaServiceController::class, 'status'])->name('spa.services.status')->middleware('permission:spa_services.edit');

        // Spa - Appointments
        Route::get('spa/appointments/data', [SpaAppointmentController::class, 'data'])->name('spa.appointments.data');
        Route::resource('spa/appointments', SpaAppointmentController::class)->names('spa.appointments')->middleware('permission:spa_appointments.view');
        Route::patch('spa/appointments/{appointment}/status', [SpaAppointmentController::class, 'status'])->name('spa.appointments.status')->middleware('permission:spa_appointments.edit');

        // Travel Desk - Transport Types
        Route::get('travel-desk/types/data', [TransportTypeController::class, 'data'])->name('travel-desk.types.data');
        Route::resource('travel-desk/types', TransportTypeController::class)->except(['show'])->names('travel-desk.types')->middleware('permission:transport_types.view');
        Route::patch('travel-desk/types/{type}/status', [TransportTypeController::class, 'status'])->name('travel-desk.types.status')->middleware('permission:transport_types.edit');

        // Travel Desk - Bookings
        Route::get('travel-desk/bookings/data', [TransportBookingController::class, 'data'])->name('travel-desk.bookings.data');
        Route::get('travel-desk/bookings/{booking}/toll-print', [TransportBookingController::class, 'tollPrint'])->name('travel-desk.bookings.toll-print')->middleware('permission:transport_bookings.view');
        Route::get('transport/invoices/{booking}/toll-print', [TransportBookingController::class, 'tollPrint'])->name('transport.invoices.toll-print')->middleware('permission:transport_bookings.view');
        Route::resource('travel-desk/bookings', TransportBookingController::class)->names('travel-desk.bookings')->middleware('permission:transport_bookings.view');
        Route::patch('travel-desk/bookings/{booking}/status', [TransportBookingController::class, 'status'])->name('travel-desk.bookings.status')->middleware('permission:transport_bookings.edit');

        // Finance - Chart of Accounts
        Route::get('finance/chart-of-accounts/data', [ChartOfAccountsController::class, 'data'])->name('finance.chart-of-accounts.data');
        Route::get('finance/chart-of-accounts/tree', [ChartOfAccountsController::class, 'tree'])->name('finance.chart-of-accounts.tree');
        Route::resource('finance/chart-of-accounts', ChartOfAccountsController::class)->except(['show'])->names('finance.chart-of-accounts')->middleware('permission:chart_of_accounts.view');
        Route::patch('finance/chart-of-accounts/{account}/status', [ChartOfAccountsController::class, 'status'])->name('finance.chart-of-accounts.status')->middleware('permission:chart_of_accounts.edit');

        // Finance - Journal Entries
        Route::get('finance/journal-entries/data', [JournalEntryController::class, 'data'])->name('finance.journal-entries.data');
        Route::resource('finance/journal-entries', JournalEntryController::class)->except(['show'])->names('finance.journal-entries')->middleware('permission:journal_entries.view');
        Route::get('finance/journal-entries/{journal_entry}', [JournalEntryController::class, 'show'])->name('finance.journal-entries.show')->middleware('permission:journal_entries.view');
        Route::patch('finance/journal-entries/{journal_entry}/status', [JournalEntryController::class, 'status'])->name('finance.journal-entries.status')->middleware('permission:journal_entries.edit');
        Route::post('finance/journal-entries/{journal_entry}/post', [JournalEntryController::class, 'post'])->name('finance.journal-entries.post')->middleware('permission:journal_entries.edit');
        Route::post('finance/journal-entries/{journal_entry}/void', [JournalEntryController::class, 'void_'])->name('finance.journal-entries.void')->middleware('permission:journal_entries.edit');

        // Finance - Accounts Payable
        Route::get('finance/accounts-payable/data', [AccountsPayableController::class, 'data'])->name('finance.accounts-payable.data');
        Route::resource('finance/accounts-payable', AccountsPayableController::class)->except(['show'])->names('finance.accounts-payable')->middleware('permission:accounts_payable.view');
        Route::get('finance/accounts-payable/{accounts_payable}', [AccountsPayableController::class, 'show'])->name('finance.accounts-payable.show')->middleware('permission:accounts_payable.view');
        Route::patch('finance/accounts-payable/{accounts_payable}/status', [AccountsPayableController::class, 'status'])->name('finance.accounts-payable.status')->middleware('permission:accounts_payable.edit');
        Route::post('finance/accounts-payable/{accounts_payable}/payment', [AccountsPayableController::class, 'storePayment'])->name('finance.accounts-payable.payment')->middleware('permission:accounts_payable.edit');

        // Finance - Accounts Receivable
        Route::get('finance/accounts-receivable/data', [AccountsReceivableController::class, 'data'])->name('finance.accounts-receivable.data');
        Route::resource('finance/accounts-receivable', AccountsReceivableController::class)->except(['show'])->names('finance.accounts-receivable')->middleware('permission:accounts_receivable.view');
        Route::get('finance/accounts-receivable/{accounts_receivable}', [AccountsReceivableController::class, 'show'])->name('finance.accounts-receivable.show')->middleware('permission:accounts_receivable.view');
        Route::patch('finance/accounts-receivable/{accounts_receivable}/status', [AccountsReceivableController::class, 'status'])->name('finance.accounts-receivable.status')->middleware('permission:accounts_receivable.edit');
        Route::post('finance/accounts-receivable/{accounts_receivable}/receipt', [AccountsReceivableController::class, 'storeReceipt'])->name('finance.accounts-receivable.receipt')->middleware('permission:accounts_receivable.edit');

        // Finance - GST
        Route::get('finance/gst/data', [GstController::class, 'data'])->name('finance.gst.data');
        Route::get('finance/gst/summary', [GstController::class, 'summary'])->name('finance.gst.summary')->middleware('permission:gst_returns.view');
        Route::resource('finance/gst', GstController::class)->except(['show'])->names('finance.gst')->middleware('permission:gst_returns.view');
        Route::patch('finance/gst/{gst}/status', [GstController::class, 'status'])->name('finance.gst.status')->middleware('permission:gst_returns.edit');

        // Finance - HSN/SAC Codes
        Route::get('finance/hsn-sac-codes/data', [HsnSacCodeController::class, 'data'])->name('finance.hsn-sac-codes.data');
        Route::resource('finance/hsn-sac-codes', HsnSacCodeController::class)->except(['show'])->names('finance.hsn-sac-codes')->middleware('permission:gst_returns.view');
        Route::patch('finance/hsn-sac-codes/{hsnSacCode}/status', [HsnSacCodeController::class, 'status'])->name('finance.hsn-sac-codes.status')->middleware('permission:gst_returns.edit');

        // Finance - Expenses
        Route::get('finance/expenses/data', [ExpenseController::class, 'data'])->name('finance.expenses.data');
        Route::resource('finance/expenses', ExpenseController::class)->except(['show'])->names('finance.expenses')->middleware('permission:expenses.view');
        Route::patch('finance/expenses/{expense}/status', [ExpenseController::class, 'status'])->name('finance.expenses.status')->middleware('permission:expenses.edit');

        // Finance - Reports
        Route::get('finance/reports/dashboard', [FinanceReportController::class, 'dashboard'])->name('finance.reports.dashboard')->middleware('permission:finance_reports.view');
        Route::get('finance/reports/trial-balance', [ReportController::class, 'trialBalance'])->name('finance.reports.trial-balance')->middleware('permission:finance_reports.view');
        Route::get('finance/reports/profit-loss', [ReportController::class, 'profitLoss'])->name('finance.reports.profit-loss')->middleware('permission:finance_reports.view');
        Route::get('finance/reports/balance-sheet', [ReportController::class, 'balanceSheet'])->name('finance.reports.balance-sheet')->middleware('permission:finance_reports.view');

        // Payment Gateways
        Route::get('payment-gateways', [PaymentGatewayController::class, 'index'])->name('payment-gateways.index')->middleware('permission:payment_gateways.view');
        Route::put('payment-gateways/{gateway}', [PaymentGatewayController::class, 'update'])->name('payment-gateways.update')->middleware('permission:payment_gateways.edit');
        Route::post('payment-gateways/{gateway}/toggle', [PaymentGatewayController::class, 'toggleStatus'])->name('payment-gateways.toggle')->middleware('permission:payment_gateways.edit');
        Route::post('payment-gateways/{gateway}/test', [PaymentGatewayController::class, 'testConnection'])->name('payment-gateways.test')->middleware('permission:payment_gateways.view');
        Route::post('payment-gateways/seed', [PaymentGatewayController::class, 'seed'])->name('payment-gateways.seed')->middleware('permission:payment_gateways.create');

        // HR - Employees
        Route::get('hr/employees/data', [EmployeeController::class, 'data'])->name('hr.employees.data');
        Route::resource('hr/employees', EmployeeController::class)->except(['show'])->names('hr.employees')->middleware('permission:hr_employees.view');
        Route::patch('hr/employees/{employee}/status', [EmployeeController::class, 'status'])->name('hr.employees.status')->middleware('permission:hr_employees.edit');

        // HR - Attendance
        Route::get('hr/attendance/data', [AttendanceController::class, 'data'])->name('hr.attendance.data');
        Route::resource('hr/attendance', AttendanceController::class)->except(['show'])->names('hr.attendance')->middleware('permission:hr_attendance.view');
        Route::patch('hr/attendance/{attendance}/status', [AttendanceController::class, 'status'])->name('hr.attendance.status')->middleware('permission:hr_attendance.edit');

        // HR - Payroll
        Route::get('hr/payroll/data', [HrPayrollController::class, 'data'])->name('hr.payroll.data');
        Route::resource('hr/payroll', HrPayrollController::class)->except(['show'])->names('hr.payroll')->middleware('permission:hr_payroll.view');
        Route::get('hr/payroll/{payroll}', [HrPayrollController::class, 'show'])->name('hr.payroll.show')->middleware('permission:hr_payroll.view');
        Route::patch('hr/payroll/{payroll}/status', [HrPayrollController::class, 'status'])->name('hr.payroll.status')->middleware('permission:hr_payroll.edit');

        // Maintenance - Assets
        Route::get('maintenance/assets/data', [MaintenanceAssetController::class, 'data'])->name('maintenance.assets.data');
        Route::resource('maintenance/assets', MaintenanceAssetController::class)->names('maintenance.assets')->middleware('permission:maintenance_assets.view');

        // Maintenance - AMC
        Route::get('maintenance/amcs/data', [MaintenanceAmcController::class, 'data'])->name('maintenance.amcs.data');
        Route::resource('maintenance/amcs', MaintenanceAmcController::class)->names('maintenance.amcs')->middleware('permission:maintenance_amcs.view');
        Route::patch('maintenance/amcs/{amc}/status', [MaintenanceAmcController::class, 'status'])->name('maintenance.amcs.status')->middleware('permission:maintenance_amcs.edit');

        // Maintenance - Work Orders
        Route::get('maintenance/work-orders/data', [MaintenanceWorkOrderController::class, 'data'])->name('maintenance.work-orders.data');
        Route::resource('maintenance/work-orders', MaintenanceWorkOrderController::class)->names('maintenance.work-orders')->middleware('permission:maintenance_work_orders.view');
        Route::patch('maintenance/work-orders/{work_order}/status', [MaintenanceWorkOrderController::class, 'status'])->name('maintenance.work-orders.status')->middleware('permission:maintenance_work_orders.edit');

        // Channel Manager - Sync All
        Route::post('channel-manager/sync-all', [SyncController::class, 'syncAll'])->name('channel-manager.sync-all')->middleware('permission:channel_manager.sync');
        Route::post('channel-manager/sync/{channel}', [SyncController::class, 'syncChannel'])->name('channel-manager.sync-channel')->middleware('permission:channel_manager.sync');

        // Channel Manager - OTA Channels
        Route::get('channel-manager/channels/data', [OtaChannelController::class, 'data'])->name('channel-manager.channels.data');
        Route::resource('channel-manager/channels', OtaChannelController::class)->except(['show'])->names('channel-manager.channels')->middleware('permission:channel_manager.view');
        Route::patch('channel-manager/channels/{channel}/status', [OtaChannelController::class, 'status'])->name('channel-manager.channels.status')->middleware('permission:channel_manager.edit');

        // Channel Manager - Room Mappings
        Route::get('channel-manager/mappings/data', [ChannelMappingController::class, 'data'])->name('channel-manager.mappings.data');
        Route::resource('channel-manager/mappings', ChannelMappingController::class)->except(['show'])->names('channel-manager.mappings')->middleware('permission:channel_manager.view');

        // Channel Manager - Sync Logs
        Route::get('channel-manager/sync-logs/data', [SyncLogController::class, 'data'])->name('channel-manager.sync-logs.data');
        Route::get('channel-manager/sync-logs', [SyncLogController::class, 'index'])->name('channel-manager.sync-logs.index')->middleware('permission:channel_manager.view');

        // Channel Manager - Settings
        Route::get('channel-manager/settings', [OtaChannelController::class, 'settings'])->name('channel-manager.settings')->middleware('permission:channel_manager.edit');
        Route::put('channel-manager/settings', [OtaChannelController::class, 'updateSettings'])->name('channel-manager.settings.update')->middleware('permission:channel_manager.edit');

        // Channel Manager - Test API
        Route::get('channel-manager/test-api', [TestApiController::class, 'index'])->name('channel-manager.test-api')->middleware('permission:channel_manager.view');
        Route::post('channel-manager/test-api/{channel}/pull', [TestApiController::class, 'testPull'])->name('channel-manager.test-api.pull')->middleware('permission:channel_manager.sync');
        Route::post('channel-manager/test-api/{channel}/push-rates', [TestApiController::class, 'testPushRates'])->name('channel-manager.test-api.push-rates')->middleware('permission:channel_manager.sync');
        Route::post('channel-manager/test-api/{channel}/push-availability', [TestApiController::class, 'testPushAvailability'])->name('channel-manager.test-api.push-availability')->middleware('permission:channel_manager.sync');
        Route::post('channel-manager/test-api/{channel}/full-sync', [TestApiController::class, 'testFullSync'])->name('channel-manager.test-api.full-sync')->middleware('permission:channel_manager.sync');

        // Integrations - Devices
        Route::get('integrations/devices/data', [DeviceController::class, 'data'])->name('integrations.devices.data');
        Route::resource('integrations/devices', DeviceController::class)->except(['show'])->names('integrations.devices')->middleware('permission:devices.view');
        Route::patch('integrations/devices/{device}/status', [DeviceController::class, 'status'])->name('integrations.devices.status')->middleware('permission:devices.edit');
        Route::get('integrations/devices/{device}', [DeviceController::class, 'show'])->name('integrations.devices.show')->middleware('permission:devices.view');
        Route::post('integrations/devices/{device}/test-connection', [DeviceController::class, 'testConnection'])->name('integrations.devices.test-connection')->middleware('permission:devices.edit');
        Route::post('integrations/devices/{device}/sync', [DeviceController::class, 'syncBiometric'])->name('integrations.devices.sync')->middleware('permission:devices.edit');
        Route::post('integrations/devices/{device}/lock', [DeviceController::class, 'lockDoor'])->name('integrations.devices.lock')->middleware('permission:devices.edit');
        Route::post('integrations/devices/{device}/unlock', [DeviceController::class, 'unlockDoor'])->name('integrations.devices.unlock')->middleware('permission:devices.edit');

        // Integrations - Device Logs
        Route::get('integrations/logs/data', [DeviceController::class, 'logsData'])->name('integrations.logs.data');
        Route::get('integrations/logs', [DeviceController::class, 'logs'])->name('integrations.logs.index')->middleware('permission:devices.view');

        // Integrations - Access Codes
        Route::get('integrations/access-codes/data', [DeviceController::class, 'accessCodesData'])->name('integrations.access-codes.data');
        Route::get('integrations/access-codes', [DeviceController::class, 'accessCodes'])->name('integrations.access-codes.index')->middleware('permission:devices.view');

        // Audit Logs
        Route::get('audit-logs/data', [AuditLogController::class, 'data'])->name('audit-logs.data');
        Route::get('audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index')->middleware('permission:dashboard.view');
        Route::get('audit-logs/{auditLog}', [AuditLogController::class, 'show'])->name('audit-logs.show')->middleware('permission:dashboard.view');

        // Notifications
        Route::get('notifications/data', [NotificationController::class, 'data'])->name('notifications.data');
        Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
        Route::post('notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
        Route::post('notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
        Route::get('notifications/unread-count', [NotificationController::class, 'getUnreadCount'])->name('notifications.unread-count');
        Route::get('notifications/recent', [NotificationController::class, 'getRecent'])->name('notifications.recent');

        // Approvals
        Route::get('approvals/data', [ApprovalController::class, 'data'])->name('approvals.data');
        Route::get('approvals', [ApprovalController::class, 'index'])->name('approvals.index');
        Route::post('approvals/{approval}/approve', [ApprovalController::class, 'approve'])->name('approvals.approve');
        Route::post('approvals/{approval}/reject', [ApprovalController::class, 'reject'])->name('approvals.reject');
        Route::get('approvals/rules', [ApprovalController::class, 'rules'])->name('approvals.rules');
        Route::post('approvals/rules', [ApprovalController::class, 'storeRule'])->name('approvals.store-rule');
        Route::delete('approvals/rules/{rule}', [ApprovalController::class, 'destroyRule'])->name('approvals.destroy-rule');

        // Communication Templates
        Route::get('communications/data', [CommunicationController::class, 'data'])->name('communications.data');
        Route::resource('communications', CommunicationController::class)->except(['show']);
        Route::get('communications/{template}/preview', [CommunicationController::class, 'preview'])->name('communications.preview');
        Route::get('communications/logs', [CommunicationController::class, 'logs'])->name('communications.logs');
        Route::get('communications/logs/data', [CommunicationController::class, 'logsData'])->name('communications.logs-data');

        // Languages
        Route::get('languages/data', [LanguageController::class, 'data'])->name('languages.data');
        Route::resource('languages', LanguageController::class)->except(['show']);
        Route::get('languages/{language}/translations', [LanguageController::class, 'translations'])->name('languages.translations');
        Route::post('languages/{language}/translations', [LanguageController::class, 'storeTranslation'])->name('languages.store-translation');
        Route::delete('languages/translations/{translation}', [LanguageController::class, 'destroyTranslation'])->name('languages.destroy-translation');
    });
});
