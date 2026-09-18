<?php

namespace App\Http\Controllers\Admin\Finance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AccountsReceivable;
use App\Models\ArReceipt;
use App\Models\Guest;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Exception;

class AccountsReceivableController extends Controller
{
    public function index()
    {
        return view('admin.finance.accounts-receivable.index');
    }

    public function data(Request $request)
    {
        $query = AccountsReceivable::with('guest')->select('accounts_receivable.*');
        return DataTables::of($query)
            ->filterColumn('invoice_number', function ($query, $value) {
                $query->where('invoice_number', 'like', "%{$value}%");
            })
            ->addColumn('guest_name', fn($inv) => $inv->guest->name ?? '-')
            ->addColumn('status_url', fn($inv) => route('admin.finance.accounts-receivable.status', $inv))
            ->addColumn('edit_url', fn($inv) => route('admin.finance.accounts-receivable.edit', $inv))
            ->addColumn('delete_url', fn($inv) => route('admin.finance.accounts-receivable.destroy', $inv))
            ->addColumn('show_url', fn($inv) => route('admin.finance.accounts-receivable.show', $inv))
            ->rawColumns([])
            ->make(true);
    }

    public function show(AccountsReceivable $accounts_receivable)
    {
        $account = $accounts_receivable;
        $account->load('receipts', 'guest');
        return view('admin.finance.accounts-receivable.show', ['account' => $account]);
    }

    public function create()
    {
        $account = null;
        $guests = Guest::orderBy('first_name')->get();
        return view('admin.finance.accounts-receivable.form', compact('account', 'guests'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'invoice_date' => 'required|date',
                'due_date' => 'required|date|after_or_equal:invoice_date',
                'guest_id' => 'nullable|exists:guests,id',
                'subtotal' => 'required|numeric|min:0',
                'tax_amount' => 'nullable|numeric|min:0',
                'notes' => 'nullable|string',
            ]);

            $total = $request->subtotal + ($request->tax_amount ?? 0);

            AccountsReceivable::create([
                'hotel_id' => auth()->user()->branch_id,
                'invoice_number' => 'INV-' . now()->format('YmdHis'),
                'guest_id' => $request->guest_id,
                'invoice_date' => $request->invoice_date,
                'due_date' => $request->due_date,
                'subtotal' => $request->subtotal,
                'tax_amount' => $request->tax_amount ?? 0,
                'total_amount' => $total,
                'received_amount' => 0,
                'balance' => $total,
                'notes' => $request->notes,
                'status' => 'draft',
                'created_by' => auth()->id(),
            ]);

            return redirect()->route('admin.finance.accounts-receivable.index')->with('success', 'Invoice created successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit(AccountsReceivable $accounts_receivable)
    {
        $account = $accounts_receivable;
        $guests = Guest::orderBy('first_name')->get();
        return view('admin.finance.accounts-receivable.form', compact('account', 'guests'));
    }

    public function update(Request $request, AccountsReceivable $accounts_receivable)
    {
        try {
            $account = $accounts_receivable;
            $request->validate([
                'invoice_date' => 'required|date',
                'due_date' => 'required|date|after_or_equal:invoice_date',
                'guest_id' => 'nullable|exists:guests,id',
                'subtotal' => 'required|numeric|min:0',
                'tax_amount' => 'nullable|numeric|min:0',
                'notes' => 'nullable|string',
            ]);

            $total = $request->subtotal + ($request->tax_amount ?? 0);
            $account->update([
                'guest_id' => $request->guest_id,
                'invoice_date' => $request->invoice_date,
                'due_date' => $request->due_date,
                'subtotal' => $request->subtotal,
                'tax_amount' => $request->tax_amount ?? 0,
                'total_amount' => $total,
                'balance' => $total - $account->received_amount,
                'notes' => $request->notes,
            ]);

            return redirect()->route('admin.finance.accounts-receivable.index')->with('success', 'Invoice updated successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(Request $request, AccountsReceivable $accounts_receivable)
    {
        try {
            $account = $accounts_receivable;
            $account->delete();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Invoice deleted!']);
            }
            return redirect()->route('admin.finance.accounts-receivable.index')->with('success', 'Invoice deleted!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    public function status(Request $request, AccountsReceivable $accounts_receivable)
    {
        try {
            $account = $accounts_receivable;
            $statuses = ['draft' => 'sent', 'sent' => 'sent'];
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

    public function storeReceipt(Request $request, AccountsReceivable $accounts_receivable)
    {
        try {
            $account = $accounts_receivable;
            $request->validate([
                'receipt_date' => 'required|date',
                'amount' => 'required|numeric|min:0.01|max:' . $account->balance,
                'payment_method' => 'required|in:cash,bank_transfer,cheque,online,card',
                'reference_number' => 'nullable|string',
                'notes' => 'nullable|string',
            ]);

            DB::beginTransaction();

            $receipt = ArReceipt::create([
                'hotel_id' => $account->hotel_id,
                'receipt_number' => 'RCP-' . now()->format('YmdHis'),
                'accounts_receivable_id' => $account->id,
                'receipt_date' => $request->receipt_date,
                'amount' => $request->amount,
                'payment_method' => $request->payment_method,
                'reference_number' => $request->reference_number,
                'notes' => $request->notes,
                'created_by' => auth()->id(),
            ]);

            $account->received_amount += $request->amount;
            $account->balance = $account->total_amount - $account->received_amount;
            $account->status = $account->balance <= 0 ? 'paid' : 'partial';
            $account->save();

            DB::commit();
            return redirect()->route('admin.finance.accounts-receivable.show', $account)->with('success', 'Receipt recorded successfully!');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', $e->getMessage());
        }
    }
}
