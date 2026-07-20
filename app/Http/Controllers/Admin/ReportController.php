<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VehicleInventory;
use App\Models\SparePart;
use App\Models\SparePartStockTransaction;
use App\Models\VehicleSalesInvoice;
use App\Models\PartSalesInvoice;
use App\Models\VehiclePurchaseOrder;
use App\Models\PurchaseOrder;
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
            $escapedSearch = '%' . addcslashes($search, '%_') . '%';
            $summaryQuery->where('vehicle_description', 'like', $escapedSearch);
        }
        $summaries = $summaryQuery->get();

        // 2. Chronological Ledger transactions
        $ledgerQuery = VehicleInventory::with('purchaseOrder')->orderBy('created_at', 'desc');

        if ($search) {
            $escapedSearch = '%' . addcslashes($search, '%_') . '%';
            $ledgerQuery->where('vehicle_description', 'like', $escapedSearch);
        }
        if ($chassis) {
            $escapedChassis = '%' . addcslashes($chassis, '%_') . '%';
            $ledgerQuery->where('chassis_number', 'like', $escapedChassis);
        }
        if ($engine) {
            $escapedEngine = '%' . addcslashes($engine, '%_') . '%';
            $ledgerQuery->where('engine_number', 'like', $escapedEngine);
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
            $escapedSearch = '%' . addcslashes($search, '%_') . '%';
            $summaryQuery->where(function ($q) use ($escapedSearch) {
                $q->where('spare_parts.name', 'like', $escapedSearch)
                  ->orWhere('spare_parts.part_no', 'like', $escapedSearch);
            });
        }
        $summaries = $summaryQuery->get();

        // 2. Ledger Transactions List
        $ledgerQuery = SparePartStockTransaction::with('sparePart')->orderBy('created_at', 'desc');

        if ($search) {
            $escapedSearch = '%' . addcslashes($search, '%_') . '%';
            $ledgerQuery->whereHas('sparePart', function ($q) use ($escapedSearch) {
                $q->where('name', 'like', $escapedSearch)
                  ->orWhere('part_no', 'like', $escapedSearch);
            });
        }
        if ($type) {
            $ledgerQuery->where('transaction_type', $type);
        }

        $ledger = $ledgerQuery->paginate(20)->withQueryString();

        return view('admin.reports.part_ledger', compact('summaries', 'ledger', 'search', 'type'));
    }

    public function outstandingLedger(Request $request)
    {
        $tab = $request->input('tab', 'sales');
        $search = $request->input('search');
        $type = $request->input('type', 'all');
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        // Calculate total summaries
        $totalOutstandingSalesVehicle = VehicleSalesInvoice::where('balance', '>', 0)->sum('balance');
        $totalOutstandingSalesParts = PartSalesInvoice::where('balance', '>', 0)->sum('balance');
        $totalOutstandingSales = $totalOutstandingSalesVehicle + $totalOutstandingSalesParts;

        $totalOutstandingPurchasesVehicle = VehiclePurchaseOrder::where('balance', '>', 0)->sum('balance');
        $totalOutstandingPurchasesParts = PurchaseOrder::where('balance', '>', 0)->sum('balance');
        $totalOutstandingPurchases = $totalOutstandingPurchasesVehicle + $totalOutstandingPurchasesParts;

        $ledger = null;

        if ($tab === 'sales') {
            $salesQuery1 = null;
            $salesQuery2 = null;

            if ($type === 'all' || $type === 'vehicle') {
                $q = VehicleSalesInvoice::with('customer')
                    ->where('balance', '>', 0)
                    ->select(
                        'id',
                        'invoice_number as doc_number',
                        'invoice_date as doc_date',
                        'customer_name as party_name',
                        'grand_total as total_amount',
                        'received_amount',
                        'balance',
                        DB::raw("'vehicle' as sub_type")
                    );
                if ($search) {
                    $escapedSearch = '%' . addcslashes($search, '%_') . '%';
                    $q->where(function($sq) use ($escapedSearch) {
                        $sq->where('invoice_number', 'like', $escapedSearch)
                           ->orWhere('customer_name', 'like', $escapedSearch)
                           ->orWhere('customer_mobile', 'like', $escapedSearch);
                    });
                }
                if ($fromDate) {
                    $q->whereDate('invoice_date', '>=', $fromDate);
                }
                if ($toDate) {
                    $q->whereDate('invoice_date', '<=', $toDate);
                }
                $salesQuery1 = $q;
            }

            if ($type === 'all' || $type === 'part') {
                $q = PartSalesInvoice::with('customer')
                    ->where('balance', '>', 0)
                    ->select(
                        'id',
                        'invoice_number as doc_number',
                        'invoice_date as doc_date',
                        'customer_name as party_name',
                        'total_amount',
                        'received_amount',
                        'balance',
                        DB::raw("'part' as sub_type")
                    );
                if ($search) {
                    $escapedSearch = '%' . addcslashes($search, '%_') . '%';
                    $q->where(function($sq) use ($escapedSearch) {
                        $sq->where('invoice_number', 'like', $escapedSearch)
                           ->orWhere('customer_name', 'like', $escapedSearch)
                           ->orWhere('customer_mobile', 'like', $escapedSearch);
                    });
                }
                if ($fromDate) {
                    $q->whereDate('invoice_date', '>=', $fromDate);
                }
                if ($toDate) {
                    $q->whereDate('invoice_date', '<=', $toDate);
                }
                $salesQuery2 = $q;
            }

            if ($salesQuery1 && $salesQuery2) {
                $unionQuery = $salesQuery1->union($salesQuery2);
                $unionSql = $unionQuery->toSql();
                
                $finalQuery = DB::table(DB::raw("({$unionSql}) as union_table"))
                    ->mergeBindings($unionQuery->getQuery())
                    ->orderBy('doc_date', 'desc')
                    ->orderBy('id', 'desc');
                
                $ledger = $finalQuery->paginate(20)->withQueryString();
            } elseif ($salesQuery1) {
                $ledger = $salesQuery1->orderBy('invoice_date', 'desc')->orderBy('id', 'desc')->paginate(20)->withQueryString();
            } elseif ($salesQuery2) {
                $ledger = $salesQuery2->orderBy('invoice_date', 'desc')->orderBy('id', 'desc')->paginate(20)->withQueryString();
            }
        } else {
            // tab === 'purchases'
            $purchaseQuery1 = null;
            $purchaseQuery2 = null;

            if ($type === 'all' || $type === 'vehicle') {
                $q = VehiclePurchaseOrder::with('supplier')
                    ->where('balance', '>', 0)
                    ->select(
                        'id',
                        'po_number as doc_number',
                        'order_date as doc_date',
                        DB::raw("(SELECT name FROM suppliers WHERE suppliers.id = vehicle_purchase_orders.supplier_id) as party_name"),
                        'total_amount',
                        'received_amount',
                        'balance',
                        DB::raw("'vehicle' as sub_type")
                    );
                if ($search) {
                    $escapedSearch = '%' . addcslashes($search, '%_') . '%';
                    $q->where(function($sq) use ($escapedSearch) {
                        $sq->where('po_number', 'like', $escapedSearch)
                           ->orWhereHas('supplier', function($supq) use ($escapedSearch) {
                               $supq->where('name', 'like', $escapedSearch);
                           });
                    });
                }
                if ($fromDate) {
                    $q->whereDate('order_date', '>=', $fromDate);
                }
                if ($toDate) {
                    $q->whereDate('order_date', '<=', $toDate);
                }
                $purchaseQuery1 = $q;
            }

            if ($type === 'all' || $type === 'part') {
                $q = PurchaseOrder::with('supplier')
                    ->where('balance', '>', 0)
                    ->select(
                        'id',
                        'order_number as doc_number',
                        'order_date as doc_date',
                        DB::raw("(SELECT name FROM suppliers WHERE suppliers.id = purchase_orders.supplier_id) as party_name"),
                        'total_amount',
                        'received_amount',
                        'balance',
                        DB::raw("'part' as sub_type")
                    );
                if ($search) {
                    $escapedSearch = '%' . addcslashes($search, '%_') . '%';
                    $q->where(function($sq) use ($escapedSearch) {
                        $sq->where('order_number', 'like', $escapedSearch)
                           ->orWhereHas('supplier', function($supq) use ($escapedSearch) {
                               $supq->where('name', 'like', $escapedSearch);
                           });
                    });
                }
                if ($fromDate) {
                    $q->whereDate('order_date', '>=', $fromDate);
                }
                if ($toDate) {
                    $q->whereDate('order_date', '<=', $toDate);
                }
                $purchaseQuery2 = $q;
            }

            if ($purchaseQuery1 && $purchaseQuery2) {
                $unionQuery = $purchaseQuery1->union($purchaseQuery2);
                $unionSql = $unionQuery->toSql();
                
                $finalQuery = DB::table(DB::raw("({$unionSql}) as union_table"))
                    ->mergeBindings($unionQuery->getQuery())
                    ->orderBy('doc_date', 'desc')
                    ->orderBy('id', 'desc');
                
                $ledger = $finalQuery->paginate(20)->withQueryString();
            } elseif ($purchaseQuery1) {
                $ledger = $purchaseQuery1->orderBy('order_date', 'desc')->orderBy('id', 'desc')->paginate(20)->withQueryString();
            } elseif ($purchaseQuery2) {
                $ledger = $purchaseQuery2->orderBy('order_date', 'desc')->orderBy('id', 'desc')->paginate(20)->withQueryString();
            }
        }

        return view('admin.reports.outstanding_ledger', compact(
            'tab',
            'search',
            'type',
            'fromDate',
            'toDate',
            'totalOutstandingSales',
            'totalOutstandingSalesVehicle',
            'totalOutstandingSalesParts',
            'totalOutstandingPurchases',
            'totalOutstandingPurchasesVehicle',
            'totalOutstandingPurchasesParts',
            'ledger'
        ));
    }
}
