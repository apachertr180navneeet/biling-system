@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-slider"></i></div>
            <div>
                <h4 class="m-page-title">Stock Adjustments</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Inventory</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">Stock Adjustments</li>
                </ul>
            </div>
        </div>
        @if(auth()->user()->hasPermission('inventory_stock_adjustments.create'))
        <a href="{{ route('admin.inventory.stock-adjustments.create') }}" class="btn btn-primary m-btn-add"><i class="bx bx-plus"></i> Add Stock Adjustment</a>
        @endif
    </div>

    <div class="card">
        <div class="card-body">
            <table id="stock-adjustment-table" class="table table-bordered table-striped" style="width:100%">
                <thead><tr><th>#</th><th>Adj No</th><th>Hotel</th><th>Item</th><th>Date</th><th>Type</th><th>Qty Before</th><th>Adj Qty</th><th>Qty After</th><th>Status</th><th>Actions</th></tr></thead>
            </table>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
function stockAdjustmentTypeBadge(type) {
    var colors = {
        'addition': 'success',
        'subtraction': 'warning',
        'damage': 'danger',
        'expired': 'info',
        'theft': 'danger',
        'correction': 'primary'
    };
    var color = colors[type] || 'secondary';
    return '<span class="badge bg-label-' + color + '">' + type.charAt(0).toUpperCase() + type.slice(1) + '</span>';
}

function stockAdjustmentStatusBadge(status, statusUrl) {
    @if(auth()->user()->hasPermission('inventory_stock_adjustments.edit'))
    return '<button type="button" class="btn btn-sm btn-status-toggle btn-' + (status === 'active' ? 'success' : 'warning') + '" data-url="' + statusUrl + '">' + status.charAt(0).toUpperCase() + status.slice(1) + '</button>';
    @else
    return '<span class="badge bg-label-' + (status === 'active' ? 'success' : 'warning') + '">' + status.charAt(0).toUpperCase() + status.slice(1) + '</span>';
    @endif
}

function stockAdjustmentActions(row) {
    var html = '<div class="btn-group btn-group-sm">';
    @if(auth()->user()->hasPermission('inventory_stock_adjustments.edit'))
    html += '<a href="' + row.edit_url + '" class="btn btn-outline-primary"><i class="bx bx-edit"></i></a>';
    @endif
    @if(auth()->user()->hasPermission('inventory_stock_adjustments.delete'))
    html += '<button type="button" class="btn btn-outline-danger btn-delete-item" data-url="' + row.delete_url + '" data-name="' + row.adjustment_no + '"><i class="bx bx-trash"></i></button>';
    @endif
    html += '</div>';
    return html;
}

var table = $('#stock-adjustment-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: "{{ route('admin.inventory.stock-adjustments.data') }}",
    columns: [
        { data: 'id', name: 'id', orderable: false, searchable: false },
        { data: 'adjustment_no', name: 'adjustment_no' },
        { data: 'hotel_name', name: 'hotel_name' },
        { data: 'item_name', name: 'item_name' },
        { data: 'adjustment_date', name: 'adjustment_date', render: function(data) { if (!data) return '-'; var d = new Date(data); return ('0' + d.getDate()).slice(-2) + '-' + ('0' + (d.getMonth()+1)).slice(-2) + '-' + d.getFullYear(); } },
        { data: 'adjustment_type', name: 'adjustment_type', orderable: false, searchable: false, render: function(data) {
            return stockAdjustmentTypeBadge(data);
        }},
        { data: 'quantity_before', name: 'quantity_before' },
        { data: 'adjustment_quantity', name: 'adjustment_quantity' },
        { data: 'quantity_after', name: 'quantity_after' },
        { data: 'status', name: 'status', orderable: false, searchable: false, render: function(data, type, row) {
            return stockAdjustmentStatusBadge(data, row.status_url);
        }},
        { data: 'actions', name: 'actions', orderable: false, searchable: false, render: function(data, type, row) {
            return stockAdjustmentActions(row);
        }}
    ],
    order: [[0, 'desc']]
});
</script>
@endsection