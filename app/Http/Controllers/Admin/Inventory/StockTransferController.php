<?php

namespace App\Http\Controllers\Admin\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InventoryStockTransfer;
use App\Models\InventoryStockTransferItem;
use App\Models\InventoryItem;
use App\Models\Hotel;
use App\Helpers\Helper;
use Yajra\DataTables\Facades\DataTables;
use Exception;

class StockTransferController extends Controller
{
    public function index()
    {
        $hotels = Hotel::where('status', 'active')->get();
        return view('admin.inventory.stock-transfers.index', compact('hotels'));
    }

    public function data(Request $request)
    {
        $query = InventoryStockTransfer::query()->with(['createdBy', 'approvedBy']);

        return DataTables::of($query)
            ->filterColumn('transfer_number', function ($query, $value) {
                $query->where('transfer_number', 'like', "%{$value}%");
            })
            ->filterColumn('hotel_name', function ($query, $value) {
                $query->whereHas('hotel', function ($q) use ($value) {
                    $q->where('name', 'like', "%{$value}%");
                });
            })
            ->addColumn('transfer_no', fn($t) => $t->transfer_number ?? '')
            ->addColumn('hotel_name', fn($t) => $t->hotel->name ?? '')
            ->addColumn('transfer_status_badge', function ($row) {
                $colors = ['pending' => 'secondary', 'in_transit' => 'info', 'completed' => 'success', 'cancelled' => 'danger'];
                $color = $colors[$row->transfer_status] ?? 'secondary';
                return '<span class="badge bg-label-' . $color . '">' . str_replace('_', ' ', ucfirst($row->transfer_status)) . '</span>';
            })
            ->addColumn('created_by_name', fn($t) => $t->createdBy->name ?? '')
            ->addColumn('status_url', fn($t) => route('admin.inventory.stock-transfers.status', $t))
            ->addColumn('edit_url', fn($t) => route('admin.inventory.stock-transfers.edit', $t))
            ->addColumn('delete_url', fn($t) => route('admin.inventory.stock-transfers.destroy', $t))
            ->rawColumns(['transfer_status_badge'])
            ->make(true);
    }

    public function create()
    {
        $stockTransfer = null;
        $hotels = Hotel::where('status', 'active')->get();
        $items = InventoryItem::where('status', 'active')->get();
        return view('admin.inventory.stock-transfers.form', compact('stockTransfer', 'hotels', 'items'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'hotel_id' => 'required|exists:hotels,id',
                'transfer_date' => 'required|date',
                'from_location' => 'required|string|max:255',
                'to_location' => 'required|string|max:255',
                'notes' => 'nullable|string',
                'transfer_status' => 'required|in:pending,in_transit,completed,cancelled',
                'items' => 'required|array|min:1',
                'items.*.item_id' => 'required|exists:inventory_items,id',
                'items.*.quantity_sent' => 'required|integer|min:1',
            ]);

            $data = $request->all();
            $data['transfer_number'] = 'ST-' . date('YmdHis');
            $data['slug'] = Helper::slug('inventory_stock_transfers', $data['transfer_number']);
            $data['created_by'] = auth()->id();

            $transfer = InventoryStockTransfer::create(collect($data)->only([
                'hotel_id', 'transfer_number', 'slug', 'transfer_date',
                'from_location', 'to_location', 'notes', 'transfer_status', 'created_by',
            ])->toArray());

            foreach ($request->items as $item) {
                $transfer->items()->create([
                    'item_id' => $item['item_id'],
                    'quantity_sent' => $item['quantity_sent'],
                    'quantity_received' => $item['quantity_sent'],
                    'notes' => $item['notes'] ?? null,
                ]);
            }

            return redirect()->route('admin.inventory.stock-transfers.index')->with('success', 'Stock Transfer created successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit(InventoryStockTransfer $stockTransfer)
    {
        $hotels = Hotel::where('status', 'active')->get();
        $items = InventoryItem::where('status', 'active')->get();
        $stockTransfer->load('items');
        return view('admin.inventory.stock-transfers.form', compact('stockTransfer', 'hotels', 'items'));
    }

    public function update(Request $request, InventoryStockTransfer $stockTransfer)
    {
        try {
            $request->validate([
                'hotel_id' => 'required|exists:hotels,id',
                'transfer_date' => 'required|date',
                'from_location' => 'required|string|max:255',
                'to_location' => 'required|string|max:255',
                'notes' => 'nullable|string',
                'transfer_status' => 'required|in:pending,in_transit,completed,cancelled',
                'items' => 'required|array|min:1',
                'items.*.item_id' => 'required|exists:inventory_items,id',
                'items.*.quantity_sent' => 'required|integer|min:1',
            ]);

            $stockTransfer->update(collect($request->all())->only([
                'hotel_id', 'transfer_date', 'from_location', 'to_location',
                'notes', 'transfer_status',
            ])->toArray());

            $stockTransfer->items()->delete();
            foreach ($request->items as $item) {
                $stockTransfer->items()->create([
                    'item_id' => $item['item_id'],
                    'quantity_sent' => $item['quantity_sent'],
                    'quantity_received' => $item['quantity_sent'],
                    'notes' => $item['notes'] ?? null,
                ]);
            }

            return redirect()->route('admin.inventory.stock-transfers.index')->with('success', 'Stock Transfer updated successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(Request $request, InventoryStockTransfer $stockTransfer)
    {
        try {
            $stockTransfer->items()->delete();
            $stockTransfer->delete();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Stock Transfer deleted successfully!']);
            }
            return redirect()->route('admin.inventory.stock-transfers.index')->with('success', 'Stock Transfer deleted successfully!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    public function status(Request $request, InventoryStockTransfer $stockTransfer)
    {
        try {
            $statuses = ['pending', 'in_transit', 'completed', 'cancelled'];
            $currentIndex = array_search($stockTransfer->transfer_status, $statuses);
            $nextIndex = ($currentIndex + 1) % count($statuses);
            $stockTransfer->transfer_status = $statuses[$nextIndex];

            if ($stockTransfer->transfer_status === 'completed') {
                $stockTransfer->approved_by = auth()->id();
                $stockTransfer->approved_at = now();
            }

            $stockTransfer->save();
            if ($request->ajax()) {
                return response()->json(['success' => true, 'status' => $stockTransfer->transfer_status, 'message' => 'Status updated successfully!']);
            }
            return redirect()->back()->with('success', 'Status updated successfully!');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }
}
