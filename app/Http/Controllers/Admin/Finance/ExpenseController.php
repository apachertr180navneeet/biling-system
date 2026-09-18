<?php

namespace App\Http\Controllers\Admin\Finance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\ChartOfAccount;
use App\Models\Vendor;
use Yajra\DataTables\Facades\DataTables;
use Exception;

class ExpenseController extends Controller
{
    public function index()
    {
        return view('admin.finance.expenses.index');
    }

    public function data(Request $request)
    {
        $query = Expense::with('account', 'vendor')->select('expenses.*');
        return DataTables::of($query)
            ->filterColumn('expense_number', function ($query, $value) {
                $query->where('expense_number', 'like', "%{$value}%");
            })
            ->addColumn('status_url', fn($exp) => route('admin.finance.expenses.status', $exp))
            ->addColumn('edit_url', fn($exp) => route('admin.finance.expenses.edit', $exp))
            ->addColumn('delete_url', fn($exp) => route('admin.finance.expenses.destroy', $exp))
            ->addColumn('show_url', fn($exp) => route('admin.finance.expenses.edit', $exp))
            ->rawColumns([])
            ->make(true);
    }

    public function create()
    {
        $expense = null;
        $accounts = ChartOfAccount::active()->where('type', 'expense')->where('is_group', false)->orderBy('code')->get();
        $vendors = Vendor::orderBy('company_name')->get();
        return view('admin.finance.expenses.form', compact('expense', 'accounts', 'vendors'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'expense_date' => 'required|date',
                'account_id' => 'required|exists:chart_of_accounts,id',
                'amount' => 'required|numeric|min:0.01',
                'tax_amount' => 'nullable|numeric|min:0',
                'payment_method' => 'required|in:cash,bank_transfer,cheque,online,card,credit',
                'description' => 'required|string',
                'vendor_id' => 'nullable|exists:vendors,id',
                'notes' => 'nullable|string',
                'status' => 'required|in:draft,pending,approved,rejected,paid',
            ]);

            $expense = Expense::create([
                'hotel_id' => auth()->user()->branch_id,
                'expense_number' => 'EXP-' . now()->format('YmdHis'),
                'account_id' => $request->account_id,
                'vendor_id' => $request->vendor_id,
                'expense_date' => $request->expense_date,
                'amount' => $request->amount,
                'tax_amount' => $request->tax_amount ?? 0,
                'total_amount' => $request->amount + ($request->tax_amount ?? 0),
                'payment_method' => $request->payment_method,
                'description' => $request->description,
                'notes' => $request->notes,
                'status' => $request->status,
                'created_by' => auth()->id(),
            ]);

            // Auto-request approval for expenses above threshold
            if ($expense->total_amount > 0) {
                \App\Services\ApprovalService::requestApproval(
                    'expense',
                    $expense->id,
                    $expense->total_amount,
                    auth()->id(),
                    'Expense ' . $expense->expense_number . ' - Amount: ' . number_format($expense->total_amount, 2)
                );
            }

            return redirect()->route('admin.finance.expenses.index')->with('success', 'Expense created successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit(Expense $expense)
    {
        $accounts = ChartOfAccount::active()->where('type', 'expense')->where('is_group', false)->orderBy('code')->get();
        $vendors = Vendor::orderBy('company_name')->get();
        return view('admin.finance.expenses.form', compact('expense', 'accounts', 'vendors'));
    }

    public function update(Request $request, Expense $expense)
    {
        try {
            $request->validate([
                'expense_date' => 'required|date',
                'account_id' => 'required|exists:chart_of_accounts,id',
                'amount' => 'required|numeric|min:0.01',
                'tax_amount' => 'nullable|numeric|min:0',
                'payment_method' => 'required|in:cash,bank_transfer,cheque,online,card,credit',
                'description' => 'required|string',
                'vendor_id' => 'nullable|exists:vendors,id',
                'notes' => 'nullable|string',
                'status' => 'required|in:draft,pending,approved,rejected,paid',
            ]);

            $expense->update([
                'account_id' => $request->account_id,
                'vendor_id' => $request->vendor_id,
                'expense_date' => $request->expense_date,
                'amount' => $request->amount,
                'tax_amount' => $request->tax_amount ?? 0,
                'total_amount' => $request->amount + ($request->tax_amount ?? 0),
                'payment_method' => $request->payment_method,
                'description' => $request->description,
                'notes' => $request->notes,
                'status' => $request->status,
            ]);

            return redirect()->route('admin.finance.expenses.index')->with('success', 'Expense updated successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(Request $request, Expense $expense)
    {
        try {
            $expense->delete();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Expense deleted!']);
            }
            return redirect()->route('admin.finance.expenses.index')->with('success', 'Expense deleted!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    public function status(Request $request, Expense $expense)
    {
        try {
            $statuses = [
                'draft' => 'pending',
                'pending' => 'approved',
                'approved' => 'paid',
                'paid' => 'rejected',
                'rejected' => 'draft',
            ];
            $expense->status = $statuses[$expense->status] ?? 'draft';
            $expense->save();
            
            if ($request->ajax()) {
                return response()->json(['success' => true, 'status' => $expense->status, 'message' => 'Status updated!']);
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
