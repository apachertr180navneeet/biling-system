@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-transfer"></i></div>
            <div>
                <h4 class="m-page-title">Stock Transfers</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Inventory</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">Stock Transfers</li>
                </ul>
            </div>
        </div>
        @if(auth()->user()->hasPermission('inventory_stock_transfers.create'))
        <a href="{{ route('admin.inventory.stock-transfers.create') }}" class="btn btn-primary m-btn-add"><i class="bx bx-plus"></i> Add Stock Transfer</a>
        @endif
    </div>

    <div class="card">
        <div class="card-body">
            <table id="stock-transfer-table" class="table table-bordered table-striped" style="width:100%">
                <thead><tr><th>#</th><th>Transfer No</th><th>Hotel</th><th>Date</th><th>From</th><th>To</th><th>Status</th><th>Actions</th></tr></thead>
            </table>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
function stockTransferStatusBadge(status, statusUrl) {
    @if(auth()->user()->hasPermission('inventory_stock_transfers.edit'))
    return '<button type="button" class="btn btn-sm btn-status-toggle btn-' + (status === 'active' ? 'success' : 'warning') + '" data-url="' + statusUrl + '">' + status.charAt(0).toUpperCase() + status.slice(1) + '</button>';
    @else
    return '<span class="badge bg-label-' + (status === 'active' ? 'success' : 'warning') + '">' + status.charAt(0).toUpperCase() + status.slice(1) + '</span>';
    @endif
}

function stockTransferActions(row) {
    var html = '<div class="btn-group btn-group-sm">';
    @if(auth()->user()->hasPermission('inventory_stock_transfers.edit'))
    html += '<a href="' + row.edit_url + '" class="btn btn-outline-primary"><i class="bx bx-edit"></i></a>';
    @endif
    @if(auth()->user()->hasPermission('inventory_stock_transfers.delete'))
    html += '<button type="button" class="btn btn-outline-danger btn-delete-item" data-url="' + row.delete_url + '" data-name="' + row.transfer_no + '"><i class="bx bx-trash"></i></button>';
    @endif
    html += '</div>';
    return html;
}

var table = $('#stock-transfer-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: "{{ route('admin.inventory.stock-transfers.data') }}",
    columns: [
        { data: 'id', name: 'id', orderable: false, searchable: false },
        { data: 'transfer_no', name: 'transfer_no' },
        { data: 'hotel_name', name: 'hotel_name' },
        { data: 'transfer_date', name: 'transfer_date', render: function(data) { if (!data) return '-'; var d = new Date(data); return ('0' + d.getDate()).slice(-2) + '-' + ('0' + (d.getMonth()+1)).slice(-2) + '-' + d.getFullYear(); } },
        { data: 'from_location', name: 'from_location' },
        { data: 'to_location', name: 'to_location' },
        { data: 'status', name: 'status', orderable: false, searchable: false, render: function(data, type, row) {
            return stockTransferStatusBadge(data, row.status_url);
        }},
        { data: 'actions', name: 'actions', orderable: false, searchable: false, render: function(data, type, row) {
            return stockTransferActions(row);
        }}
    ],
    order: [[0, 'desc']]
});
</script>
@endsection