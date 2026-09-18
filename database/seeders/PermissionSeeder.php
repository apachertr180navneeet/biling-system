<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $modules = [
            'dashboard' => [
                'dashboard.view' => 'View Dashboard',
            ],
            'users' => [
                'users.view' => 'View Users',
                'users.create' => 'Create Users',
                'users.edit' => 'Edit Users',
                'users.delete' => 'Delete Users',
            ],
            'roles' => [
                'roles.view' => 'View Roles',
                'roles.create' => 'Create Roles',
                'roles.edit' => 'Edit Roles',
                'roles.delete' => 'Delete Roles',
            ],
            'company' => [
                'company.view' => 'View Company',
                'company.edit' => 'Edit Company',
            ],
            'branches' => [
                'branches.view' => 'View Branches',
                'branches.create' => 'Create Branches',
                'branches.edit' => 'Edit Branches',
                'branches.delete' => 'Delete Branches',
            ],
            'departments' => [
                'departments.view' => 'View Departments',
                'departments.create' => 'Create Departments',
                'departments.edit' => 'Edit Departments',
                'departments.delete' => 'Delete Departments',
            ],
            'designations' => [
                'designations.view' => 'View Designations',
                'designations.create' => 'Create Designations',
                'designations.edit' => 'Edit Designations',
                'designations.delete' => 'Delete Designations',
            ],
            'financial_years' => [
                'financial_years.view' => 'View Financial Years',
                'financial_years.create' => 'Create Financial Years',
                'financial_years.edit' => 'Edit Financial Years',
                'financial_years.delete' => 'Delete Financial Years',
            ],
            'currencies' => [
                'currencies.view' => 'View Currencies',
                'currencies.create' => 'Create Currencies',
                'currencies.edit' => 'Edit Currencies',
                'currencies.delete' => 'Delete Currencies',
            ],
            'taxes' => [
                'taxes.view' => 'View Taxes',
                'taxes.create' => 'Create Taxes',
                'taxes.edit' => 'Edit Taxes',
                'taxes.delete' => 'Delete Taxes',
            ],
            'tax_groups' => [
                'tax_groups.view' => 'View Tax Groups',
                'tax_groups.create' => 'Create Tax Groups',
                'tax_groups.edit' => 'Edit Tax Groups',
                'tax_groups.delete' => 'Delete Tax Groups',
            ],
            'number_series' => [
                'number_series.view' => 'View Number Series',
                'number_series.create' => 'Create Number Series',
                'number_series.edit' => 'Edit Number Series',
                'number_series.delete' => 'Delete Number Series',
            ],
            'hotels' => [
                'hotels.view' => 'View Hotels',
                'hotels.create' => 'Create Hotels',
                'hotels.edit' => 'Edit Hotels',
                'hotels.delete' => 'Delete Hotels',
            ],
            'buildings' => [
                'buildings.view' => 'View Buildings',
                'buildings.create' => 'Create Buildings',
                'buildings.edit' => 'Edit Buildings',
                'buildings.delete' => 'Delete Buildings',
            ],
            'floors' => [
                'floors.view' => 'View Floors',
                'floors.create' => 'Create Floors',
                'floors.edit' => 'Edit Floors',
                'floors.delete' => 'Delete Floors',
            ],
            'wings' => [
                'wings.view' => 'View Wings',
                'wings.create' => 'Create Wings',
                'wings.edit' => 'Edit Wings',
                'wings.delete' => 'Delete Wings',
            ],
            'room_types' => [
                'room_types.view' => 'View Room Types',
                'room_types.create' => 'Create Room Types',
                'room_types.edit' => 'Edit Room Types',
                'room_types.delete' => 'Delete Room Types',
            ],
            'amenities' => [
                'amenities.view' => 'View Amenities',
                'amenities.create' => 'Create Amenities',
                'amenities.edit' => 'Edit Amenities',
                'amenities.delete' => 'Delete Amenities',
            ],
            'bed_types' => [
                'bed_types.view' => 'View Bed Types',
                'bed_types.create' => 'Create Bed Types',
                'bed_types.edit' => 'Edit Bed Types',
                'bed_types.delete' => 'Delete Bed Types',
            ],
            'room_status' => [
                'room_status.view' => 'View Room Status',
                'room_status.create' => 'Create Room Status',
                'room_status.edit' => 'Edit Room Status',
                'room_status.delete' => 'Delete Room Status',
            ],
            'rooms' => [
                'rooms.view' => 'View Rooms',
                'rooms.create' => 'Create Rooms',
                'rooms.edit' => 'Edit Rooms',
                'rooms.delete' => 'Delete Rooms',
            ],
            'guests' => [
                'guests.view' => 'View Guests',
                'guests.create' => 'Create Guests',
                'guests.edit' => 'Edit Guests',
                'guests.delete' => 'Delete Guests',
            ],
            'bookings' => [
                'bookings.view' => 'View Bookings',
                'bookings.create' => 'Create Bookings',
                'bookings.edit' => 'Edit Bookings',
                'bookings.delete' => 'Delete Bookings',
            ],
            'group_bookings' => [
                'group_bookings.view' => 'View Group Bookings',
                'group_bookings.create' => 'Create Group Bookings',
                'group_bookings.edit' => 'Edit Group Bookings',
                'group_bookings.delete' => 'Delete Group Bookings',
            ],
            'rate_plans' => [
                'rate_plans.view' => 'View Rate Plans',
                'rate_plans.create' => 'Create Rate Plans',
                'rate_plans.edit' => 'Edit Rate Plans',
                'rate_plans.delete' => 'Delete Rate Plans',
            ],
            'payments' => [
                'payments.view' => 'View Payments',
                'payments.create' => 'Create Payments',
                'payments.delete' => 'Delete Payments',
            ],
            'check_ins' => [
                'check_ins.view' => 'View Check Ins',
                'check_ins.create' => 'Create Check Ins',
                'check_ins.edit' => 'Edit Check Ins',
                'check_ins.delete' => 'Delete Check Ins',
            ],
            'check_outs' => [
                'check_outs.view' => 'View Check Outs',
                'check_outs.create' => 'Create Check Outs',
                'check_outs.edit' => 'Edit Check Outs',
                'check_outs.delete' => 'Delete Check Outs',
            ],
            'night_audits' => [
                'night_audits.view' => 'View Night Audits',
                'night_audits.create' => 'Create Night Audits',
                'night_audits.edit' => 'Edit Night Audits',
                'night_audits.delete' => 'Delete Night Audits',
            ],
            'guest_profiles' => [
                'guest_profiles.view' => 'View Guest Profiles',
                'guest_profiles.create' => 'Create Guest Profiles',
                'guest_profiles.edit' => 'Edit Guest Profiles',
                'guest_profiles.delete' => 'Delete Guest Profiles',
            ],
            'loyalty_members' => [
                'loyalty_members.view' => 'View Loyalty Members',
                'loyalty_members.create' => 'Create Loyalty Members',
                'loyalty_members.edit' => 'Edit Loyalty Members',
                'loyalty_members.delete' => 'Delete Loyalty Members',
            ],
            'loyalty_tiers' => [
                'loyalty_tiers.view' => 'View Loyalty Tiers',
                'loyalty_tiers.create' => 'Create Loyalty Tiers',
                'loyalty_tiers.edit' => 'Edit Loyalty Tiers',
                'loyalty_tiers.delete' => 'Delete Loyalty Tiers',
            ],
            'cleaning_schedules' => [
                'cleaning_schedules.view' => 'View Cleaning Schedules',
                'cleaning_schedules.create' => 'Create Cleaning Schedules',
                'cleaning_schedules.edit' => 'Edit Cleaning Schedules',
                'cleaning_schedules.delete' => 'Delete Cleaning Schedules',
            ],
            'laundry_items' => [
                'laundry_items.view' => 'View Laundry Items',
                'laundry_items.create' => 'Create Laundry Items',
                'laundry_items.edit' => 'Edit Laundry Items',
                'laundry_items.delete' => 'Delete Laundry Items',
            ],
            'laundry_orders' => [
                'laundry_orders.view' => 'View Laundry Orders',
                'laundry_orders.create' => 'Create Laundry Orders',
                'laundry_orders.edit' => 'Edit Laundry Orders',
                'laundry_orders.delete' => 'Delete Laundry Orders',
            ],
            'restaurant_tables' => [
                'restaurant_tables.view' => 'View Restaurant Tables',
                'restaurant_tables.create' => 'Create Restaurant Tables',
                'restaurant_tables.edit' => 'Edit Restaurant Tables',
                'restaurant_tables.delete' => 'Delete Restaurant Tables',
            ],
            'menu_items' => [
                'menu_items.view' => 'View Menu Items',
                'menu_items.create' => 'Create Menu Items',
                'menu_items.edit' => 'Edit Menu Items',
                'menu_items.delete' => 'Delete Menu Items',
            ],
            'restaurant_orders' => [
                'restaurant_orders.view' => 'View Restaurant Orders',
                'restaurant_orders.create' => 'Create Restaurant Orders',
                'restaurant_orders.edit' => 'Edit Restaurant Orders',
                'restaurant_orders.delete' => 'Delete Restaurant Orders',
            ],
            'room_service' => [
                'room_service.view' => 'View Room Service',
                'room_service.create' => 'Create Room Service',
                'room_service.edit' => 'Edit Room Service',
                'room_service.delete' => 'Delete Room Service',
            ],
            'inventory_categories' => [
                'inventory_categories.view' => 'View Inventory Categories',
                'inventory_categories.create' => 'Create Inventory Categories',
                'inventory_categories.edit' => 'Edit Inventory Categories',
                'inventory_categories.delete' => 'Delete Inventory Categories',
            ],
            'inventory_units' => [
                'inventory_units.view' => 'View Inventory Units',
                'inventory_units.create' => 'Create Inventory Units',
                'inventory_units.edit' => 'Edit Inventory Units',
                'inventory_units.delete' => 'Delete Inventory Units',
            ],
            'inventory_items' => [
                'inventory_items.view' => 'View Inventory Items',
                'inventory_items.create' => 'Create Inventory Items',
                'inventory_items.edit' => 'Edit Inventory Items',
                'inventory_items.delete' => 'Delete Inventory Items',
            ],
            'inventory_suppliers' => [
                'inventory_suppliers.view' => 'View Inventory Suppliers',
                'inventory_suppliers.create' => 'Create Inventory Suppliers',
                'inventory_suppliers.edit' => 'Edit Inventory Suppliers',
                'inventory_suppliers.delete' => 'Delete Inventory Suppliers',
            ],
            'inventory_purchase_orders' => [
                'inventory_purchase_orders.view' => 'View Purchase Orders',
                'inventory_purchase_orders.create' => 'Create Purchase Orders',
                'inventory_purchase_orders.edit' => 'Edit Purchase Orders',
                'inventory_purchase_orders.delete' => 'Delete Purchase Orders',
            ],
            'inventory_grn' => [
                'inventory_grn.view' => 'View GRN',
                'inventory_grn.create' => 'Create GRN',
                'inventory_grn.edit' => 'Edit GRN',
                'inventory_grn.delete' => 'Delete GRN',
            ],
            'inventory_stocks' => [
                'inventory_stocks.view' => 'View Stock',
            ],
            'inventory_stock_transfers' => [
                'inventory_stock_transfers.view' => 'View Stock Transfers',
                'inventory_stock_transfers.create' => 'Create Stock Transfers',
                'inventory_stock_transfers.edit' => 'Edit Stock Transfers',
                'inventory_stock_transfers.delete' => 'Delete Stock Transfers',
            ],
            'inventory_stock_adjustments' => [
                'inventory_stock_adjustments.view' => 'View Stock Adjustments',
                'inventory_stock_adjustments.create' => 'Create Stock Adjustments',
                'inventory_stock_adjustments.edit' => 'Edit Stock Adjustments',
                'inventory_stock_adjustments.delete' => 'Delete Stock Adjustments',
            ],

            // Procurement
            'vendor_categories' => [
                'vendor_categories.view' => 'View Vendor Categories',
                'vendor_categories.create' => 'Create Vendor Categories',
                'vendor_categories.edit' => 'Edit Vendor Categories',
                'vendor_categories.delete' => 'Delete Vendor Categories',
            ],
            'vendors' => [
                'vendors.view' => 'View Vendors',
                'vendors.create' => 'Create Vendors',
                'vendors.edit' => 'Edit Vendors',
                'vendors.delete' => 'Delete Vendors',
            ],

            // Banquet
            'halls' => [
                'halls.view' => 'View Halls',
                'halls.create' => 'Create Halls',
                'halls.edit' => 'Edit Halls',
                'halls.delete' => 'Delete Halls',
            ],
            'events' => [
                'events.view' => 'View Events',
                'events.create' => 'Create Events',
                'events.edit' => 'Edit Events',
                'events.delete' => 'Delete Events',
            ],
            'spa_services' => [
                'spa_services.view' => 'View Spa Services',
                'spa_services.create' => 'Create Spa Services',
                'spa_services.edit' => 'Edit Spa Services',
                'spa_services.delete' => 'Delete Spa Services',
            ],
            'spa_appointments' => [
                'spa_appointments.view' => 'View Spa Appointments',
                'spa_appointments.create' => 'Create Spa Appointments',
                'spa_appointments.edit' => 'Edit Spa Appointments',
                'spa_appointments.delete' => 'Delete Spa Appointments',
            ],
            'transport_types' => [
                'transport_types.view' => 'View Transport Types',
                'transport_types.create' => 'Create Transport Types',
                'transport_types.edit' => 'Edit Transport Types',
                'transport_types.delete' => 'Delete Transport Types',
            ],
            'transport_bookings' => [
                'transport_bookings.view' => 'View Transport Bookings',
                'transport_bookings.create' => 'Create Transport Bookings',
                'transport_bookings.edit' => 'Edit Transport Bookings',
                'transport_bookings.delete' => 'Delete Transport Bookings',
            ],

            // Finance
            'chart_of_accounts' => [
                'chart_of_accounts.view' => 'View Chart of Accounts',
                'chart_of_accounts.create' => 'Create Chart of Accounts',
                'chart_of_accounts.edit' => 'Edit Chart of Accounts',
                'chart_of_accounts.delete' => 'Delete Chart of Accounts',
            ],
            'journal_entries' => [
                'journal_entries.view' => 'View Journal Entries',
                'journal_entries.create' => 'Create Journal Entries',
                'journal_entries.edit' => 'Edit Journal Entries',
                'journal_entries.delete' => 'Delete Journal Entries',
            ],
            'accounts_payable' => [
                'accounts_payable.view' => 'View Accounts Payable',
                'accounts_payable.create' => 'Create Accounts Payable',
                'accounts_payable.edit' => 'Edit Accounts Payable',
                'accounts_payable.delete' => 'Delete Accounts Payable',
            ],
            'accounts_receivable' => [
                'accounts_receivable.view' => 'View Accounts Receivable',
                'accounts_receivable.create' => 'Create Accounts Receivable',
                'accounts_receivable.edit' => 'Edit Accounts Receivable',
                'accounts_receivable.delete' => 'Delete Accounts Receivable',
            ],
            'gst_returns' => [
                'gst_returns.view' => 'View GST Returns',
                'gst_returns.create' => 'Create GST Returns',
                'gst_returns.edit' => 'Edit GST Returns',
                'gst_returns.delete' => 'Delete GST Returns',
            ],
            'expenses' => [
                'expenses.view' => 'View Expenses',
                'expenses.create' => 'Create Expenses',
                'expenses.edit' => 'Edit Expenses',
                'expenses.delete' => 'Delete Expenses',
            ],
            'finance_reports' => [
                'finance_reports.view' => 'View Finance Reports',
            ],
            'payment_gateways' => [
                'payment_gateways.view' => 'View Payment Gateways',
                'payment_gateways.create' => 'Create Payment Gateways',
                'payment_gateways.edit' => 'Edit Payment Gateways',
                'payment_gateways.delete' => 'Delete Payment Gateways',
            ],

            // HR
            'hr_employees' => [
                'hr_employees.view' => 'View Employees',
                'hr_employees.create' => 'Create Employees',
                'hr_employees.edit' => 'Edit Employees',
                'hr_employees.delete' => 'Delete Employees',
            ],
            'hr_attendance' => [
                'hr_attendance.view' => 'View Attendance',
                'hr_attendance.create' => 'Create Attendance',
                'hr_attendance.edit' => 'Edit Attendance',
                'hr_attendance.delete' => 'Delete Attendance',
            ],
            'hr_payroll' => [
                'hr_payroll.view' => 'View Payroll',
                'hr_payroll.create' => 'Create Payroll',
                'hr_payroll.edit' => 'Edit Payroll',
                'hr_payroll.delete' => 'Delete Payroll',
            ],
            'maintenance_assets' => [
                'maintenance_assets.view' => 'View Assets',
                'maintenance_assets.create' => 'Create Assets',
                'maintenance_assets.edit' => 'Edit Assets',
                'maintenance_assets.delete' => 'Delete Assets',
            ],
            'maintenance_amcs' => [
                'maintenance_amcs.view' => 'View AMC',
                'maintenance_amcs.create' => 'Create AMC',
                'maintenance_amcs.edit' => 'Edit AMC',
                'maintenance_amcs.delete' => 'Delete AMC',
            ],
            'maintenance_work_orders' => [
                'maintenance_work_orders.view' => 'View Work Orders',
                'maintenance_work_orders.create' => 'Create Work Orders',
                'maintenance_work_orders.edit' => 'Edit Work Orders',
                'maintenance_work_orders.delete' => 'Delete Work Orders',
            ],
            'marketing_campaigns' => [
                'marketing_campaigns.view' => 'View Campaigns',
                'marketing_campaigns.create' => 'Create Campaigns',
                'marketing_campaigns.edit' => 'Edit Campaigns',
                'marketing_campaigns.delete' => 'Delete Campaigns',
            ],
            'channel_manager' => [
                'channel_manager.view' => 'View Channel Manager',
                'channel_manager.create' => 'Create OTA Connections',
                'channel_manager.edit' => 'Edit OTA Connections',
                'channel_manager.delete' => 'Delete OTA Connections',
                'channel_manager.sync' => 'Sync with OTAs',
            ],
            'devices' => [
                'devices.view' => 'View Devices',
                'devices.create' => 'Create Devices',
                'devices.edit' => 'Edit Devices',
                'devices.delete' => 'Delete Devices',
            ],
        ];

        foreach ($modules as $module => $perms) {
            foreach ($perms as $slug => $name) {
                Permission::updateOrCreate(
                    ['slug' => $slug],
                    [
                        'name' => $name,
                        'slug' => $slug,
                        'module' => $module,
                    ]
                );
            }
        }

        $allPermissions = Permission::pluck('id')->toArray();

        Role::where('slug', 'super-admin')->first()?->permissions()->sync($allPermissions);

        $adminPerms = Permission::whereIn('slug', [
            'dashboard.view',
            'users.view', 'users.create', 'users.edit', 'users.delete',
            'roles.view', 'roles.create', 'roles.edit', 'roles.delete',
            'company.view', 'company.edit',
            'branches.view', 'branches.create', 'branches.edit', 'branches.delete',
            'departments.view', 'departments.create', 'departments.edit', 'departments.delete',
            'designations.view', 'designations.create', 'designations.edit', 'designations.delete',
            'financial_years.view', 'financial_years.create', 'financial_years.edit', 'financial_years.delete',
            'currencies.view', 'currencies.create', 'currencies.edit', 'currencies.delete',
            'taxes.view', 'taxes.create', 'taxes.edit', 'taxes.delete',
            'tax_groups.view', 'tax_groups.create', 'tax_groups.edit', 'tax_groups.delete',
            'number_series.view', 'number_series.create', 'number_series.edit', 'number_series.delete',
            'hotels.view', 'hotels.create', 'hotels.edit', 'hotels.delete',
            'buildings.view', 'buildings.create', 'buildings.edit', 'buildings.delete',
            'floors.view', 'floors.create', 'floors.edit', 'floors.delete',
            'wings.view', 'wings.create', 'wings.edit', 'wings.delete',
            'room_types.view', 'room_types.create', 'room_types.edit', 'room_types.delete',
            'amenities.view', 'amenities.create', 'amenities.edit', 'amenities.delete',
            'bed_types.view', 'bed_types.create', 'bed_types.edit', 'bed_types.delete',
            'room_status.view', 'room_status.create', 'room_status.edit', 'room_status.delete',
            'rooms.view', 'rooms.create', 'rooms.edit', 'rooms.delete',
            'guests.view', 'guests.create', 'guests.edit', 'guests.delete',
            'bookings.view', 'bookings.create', 'bookings.edit', 'bookings.delete',
            'group_bookings.view', 'group_bookings.create', 'group_bookings.edit', 'group_bookings.delete',
            'rate_plans.view', 'rate_plans.create', 'rate_plans.edit', 'rate_plans.delete',
            'payments.view', 'payments.create', 'payments.delete',
            'check_ins.view', 'check_ins.create', 'check_ins.edit', 'check_ins.delete',
            'check_outs.view', 'check_outs.create', 'check_outs.edit', 'check_outs.delete',
            'night_audits.view', 'night_audits.create', 'night_audits.edit', 'night_audits.delete',
            'guest_profiles.view', 'guest_profiles.create', 'guest_profiles.edit', 'guest_profiles.delete',
            'loyalty_members.view', 'loyalty_members.create', 'loyalty_members.edit', 'loyalty_members.delete',
            'loyalty_tiers.view', 'loyalty_tiers.create', 'loyalty_tiers.edit', 'loyalty_tiers.delete',
            'cleaning_schedules.view', 'cleaning_schedules.create', 'cleaning_schedules.edit', 'cleaning_schedules.delete',
            'laundry_items.view', 'laundry_items.create', 'laundry_items.edit', 'laundry_items.delete',
            'laundry_orders.view', 'laundry_orders.create', 'laundry_orders.edit', 'laundry_orders.delete',
            'restaurant_tables.view', 'restaurant_tables.create', 'restaurant_tables.edit', 'restaurant_tables.delete',
            'menu_items.view', 'menu_items.create', 'menu_items.edit', 'menu_items.delete',
            'restaurant_orders.view', 'restaurant_orders.create', 'restaurant_orders.edit', 'restaurant_orders.delete',
            'room_service.view', 'room_service.create', 'room_service.edit', 'room_service.delete',
            'inventory_categories.view', 'inventory_categories.create', 'inventory_categories.edit', 'inventory_categories.delete',
            'inventory_units.view', 'inventory_units.create', 'inventory_units.edit', 'inventory_units.delete',
            'inventory_items.view', 'inventory_items.create', 'inventory_items.edit', 'inventory_items.delete',
            'inventory_suppliers.view', 'inventory_suppliers.create', 'inventory_suppliers.edit', 'inventory_suppliers.delete',
            'inventory_purchase_orders.view', 'inventory_purchase_orders.create', 'inventory_purchase_orders.edit', 'inventory_purchase_orders.delete',
            'inventory_grn.view', 'inventory_grn.create', 'inventory_grn.edit', 'inventory_grn.delete',
            'inventory_stocks.view',
            'inventory_stock_transfers.view', 'inventory_stock_transfers.create', 'inventory_stock_transfers.edit', 'inventory_stock_transfers.delete',
            'inventory_stock_adjustments.view', 'inventory_stock_adjustments.create', 'inventory_stock_adjustments.edit', 'inventory_stock_adjustments.delete',
            'vendor_categories.view', 'vendor_categories.create', 'vendor_categories.edit', 'vendor_categories.delete',
            'vendors.view', 'vendors.create', 'vendors.edit', 'vendors.delete',
            'halls.view', 'halls.create', 'halls.edit', 'halls.delete',
            'events.view', 'events.create', 'events.edit', 'events.delete',
            'spa_services.view', 'spa_services.create', 'spa_services.edit', 'spa_services.delete',
            'spa_appointments.view', 'spa_appointments.create', 'spa_appointments.edit', 'spa_appointments.delete',
            'transport_types.view', 'transport_types.create', 'transport_types.edit', 'transport_types.delete',
            'transport_bookings.view', 'transport_bookings.create', 'transport_bookings.edit', 'transport_bookings.delete',
            'chart_of_accounts.view', 'chart_of_accounts.create', 'chart_of_accounts.edit', 'chart_of_accounts.delete',
            'journal_entries.view', 'journal_entries.create', 'journal_entries.edit', 'journal_entries.delete',
            'accounts_payable.view', 'accounts_payable.create', 'accounts_payable.edit', 'accounts_payable.delete',
            'accounts_receivable.view', 'accounts_receivable.create', 'accounts_receivable.edit', 'accounts_receivable.delete',
            'gst_returns.view', 'gst_returns.create', 'gst_returns.edit', 'gst_returns.delete',
            'expenses.view', 'expenses.create', 'expenses.edit', 'expenses.delete',
            'finance_reports.view',
            'payment_gateways.view', 'payment_gateways.create', 'payment_gateways.edit', 'payment_gateways.delete',
            'hr_employees.view', 'hr_employees.create', 'hr_employees.edit', 'hr_employees.delete',
            'hr_attendance.view', 'hr_attendance.create', 'hr_attendance.edit', 'hr_attendance.delete',
            'hr_payroll.view', 'hr_payroll.create', 'hr_payroll.edit', 'hr_payroll.delete',
            'maintenance_assets.view', 'maintenance_assets.create', 'maintenance_assets.edit', 'maintenance_assets.delete',
            'maintenance_amcs.view', 'maintenance_amcs.create', 'maintenance_amcs.edit', 'maintenance_amcs.delete',
            'maintenance_work_orders.view', 'maintenance_work_orders.create', 'maintenance_work_orders.edit', 'maintenance_work_orders.delete',
            'marketing_campaigns.view', 'marketing_campaigns.create', 'marketing_campaigns.edit', 'marketing_campaigns.delete',
            'channel_manager.view', 'channel_manager.create', 'channel_manager.edit', 'channel_manager.delete', 'channel_manager.sync',
            'devices.view', 'devices.create', 'devices.edit', 'devices.delete',
        ])->pluck('id')->toArray();

        Role::where('slug', 'admin')->first()?->permissions()->sync($adminPerms);

        $managerPerms = Permission::whereIn('slug', [
            'dashboard.view',
            'users.view',
            'company.view',
            'branches.view',
            'departments.view',
            'designations.view',
            'financial_years.view',
            'currencies.view',
            'taxes.view',
            'tax_groups.view',
            'number_series.view',
            'hotels.view',
            'buildings.view',
            'floors.view',
            'wings.view',
            'room_types.view',
            'amenities.view',
            'bed_types.view',
            'room_status.view',
            'rooms.view',
            'guests.view',
            'bookings.view',
            'group_bookings.view',
            'rate_plans.view',
            'payments.view',
            'check_ins.view',
            'check_outs.view',
            'night_audits.view',
            'guest_profiles.view',
            'loyalty_members.view',
            'loyalty_tiers.view',
            'cleaning_schedules.view',
            'laundry_items.view',
            'laundry_orders.view',
            'restaurant_tables.view',
            'menu_items.view',
            'restaurant_orders.view',
            'room_service.view',
            'inventory_categories.view',
            'inventory_units.view',
            'inventory_items.view',
            'inventory_suppliers.view',
            'inventory_purchase_orders.view',
            'inventory_grn.view',
            'inventory_stocks.view',
            'inventory_stock_transfers.view',
            'inventory_stock_adjustments.view',
            'vendor_categories.view',
            'vendors.view',
            'halls.view',
            'events.view',
            'spa_services.view',
            'spa_appointments.view',
            'transport_types.view',
            'transport_bookings.view',
            'chart_of_accounts.view',
            'journal_entries.view',
            'accounts_payable.view',
            'accounts_receivable.view',
            'gst_returns.view',
            'expenses.view',
            'finance_reports.view',
            'payment_gateways.view',
            'hr_employees.view',
            'hr_attendance.view',
            'hr_payroll.view',
            'maintenance_assets.view',
            'maintenance_amcs.view',
            'maintenance_work_orders.view',
            'marketing_campaigns.view',
            'channel_manager.view',
            'devices.view',
        ])->pluck('id')->toArray();

        Role::where('slug', 'manager')->first()?->permissions()->sync($managerPerms);

        $staffPerms = Permission::whereIn('slug', [
            'dashboard.view',
        ])->pluck('id')->toArray();

        Role::where('slug', 'staff')->first()?->permissions()->sync($staffPerms);
    }
}
