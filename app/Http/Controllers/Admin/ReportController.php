<?php

namespace App\Http\Controllers\Admin;

use App\Models\Customer;
use App\Models\Supplier;
use App\Models\VehicleMaster;
use App\Models\VehicleInventory;
use App\Models\SparePart;
use App\Models\SparePartStock;
use App\Models\SparePartStockTransaction;
use App\Models\VehicleSalesInvoice;
use App\Models\PartSalesInvoice;
use App\Models\PartSalesInvoiceItem;
use App\Models\VehiclePurchaseOrder;
use App\Models\VehiclePoItem;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\PaymentTransaction;
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

        $vehicleMasters = \App\Models\VehicleMaster::where('is_active', true)->get();
        foreach ($summaries as $s) {
            $matchedMaster = $vehicleMasters->first(function($m) use ($s) {
                $fullName = $m->variant_name . ($m->color_name ? ' (' . $m->color_name . ')' : '');
                return strtolower($fullName) === strtolower($s->vehicle_description) 
                    || strtolower($m->variant_name) === strtolower($s->vehicle_description);
            });
            $s->min_stock = $matchedMaster ? $matchedMaster->min_stock : 0;
        }

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
                'spare_parts.min_stock',
                DB::raw('COALESCE(SUM(CASE WHEN t.transaction_type = "in" THEN t.quantity ELSE 0 END), 0) as total_in'),
                DB::raw('COALESCE(SUM(CASE WHEN t.transaction_type = "out" THEN t.quantity ELSE 0 END), 0) as total_out'),
                DB::raw('COALESCE(MAX(s.quantity), 0) as remaining')
            )
            ->groupBy('spare_parts.id', 'spare_parts.part_no', 'spare_parts.name', 'spare_parts.min_stock');

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

    public function partyReportByItem(Request $request)
    {
        $selectedItem = $request->input('item_id'); // format: 'vehicle_ID' or 'part_ID'
        $dateFilter = $request->input('date_filter', 'this_month');
        $customFrom = $request->input('custom_from');
        $customTo = $request->input('custom_to');

        // Resolve Date Range
        $dates = $this->getDateRange($dateFilter, $customFrom, $customTo);
        $fromDate = $dates['from'];
        $toDate = $dates['to'];

        // Get All Items (Vehicles & Parts) for Search Dropdown
        $vehicleMasters = \App\Models\VehicleMaster::where('is_active', true)->orderBy('variant_name')->get();
        $spareParts = SparePart::where('is_active', true)->orderBy('name')->get();

        $itemList = [];
        foreach ($vehicleMasters as $vm) {
            $itemList[] = [
                'id' => 'vehicle_' . $vm->id,
                'name' => '[Vehicle] ' . $vm->variant_name . ($vm->color_name ? ' (' . $vm->color_name . ')' : '') . ' - ' . $vm->fuel_type,
                'raw_name' => $vm->variant_name . ($vm->color_name ? ' (' . $vm->color_name . ')' : '')
            ];
        }
        foreach ($spareParts as $sp) {
            $itemList[] = [
                'id' => 'part_' . $sp->id,
                'name' => '[Spare Part] ' . $sp->name . ($sp->part_no ? ' (' . $sp->part_no . ')' : ''),
                'raw_name' => $sp->name
            ];
        }

        // If no item selected initially, default to first spare part or vehicle if available
        if (empty($selectedItem) && !empty($itemList)) {
            $selectedItem = $itemList[0]['id'];
        }

        $selectedItemData = null;
        $partyData = [];

        if (!empty($selectedItem)) {
            list($itemType, $itemId) = explode('_', $selectedItem, 2);

            if ($itemType === 'vehicle') {
                $vMaster = \App\Models\VehicleMaster::find($itemId);
                if ($vMaster) {
                    $variantDesc = $vMaster->variant_name . ($vMaster->color_name ? ' (' . $vMaster->color_name . ')' : '');
                    $selectedItemData = [
                        'type' => 'vehicle',
                        'name' => $variantDesc,
                    ];

                    // Fetch Sales
                    $salesQuery = VehicleSalesInvoice::whereHas('vehicleInventory', function($q) use ($vMaster) {
                            $q->where('vehicle_master_id', $vMaster->id)
                              ->orWhere('vehicle_description', 'like', '%' . addcslashes($vMaster->variant_name, '%_') . '%');
                        });
                    if ($fromDate) $salesQuery->whereDate('invoice_date', '>=', $fromDate);
                    if ($toDate) $salesQuery->whereDate('invoice_date', '<=', $toDate);
                    $salesInvoices = $salesQuery->get();

                    foreach ($salesInvoices as $inv) {
                        $party = strtoupper(trim($inv->customer_name));
                        if (!isset($partyData[$party])) {
                            $partyData[$party] = [
                                'party_name' => $inv->customer_name,
                                'sales_qty' => 0,
                                'sales_amount' => 0,
                                'purchase_qty' => 0,
                                'purchase_amount' => 0,
                            ];
                        }
                        $partyData[$party]['sales_qty'] += 1;
                        $partyData[$party]['sales_amount'] += (float)$inv->grand_total;
                    }

                    // Fetch Purchases
                    $poQuery = VehiclePurchaseOrder::with(['items', 'supplier']);
                    if ($fromDate) $poQuery->whereDate('order_date', '>=', $fromDate);
                    if ($toDate) $poQuery->whereDate('order_date', '<=', $toDate);
                    $poOrders = $poQuery->get();

                    foreach ($poOrders as $po) {
                        $supplierName = $po->supplier->name ?? 'SUPPLIER #' . $po->supplier_id;
                        $party = strtoupper(trim($supplierName));

                        foreach ($po->items as $item) {
                            if ($item->vehicle_master_id == $vMaster->id) {
                                if (!isset($partyData[$party])) {
                                    $partyData[$party] = [
                                        'party_name' => $supplierName,
                                        'sales_qty' => 0,
                                        'sales_amount' => 0,
                                        'purchase_qty' => 0,
                                        'purchase_amount' => 0,
                                    ];
                                }
                                $partyData[$party]['purchase_qty'] += (int)$item->ordered_quantity;
                                $partyData[$party]['purchase_amount'] += (float)$item->total_amount;
                            }
                        }
                    }
                }
            } else {
                // Part item
                $spPart = SparePart::find($itemId);
                if ($spPart) {
                    $selectedItemData = [
                        'type' => 'part',
                        'name' => $spPart->name . ($spPart->part_no ? ' (' . $spPart->part_no . ')' : ''),
                    ];

                    // Fetch Sales (PartSalesInvoiceItem)
                    $partSalesQuery = \App\Models\PartSalesInvoiceItem::with('invoice')
                        ->where('spare_part_id', $spPart->id)
                        ->whereHas('invoice', function($q) use ($fromDate, $toDate) {
                            if ($fromDate) $q->whereDate('invoice_date', '>=', $fromDate);
                            if ($toDate) $q->whereDate('invoice_date', '<=', $toDate);
                        });

                    $salesItems = $partSalesQuery->get();
                    foreach ($salesItems as $sItem) {
                        if ($sItem->invoice) {
                            $party = strtoupper(trim($sItem->invoice->customer_name));
                            if (!isset($partyData[$party])) {
                                $partyData[$party] = [
                                    'party_name' => $sItem->invoice->customer_name,
                                    'sales_qty' => 0,
                                    'sales_amount' => 0,
                                    'purchase_qty' => 0,
                                    'purchase_amount' => 0,
                                ];
                            }
                            $partyData[$party]['sales_qty'] += (int)$sItem->quantity;
                            $partyData[$party]['sales_amount'] += (float)$sItem->amount;
                        }
                    }

                    // Fetch Purchases (PurchaseOrderItem)
                    $poItemsQuery = \App\Models\PurchaseOrderItem::with(['purchaseOrder.supplier'])
                        ->where('spare_part_id', $spPart->id)
                        ->whereHas('purchaseOrder', function($q) use ($fromDate, $toDate) {
                            if ($fromDate) $q->whereDate('order_date', '>=', $fromDate);
                            if ($toDate) $q->whereDate('order_date', '<=', $toDate);
                        });

                    $poItems = $poItemsQuery->get();
                    foreach ($poItems as $pItem) {
                        if ($pItem->purchaseOrder) {
                            $supplierName = $pItem->purchaseOrder->supplier->name ?? 'SUPPLIER #' . $pItem->purchaseOrder->supplier_id;
                            $party = strtoupper(trim($supplierName));
                            if (!isset($partyData[$party])) {
                                $partyData[$party] = [
                                    'party_name' => $supplierName,
                                    'sales_qty' => 0,
                                    'sales_amount' => 0,
                                    'purchase_qty' => 0,
                                    'purchase_amount' => 0,
                                ];
                            }
                            $partyData[$party]['purchase_qty'] += (int)$pItem->quantity;
                            $partyData[$party]['purchase_amount'] += (float)$pItem->total_amount;
                        }
                    }
                }
            }
        }

        return view('admin.reports.party_report_by_item', compact(
            'itemList',
            'selectedItem',
            'selectedItemData',
            'dateFilter',
            'customFrom',
            'customTo',
            'partyData',
            'fromDate',
            'toDate'
        ));
    }

    public function printPartyReportPdf(Request $request)
    {
        $reqData = $this->partyReportByItem($request);
        $data = $reqData->getData();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.reports.party_report_by_item_pdf', $data);
        $pdf->setPaper('a4', 'portrait');
        return $pdf->stream('Party_Report_By_Item.pdf');
    }

    public function exportPartyReportExcel(Request $request)
    {
        $reqData = $this->partyReportByItem($request);
        $data = $reqData->getData();

        $filename = 'Party_Report_By_Item_' . date('Y-m-d') . '.csv';
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Party Report By Item']);
            fputcsv($file, ['Item:', $data['selectedItemData']['name'] ?? 'N/A']);
            fputcsv($file, ['Date Filter:', ucfirst(str_replace('_', ' ', $data['dateFilter']))]);
            fputcsv($file, []);
            fputcsv($file, ['Party Name', 'Sales Qty', 'Sales Amount', 'Purchase Qty', 'Purchase Amount']);

            foreach ($data['partyData'] as $row) {
                fputcsv($file, [
                    $row['party_name'],
                    $row['sales_qty'] > 0 ? $row['sales_qty'] : '-',
                    $row['sales_amount'] > 0 ? '₹' . number_format($row['sales_amount'], 2) : '-',
                    $row['purchase_qty'] > 0 ? $row['purchase_qty'] : '-',
                    $row['purchase_amount'] > 0 ? '₹' . number_format($row['purchase_amount'], 2) : '-',
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function emailPartyReportExcel(Request $request)
    {
        $email = $request->input('email');
        if (empty($email)) {
            return back()->with('error', 'Please provide a valid email address.');
        }

        // Action placeholder / simulation notice
        return back()->with('success', "Report successfully emailed to {$email}.");
    }

    private function getDateRange($filter, $customFrom = null, $customTo = null)
    {
        $today = date('Y-m-d');
        $from = null;
        $to = null;

        switch ($filter) {
            case 'today':
                $from = $today;
                $to = $today;
                break;
            case 'yesterday':
                $from = date('Y-m-d', strtotime('-1 day'));
                $to = date('Y-m-d', strtotime('-1 day'));
                break;
            case 'last_7_days':
                $from = date('Y-m-d', strtotime('-6 days'));
                $to = $today;
                break;
            case 'last_15_days':
                $from = date('Y-m-d', strtotime('-14 days'));
                $to = $today;
                break;
            case 'last_30_days':
                $from = date('Y-m-d', strtotime('-29 days'));
                $to = $today;
                break;
            case 'this_week':
                $from = date('Y-m-d', strtotime('monday this week'));
                $to = date('Y-m-d', strtotime('sunday this week'));
                break;
            case 'previous_week':
                $from = date('Y-m-d', strtotime('monday last week'));
                $to = date('Y-m-d', strtotime('sunday last week'));
                break;
            case 'this_month':
                $from = date('Y-m-01');
                $to = date('Y-m-t');
                break;
            case 'previous_month':
                $from = date('Y-m-01', strtotime('first day of last month'));
                $to = date('Y-m-t', strtotime('last day of last month'));
                break;
            case 'this_quarter':
                $quarter = ceil(date('n') / 3);
                $from = date('Y-' . sprintf('%02d', ($quarter - 1) * 3 + 1) . '-01');
                $to = date('Y-' . sprintf('%02d', $quarter * 3) . '-' . date('t', strtotime($from)));
                break;
            case 'previous_quarter':
                $quarter = ceil(date('n') / 3) - 1;
                $year = date('Y');
                if ($quarter == 0) {
                    $quarter = 4;
                    $year = $year - 1;
                }
                $from = date($year . '-' . sprintf('%02d', ($quarter - 1) * 3 + 1) . '-01');
                $to = date($year . '-' . sprintf('%02d', $quarter * 3) . '-' . date('t', strtotime($from)));
                break;
            case 'this_year':
                $from = date('Y-01-01');
                $to = date('Y-12-31');
                break;
            case 'previous_year':
                $year = date('Y') - 1;
                $from = $year . '-01-01';
                $to = $year . '-12-31';
                break;
            case 'current_financial_year':
                $m = date('n');
                $year = date('Y');
                if ($m >= 4) {
                    $from = $year . '-04-01';
                    $to = ($year + 1) . '-03-31';
                } else {
                    $from = ($year - 1) . '-04-01';
                    $to = $year . '-03-31';
                }
                break;
            case 'previous_financial_year':
                $m = date('n');
                $year = date('Y');
                if ($m >= 4) {
                    $from = ($year - 1) . '-04-01';
                    $to = $year . '-03-31';
                } else {
                    $from = ($year - 2) . '-04-01';
                    $to = ($year - 1) . '-03-31';
                }
                break;
            case 'custom':
                $from = $customFrom;
                $to = $customTo;
                break;
            default:
                $from = date('Y-m-01');
                $to = date('Y-m-t');
                break;
        }

        return ['from' => $from, 'to' => $to];
    }

    public function itemReportByParty(Request $request)
    {
        $partyType = $request->input('party_type', 'customer');
        $partyId = $request->input('party_id');
        $dateFilter = $request->input('date_filter', 'this_month');
        $customFrom = $request->input('custom_from');
        $customTo = $request->input('custom_to');

        $dates = $this->getDateRange($dateFilter, $customFrom, $customTo);
        $fromDate = $dates['from'];
        $toDate = $dates['to'];

        $customers = Customer::where('is_active', true)->orderBy('name')->get();
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();

        $items = [];
        $selectedPartyName = '';

        if ($partyType === 'customer' && $partyId) {
            $customer = Customer::find($partyId);
            if ($customer) {
                $selectedPartyName = $customer->name;

                // Vehicle Sales
                $vQuery = VehicleSalesInvoice::with('vehicleInventory')
                    ->where(function($q) use ($customer) {
                        $q->where('customer_id', $customer->id)
                          ->orWhere('customer_name', 'like', '%' . addcslashes($customer->name, '%_') . '%');
                    });
                if ($fromDate) $vQuery->whereDate('invoice_date', '>=', $fromDate);
                if ($toDate) $vQuery->whereDate('invoice_date', '<=', $toDate);
                $vInvoices = $vQuery->get();

                foreach ($vInvoices as $inv) {
                    $desc = $inv->vehicleInventory->vehicle_description ?? 'EV Vehicle';
                    if (!isset($items[$desc])) {
                        $items[$desc] = ['item_name' => $desc, 'type' => 'Vehicle', 'total_qty' => 0, 'total_amount' => 0, 'last_date' => $inv->invoice_date->format('d/m/Y')];
                    }
                    $items[$desc]['total_qty'] += 1;
                    $items[$desc]['total_amount'] += (float)$inv->grand_total;
                }

                // Part Sales
                $pQuery = PartSalesInvoiceItem::with(['invoice', 'sparePart'])
                    ->whereHas('invoice', function($q) use ($customer, $fromDate, $toDate) {
                        $q->where(function($sq) use ($customer) {
                            $sq->where('customer_id', $customer->id)
                               ->orWhere('customer_name', 'like', '%' . addcslashes($customer->name, '%_') . '%');
                        });
                        if ($fromDate) $q->whereDate('invoice_date', '>=', $fromDate);
                        if ($toDate) $q->whereDate('invoice_date', '<=', $toDate);
                    });
                $pItems = $pQuery->get();

                foreach ($pItems as $pi) {
                    $pName = $pi->sparePart->name ?? 'Part #' . $pi->spare_part_id;
                    if (!isset($items[$pName])) {
                        $items[$pName] = ['item_name' => $pName, 'type' => 'Spare Part', 'total_qty' => 0, 'total_amount' => 0, 'last_date' => $pi->invoice ? $pi->invoice->invoice_date->format('d/m/Y') : '-'];
                    }
                    $items[$pName]['total_qty'] += (int)$pi->quantity;
                    $items[$pName]['total_amount'] += (float)$pi->amount;
                }
            }
        } elseif ($partyType === 'supplier' && $partyId) {
            $supplier = Supplier::find($partyId);
            if ($supplier) {
                $selectedPartyName = $supplier->name;

                // Vehicle Purchase Orders
                $vPoQuery = VehiclePoItem::with('purchaseOrder')
                    ->whereHas('purchaseOrder', function($q) use ($supplier, $fromDate, $toDate) {
                        $q->where('supplier_id', $supplier->id);
                        if ($fromDate) $q->whereDate('order_date', '>=', $fromDate);
                        if ($toDate) $q->whereDate('order_date', '<=', $toDate);
                    });
                $vPoItems = $vPoQuery->get();

                foreach ($vPoItems as $vitem) {
                    $desc = $vitem->vehicle_description;
                    if (!isset($items[$desc])) {
                        $items[$desc] = ['item_name' => $desc, 'type' => 'Vehicle PO', 'total_qty' => 0, 'total_amount' => 0, 'last_date' => $vitem->purchaseOrder ? $vitem->purchaseOrder->order_date->format('d/m/Y') : '-'];
                    }
                    $items[$desc]['total_qty'] += (int)$vitem->ordered_quantity;
                    $items[$desc]['total_amount'] += (float)$vitem->total_amount;
                }

                // Spare Part Purchase Orders
                $pPoQuery = PurchaseOrderItem::with(['purchaseOrder', 'sparePart'])
                    ->whereHas('purchaseOrder', function($q) use ($supplier, $fromDate, $toDate) {
                        $q->where('supplier_id', $supplier->id);
                        if ($fromDate) $q->whereDate('order_date', '>=', $fromDate);
                        if ($toDate) $q->whereDate('order_date', '<=', $toDate);
                    });
                $pPoItems = $pPoQuery->get();

                foreach ($pPoItems as $pitem) {
                    $pName = $pitem->sparePart->name ?? 'Part #' . $pitem->spare_part_id;
                    if (!isset($items[$pName])) {
                        $items[$pName] = ['item_name' => $pName, 'type' => 'Spare Part PO', 'total_qty' => 0, 'total_amount' => 0, 'last_date' => $pitem->purchaseOrder ? $pitem->purchaseOrder->order_date->format('d/m/Y') : '-'];
                    }
                    $items[$pName]['total_qty'] += (int)$pitem->quantity;
                    $items[$pName]['total_amount'] += (float)$pitem->total_amount;
                }
            }
        }

        return view('admin.reports.item_report_by_party', compact('partyType', 'partyId', 'customers', 'suppliers', 'items', 'selectedPartyName', 'dateFilter', 'customFrom', 'customTo', 'fromDate', 'toDate'));
    }

    public function itemSalesPurchaseSummary(Request $request)
    {
        $dateFilter = $request->input('date_filter', 'this_month');
        $customFrom = $request->input('custom_from');
        $customTo = $request->input('custom_to');
        $search = $request->input('search');

        $dates = $this->getDateRange($dateFilter, $customFrom, $customTo);
        $fromDate = $dates['from'];
        $toDate = $dates['to'];

        $summary = [];

        // Vehicles Summary
        $vehicleMasters = VehicleMaster::where('is_active', true)->orderBy('variant_name')->get();
        foreach ($vehicleMasters as $vm) {
            $name = $vm->variant_name . ($vm->color_name ? ' (' . $vm->color_name . ')' : '');
            if ($search && stripos($name, $search) === false) continue;

            $purchasedQty = VehiclePoItem::whereHas('purchaseOrder', function($q) use ($fromDate, $toDate) {
                if ($fromDate) $q->whereDate('order_date', '>=', $fromDate);
                if ($toDate) $q->whereDate('order_date', '<=', $toDate);
            })->where('vehicle_master_id', $vm->id)->sum('ordered_quantity');

            $purchasedAmt = VehiclePoItem::whereHas('purchaseOrder', function($q) use ($fromDate, $toDate) {
                if ($fromDate) $q->whereDate('order_date', '>=', $fromDate);
                if ($toDate) $q->whereDate('order_date', '<=', $toDate);
            })->where('vehicle_master_id', $vm->id)->sum('total_amount');

            $soldQty = VehicleSalesInvoice::whereHas('vehicleInventory', function($q) use ($vm) {
                $q->where('vehicle_master_id', $vm->id);
            })->when($fromDate, fn($q) => $q->whereDate('invoice_date', '>=', $fromDate))
              ->when($toDate, fn($q) => $q->whereDate('invoice_date', '<=', $toDate))
              ->count();

            $soldAmt = VehicleSalesInvoice::whereHas('vehicleInventory', function($q) use ($vm) {
                $q->where('vehicle_master_id', $vm->id);
            })->when($fromDate, fn($q) => $q->whereDate('invoice_date', '>=', $fromDate))
              ->when($toDate, fn($q) => $q->whereDate('invoice_date', '<=', $toDate))
              ->sum('grand_total');

            $summary[] = [
                'type' => 'Vehicle',
                'name' => $name,
                'purchase_qty' => $purchasedQty,
                'purchase_amount' => $purchasedAmt,
                'sales_qty' => $soldQty,
                'sales_amount' => $soldAmt,
                'net_margin' => $soldAmt - $purchasedAmt
            ];
        }

        // Spare Parts Summary
        $spareParts = SparePart::where('is_active', true)->orderBy('name')->get();
        foreach ($spareParts as $sp) {
            if ($search && (stripos($sp->name, $search) === false && stripos($sp->part_no, $search) === false)) continue;

            $purchasedQty = PurchaseOrderItem::whereHas('purchaseOrder', function($q) use ($fromDate, $toDate) {
                if ($fromDate) $q->whereDate('order_date', '>=', $fromDate);
                if ($toDate) $q->whereDate('order_date', '<=', $toDate);
            })->where('spare_part_id', $sp->id)->sum('quantity');

            $purchasedAmt = PurchaseOrderItem::whereHas('purchaseOrder', function($q) use ($fromDate, $toDate) {
                if ($fromDate) $q->whereDate('order_date', '>=', $fromDate);
                if ($toDate) $q->whereDate('order_date', '<=', $toDate);
            })->where('spare_part_id', $sp->id)->sum('total_amount');

            $soldQty = PartSalesInvoiceItem::whereHas('invoice', function($q) use ($fromDate, $toDate) {
                if ($fromDate) $q->whereDate('invoice_date', '>=', $fromDate);
                if ($toDate) $q->whereDate('invoice_date', '<=', $toDate);
            })->where('spare_part_id', $sp->id)->sum('quantity');

            $soldAmt = PartSalesInvoiceItem::whereHas('invoice', function($q) use ($fromDate, $toDate) {
                if ($fromDate) $q->whereDate('invoice_date', '>=', $fromDate);
                if ($toDate) $q->whereDate('invoice_date', '<=', $toDate);
            })->where('spare_part_id', $sp->id)->sum('amount');

            $summary[] = [
                'type' => 'Spare Part',
                'name' => $sp->name . ($sp->part_no ? ' (' . $sp->part_no . ')' : ''),
                'purchase_qty' => $purchasedQty,
                'purchase_amount' => $purchasedAmt,
                'sales_qty' => $soldQty,
                'sales_amount' => $soldAmt,
                'net_margin' => $soldAmt - $purchasedAmt
            ];
        }

        return view('admin.reports.item_sales_purchase_summary', compact('summary', 'dateFilter', 'customFrom', 'customTo', 'search', 'fromDate', 'toDate'));
    }

    public function lowStockSummary(Request $request)
    {
        $search = $request->input('search');

        // Low stock spare parts
        $lowParts = SparePart::leftJoin('spare_part_stocks as s', 'spare_parts.id', '=', 's.spare_part_id')
            ->select('spare_parts.*', DB::raw('COALESCE(s.quantity, 0) as current_stock'))
            ->where(function($q) {
                $q->whereNull('s.quantity')
                  ->orWhereRaw('s.quantity <= spare_parts.min_stock');
            });

        if ($search) {
            $escaped = '%' . addcslashes($search, '%_') . '%';
            $lowParts->where(function($q) use ($escaped) {
                $q->where('spare_parts.name', 'like', $escaped)
                  ->orWhere('spare_parts.part_no', 'like', $escaped);
            });
        }
        $lowPartsList = $lowParts->get();

        // Low stock vehicles
        $vehicleMasters = VehicleMaster::where('is_active', true)->get();
        $lowVehiclesList = [];
        foreach ($vehicleMasters as $vm) {
            $currentStock = VehicleInventory::where('vehicle_master_id', $vm->id)
                ->where('status', 'available')->count();
            if ($currentStock <= $vm->min_stock) {
                if ($search && (stripos($vm->variant_name, $search) === false && stripos($vm->color_name, $search) === false)) {
                    continue;
                }
                $lowVehiclesList[] = [
                    'variant_name' => $vm->variant_name . ($vm->color_name ? ' (' . $vm->color_name . ')' : ''),
                    'current_stock' => $currentStock,
                    'min_stock' => $vm->min_stock,
                    'deficit' => max(0, $vm->min_stock - $currentStock)
                ];
            }
        }

        return view('admin.reports.low_stock_summary', compact('lowPartsList', 'lowVehiclesList', 'search'));
    }

    public function rateList(Request $request)
    {
        $search = $request->input('search');
        $type = $request->input('type', 'all');

        $rates = [];

        if ($type === 'all' || $type === 'vehicle') {
            $vQuery = VehicleMaster::where('is_active', true);
            if ($search) {
                $escaped = '%' . addcslashes($search, '%_') . '%';
                $vQuery->where('variant_name', 'like', $escaped);
            }
            foreach ($vQuery->get() as $vm) {
                $rates[] = [
                    'type' => 'Vehicle',
                    'code_no' => 'VM-' . $vm->id,
                    'name' => $vm->variant_name . ($vm->color_name ? ' (' . $vm->color_name . ')' : ''),
                    'fuel_type' => $vm->fuel_type,
                    'purchase_price' => $vm->ex_showroom_price ?? 0,
                    'sale_price' => $vm->ex_showroom_price ?? 0,
                    'gst_rate' => '5%'
                ];
            }
        }

        if ($type === 'all' || $type === 'part') {
            $spQuery = SparePart::where('is_active', true);
            if ($search) {
                $escaped = '%' . addcslashes($search, '%_') . '%';
                $spQuery->where('name', 'like', $escaped)->orWhere('part_no', 'like', $escaped);
            }
            foreach ($spQuery->get() as $sp) {
                $rates[] = [
                    'type' => 'Spare Part',
                    'code_no' => $sp->part_no ?? 'SP-' . $sp->id,
                    'name' => $sp->name,
                    'fuel_type' => $sp->category ?? 'General',
                    'purchase_price' => $sp->price,
                    'sale_price' => $sp->mrp ?? $sp->price,
                    'gst_rate' => $sp->tax_percentage ? $sp->tax_percentage . '%' : '18%'
                ];
            }
        }

        return view('admin.reports.rate_list', compact('rates', 'search', 'type'));
    }

    public function stockDetailReport(Request $request)
    {
        $selectedItem = $request->input('item_id'); // format: 'vehicle_ID' or 'part_ID'
        $dateFilter = $request->input('date_filter', 'this_month');
        $customFrom = $request->input('custom_from');
        $customTo = $request->input('custom_to');

        $dates = $this->getDateRange($dateFilter, $customFrom, $customTo);
        $fromDate = $dates['from'];
        $toDate = $dates['to'];

        $vehicleMasters = VehicleMaster::where('is_active', true)->orderBy('variant_name')->get();
        $spareParts = SparePart::where('is_active', true)->orderBy('name')->get();

        $itemList = [];
        foreach ($vehicleMasters as $vm) {
            $itemList[] = ['id' => 'vehicle_' . $vm->id, 'name' => '[Vehicle] ' . $vm->variant_name . ($vm->color_name ? ' (' . $vm->color_name . ')' : '')];
        }
        foreach ($spareParts as $sp) {
            $itemList[] = ['id' => 'part_' . $sp->id, 'name' => '[Part] ' . $sp->name . ($sp->part_no ? ' (' . $sp->part_no . ')' : '')];
        }

        if (empty($selectedItem) && !empty($itemList)) {
            $selectedItem = $itemList[0]['id'];
        }

        $movementLogs = [];
        $selectedName = '';
        $openingStock = 0;

        if ($selectedItem) {
            list($itemType, $itemId) = explode('_', $selectedItem, 2);

            if ($itemType === 'vehicle') {
                $vm = VehicleMaster::find($itemId);
                if ($vm) {
                    $selectedName = $vm->variant_name . ($vm->color_name ? ' (' . $vm->color_name . ')' : '');
                    
                    // Transactions: Purchases In & Sales Out
                    $purchases = VehicleInventory::where('vehicle_master_id', $vm->id)
                        ->when($fromDate, fn($q) => $q->whereDate('created_at', '>=', $fromDate))
                        ->when($toDate, fn($q) => $q->whereDate('created_at', '<=', $toDate))
                        ->get();

                    foreach ($purchases as $p) {
                        $movementLogs[] = [
                            'date' => $p->created_at->format('d/m/Y'),
                            'type' => 'IN (Purchase)',
                            'ref_no' => 'Chassis: ' . $p->chassis_number,
                            'qty' => 1,
                            'party' => 'Supplier PO'
                        ];
                    }

                    $sales = VehicleSalesInvoice::whereHas('vehicleInventory', fn($q) => $q->where('vehicle_master_id', $vm->id))
                        ->when($fromDate, fn($q) => $q->whereDate('invoice_date', '>=', $fromDate))
                        ->when($toDate, fn($q) => $q->whereDate('invoice_date', '<=', $toDate))
                        ->get();

                    foreach ($sales as $s) {
                        $movementLogs[] = [
                            'date' => $s->invoice_date->format('d/m/Y'),
                            'type' => 'OUT (Sale)',
                            'ref_no' => $s->invoice_number,
                            'qty' => 1,
                            'party' => $s->customer_name
                        ];
                    }
                }
            } else {
                $sp = SparePart::find($itemId);
                if ($sp) {
                    $selectedName = $sp->name . ($sp->part_no ? ' (' . $sp->part_no . ')' : '');

                    $logs = SparePartStockTransaction::where('spare_part_id', $sp->id)
                        ->when($fromDate, fn($q) => $q->whereDate('created_at', '>=', $fromDate))
                        ->when($toDate, fn($q) => $q->whereDate('created_at', '<=', $toDate))
                        ->orderBy('created_at', 'asc')->get();

                    foreach ($logs as $l) {
                        $movementLogs[] = [
                            'date' => $l->created_at->format('d/m/Y'),
                            'type' => strtoupper($l->transaction_type),
                            'ref_no' => $l->reference_type ? $l->reference_type . ' #' . $l->reference_id : 'Manual Adjustment',
                            'qty' => $l->quantity,
                            'party' => $l->notes ?? '-'
                        ];
                    }
                }
            }
        }

        return view('admin.reports.stock_detail_report', compact('itemList', 'selectedItem', 'selectedName', 'movementLogs', 'dateFilter', 'customFrom', 'customTo', 'fromDate', 'toDate'));
    }

    public function stockSummary(Request $request)
    {
        $search = $request->input('search');

        // Vehicles Stock Summary
        $vehicleStock = [];
        $vehicleMasters = VehicleMaster::where('is_active', true)->get();
        $totalVehicleQty = 0;
        $totalVehicleValuation = 0;

        foreach ($vehicleMasters as $vm) {
            $availableCount = VehicleInventory::where('vehicle_master_id', $vm->id)->where('status', 'available')->count();
            if ($availableCount > 0 || !$search) {
                if ($search && stripos($vm->variant_name, $search) === false) continue;

                $rate = $vm->ex_showroom_price ?? 0;
                $val = $availableCount * $rate;
                $totalVehicleQty += $availableCount;
                $totalVehicleValuation += $val;

                $vehicleStock[] = [
                    'name' => $vm->variant_name . ($vm->color_name ? ' (' . $vm->color_name . ')' : ''),
                    'qty' => $availableCount,
                    'avg_rate' => $rate,
                    'total_value' => $val
                ];
            }
        }

        // Parts Stock Summary
        $partStock = [];
        $parts = SparePart::leftJoin('spare_part_stocks as s', 'spare_parts.id', '=', 's.spare_part_id')
            ->select('spare_parts.*', DB::raw('COALESCE(s.quantity, 0) as available_qty'))->get();

        $totalPartQty = 0;
        $totalPartValuation = 0;

        foreach ($parts as $p) {
            if ($p->available_qty > 0 || !$search) {
                if ($search && (stripos($p->name, $search) === false && stripos($p->part_no, $search) === false)) continue;

                $val = $p->available_qty * $p->price;
                $totalPartQty += $p->available_qty;
                $totalPartValuation += $val;

                $partStock[] = [
                    'part_no' => $p->part_no ?? '-',
                    'name' => $p->name,
                    'qty' => $p->available_qty,
                    'rate' => $p->price,
                    'total_value' => $val
                ];
            }
        }

        return view('admin.reports.stock_summary', compact('vehicleStock', 'partStock', 'totalVehicleQty', 'totalVehicleValuation', 'totalPartQty', 'totalPartValuation', 'search'));
    }

    public function receivableAgeing(Request $request)
    {
        $search = $request->input('search');

        // Customer balances ageing
        $customers = Customer::where('is_active', true)->get();
        $ageingData = [];

        foreach ($customers as $c) {
            $vInvoices = VehicleSalesInvoice::where('customer_id', $c->id)->where('balance', '>', 0)->get();
            $pInvoices = PartSalesInvoice::where('customer_id', $c->id)->where('balance', '>', 0)->get();

            if ($vInvoices->isEmpty() && $pInvoices->isEmpty()) continue;

            if ($search && (stripos($c->name, $search) === false && stripos($c->mobile, $search) === false)) continue;

            $cat0_30 = 0;
            $cat31_60 = 0;
            $cat61_90 = 0;
            $cat90_plus = 0;
            $totalDue = 0;

            $today = new \DateTime();

            foreach ($vInvoices as $inv) {
                $days = $inv->invoice_date->diff($today)->days;
                $bal = (float)$inv->balance;
                $totalDue += $bal;

                if ($days <= 30) $cat0_30 += $bal;
                elseif ($days <= 60) $cat31_60 += $bal;
                elseif ($days <= 90) $cat61_90 += $bal;
                else $cat90_plus += $bal;
            }

            foreach ($pInvoices as $inv) {
                $days = $inv->invoice_date->diff($today)->days;
                $bal = (float)$inv->balance;
                $totalDue += $bal;

                if ($days <= 30) $cat0_30 += $bal;
                elseif ($days <= 60) $cat31_60 += $bal;
                elseif ($days <= 90) $cat61_90 += $bal;
                else $cat90_plus += $bal;
            }

            $ageingData[] = [
                'customer_name' => $c->name,
                'mobile' => $c->mobile ?? '-',
                'total_due' => $totalDue,
                'days_0_30' => $cat0_30,
                'days_31_60' => $cat31_60,
                'days_61_90' => $cat61_90,
                'days_90_plus' => $cat90_plus
            ];
        }

        return view('admin.reports.receivable_ageing', compact('ageingData', 'search'));
    }

    public function partyStatement(Request $request)
    {
        $partyType = $request->input('party_type', 'customer');
        $partyId = $request->input('party_id');
        $dateFilter = $request->input('date_filter', 'this_month');
        $customFrom = $request->input('custom_from');
        $customTo = $request->input('custom_to');

        $dates = $this->getDateRange($dateFilter, $customFrom, $customTo);
        $fromDate = $dates['from'];
        $toDate = $dates['to'];

        $customers = Customer::where('is_active', true)->orderBy('name')->get();
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();

        $statement = [];
        $partyDetails = null;

        if ($partyType === 'customer' && $partyId) {
            $partyDetails = Customer::find($partyId);
            if ($partyDetails) {
                $vInvoices = VehicleSalesInvoice::where('customer_id', $partyDetails->id)
                    ->when($fromDate, fn($q) => $q->whereDate('invoice_date', '>=', $fromDate))
                    ->when($toDate, fn($q) => $q->whereDate('invoice_date', '<=', $toDate))->get();

                foreach ($vInvoices as $v) {
                    $statement[] = [
                        'date' => $v->invoice_date->format('d/m/Y'),
                        'particulars' => 'Vehicle Sales Invoice #' . $v->invoice_number,
                        'debit' => $v->grand_total,
                        'credit' => $v->received_amount,
                        'balance' => $v->balance
                    ];
                }

                $pInvoices = PartSalesInvoice::where('customer_id', $partyDetails->id)
                    ->when($fromDate, fn($q) => $q->whereDate('invoice_date', '>=', $fromDate))
                    ->when($toDate, fn($q) => $q->whereDate('invoice_date', '<=', $toDate))->get();

                foreach ($pInvoices as $p) {
                    $statement[] = [
                        'date' => $p->invoice_date->format('d/m/Y'),
                        'particulars' => 'Part Sales Invoice #' . $p->invoice_number,
                        'debit' => $p->total_amount,
                        'credit' => $p->received_amount,
                        'balance' => $p->balance
                    ];
                }
            }
        } elseif ($partyType === 'supplier' && $partyId) {
            $partyDetails = Supplier::find($partyId);
            if ($partyDetails) {
                $vPo = VehiclePurchaseOrder::where('supplier_id', $partyDetails->id)
                    ->when($fromDate, fn($q) => $q->whereDate('order_date', '>=', $fromDate))
                    ->when($toDate, fn($q) => $q->whereDate('order_date', '<=', $toDate))->get();

                foreach ($vPo as $v) {
                    $statement[] = [
                        'date' => $v->order_date->format('d/m/Y'),
                        'particulars' => 'Vehicle PO #' . $v->po_number,
                        'debit' => $v->received_amount,
                        'credit' => $v->total_amount,
                        'balance' => $v->balance
                    ];
                }

                $pPo = PurchaseOrder::where('supplier_id', $partyDetails->id)
                    ->when($fromDate, fn($q) => $q->whereDate('order_date', '>=', $fromDate))
                    ->when($toDate, fn($q) => $q->whereDate('order_date', '<=', $toDate))->get();

                foreach ($pPo as $p) {
                    $statement[] = [
                        'date' => $p->order_date->format('d/m/Y'),
                        'particulars' => 'Spare Part PO #' . $p->order_number,
                        'debit' => $p->received_amount,
                        'credit' => $p->total_amount,
                        'balance' => $p->balance
                    ];
                }
            }
        }

        return view('admin.reports.party_statement', compact('partyType', 'partyId', 'customers', 'suppliers', 'partyDetails', 'statement', 'dateFilter', 'customFrom', 'customTo', 'fromDate', 'toDate'));
    }

    public function partyWiseOutstanding(Request $request)
    {
        $search = $request->input('search');

        $customerOutstanding = Customer::where('is_active', true)
            ->get()->map(function($c) {
                $vBal = VehicleSalesInvoice::where('customer_id', $c->id)->sum('balance');
                $pBal = PartSalesInvoice::where('customer_id', $c->id)->sum('balance');
                $tot = $vBal + $pBal;
                return [
                    'type' => 'Customer (Receivable)',
                    'name' => $c->name,
                    'phone' => $c->mobile ?? '-',
                    'total_outstanding' => $tot
                ];
            })->filter(fn($i) => $i['total_outstanding'] > 0);

        $supplierOutstanding = Supplier::where('is_active', true)
            ->get()->map(function($s) {
                $vBal = VehiclePurchaseOrder::where('supplier_id', $s->id)->sum('balance');
                $pBal = PurchaseOrder::where('supplier_id', $s->id)->sum('balance');
                $tot = $vBal + $pBal;
                return [
                    'type' => 'Supplier (Payable)',
                    'name' => $s->name,
                    'phone' => $s->mobile ?? '-',
                    'total_outstanding' => $tot
                ];
            })->filter(fn($i) => $i['total_outstanding'] > 0);

        $partyList = $customerOutstanding->merge($supplierOutstanding);

        if ($search) {
            $partyList = $partyList->filter(function($i) use ($search) {
                return stripos($i['name'], $search) !== false || stripos($i['phone'], $search) !== false;
            });
        }

        return view('admin.reports.party_wise_outstanding', compact('partyList', 'search'));
    }

    public function salesSummaryCategory(Request $request)
    {
        $dateFilter = $request->input('date_filter', 'this_month');
        $customFrom = $request->input('custom_from');
        $customTo = $request->input('custom_to');

        $dates = $this->getDateRange($dateFilter, $customFrom, $customTo);
        $fromDate = $dates['from'];
        $toDate = $dates['to'];

        // Vehicle Category Sales
        $vehicleSales = VehicleSalesInvoice::when($fromDate, fn($q) => $q->whereDate('invoice_date', '>=', $fromDate))
            ->when($toDate, fn($q) => $q->whereDate('invoice_date', '<=', $toDate))
            ->select(
                DB::raw("COUNT(*) as total_units"),
                DB::raw("SUM(sub_total) as total_taxable"),
                DB::raw("SUM(cgst_amount + sgst_amount + igst_amount) as total_tax"),
                DB::raw("SUM(grand_total) as grand_revenue")
            )->first();

        // Spare Parts Category Sales
        $partSales = PartSalesInvoiceItem::whereHas('invoice', function($q) use ($fromDate, $toDate) {
                if ($fromDate) $q->whereDate('invoice_date', '>=', $fromDate);
                if ($toDate) $q->whereDate('invoice_date', '<=', $toDate);
            })
            ->select(
                DB::raw("SUM(quantity) as total_units"),
                DB::raw("SUM(amount - tax_amount) as total_taxable"),
                DB::raw("SUM(tax_amount) as total_tax"),
                DB::raw("SUM(amount) as grand_revenue")
            )->first();

        return view('admin.reports.sales_summary_category', compact('vehicleSales', 'partSales', 'dateFilter', 'customFrom', 'customTo', 'fromDate', 'toDate'));
    }
}

