<?php

namespace App\Http\Controllers\Admin\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InventoryStock;
use App\Models\Hotel;
use Yajra\DataTables\Facades\DataTables;

class StockController extends Controller
{
    public function index()
    {
        $hotels = Hotel::where('status', 'active')->get();
        return view('admin.inventory.stocks.index', compact('hotels'));
    }

    public function data(Request $request)
    {
        $query = InventoryStock::query()->with(['item.category', 'item.unit', 'hotel']);

        return DataTables::of($query)
            ->filterColumn('item_name', function ($query, $value) {
                $query->whereHas('item', function ($q) use ($value) {
                    $q->where('name', 'like', "%{$value}%");
                });
            })
            ->filterColumn('hotel_name', function ($query, $value) {
                $query->whereHas('hotel', function ($q) use ($value) {
                    $q->where('name', 'like', "%{$value}%");
                });
            })
            ->addColumn('hotel_name', fn($s) => $s->hotel->name ?? '')
            ->addColumn('item_name', fn($s) => $s->item->name ?? '')
            ->addColumn('category_name', fn($s) => $s->item->category->name ?? '')
            ->addColumn('unit_name', fn($s) => $s->item->unit->name ?? '')
            ->addColumn('available', fn($s) => $s->quantity - $s->reserved_quantity)
            ->addColumn('avg_cost', fn($s) => number_format($s->average_cost, 2))
            ->addColumn('status', fn($s) => ($s->quantity - $s->reserved_quantity) <= ($s->item->min_stock ?? 0) ? 'low' : 'ok')
            ->rawColumns([])
            ->make(true);
    }
}
