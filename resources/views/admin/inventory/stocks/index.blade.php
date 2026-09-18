@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-package"></i></div>
            <div>
                <h4 class="m-page-title">Current Stock</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Inventory</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">Stock</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <table id="stock-table" class="table table-bordered table-striped" style="width:100%">
                <thead><tr><th>#</th><th>Hotel</th><th>Item</th><th>Category</th><th>Unit</th><th>Qty</th><th>Reserved</th><th>Available</th><th>Avg Cost</th><th>Status</th></tr></thead>
            </table>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
function stockAvailable(data, type, row) {
    var available = row.quantity - row.reserved_quantity;
    return available;
}

function stockStatusBadge(data, type, row) {
    if (row.quantity <= row.min_stock) {
        return '<span class="badge bg-label-danger">Low Stock</span>';
    }
    return '<span class="badge bg-label-success">In Stock</span>';
}

@if(auth()->user()->hasPermission('inventory_stocks.view'))
var table = $('#stock-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: "{{ route('admin.inventory.stocks.data') }}",
    columns: [
        { data: 'id', name: 'id', orderable: false, searchable: false },
        { data: 'hotel_name', name: 'hotel_name' },
        { data: 'item_name', name: 'item_name' },
        { data: 'category_name', name: 'category_name' },
        { data: 'unit_name', name: 'unit_name' },
        { data: 'quantity', name: 'quantity' },
        { data: 'reserved_quantity', name: 'reserved_quantity' },
        { data: 'available', name: 'available', orderable: false, searchable: false, render: function(data, type, row) {
            return stockAvailable(data, type, row);
        }},
        { data: 'avg_cost', name: 'avg_cost' },
        { data: 'status', name: 'status', orderable: false, searchable: false, render: function(data, type, row) {
            return stockStatusBadge(data, type, row);
        }}
    ],
    order: [[0, 'desc']]
});
@endif
</script>
@endsection