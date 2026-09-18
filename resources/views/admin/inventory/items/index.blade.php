@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-package"></i></div>
            <div>
                <h4 class="m-page-title">Inventory Items</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Inventory</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">Items</li>
                </ul>
            </div>
        </div>
        @if(auth()->user()->hasPermission('inventory_items.create'))
        <a href="{{ route('admin.inventory.items.create') }}" class="btn btn-primary m-btn-add"><i class="bx bx-plus"></i> Add Item</a>
        @endif
    </div>

    <div class="card">
        <div class="card-body">
            <table id="item-table" class="table table-bordered table-striped" style="width:100%">
                <thead><tr><th>#</th><th>Hotel</th><th>Name</th><th>SKU</th><th>Category</th><th>Unit</th><th>Cost Price</th><th>Sell Price</th><th>Status</th><th>Actions</th></tr></thead>
            </table>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
function itemStatusBadge(status, statusUrl) {
    @if(auth()->user()->hasPermission('inventory_items.edit'))
    return '<button type="button" class="btn btn-sm btn-status-toggle btn-' + (status === 'active' ? 'success' : 'warning') + '" data-url="' + statusUrl + '">' + status.charAt(0).toUpperCase() + status.slice(1) + '</button>';
    @else
    return '<span class="badge bg-label-' + (status === 'active' ? 'success' : 'warning') + '">' + status.charAt(0).toUpperCase() + status.slice(1) + '</span>';
    @endif
}

function itemActions(row) {
    var html = '<div class="btn-group btn-group-sm">';
    @if(auth()->user()->hasPermission('inventory_items.edit'))
    html += '<a href="' + row.edit_url + '" class="btn btn-outline-primary"><i class="bx bx-edit"></i></a>';
    @endif
    @if(auth()->user()->hasPermission('inventory_items.delete'))
    html += '<button type="button" class="btn btn-outline-danger btn-delete-item" data-url="' + row.delete_url + '" data-name="' + row.name + '"><i class="bx bx-trash"></i></button>';
    @endif
    html += '</div>';
    return html;
}

var table = $('#item-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: "{{ route('admin.inventory.items.data') }}",
    columns: [
        { data: 'id', name: 'id', orderable: false, searchable: false },
        { data: 'hotel_name', name: 'hotel.name' },
        { data: 'name', name: 'name' },
        { data: 'sku', name: 'sku' },
        { data: 'category_name', name: 'category.name' },
        { data: 'unit_short_name', name: 'unit.name' },
        { data: 'cost_price', name: 'cost_price' },
        { data: 'sell_price', name: 'sell_price' },
        { data: 'status', name: 'status', orderable: false, searchable: false, render: function(data, type, row) {
            return itemStatusBadge(data, row.status_url);
        }},
        { data: 'actions', name: 'actions', orderable: false, searchable: false, render: function(data, type, row) {
            return itemActions(row);
        }}
    ],
    order: [[0, 'desc']]
});
</script>
@endsection
