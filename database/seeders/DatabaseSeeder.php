<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            PermissionSeeder::class,
            UserSeeder::class,
            CurrencySeeder::class,
            TaxSeeder::class,
            NumberSeriesSeeder::class,
            BranchDummyDataSeeder::class,
            PropertySeeder::class,
            DepartmentDesignationDummyDataSeeder::class,
            EmployeeDummyDataSeeder::class,
            EmployeeAttendancePayrollDummyDataSeeder::class,
            TaxGroupDummyDataSeeder::class,
            HsnSacCodeSeeder::class,
            FinanceHrSeeder::class,
            DummyDataSeeder::class,
            InventoryDummyDataSeeder::class,
            VendorDummyDataSeeder::class,
            BanquetDummyDataSeeder::class,
            SpaTravelDeskDummyDataSeeder::class,
            FinanceDummyDataSeeder::class,
            CheckOutDummyDataSeeder::class,
            MaintenanceDummyDataSeeder::class,
            MarketingCampaignDummyDataSeeder::class,
            CmsSeeder::class,
            DeviceSeeder::class,
            LanguageSeeder::class,
            TimeZoneSeeder::class,
        ]);
    }
}
