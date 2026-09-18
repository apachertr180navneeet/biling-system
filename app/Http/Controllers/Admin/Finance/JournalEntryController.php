<?php

namespace App\Http\Controllers\Admin\Finance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\ChartOfAccount;
use App\Models\FinancialYear;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Exception;

class JournalEntryController extends Controller
{
    public function index()
    {
        return view('admin.finance.journal-entries.index');
    }

    public function data(Request $request)
    {
        $query = JournalEntry::with('creator')->select('journal_entries.*');
        return DataTables::of($query)
            ->filterColumn('entry_number', function ($query, $value) {
                $query->where('entry_number', 'like', "%{$value}%");
            })
            ->addColumn('status_url', fn($entry) => route('admin.finance.journal-entries.status', $entry))
            ->addColumn('edit_url', fn($entry) => route('admin.finance.journal-entries.edit', $entry))
            ->addColumn('delete_url', fn($entry) => route('admin.finance.journal-entries.destroy', $entry))
            ->addColumn('view_url', fn($entry) => route('admin.finance.journal-entries.show', $entry))
            ->rawColumns([])
            ->make(true);
    }

    public function show(JournalEntry $journal_entry)
    {
        $journalEntry = $journal_entry;
        $journalEntry->load('lines.account');
        return view('admin.finance.journal-entries.show', ['entry' => $journalEntry]);
    }

    public function create()
    {
        $entry = null;
        $accounts = ChartOfAccount::active()->where('is_group', false)->orderBy('code')->get();
        $financialYears = FinancialYear::orderByDesc('is_current')->get();
        return view('admin.finance.journal-entries.form', compact('entry', 'accounts', 'financialYears'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'entry_date' => 'required|date',
                'type' => 'required|in:general,sales,purchase,receipt,payment,contra,adjusting',
                'description' => 'required|string',
                'lines' => 'required|array|min:2',
                'lines.*.account_id' => 'required|exists:chart_of_accounts,id',
                'lines.*.debit' => 'required|numeric|min:0',
                'lines.*.credit' => 'required|numeric|min:0',
                'lines.*.description' => 'nullable|string',
                'financial_year_id' => 'nullable|exists:financial_years,id',
            ]);

            $totalDebit = collect($request->lines)->sum('debit');
            $totalCredit = collect($request->lines)->sum('credit');

            if (round($totalDebit, 2) !== round($totalCredit, 2)) {
                return back()->withInput()->with('error', 'Total debit must equal total credit!');
            }

            DB::beginTransaction();

            $entry = JournalEntry::create([
                'hotel_id' => auth()->user()->branch_id,
                'entry_number' => 'JV-' . now()->format('YmdHis'),
                'entry_date' => $request->entry_date,
                'type' => $request->type,
                'description' => $request->description,
                'total_debit' => $totalDebit,
                'total_credit' => $totalCredit,
                'status' => 'draft',
                'financial_year_id' => $request->financial_year_id,
                'created_by' => auth()->id(),
            ]);

            foreach ($request->lines as $line) {
                JournalEntryLine::create([
                    'journal_entry_id' => $entry->id,
                    'account_id' => $line['account_id'],
                    'debit' => $line['debit'],
                    'credit' => $line['credit'],
                    'description' => $line['description'] ?? null,
                ]);
            }

            DB::commit();
            return redirect()->route('admin.finance.journal-entries.index')->with('success', 'Journal entry created successfully!');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit(JournalEntry $journal_entry)
    {
        $journalEntry = $journal_entry;
        $journalEntry->load('lines');
        $accounts = ChartOfAccount::active()->where('is_group', false)->orderBy('code')->get();
        $financialYears = FinancialYear::orderByDesc('is_current')->get();
        return view('admin.finance.journal-entries.form', ['entry' => $journalEntry, 'accounts' => $accounts, 'financialYears' => $financialYears]);
    }

    public function update(Request $request, JournalEntry $journal_entry)
    {
        try {
            $journalEntry = $journal_entry;
            $request->validate([
                'entry_date' => 'required|date',
                'type' => 'required|in:general,sales,purchase,receipt,payment,contra,adjusting',
                'description' => 'required|string',
                'lines' => 'required|array|min:2',
                'lines.*.account_id' => 'required|exists:chart_of_accounts,id',
                'lines.*.debit' => 'required|numeric|min:0',
                'lines.*.credit' => 'required|numeric|min:0',
                'lines.*.description' => 'nullable|string',
                'financial_year_id' => 'nullable|exists:financial_years,id',
            ]);

            $totalDebit = collect($request->lines)->sum('debit');
            $totalCredit = collect($request->lines)->sum('credit');

            if (round($totalDebit, 2) !== round($totalCredit, 2)) {
                return back()->withInput()->with('error', 'Total debit must equal total credit!');
            }

            DB::beginTransaction();

            $journalEntry->update([
                'entry_date' => $request->entry_date,
                'type' => $request->type,
                'description' => $request->description,
                'total_debit' => $totalDebit,
                'total_credit' => $totalCredit,
                'financial_year_id' => $request->financial_year_id,
            ]);

            $journalEntry->lines()->delete();
            foreach ($request->lines as $line) {
                JournalEntryLine::create([
                    'journal_entry_id' => $journalEntry->id,
                    'account_id' => $line['account_id'],
                    'debit' => $line['debit'],
                    'credit' => $line['credit'],
                    'description' => $line['description'] ?? null,
                ]);
            }

            DB::commit();
            return redirect()->route('admin.finance.journal-entries.index')->with('success', 'Journal entry updated successfully!');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(Request $request, JournalEntry $journal_entry)
    {
        try {
            $journalEntry = $journal_entry;
            if ($journalEntry->status === 'posted') {
                return back()->with('error', 'Cannot delete a posted entry!');
            }
            DB::beginTransaction();
            $journalEntry->lines()->delete();
            $journalEntry->delete();
            DB::commit();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Journal entry deleted!']);
            }
            return redirect()->route('admin.finance.journal-entries.index')->with('success', 'Journal entry deleted!');
        } catch (Exception $e) {
            DB::rollBack();
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    public function post(Request $request, JournalEntry $journal_entry)
    {
        try {
            $journalEntry = $journal_entry;
            if ($journalEntry->status !== 'draft') {
                return back()->with('error', 'Only draft entries can be posted!');
            }
            $journalEntry->update(['status' => 'posted']);
            return redirect()->back()->with('success', 'Journal entry posted successfully!');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function void_(Request $request, JournalEntry $journal_entry)
    {
        try {
            $journalEntry = $journal_entry;
            if ($journalEntry->status !== 'posted') {
                return back()->with('error', 'Only posted entries can be voided!');
            }
            $journalEntry->update(['status' => 'void']);
            return redirect()->back()->with('success', 'Journal entry voided successfully!');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function status(Request $request, JournalEntry $journal_entry)
    {
        try {
            $journalEntry = $journal_entry;
            $journalEntry->status = $journalEntry->status === 'draft' ? 'posted' : 'draft';
            $journalEntry->save();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'status' => $journalEntry->status, 'message' => 'Status updated!']);
            }
            return redirect()->back()->with('success', 'Status updated!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }
}
