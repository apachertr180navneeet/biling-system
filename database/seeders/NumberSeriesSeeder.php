<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\NumberSeries;

class NumberSeriesSeeder extends Seeder
{
    public function run(): void
    {
        $series = [
            ['module' => 'invoice', 'prefix' => 'INV-', 'suffix' => '', 'next_number' => 1, 'padding' => 6, 'description' => 'Invoice Number Series'],
            ['module' => 'quotation', 'prefix' => 'QUO-', 'suffix' => '', 'next_number' => 1, 'padding' => 6, 'description' => 'Quotation Number Series'],
            ['module' => 'purchase_order', 'prefix' => 'PO-', 'suffix' => '', 'next_number' => 1, 'padding' => 6, 'description' => 'Purchase Order Number Series'],
            ['module' => 'receipt', 'prefix' => 'RCP-', 'suffix' => '', 'next_number' => 1, 'padding' => 6, 'description' => 'Receipt Number Series'],
            ['module' => 'payment', 'prefix' => 'PAY-', 'suffix' => '', 'next_number' => 1, 'padding' => 6, 'description' => 'Payment Number Series'],
            ['module' => 'expense', 'prefix' => 'EXP-', 'suffix' => '', 'next_number' => 1, 'padding' => 6, 'description' => 'Expense Number Series'],
            ['module' => 'journal', 'prefix' => 'JNL-', 'suffix' => '', 'next_number' => 1, 'padding' => 6, 'description' => 'Journal Entry Number Series'],
            ['module' => 'journal_voucher', 'prefix' => 'JV-', 'suffix' => '', 'next_number' => 1, 'padding' => 6, 'description' => 'Journal Voucher Number Series'],
            ['module' => 'reservations', 'prefix' => 'RES-', 'suffix' => '', 'next_number' => 1, 'padding' => 6, 'description' => 'Reservation Number Series'],
        ];

        foreach ($series as $item) {
            NumberSeries::updateOrCreate(
                ['module' => $item['module']],
                $item
            );
        }
    }
}
