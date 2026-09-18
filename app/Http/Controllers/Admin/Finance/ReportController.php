<?php

namespace App\Http\Controllers\Admin\Finance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\ChartOfAccount;
use App\Models\FinancialYear;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function trialBalance(Request $request)
    {
        $from = $request->get('from_date', now()->startOfYear()->format('Y-m-d'));
        $to = $request->get('to_date', now()->format('Y-m-d'));

        $trialBalance = ChartOfAccount::active()->where('is_group', false)->orderBy('code')->get()->map(function ($account) use ($from, $to) {
            $lines = JournalEntryLine::where('account_id', $account->id)
                ->whereHas('journalEntry', function ($q) use ($from, $to) {
                    $q->where('status', 'posted')->whereBetween('entry_date', [$from, $to]);
                })
                ->selectRaw('SUM(debit) as total_debit, SUM(credit) as total_credit')
                ->first();

            $account->total_debit = $lines->total_debit ?? 0;
            $account->total_credit = $lines->total_credit ?? 0;
            $account->balance = $account->total_debit - $account->total_credit;
            return $account;
        })->filter(function ($account) {
            return $account->total_debit > 0 || $account->total_credit > 0;
        });

        $totalDebit = $trialBalance->sum('total_debit');
        $totalCredit = $trialBalance->sum('total_credit');

        return view('admin.finance.reports.trial-balance', compact('trialBalance', 'totalDebit', 'totalCredit', 'from', 'to'));
    }

    public function profitLoss(Request $request)
    {
        $from = $request->get('from_date', now()->startOfYear()->format('Y-m-d'));
        $to = $request->get('to_date', now()->format('Y-m-d'));

        $revenues = $this->getAccountBalances('revenue', $from, $to);
        $cogs = $this->getAccountBalances('cost_of_goods', $from, $to);
        $operatingExpenses = $this->getAccountBalances('operating_expense', $from, $to);
        $nonOperatingExpenses = $this->getAccountBalances('non_operating_expense', $from, $to);

        $totalRevenue = $revenues->sum('balance');
        $totalCogs = $cogs->sum('balance');
        $grossProfit = $totalRevenue - $totalCogs;
        $totalOperatingExpenses = $operatingExpenses->sum('balance');
        $operatingIncome = $grossProfit - $totalOperatingExpenses;
        $totalNonOperatingExpenses = $nonOperatingExpenses->sum('balance');
        $netIncome = $operatingIncome - $totalNonOperatingExpenses;

        return view('admin.finance.reports.profit-loss', compact(
            'revenues', 'cogs', 'operatingExpenses', 'nonOperatingExpenses',
            'totalRevenue', 'totalCogs', 'grossProfit', 'totalOperatingExpenses',
            'operatingIncome', 'totalNonOperatingExpenses', 'netIncome', 'from', 'to'
        ));
    }

    public function balanceSheet(Request $request)
    {
        $from = $request->get('from_date', now()->startOfYear()->format('Y-m-d'));
        $to = $request->get('to_date', now()->format('Y-m-d'));

        $currentAssets = $this->getAccountBalancesBySubType('current_asset', $from, $to);
        $fixedAssets = $this->getAccountBalancesBySubType('fixed_asset', $from, $to);
        $currentLiabilities = $this->getAccountBalancesBySubType('current_liability', $from, $to);
        $longTermLiabilities = $this->getAccountBalancesBySubType('long_term_liability', $from, $to);
        $equity = $this->getAccountBalancesBySubType('equity', $from, $to);

        $totalCurrentAssets = $currentAssets->sum('balance');
        $totalFixedAssets = $fixedAssets->sum('balance');
        $totalAssets = $totalCurrentAssets + $totalFixedAssets;

        $totalCurrentLiabilities = $currentLiabilities->sum('balance');
        $totalLongTermLiabilities = $longTermLiabilities->sum('balance');
        $totalLiabilities = $totalCurrentLiabilities + $totalLongTermLiabilities;
        $totalEquity = $equity->sum('balance');

        // Calculate Net Income for the period to balance the Balance Sheet
        $revenues = $this->getAccountBalances('revenue', $from, $to);
        $cogs = $this->getAccountBalances('cost_of_goods', $from, $to);
        $operatingExpenses = $this->getAccountBalances('operating_expense', $from, $to);
        $nonOperatingExpenses = $this->getAccountBalances('non_operating_expense', $from, $to);

        $totalRevenue = $revenues->sum('balance');
        $totalCogs = $cogs->sum('balance');
        $totalOperatingExpenses = $operatingExpenses->sum('balance');
        $totalNonOperatingExpenses = $nonOperatingExpenses->sum('balance');

        $netIncome = $totalRevenue - $totalCogs - $totalOperatingExpenses - $totalNonOperatingExpenses;
        $totalEquity += $netIncome;

        $totalLiabilitiesEquity = $totalLiabilities + $totalEquity;

        return view('admin.finance.reports.balance-sheet', compact(
            'currentAssets', 'fixedAssets', 'currentLiabilities', 'longTermLiabilities', 'equity',
            'totalCurrentAssets', 'totalFixedAssets', 'totalAssets',
            'totalCurrentLiabilities', 'totalLongTermLiabilities', 'totalLiabilities', 'totalEquity',
            'netIncome', 'totalLiabilitiesEquity',
            'from', 'to'
        ));
    }

    private function getAccountBalances($subType, $from, $to)
    {
        return ChartOfAccount::active()->where('is_group', false)
            ->where('sub_type', $subType)->orderBy('code')->get()
            ->map(function ($account) use ($from, $to) {
                $lines = JournalEntryLine::where('account_id', $account->id)
                    ->whereHas('journalEntry', function ($q) use ($from, $to) {
                        $q->where('status', 'posted')->whereBetween('entry_date', [$from, $to]);
                    })
                    ->selectRaw('SUM(debit) as total_debit, SUM(credit) as total_credit')
                    ->first();
                $account->total_debit = $lines->total_debit ?? 0;
                $account->total_credit = $lines->total_credit ?? 0;
                
                // Normal balance logic
                if (in_array($account->type, ['liability', 'equity', 'revenue'])) {
                    $account->balance = $account->total_credit - $account->total_debit;
                } else {
                    $account->balance = $account->total_debit - $account->total_credit;
                }
                return $account;
            })
            ->filter(function ($account) {
                return $account->total_debit > 0 || $account->total_credit > 0;
            });
    }

    private function getAccountBalancesBySubType($subType, $from, $to)
    {
        return $this->getAccountBalances($subType, $from, $to);
    }
}
