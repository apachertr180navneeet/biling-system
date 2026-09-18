<?php

namespace App\Http\Controllers\Admin\Finance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GstReturn;
use App\Models\Reservation;
use App\Models\Expense;
use App\Models\AccountsPayable;
use App\Models\AccountsReceivable;
use App\Models\Tax;
use App\Models\Company;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;
use Exception;

class GstController extends Controller
{
    public function index()
    {
        return view('admin.finance.gst.index');
    }

    public function summary(Request $request)
    {
        $from = $request->get('from_date', now()->startOfMonth()->format('Y-m-d'));
        $to = $request->get('to_date', now()->format('Y-m-d'));
        $company = Company::first();

        $outputTax = $this->calculateOutputTax($from, $to);
        $inputTax = $this->calculateInputTax($from, $to);

        $totalOutputCgst = $outputTax['cgst'];
        $totalOutputSgst = $outputTax['sgst'];
        $totalOutputIgst = $outputTax['igst'];
        $totalOutputTax = $totalOutputCgst + $totalOutputSgst + $totalOutputIgst;

        $totalInputCgst = $inputTax['cgst'];
        $totalInputSgst = $inputTax['sgst'];
        $totalInputIgst = $inputTax['igst'];
        $totalInputTax = $totalInputCgst + $totalInputSgst + $totalInputIgst;

        $netCgst = max(0, $totalOutputCgst - $totalInputCgst);
        $netSgst = max(0, $totalOutputSgst - $totalInputSgst);
        $netIgst = max(0, $totalOutputIgst - $totalInputIgst);
        $netLiability = max(0, $totalOutputTax - $totalInputTax);
        $inputCreditAvailable = max(0, $totalInputTax - $totalOutputTax);

        $monthlySummary = $this->getMonthlySummary($company);

        return view('admin.finance.gst.summary', compact(
            'company', 'from', 'to',
            'outputTax', 'inputTax',
            'totalOutputCgst', 'totalOutputSgst', 'totalOutputIgst', 'totalOutputTax',
            'totalInputCgst', 'totalInputSgst', 'totalInputIgst', 'totalInputTax',
            'netCgst', 'netSgst', 'netIgst', 'netLiability', 'inputCreditAvailable',
            'monthlySummary'
        ));
    }

    public function data(Request $request)
    {
        $query = GstReturn::select('gst_returns.*');
        return DataTables::of($query)
            ->filterColumn('return_number', fn($q, $v) => $q->where('return_number', 'like', "%{$v}%"))
            ->filterColumn('period', fn($q, $v) => $q->where('period', 'like', "%{$v}%"))
            ->addColumn('status_url', fn($gst) => route('admin.finance.gst.status', $gst))
            ->addColumn('edit_url', fn($gst) => route('admin.finance.gst.edit', $gst))
            ->addColumn('delete_url', fn($gst) => route('admin.finance.gst.destroy', $gst))
            ->addColumn('show_url', fn($gst) => route('admin.finance.gst.edit', $gst))
            ->rawColumns([])
            ->make(true);
    }

