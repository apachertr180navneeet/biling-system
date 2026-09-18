<?php

namespace App\Http\Controllers\Admin\Finance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AccountsPayable;
use App\Models\ApPayment;
use App\Models\Vendor;
use App\Models\InventorySupplier;
use App\Models\ChartOfAccount;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Exception;

class AccountsPayableController extends Controller
{
    public function index()
    {
        return view('admin.finance.accounts-payable.index');
    }

    public function data(Request $request)
    {
        $query = AccountsPayable::with('vendor', 'supplier')->select('accounts_payable.*');
        return DataTables::of($query)
            ->filterColumn('bill_number', function ($query, $value) {
                $query->where('bill_number', 'like', "%{$value}%");
            })
            ->addColumn('vendor_supplier', fn($bill) => $bill->vendor->name ?? $bill->supplier->name ?? '-')
            ->addColumn('status_url', fn($bill) => route('admin.finance.accounts-payable.status', $bill))
            ->addColumn('edit_url', fn($bill) => route('admin.finance.accounts-payable.edit', $bill))
            ->addColumn('delete_url', fn($bill) => route('admin.finance.accounts-payable.destroy', $bill))
            ->addColumn('show_url', fn($bill) => route('admin.finance.accounts-payable.show', $bill))
            ->rawColumns([])
            ->make(true);
    }

    public function show(AccountsPayable $accounts_payable)
    {
        $account = $accounts_payable;
        $account->load('payments', 'vendor', 'supplier');
        return view('admin.finance.accounts-payable.show', ['account' => $account]);
    }

    public function create()
    {
        $account = null;
        $vendors = Vendor::orderBy('company_name')->get();
        $suppliers = InventorySupplier::orderBy('name')->get();
        return view('admin.finance.accounts-payable.form', compact('account', 'vendors', 'suppliers'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'bill_date' => 'required|date',
                'due_date' => 'required|date|after_or_equal:bill_date',
                'vendor_id' => 'nullable|exists:vendors,id',
                'supplier_id' => 'nullable|exists:inventory_suppliers,id',
                'subtotal' => 'required|numeric|min:0',
                'tax_amount' => 'nullable|numeric|min:0',
                'notes' => 'nullable|string',
            ]);

            $total = $request->subtotal + ($request->tax_amount ?? 0);

            AccountsPayable::create([
                'hotel_id' => auth()->user()->branch_id,
                'bill_number' => 'AP-' . now()->format('YmdHis'),
                'vendor_id' => $request->vendor_id,
                'supplier_id' => $request->supplier_id,
                'bill_date' => $request->bill_date,
                'due_date' => $request->due_date,
                'subtotal' => $request->subtotal,
                'tax_amount' => $request->tax_amount ?? 0,
                'total_amount' => $total,
                'paid_amount' => 0,
                'balance' => $total,
                'notes' => $request->notes,
                'status' => 'draft',
                'created_by' => auth()->id(),
            ]);

            return redirect()->route('admin.finance.accounts-payable.index')->with('success', 'Bill created successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit(AccountsPayable $accounts_payable)
    {
        $account = $accounts_payable;
        $vendors = Vendor::orderBy('company_name')->get();
        $suppliers = InventorySupplier::orderBy('name')->get();
        return view('admin.finance.accounts-payable.form', compact('account', 'vendors', 'suppliers'));
    }

    public function update(Request $request, AccountsPayable $accounts_payable)
    {
        try {
            $account = $accounts_payable;
            $request->validate([
                'bill_date' => 'required|date',
                'due_date' => 'required|date|after_or_equal:bill_date',
                'vendor_id' => 'nullable|exists:vendors,id',
                'supplier_id' => 'nullable|exists:inventory_suppliers,id',
                'subtotal' => 'required|numeric|min:0',
                'tax_amount' => 'nullable|numeric|min:0',
                'notes' => 'nullable|string',
            ]);

            $total = $request->subtotal + ($request->tax_amount ?? 0);
            $account->update([
                'vendor_id' => $request->vendor_id,
                'supplier_id' => $request->supplier_id,
                'bill_date' => $request->bill_date,
                'due_date' => $request->due_date,
                'subtotal' => $request->subtotal,
                'tax_amount' => $request->tax_amount ?? 0,
                'total_amount' => $total,
                'balance' => $total - $account->paid_amount,
                'notes' => $request->notes,
            ]);

            return redirect()->route('admin.finance.accounts-payable.index')->with('success', 'Bill updated successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(Request $request, AccountsPayable $accounts_payable)
    {
        try {
            $account = $accounts_payable;
            $account->delete();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Bill deleted!']);
            }
            return redirect()->route('admin.finance.accounts-payable.index')->with('success', 'Bill deleted!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    public function status(Request $request, AccountsPayable $accounts_payable)
    {
        try {
            $account = $accounts_payable;
            $statuses = ['draft' => 'approved', 'approved' => 'approved'];
            $account->status = $statuses[$account->status] ?? $account->status;
            $account->save();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'status' => $account->status, 'message' => 'Status updated!']);
            }
            return redirect()->back()->with('success', 'Status updated!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    public function storePayment(Request $request, AccountsPayable $accounts_payable)
    {
        try {
            $account = $accounts_payable;
            $request->validate([
                'payment_date' => 'required|date',
                'amount' => 'required|numeric|min:0.01|max:' . $account->balance,
                'payment_method' => 'required|in:cash,bank_transfer,cheque,online,card',
                'reference_number' => 'nullable|string',
                'notes' => 'nullable|string',
            ]);

            DB::beginTransaction();

            $payment = ApPayment::create([
                'hotel_id' => $account->hotel_id,
                'payment_number' => 'APP-' . now()->format('YmdHis'),
                'accounts_payable_id' => $account->id,
                'payment_date' => $request->payment_date,
                'amount' => $request->amount,
                'payment_method' => $request->payment_method,
                'reference_number' => $request->reference_number,
                'notes' => $request->notes,
                'created_by' => auth()->id(),
            ]);

            $account->paid_amount += $request->amount;
            $account->balance = $account->total_amount - $account->paid_amount;
            $account->status = $account->balance <= 0 ? 'paid' : 'partial';
            $account->save();

            DB::commit();
            return redirect()->route('admin.finance.accounts-payable.show', $account)->with('success', 'Payment recorded successfully!');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', $e->getMessage());
        }
    }
}
