<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Helpers\Helper;

class FinanceHrSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            ['code' => '1000', 'name' => 'Cash', 'type' => 'asset', 'sub_type' => 'current_asset', 'is_group' => 0, 'is_bank_account' => 0],
            ['code' => '1010', 'name' => 'Bank Account', 'type' => 'asset', 'sub_type' => 'current_asset', 'is_group' => 0, 'is_bank_account' => 1],
            ['code' => '1020', 'name' => 'Accounts Receivable', 'type' => 'asset', 'sub_type' => 'current_asset', 'is_group' => 0, 'is_bank_account' => 0],
            ['code' => '1030', 'name' => 'Inventory', 'type' => 'asset', 'sub_type' => 'current_asset', 'is_group' => 0, 'is_bank_account' => 0],
            ['code' => '1100', 'name' => 'Fixed Assets', 'type' => 'asset', 'sub_type' => 'fixed_asset', 'is_group' => 1, 'is_bank_account' => 0],
            ['code' => '1110', 'name' => 'Furniture & Fixtures', 'type' => 'asset', 'sub_type' => 'fixed_asset', 'is_group' => 0, 'is_bank_account' => 0],
            ['code' => '1120', 'name' => 'Equipment', 'type' => 'asset', 'sub_type' => 'fixed_asset', 'is_group' => 0, 'is_bank_account' => 0],
            ['code' => '1130', 'name' => 'Building', 'type' => 'asset', 'sub_type' => 'fixed_asset', 'is_group' => 0, 'is_bank_account' => 0],
            ['code' => '2000', 'name' => 'Accounts Payable', 'type' => 'liability', 'sub_type' => 'current_liability', 'is_group' => 0, 'is_bank_account' => 0],
            ['code' => '2010', 'name' => 'GST Payable', 'type' => 'liability', 'sub_type' => 'current_liability', 'is_group' => 0, 'is_bank_account' => 0],
            ['code' => '2020', 'name' => 'TDS Payable', 'type' => 'liability', 'sub_type' => 'current_liability', 'is_group' => 0, 'is_bank_account' => 0],
            ['code' => '2030', 'name' => 'PF Payable', 'type' => 'liability', 'sub_type' => 'current_liability', 'is_group' => 0, 'is_bank_account' => 0],
            ['code' => '2040', 'name' => 'Salary Payable', 'type' => 'liability', 'sub_type' => 'current_liability', 'is_group' => 0, 'is_bank_account' => 0],
            ['code' => '2100', 'name' => 'Long Term Loans', 'type' => 'liability', 'sub_type' => 'long_term_liability', 'is_group' => 0, 'is_bank_account' => 0],
            ['code' => '3000', 'name' => "Owner's Equity", 'type' => 'equity', 'sub_type' => 'equity', 'is_group' => 0, 'is_bank_account' => 0],
            ['code' => '3010', 'name' => 'Retained Earnings', 'type' => 'equity', 'sub_type' => 'equity', 'is_group' => 0, 'is_bank_account' => 0],
            ['code' => '4000', 'name' => 'Room Revenue', 'type' => 'revenue', 'sub_type' => 'revenue', 'is_group' => 0, 'is_bank_account' => 0],
            ['code' => '4010', 'name' => 'Restaurant Revenue', 'type' => 'revenue', 'sub_type' => 'revenue', 'is_group' => 0, 'is_bank_account' => 0],
            ['code' => '4020', 'name' => 'Banquet Revenue', 'type' => 'revenue', 'sub_type' => 'revenue', 'is_group' => 0, 'is_bank_account' => 0],
            ['code' => '4030', 'name' => 'Spa Revenue', 'type' => 'revenue', 'sub_type' => 'revenue', 'is_group' => 0, 'is_bank_account' => 0],
            ['code' => '4040', 'name' => 'Laundry Revenue', 'type' => 'revenue', 'sub_type' => 'revenue', 'is_group' => 0, 'is_bank_account' => 0],
            ['code' => '4050', 'name' => 'Other Revenue', 'type' => 'revenue', 'sub_type' => 'revenue', 'is_group' => 0, 'is_bank_account' => 0],
            ['code' => '5000', 'name' => 'Salary & Wages', 'type' => 'expense', 'sub_type' => 'operating_expense', 'is_group' => 0, 'is_bank_account' => 0],
            ['code' => '5010', 'name' => 'Food & Beverage Cost', 'type' => 'expense', 'sub_type' => 'cost_of_goods', 'is_group' => 0, 'is_bank_account' => 0],
            ['code' => '5020', 'name' => 'Housekeeping Supplies', 'type' => 'expense', 'sub_type' => 'operating_expense', 'is_group' => 0, 'is_bank_account' => 0],
            ['code' => '5030', 'name' => 'Utilities', 'type' => 'expense', 'sub_type' => 'operating_expense', 'is_group' => 0, 'is_bank_account' => 0],
            ['code' => '5040', 'name' => 'Maintenance & Repairs', 'type' => 'expense', 'sub_type' => 'operating_expense', 'is_group' => 0, 'is_bank_account' => 0],
            ['code' => '5050', 'name' => 'Marketing & Advertising', 'type' => 'expense', 'sub_type' => 'operating_expense', 'is_group' => 0, 'is_bank_account' => 0],
            ['code' => '5060', 'name' => 'Rent', 'type' => 'expense', 'sub_type' => 'operating_expense', 'is_group' => 0, 'is_bank_account' => 0],
            ['code' => '5070', 'name' => 'Insurance', 'type' => 'expense', 'sub_type' => 'operating_expense', 'is_group' => 0, 'is_bank_account' => 0],
            ['code' => '5080', 'name' => 'Depreciation', 'type' => 'expense', 'sub_type' => 'non_operating_expense', 'is_group' => 0, 'is_bank_account' => 0],
            ['code' => '5090', 'name' => 'Interest Expense', 'type' => 'expense', 'sub_type' => 'non_operating_expense', 'is_group' => 0, 'is_bank_account' => 0],
            ['code' => '5100', 'name' => 'Bank Charges', 'type' => 'expense', 'sub_type' => 'non_operating_expense', 'is_group' => 0, 'is_bank_account' => 0],
        ];

        foreach ($accounts as $account) {
            $account['slug'] = Helper::slug('chart_of_accounts', $account['name']);
            $account['status'] = 'active';
            $account['created_at'] = now();
            $account['updated_at'] = now();
            DB::table('chart_of_accounts')->insert($account);
        }
    }
}
