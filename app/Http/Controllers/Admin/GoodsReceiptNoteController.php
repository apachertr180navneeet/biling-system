<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GoodsReceiptNote;
use App\Models\GrnItem;
use App\Models\PurchaseOrder;
use App\Models\SparePartStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GoodsReceiptNoteController extends Controller
{
    public function index()
    {
        $grns = GoodsReceiptNote::with('purchaseOrder.supplier', 'items')->orderBy('created_at', 'desc')->paginate(20);
        return view('admin.goods_receipt_notes.index', compact('grns'));
    }

    public function create()
    {
        $purchaseOrders = PurchaseOrder::with('supplier', 'items.sparePart')
            ->whereIn('status', ['pending', 'partial'])
            ->orderBy('created_at', 'desc')->get();
        return view('admin.goods_receipt_notes.create', compact('purchaseOrders'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'purchase_order_id' => 'nullable|exists:purchase_orders,id',
            'received_date' => 'required|date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.spare_part_id' => 'required|exists:spare_parts,id',
            'items.*.accepted_quantity' => 'required|integer|min:0',
            'items.*.rejected_quantity' => 'required|integer|min:0',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        $lastGrn = GoodsReceiptNote::orderBy('id', 'desc')->first();
        $nextId = $lastGrn ? $lastGrn->id + 1 : 1;
        $data['grn_number'] = 'GRN-' . date('Ymd') . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
        $data['received_by'] = Auth::id();

        $items = $data['items'];
        unset($data['items']);

        DB::transaction(function () use ($data, $items) {
            $grn = GoodsReceiptNote::create($data);

            foreach ($items as $item) {
                $poItem = null;
                if ($data['purchase_order_id']) {
                    $poItem = \App\Models\PurchaseOrderItem::where('purchase_order_id', $data['purchase_order_id'])
                        ->where('spare_part_id', $item['spare_part_id'])->first();
                }

                $grnItem = GrnItem::create([
                    'grn_id' => $grn->id,
                    'purchase_order_item_id' => $poItem?->id,
                    'spare_part_id' => $item['spare_part_id'],
                    'ordered_quantity' => $poItem?->quantity ?? 0,
                    'received_quantity' => $item['accepted_quantity'] + $item['rejected_quantity'],
                    'accepted_quantity' => $item['accepted_quantity'],
                    'rejected_quantity' => $item['rejected_quantity'],
                    'unit_price' => $item['unit_price'],
                ]);

                if ($poItem) {
                    $poItem->increment('received_quantity', $item['accepted_quantity'] + $item['rejected_quantity']);
                }

                if ($item['accepted_quantity'] > 0) {
                    SparePartStock::updateOrCreate(
                        ['spare_part_id' => $item['spare_part_id']],
                        ['quantity' => DB::raw('quantity + ' . $item['accepted_quantity'])]
                    );
                }
            }

            if ($data['purchase_order_id']) {
                $po = PurchaseOrder::find($data['purchase_order_id']);
                $totalOrdered = $po->items()->sum('quantity');
                $totalReceived = $po->items()->sum('received_quantity');
                if ($totalReceived >= $totalOrdered) {
                    $po->update(['status' => 'received']);
                } else {
                    $po->update(['status' => 'partial']);
                }
            }

            $grn->update(['status' => 'completed']);
        });

        return redirect()->route('admin.goods-receipt-notes.index')->withSuccess('GRN created successfully.');
    }

    public function show(GoodsReceiptNote $goodsReceiptNote)
    {
        $goodsReceiptNote->load('purchaseOrder.supplier', 'items.sparePart.category', 'receivedBy');
        return view('admin.goods_receipt_notes.show', compact('goodsReceiptNote'));
    }

    public function destroy(GoodsReceiptNote $goodsReceiptNote)
    {
        $goodsReceiptNote->delete();
        return response()->json(['success' => true, 'message' => 'GRN deleted successfully.']);
    }

    public function toggleStatus(GoodsReceiptNote $goodsReceiptNote)
    {
        $goodsReceiptNote->update(['is_active' => !$goodsReceiptNote->is_active]);
        return response()->json(['success' => true, 'is_active' => $goodsReceiptNote->fresh()->is_active]);
    }
}
