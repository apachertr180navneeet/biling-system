<?php

namespace App\Http\Controllers\Admin\Finance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ChartOfAccount;
use App\Helpers\Helper;
use Yajra\DataTables\Facades\DataTables;
use Exception;

class ChartOfAccountsController extends Controller
{
    public function index()
    {
        return view('admin.finance.chart-of-accounts.index');
    }

    public function data(Request $request)
    {
        $query = ChartOfAccount::with('parent')->select('chart_of_accounts.*');
        return DataTables::of($query)
            ->filterColumn('name', function ($query, $value) {
                $query->where('name', 'like', "%{$value}%");
            })
            ->filterColumn('code', function ($query, $value) {
                $query->where('code', 'like', "%{$value}%");
            })
            ->filterColumn('type', function ($query, $value) {
                $query->where('type', 'like', "%{$value}%");
            })
            ->addColumn('parent_name', fn($account) => $account->parent?->name ?? '-')
            ->addColumn('status_url', fn($account) => route('admin.finance.chart-of-accounts.status', ['account' => $account]))
            ->addColumn('edit_url', fn($account) => route('admin.finance.chart-of-accounts.edit', ['chart_of_account' => $account]))
            ->addColumn('delete_url', fn($account) => route('admin.finance.chart-of-accounts.destroy', ['chart_of_account' => $account]))
            ->rawColumns([])
            ->make(true);
    }

    public function tree()
    {
        $accounts = ChartOfAccount::active()->with('children')->whereNull('parent_id')->orderBy('code')->get();
        return view('admin.finance.chart-of-accounts.tree', compact('accounts'));
    }

    public function create()
    {
        $account = null;
        $parents = ChartOfAccount::active()->where('is_group', true)->orderBy('code')->get();
        return view('admin.finance.chart-of-accounts.form', compact('account', 'parents'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'code' => 'required|string|max:20|unique:chart_of_accounts,code',
                'name' => 'required|string|max:255',
                'type' => 'required|in:asset,liability,equity,revenue,expense',
                'sub_type' => 'nullable|string',
                'is_group' => 'required|boolean',
                'parent_id' => 'nullable|exists:chart_of_accounts,id',
                'description' => 'nullable|string',
                'is_bank_account' => 'nullable|boolean',
                'bank_name' => 'nullable|string',
                'bank_account_number' => 'nullable|string',
                'status' => 'required|in:active,inactive',
            ]);
            $data = $request->all();
            $data['slug'] = Helper::slug('chart_of_accounts', $request->name);
            ChartOfAccount::create($data);
            return redirect()->route('admin.finance.chart-of-accounts.index')->with('success', 'Account created successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit(ChartOfAccount $chart_of_account)
    {
        $account = $chart_of_account;
        $parents = ChartOfAccount::active()->where('is_group', true)->where('id', '!=', $account->id)->orderBy('code')->get();
        return view('admin.finance.chart-of-accounts.form', compact('account', 'parents'));
    }

    public function update(Request $request, ChartOfAccount $chart_of_account)
    {
        try {
            $account = $chart_of_account;
            $request->validate([
                'code' => 'required|string|max:20|unique:chart_of_accounts,code,' . $account->id,
                'name' => 'required|string|max:255',
                'type' => 'required|in:asset,liability,equity,revenue,expense',
                'sub_type' => 'nullable|string',
                'is_group' => 'required|boolean',
                'parent_id' => 'nullable|exists:chart_of_accounts,id',
                'description' => 'nullable|string',
                'is_bank_account' => 'nullable|boolean',
                'bank_name' => 'nullable|string',
                'bank_account_number' => 'nullable|string',
                'status' => 'required|in:active,inactive',
            ]);
            $account->update($request->all());
            return redirect()->route('admin.finance.chart-of-accounts.index')->with('success', 'Account updated successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(Request $request, ChartOfAccount $chart_of_account)
    {
        try {
            $account = $chart_of_account;
            if ($account->children()->count() > 0) {
                throw new Exception('Cannot delete this account because it has sub-accounts. Remove them first.');
            }
            if ($account->journalEntryLines()->count() > 0) {
                throw new Exception('Cannot delete this account because it has journal entry lines.');
            }
            if (\App\Models\Expense::where('account_id', $account->id)->count() > 0) {
                throw new Exception('Cannot delete this account because it is used in expenses.');
            }

            $account->delete();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Account deleted successfully!']);
            }
            return redirect()->route('admin.finance.chart-of-accounts.index')->with('success', 'Account deleted successfully!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    public function status(Request $request, ChartOfAccount $account)
    {
        try {
            $account->status = $account->status === 'active' ? 'inactive' : 'active';
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
}
