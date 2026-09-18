<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Helpers\Helper;

class FinanceDummyDataSeeder extends Seeder
{
    public function run(): void
    {
        $userId = \App\Models\User::first()?->id ?? 1;
        $guestIds = DB::table('guests')->pluck('id')->toArray();
        $vendorIds = DB::table('vendors')->pluck('id')->toArray();
        $supplierIds = DB::table('inventory_suppliers')->pluck('id')->toArray();
        $coaIds = DB::table('chart_of_accounts')->pluck('id', 'code')->toArray();

        // ── Journal Entries ──────────────────────────────────────────────
        $journalEntries = [
            [
                'entry_number' => 'JV-20260101-001',
                'entry_date' => '2026-01-05',
                'type' => 'general',
                'description' => 'Opening balance - Cash and Bank',
                'total_debit' => 500000.00,
                'total_credit' => 500000.00,
                'status' => 'posted',
                'created_by' => $userId,
            ],
            [
                'entry_number' => 'JV-20260110-002',
                'entry_date' => '2026-01-10',
                'type' => 'sales',
                'description' => 'Room revenue - January first week',
                'total_debit' => 350000.00,
                'total_credit' => 350000.00,
                'status' => 'posted',
                'created_by' => $userId,
            ],
            [
                'entry_number' => 'JV-20260115-003',
                'entry_date' => '2026-01-15',
                'type' => 'purchase',
                'description' => 'Inventory purchase - kitchen supplies',
                'total_debit' => 125000.00,
                'total_credit' => 125000.00,
                'status' => 'posted',
                'created_by' => $userId,
            ],
            [
                'entry_number' => 'JV-20260120-004',
                'entry_date' => '2026-01-20',
                'type' => 'payment',
                'description' => 'Salary payment - January staff',
                'total_debit' => 800000.00,
                'total_credit' => 800000.00,
                'status' => 'posted',
                'created_by' => $userId,
            ],
            [
                'entry_number' => 'JV-20260125-005',
                'entry_date' => '2026-01-25',
                'type' => 'receipt',
                'description' => 'Banquet event advance received',
                'total_debit' => 200000.00,
                'total_credit' => 200000.00,
                'status' => 'posted',
                'created_by' => $userId,
            ],
            [
                'entry_number' => 'JV-20260201-006',
                'entry_date' => '2026-02-01',
                'type' => 'general',
                'description' => 'Electricity bill - January',
                'total_debit' => 45000.00,
                'total_credit' => 45000.00,
                'status' => 'posted',
                'created_by' => $userId,
            ],
            [
                'entry_number' => 'JV-20260210-007',
                'entry_date' => '2026-02-10',
                'type' => 'sales',
                'description' => 'Restaurant revenue - February first week',
                'total_debit' => 180000.00,
                'total_credit' => 180000.00,
                'status' => 'posted',
                'created_by' => $userId,
            ],
            [
                'entry_number' => 'JV-20260215-008',
                'entry_date' => '2026-02-15',
                'type' => 'contra',
                'description' => 'Cash transfer to bank account',
                'total_debit' => 300000.00,
                'total_credit' => 300000.00,
                'status' => 'posted',
                'created_by' => $userId,
            ],
            [
                'entry_number' => 'JV-20260220-009',
                'entry_date' => '2026-02-20',
                'type' => 'adjusting',
                'description' => 'Depreciation - furniture and equipment',
                'total_debit' => 25000.00,
                'total_credit' => 25000.00,
                'status' => 'draft',
                'created_by' => $userId,
            ],
            [
                'entry_number' => 'JV-20260301-010',
                'entry_date' => '2026-03-01',
                'type' => 'general',
                'description' => 'Insurance premium - annual payment',
                'total_debit' => 180000.00,
                'total_credit' => 180000.00,
                'status' => 'posted',
                'created_by' => $userId,
            ],
        ];

        $journalLineData = [
            1 => [
                ['code' => '1000', 'debit' => 200000, 'credit' => 0, 'desc' => 'Opening cash balance'],
                ['code' => '1010', 'debit' => 300000, 'credit' => 0, 'desc' => 'Opening bank balance'],
                ['code' => '3000', 'debit' => 0, 'credit' => 500000, 'desc' => 'Owner equity contribution'],
            ],
            2 => [
                ['code' => '1020', 'debit' => 350000, 'credit' => 0, 'desc' => 'AR from room bookings'],
                ['code' => '4000', 'debit' => 0, 'credit' => 350000, 'desc' => 'Room revenue earned'],
            ],
            3 => [
                ['code' => '1030', 'debit' => 125000, 'credit' => 0, 'desc' => 'Kitchen inventory received'],
                ['code' => '2000', 'debit' => 0, 'credit' => 125000, 'desc' => 'AP to supplier'],
            ],
            4 => [
                ['code' => '5000', 'debit' => 800000, 'credit' => 0, 'desc' => 'January salary expense'],
                ['code' => '1010', 'debit' => 0, 'credit' => 800000, 'desc' => 'Bank payment for salaries'],
            ],
            5 => [
                ['code' => '1010', 'debit' => 200000, 'credit' => 0, 'desc' => 'Advance received in bank'],
                ['code' => '2040', 'debit' => 0, 'credit' => 200000, 'desc' => 'Advance from banquet client'],
            ],
            6 => [
                ['code' => '5030', 'debit' => 45000, 'credit' => 0, 'desc' => 'Electricity expense'],
                ['code' => '1010', 'debit' => 0, 'credit' => 45000, 'desc' => 'Auto-debit from bank'],
            ],
            7 => [
                ['code' => '1000', 'debit' => 180000, 'credit' => 0, 'desc' => 'Cash collected from restaurant'],
                ['code' => '4010', 'debit' => 0, 'credit' => 180000, 'desc' => 'Restaurant revenue'],
            ],
            8 => [
                ['code' => '1010', 'debit' => 300000, 'credit' => 0, 'desc' => 'Deposited to bank'],
                ['code' => '1000', 'debit' => 0, 'credit' => 300000, 'desc' => 'Cash withdrawn for deposit'],
            ],
            9 => [
                ['code' => '5080', 'debit' => 25000, 'credit' => 0, 'desc' => 'Depreciation expense'],
                ['code' => '1110', 'debit' => 0, 'credit' => 25000, 'desc' => 'Accumulated depreciation - furniture'],
            ],
            10 => [
                ['code' => '5070', 'debit' => 180000, 'credit' => 0, 'desc' => 'Annual insurance premium'],
                ['code' => '1010', 'debit' => 0, 'credit' => 180000, 'desc' => 'Bank payment for insurance'],
            ],
        ];

        $financialYearId = DB::table('financial_years')->insertGetId([
            'name' => 'FY 2025-26',
            'company_id' => 1,
            'start_date' => '2025-04-01',
            'end_date' => '2026-03-31',
            'is_current' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        foreach ($journalEntries as $idx => $je) {
            $je['financial_year_id'] = $financialYearId;
            $je['created_at'] = now();
            $je['updated_at'] = now();
            $jeId = DB::table('journal_entries')->insertGetId($je);

            foreach ($journalLineData[$idx + 1] as $line) {
                DB::table('journal_entry_lines')->insert([
                    'journal_entry_id' => $jeId,
                    'account_id' => $coaIds[$line['code']],
                    'debit' => $line['debit'],
                    'credit' => $line['credit'],
                    'description' => $line['desc'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // ── Accounts Payable ─────────────────────────────────────────────
        $apBills = [
            [
                'bill_number' => 'AP-20260115-001',
                'vendor_id' => $vendorIds[0] ?? null,
                'supplier_id' => $supplierIds[0] ?? null,
                'bill_date' => '2026-01-15',
                'due_date' => '2026-02-15',
                'subtotal' => 85000.00,
                'tax_amount' => 15300.00,
                'total_amount' => 100300.00,
                'paid_amount' => 100300.00,
                'balance' => 0,
                'notes' => 'Kitchen equipment purchase',
                'status' => 'paid',
                'created_by' => $userId,
            ],
            [
                'bill_number' => 'AP-20260120-002',
                'vendor_id' => $vendorIds[1] ?? null,
                'supplier_id' => $supplierIds[1] ?? null,
                'bill_date' => '2026-01-20',
                'due_date' => '2026-02-20',
                'subtotal' => 150000.00,
                'tax_amount' => 27000.00,
                'total_amount' => 177000.00,
                'paid_amount' => 50000.00,
                'balance' => 127000.00,
                'notes' => 'Linen and towel supply - bulk order',
                'status' => 'partial',
                'created_by' => $userId,
            ],
            [
                'bill_number' => 'AP-20260201-003',
                'vendor_id' => $vendorIds[2] ?? null,
                'supplier_id' => $supplierIds[2] ?? null,
                'bill_date' => '2026-02-01',
                'due_date' => '2026-03-01',
                'subtotal' => 220000.00,
                'tax_amount' => 39600.00,
                'total_amount' => 259600.00,
                'paid_amount' => 0,
                'balance' => 259600.00,
                'notes' => 'Furniture replacement - lobby area',
                'status' => 'approved',
                'created_by' => $userId,
            ],
            [
                'bill_number' => 'AP-20260210-004',
                'vendor_id' => $vendorIds[3] ?? null,
                'supplier_id' => $supplierIds[3] ?? null,
                'bill_date' => '2026-02-10',
                'due_date' => '2026-03-10',
                'subtotal' => 65000.00,
                'tax_amount' => 11700.00,
                'total_amount' => 76700.00,
                'paid_amount' => 0,
                'balance' => 76700.00,
                'notes' => 'Cleaning chemicals - monthly supply',
                'status' => 'approved',
                'created_by' => $userId,
            ],
            [
                'bill_number' => 'AP-20260225-005',
                'vendor_id' => $vendorIds[4] ?? null,
                'supplier_id' => $supplierIds[4] ?? null,
                'bill_date' => '2026-02-25',
                'due_date' => '2026-03-25',
                'subtotal' => 45000.00,
                'tax_amount' => 8100.00,
                'total_amount' => 53100.00,
                'paid_amount' => 53100.00,
                'balance' => 0,
                'notes' => 'IT support and software license',
                'status' => 'paid',
                'created_by' => $userId,
            ],
            [
                'bill_number' => 'AP-20260305-006',
                'vendor_id' => $vendorIds[5] ?? null,
                'supplier_id' => $supplierIds[5] ?? null,
                'bill_date' => '2026-03-05',
                'due_date' => '2026-04-05',
                'subtotal' => 92000.00,
                'tax_amount' => 16560.00,
                'total_amount' => 108560.00,
                'paid_amount' => 30000.00,
                'balance' => 78560.00,
                'notes' => 'Plumbing repair and bathroom fixtures',
                'status' => 'partial',
                'created_by' => $userId,
            ],
            [
                'bill_number' => 'AP-20260310-007',
                'vendor_id' => $vendorIds[6] ?? null,
                'supplier_id' => $supplierIds[6] ?? null,
                'bill_date' => '2026-03-10',
                'due_date' => '2026-04-10',
                'subtotal' => 18000.00,
                'tax_amount' => 3240.00,
                'total_amount' => 21240.00,
                'paid_amount' => 0,
                'balance' => 21240.00,
                'notes' => 'Stationery and printed materials',
                'status' => 'draft',
                'created_by' => $userId,
            ],
            [
                'bill_number' => 'AP-20260315-008',
                'vendor_id' => $vendorIds[7] ?? null,
                'supplier_id' => $supplierIds[7] ?? null,
                'bill_date' => '2026-03-15',
                'due_date' => '2026-04-15',
                'subtotal' => 350000.00,
                'tax_amount' => 63000.00,
                'total_amount' => 413000.00,
                'paid_amount' => 0,
                'balance' => 413000.00,
                'notes' => 'HVAC system annual maintenance contract',
                'status' => 'approved',
                'created_by' => $userId,
            ],
        ];

        foreach ($apBills as $bill) {
            $bill['financial_year_id'] = $financialYearId;
            $bill['created_at'] = now();
            $bill['updated_at'] = now();
            DB::table('accounts_payable')->insert($bill);
        }

        // ── Accounts Receivable ──────────────────────────────────────────
        $arInvoices = [
            [
                'invoice_number' => 'INV-20260105-001',
                'guest_id' => $guestIds[0] ?? null,
                'invoice_date' => '2026-01-05',
                'due_date' => '2026-01-15',
                'subtotal' => 25000.00,
                'tax_amount' => 4500.00,
                'total_amount' => 29500.00,
                'received_amount' => 29500.00,
                'balance' => 0,
                'notes' => 'Room booking - 3 nights (Deluxe Suite)',
                'status' => 'paid',
                'created_by' => $userId,
            ],
            [
                'invoice_number' => 'INV-20260112-002',
                'guest_id' => $guestIds[1] ?? null,
                'invoice_date' => '2026-01-12',
                'due_date' => '2026-01-22',
                'subtotal' => 42000.00,
                'tax_amount' => 7560.00,
                'total_amount' => 49560.00,
                'received_amount' => 49560.00,
                'balance' => 0,
                'notes' => 'Room + Restaurant charges - 5 nights',
                'status' => 'paid',
                'created_by' => $userId,
            ],
            [
                'invoice_number' => 'INV-20260120-003',
                'guest_id' => $guestIds[2] ?? null,
                'invoice_date' => '2026-01-20',
                'due_date' => '2026-02-20',
                'subtotal' => 180000.00,
                'tax_amount' => 32400.00,
                'total_amount' => 212400.00,
                'received_amount' => 100000.00,
                'balance' => 112400.00,
                'notes' => 'Banquet hall booking - corporate event',
                'status' => 'partial',
                'created_by' => $userId,
            ],
            [
                'invoice_number' => 'INV-20260201-004',
                'guest_id' => $guestIds[3] ?? null,
                'invoice_date' => '2026-02-01',
                'due_date' => '2026-02-11',
                'subtotal' => 15000.00,
                'tax_amount' => 2700.00,
                'total_amount' => 17700.00,
                'received_amount' => 17700.00,
                'balance' => 0,
                'notes' => 'Spa services - couple package',
                'status' => 'paid',
                'created_by' => $userId,
            ],
            [
                'invoice_number' => 'INV-20260210-005',
                'guest_id' => $guestIds[4] ?? null,
                'invoice_date' => '2026-02-10',
                'due_date' => '2026-02-20',
                'subtotal' => 55000.00,
                'tax_amount' => 9900.00,
                'total_amount' => 64900.00,
                'received_amount' => 0,
                'balance' => 64900.00,
                'notes' => 'Room booking - Presidential Suite - 7 nights',
                'status' => 'overdue',
                'created_by' => $userId,
            ],
            [
                'invoice_number' => 'INV-20260220-006',
                'guest_id' => $guestIds[5] ?? null,
                'invoice_date' => '2026-02-20',
                'due_date' => '2026-03-02',
                'subtotal' => 8500.00,
                'tax_amount' => 1530.00,
                'total_amount' => 10030.00,
                'received_amount' => 10030.00,
                'balance' => 0,
                'notes' => 'Laundry services',
                'status' => 'paid',
                'created_by' => $userId,
            ],
            [
                'invoice_number' => 'INV-20260301-007',
                'guest_id' => $guestIds[6] ?? null,
                'invoice_date' => '2026-03-01',
                'due_date' => '2026-03-11',
                'subtotal' => 32000.00,
                'tax_amount' => 5760.00,
                'total_amount' => 37760.00,
                'received_amount' => 20000.00,
                'balance' => 17760.00,
                'notes' => 'Room + Restaurant charges - weekend stay',
                'status' => 'partial',
                'created_by' => $userId,
            ],
            [
                'invoice_number' => 'INV-20260310-008',
                'guest_id' => $guestIds[7] ?? null,
                'invoice_date' => '2026-03-10',
                'due_date' => '2026-03-20',
                'subtotal' => 120000.00,
                'tax_amount' => 21600.00,
                'total_amount' => 141600.00,
                'received_amount' => 0,
                'balance' => 141600.00,
                'notes' => 'Wedding reception - hall + rooms + catering',
                'status' => 'sent',
                'created_by' => $userId,
            ],
        ];

        foreach ($arInvoices as $inv) {
            $inv['financial_year_id'] = $financialYearId;
            $inv['created_at'] = now();
            $inv['updated_at'] = now();
            DB::table('accounts_receivable')->insert($inv);
        }

        // ── GST Returns ──────────────────────────────────────────────────
        $months = [
            ['period' => '2026-01', 'taxable' => 530000, 'cgst' => 47700, 'sgst' => 47700, 'igst' => 0, 'status' => 'filed', 'filing' => '2026-02-10'],
            ['period' => '2026-02', 'taxable' => 480000, 'cgst' => 43200, 'sgst' => 43200, 'igst' => 5000, 'status' => 'filed', 'filing' => '2026-03-08'],
            ['period' => '2026-03', 'taxable' => 620000, 'cgst' => 55800, 'sgst' => 55800, 'igst' => 0, 'status' => 'draft', 'filing' => null],
        ];

        foreach ($months as $m) {
            $tax = $m['cgst'] + $m['sgst'] + $m['igst'];
            DB::table('gst_returns')->insert([
                'return_number' => 'GST-GSTR1-' . str_replace('-', '', $m['period']),
                'period' => $m['period'],
                'return_type' => 'gstr1',
                'filing_date' => $m['filing'],
                'total_taxable_value' => $m['taxable'],
                'total_cgst' => $m['cgst'],
                'total_sgst' => $m['sgst'],
                'total_igst' => $m['igst'],
                'total_cess' => 0,
                'total_tax' => $tax,
                'status' => $m['status'],
                'created_by' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $gstr3b = [
            ['period' => '2026-01', 'taxable' => 530000, 'cgst' => 47700, 'sgst' => 47700, 'igst' => 0, 'status' => 'filed', 'filing' => '2026-02-15'],
            ['period' => '2026-02', 'taxable' => 480000, 'cgst' => 43200, 'sgst' => 43200, 'igst' => 5000, 'status' => 'filed', 'filing' => '2026-03-12'],
            ['period' => '2026-03', 'taxable' => 620000, 'cgst' => 55800, 'sgst' => 55800, 'igst' => 0, 'status' => 'draft', 'filing' => null],
        ];

        foreach ($gstr3b as $m) {
            $tax = $m['cgst'] + $m['sgst'] + $m['igst'];
            DB::table('gst_returns')->insert([
                'return_number' => 'GST-GSTR3B-' . str_replace('-', '', $m['period']),
                'period' => $m['period'],
                'return_type' => 'gstr3b',
                'filing_date' => $m['filing'],
                'total_taxable_value' => $m['taxable'],
                'total_cgst' => $m['cgst'],
                'total_sgst' => $m['sgst'],
                'total_igst' => $m['igst'],
                'total_cess' => 0,
                'total_tax' => $tax,
                'status' => $m['status'],
                'created_by' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // ── Expenses ─────────────────────────────────────────────────────
        $expenses = [
            [
                'expense_number' => 'EXP-20260105-001',
                'account_id' => $coaIds['5030'],
                'vendor_id' => $vendorIds[0] ?? null,
                'expense_date' => '2026-01-05',
                'amount' => 42000.00,
                'tax_amount' => 7560.00,
                'total_amount' => 49560.00,
                'payment_method' => 'bank_transfer',
                'description' => 'Electricity bill - January 2026',
                'notes' => 'BESCOM monthly bill',
                'created_by' => $userId,
            ],
            [
                'expense_number' => 'EXP-20260108-002',
                'account_id' => $coaIds['5030'],
                'vendor_id' => $vendorIds[1] ?? null,
                'expense_date' => '2026-01-08',
                'amount' => 15000.00,
                'tax_amount' => 2700.00,
                'total_amount' => 17700.00,
                'payment_method' => 'online',
                'description' => 'Water supply bill - January',
                'notes' => 'BWSSB water charges',
                'created_by' => $userId,
            ],
            [
                'expense_number' => 'EXP-20260115-003',
                'account_id' => $coaIds['5040'],
                'vendor_id' => $vendorIds[2] ?? null,
                'expense_date' => '2026-01-15',
                'amount' => 35000.00,
                'tax_amount' => 6300.00,
                'total_amount' => 41300.00,
                'payment_method' => 'cash',
                'description' => 'AC repair - rooms 201-210',
                'notes' => 'Emergency maintenance call',
                'created_by' => $userId,
            ],
            [
                'expense_number' => 'EXP-20260120-004',
                'account_id' => $coaIds['5050'],
                'vendor_id' => $vendorIds[3] ?? null,
                'expense_date' => '2026-01-20',
                'amount' => 75000.00,
                'tax_amount' => 13500.00,
                'total_amount' => 88500.00,
                'payment_method' => 'online',
                'description' => 'Google Ads - January campaign',
                'notes' => 'Digital marketing - peak season promo',
                'created_by' => $userId,
            ],
            [
                'expense_number' => 'EXP-20260125-005',
                'account_id' => $coaIds['5020'],
                'vendor_id' => $vendorIds[4] ?? null,
                'expense_date' => '2026-01-25',
                'amount' => 28000.00,
                'tax_amount' => 5040.00,
                'total_amount' => 33040.00,
                'payment_method' => 'bank_transfer',
                'description' => 'Housekeeping supplies - bed sheets, towels',
                'notes' => 'Monthly restocking',
                'created_by' => $userId,
            ],
            [
                'expense_number' => 'EXP-20260201-006',
                'account_id' => $coaIds['5060'],
                'vendor_id' => $vendorIds[5] ?? null,
                'expense_date' => '2026-02-01',
                'amount' => 500000.00,
                'tax_amount' => 0.00,
                'total_amount' => 500000.00,
                'payment_method' => 'cheque',
                'description' => 'Monthly rent - February 2026',
                'notes' => 'Property lease payment',
                'created_by' => $userId,
            ],
            [
                'expense_number' => 'EXP-20260205-007',
                'account_id' => $coaIds['5030'],
                'vendor_id' => $vendorIds[0] ?? null,
                'expense_date' => '2026-02-05',
                'amount' => 48000.00,
                'tax_amount' => 8640.00,
                'total_amount' => 56640.00,
                'payment_method' => 'bank_transfer',
                'description' => 'Electricity bill - February 2026',
                'notes' => 'Higher usage due to peak season',
                'created_by' => $userId,
            ],
            [
                'expense_number' => 'EXP-20260210-008',
                'account_id' => $coaIds['5100'],
                'vendor_id' => null,
                'expense_date' => '2026-02-10',
                'amount' => 2500.00,
                'tax_amount' => 0.00,
                'total_amount' => 2500.00,
                'payment_method' => 'bank_transfer',
                'description' => 'Bank processing charges - February',
                'notes' => 'NEFT/RTGS charges',
                'created_by' => $userId,
            ],
            [
                'expense_number' => 'EXP-20260215-009',
                'account_id' => $coaIds['5010'],
                'vendor_id' => $vendorIds[6] ?? null,
                'expense_date' => '2026-02-15',
                'amount' => 120000.00,
                'tax_amount' => 21600.00,
                'total_amount' => 141600.00,
                'payment_method' => 'online',
                'description' => 'Bulk grocery and vegetable purchase',
                'notes' => 'Weekly kitchen supply - restaurant & room service',
                'created_by' => $userId,
            ],
            [
                'expense_number' => 'EXP-20260220-010',
                'account_id' => $coaIds['5050'],
                'vendor_id' => $vendorIds[7] ?? null,
                'expense_date' => '2026-02-20',
                'amount' => 35000.00,
                'tax_amount' => 6300.00,
                'total_amount' => 41300.00,
                'payment_method' => 'card',
                'description' => 'Billboard advertising - highway near hotel',
                'notes' => 'Monthly rental for 2 hoardings',
                'created_by' => $userId,
            ],
            [
                'expense_number' => 'EXP-20260301-011',
                'account_id' => $coaIds['5030'],
                'vendor_id' => $vendorIds[0] ?? null,
                'expense_date' => '2026-03-01',
                'amount' => 52000.00,
                'tax_amount' => 9360.00,
                'total_amount' => 61360.00,
                'payment_method' => 'bank_transfer',
                'description' => 'Electricity bill - March 2026',
                'notes' => '',
                'created_by' => $userId,
            ],
            [
                'expense_number' => 'EXP-20260305-012',
                'account_id' => $coaIds['5040'],
                'vendor_id' => $vendorIds[8] ?? null,
                'expense_date' => '2026-03-05',
                'amount' => 18000.00,
                'tax_amount' => 3240.00,
                'total_amount' => 21240.00,
                'payment_method' => 'cash',
                'description' => 'Pool area tile replacement',
                'notes' => 'Urgent repair - cracked tiles',
                'created_by' => $userId,
            ],
            [
                'expense_number' => 'EXP-20260310-013',
                'account_id' => $coaIds['5000'],
                'vendor_id' => null,
                'expense_date' => '2026-03-10',
                'amount' => 850000.00,
                'tax_amount' => 0.00,
                'total_amount' => 850000.00,
                'payment_method' => 'bank_transfer',
                'description' => 'Staff salaries - March 2026',
                'notes' => 'Monthly payroll - all departments',
                'created_by' => $userId,
            ],
            [
                'expense_number' => 'EXP-20260312-014',
                'account_id' => $coaIds['5010'],
                'vendor_id' => $vendorIds[9] ?? null,
                'expense_date' => '2026-03-12',
                'amount' => 95000.00,
                'tax_amount' => 17100.00,
                'total_amount' => 112100.00,
                'payment_method' => 'online',
                'description' => 'Meat and seafood supply - restaurant',
                'notes' => 'Weekly order for restaurant specials',
                'created_by' => $userId,
            ],
            [
                'expense_number' => 'EXP-20260315-015',
                'account_id' => $coaIds['5070'],
                'vendor_id' => $vendorIds[10] ?? null,
                'expense_date' => '2026-03-15',
                'amount' => 180000.00,
                'tax_amount' => 32400.00,
                'total_amount' => 212400.00,
                'payment_method' => 'cheque',
                'description' => 'Annual property insurance premium',
                'notes' => 'Comprehensive insurance - building + liability',
                'created_by' => $userId,
            ],
        ];

        foreach ($expenses as $exp) {
            $exp['created_at'] = now();
            $exp['updated_at'] = now();
            DB::table('expenses')->insert($exp);
        }
    }
}