    public function create()
    {
        $gst = null;
        return view('admin.finance.gst.form', compact('gst'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'period' => 'required|string|max:20',
                'return_type' => 'required|in:gstr1,gstr3b,gstr9',
                'filing_date' => 'nullable|date',
                'total_taxable_value' => 'nullable|numeric|min:0',
                'total_cgst' => 'nullable|numeric|min:0',
                'total_sgst' => 'nullable|numeric|min:0',
                'total_igst' => 'nullable|numeric|min:0',
                'total_cess' => 'nullable|numeric|min:0',
            ]);

            $data = $request->all();
            $data['return_number'] = 'GST-' . strtoupper($request->return_type) . '-' . str_replace('-', '', $request->period);
            $data['hotel_id'] = auth()->user()->branch_id;
            $data['total_tax'] = ($request->total_cgst ?? 0) + ($request->total_sgst ?? 0) + ($request->total_igst ?? 0) + ($request->total_cess ?? 0);
            $data['created_by'] = auth()->id();
            $data['status'] = 'draft';
            GstReturn::create($data);

            return redirect()->route('admin.finance.gst.index')->with('success', 'GST return created successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit(GstReturn $gst)
    {
        return view('admin.finance.gst.form', ['gst' => $gst]);
    }

    public function update(Request $request, GstReturn $gst)
    {
        try {
            $request->validate([
                'period' => 'required|string|max:20',
                'return_type' => 'required|in:gstr1,gstr3b,gstr9',
                'filing_date' => 'nullable|date',
                'total_taxable_value' => 'nullable|numeric|min:0',
                'total_cgst' => 'nullable|numeric|min:0',
                'total_sgst' => 'nullable|numeric|min:0',
                'total_igst' => 'nullable|numeric|min:0',
                'total_cess' => 'nullable|numeric|min:0',
            ]);

            $data = $request->all();
            $data['total_tax'] = ($request->total_cgst ?? 0) + ($request->total_sgst ?? 0) + ($request->total_igst ?? 0) + ($request->total_cess ?? 0);
            $gst->update($data);

            return redirect()->route('admin.finance.gst.index')->with('success', 'GST return updated successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(Request $request, GstReturn $gst)
    {
        try {
            $gst->delete();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'GST return deleted!']);
            }
            return redirect()->route('admin.finance.gst.index')->with('success', 'GST return deleted!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    public function status(Request $request, GstReturn $gst)
    {
        try {
            $statuses = ['draft' => 'filed', 'filed' => 'draft'];
            $gst->status = $statuses[$gst->status] ?? $gst->status;
            $gst->save();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'status' => $gst->status, 'message' => 'Status updated!']);
            }
            return redirect()->back()->with('success', 'Status updated!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    private function calculateOutputTax(string $from, string $to): array
    {
        $defaultTaxRate = Tax::where('slug', 'gst')->where('status', 'active')->first()?->rate ?? 18;
        $halfRate = $defaultTaxRate / 2;

        $revenueTaxable = Reservation::whereBetween('check_in_date', [$from, $to])
            ->whereIn('status', ['confirmed', 'checked-in', 'checked-out'])
            ->sum('tax_amount');

        $expenseOutputTax = 0;
        $apOutputTax = 0;

        $totalTaxable = $revenueTaxable + $expenseOutputTax + $apOutputTax;
        $cgst = round($totalTaxable * ($halfRate / 100), 2);
        $sgst = round($totalTaxable * ($halfRate / 100), 2);
        $igst = 0;

        return [
            'taxable_value' => round($revenueTaxable, 2),
            'cgst' => $cgst,
            'sgst' => $sgst,
            'igst' => $igst,
            'total' => round($totalTaxable, 2),
        ];
    }

    private function calculateInputTax(string $from, string $to): array
    {
        $defaultTaxRate = Tax::where('slug', 'gst')->where('status', 'active')->first()?->rate ?? 18;
        $halfRate = $defaultTaxRate / 2;

        $expensesTax = Expense::whereBetween('expense_date', [$from, $to])
            ->where('status', '!=', 'cancelled')
            ->sum('tax_amount');

        $apTax = AccountsPayable::whereBetween('due_date', [$from, $to])
            ->where('status', '!=', 'cancelled')
            ->sum('tax_amount');

        $totalInputTax = $expensesTax + $apTax;
        $cgst = round($totalInputTax * ($halfRate / 100), 2);
        $sgst = round($totalInputTax * ($halfRate / 100), 2);
        $igst = 0;

        return [
            'taxable_value' => round($totalInputTax, 2),
            'cgst' => $cgst,
            'sgst' => $sgst,
            'igst' => $igst,
            'total' => round($totalInputTax, 2),
        ];
    }

    private function getMonthlySummary(?Company $company): array
    {
        $months = [];
        for ($i = 5; $i >= 0; $i--) {
            $monthStart = now()->subMonths($i)->startOfMonth();
            $monthEnd = now()->subMonths($i)->endOfMonth();
            $from = $monthStart->format('Y-m-d');
            $to = $monthEnd->format('Y-m-d');

            $output = $this->calculateOutputTax($from, $to);
            $input = $this->calculateInputTax($from, $to);

            $months[] = [
                'month' => $monthStart->format('M Y'),
                'output_tax' => $output['total'],
                'input_tax' => $input['total'],
                'net_liability' => max(0, $output['total'] - $input['total']),
            ];
        }
        return $months;
    }
}
