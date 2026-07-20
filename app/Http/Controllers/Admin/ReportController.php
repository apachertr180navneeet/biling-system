<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VehicleInventory;
use App\Models\SparePart;
use App\Models\SparePartStockTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function vehicleLedger(Request $request)
    {
        $search = $request->input('search');
        $chassis = $request->input('chassis_number');
        $engine = $request->input('engine_number');

        // 1. Summary grouped by vehicle details
        $summaryQuery = VehicleInventory::select(
            'vehicle_description',
            DB::raw('COUNT(*) as total_in'),
            DB::raw('SUM(CASE WHEN status = "sold" THEN 1 ELSE 0 END) as total_out'),
            DB::raw('SUM(CASE WHEN status = "available" THEN 1 ELSE 0 END) as remaining')
        )->groupBy('vehicle_description');

        if ($search) {
            $summaryQuery->where('vehicle_description', 'like', "%{$search}%");
        }
        $summaries = $summaryQuery->get();

        // 2. Chronological Ledger transactions
        $ledgerQuery = VehicleInventory::with('purchaseOrder')->orderBy('created_at', 'desc');

        if ($search) {
            $ledgerQuery->where('vehicle_description', 'like', "%{$search}%");
        }
        if ($chassis) {
            $ledgerQuery->where('chassis_number', 'like', "%{$chassis}%");
        }
        if ($engine) {
            $ledgerQuery->where('engine_number', 'like', "%{$engine}%");
        }

        $ledger = $ledgerQuery->paginate(20)->withQueryString();

        return view('admin.reports.vehicle_ledger', compact('summaries', 'ledger', 'search', 'chassis', 'engine'));
    }

    public function partLedger(Request $request)
    {
        $search = $request->input('search');
        $type = $request->input('transaction_type');

        // 1. Part Wise Summaries
        $summaryQuery = SparePart::leftJoin('spare_part_stock_transactions as t', 'spare_parts.id', '=', 't.spare_part_id')
            ->leftJoin('spare_part_stocks as s', 'spare_parts.id', '=', 's.spare_part_id')
            ->select(
                'spare_parts.id',
                'spare_parts.part_no',
                'spare_parts.name',
                'spare_parts.unit',
                DB::raw('COALESCE(SUM(CASE WHEN t.transaction_type = "in" THEN t.quantity ELSE 0 END), 0) as total_in'),
                DB::raw('COALESCE(SUM(CASE WHEN t.transaction_type = "out" THEN t.quantity ELSE 0 END), 0) as total_out'),
                DB::raw('COALESCE(MAX(s.quantity), 0) as remaining')
            )
            ->groupBy('spare_parts.id', 'spare_parts.part_no', 'spare_parts.name', 'spare_parts.unit');

        if ($search) {
            $summaryQuery->where(function ($q) use ($search) {
                $q->where('spare_parts.name', 'like', "%{$search}%")
                  ->orWhere('spare_parts.part_no', 'like', "%{$search}%");
            });
        }
        $summaries = $summaryQuery->get();

        // 2. Ledger Transactions List
        $ledgerQuery = SparePartStockTransaction::with('sparePart')->orderBy('created_at', 'desc');

        if ($search) {
            $ledgerQuery->whereHas('sparePart', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('part_no', 'like', "%{$search}%");
            });
        }
        if ($type) {
            $ledgerQuery->where('transaction_type', $type);
        }

        $ledger = $ledgerQuery->paginate(20)->withQueryString();

        return view('admin.reports.part_ledger', compact('summaries', 'ledger', 'search', 'type'));
    }
}
