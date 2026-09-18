@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-file"></i></div>
            <div>
                <h4 class="m-page-title">AMC Contracts</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Maintenance</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">AMC</li>
                </ul>
            </div>
        </div>
        @if(auth()->user()->hasPermission('maintenance_amcs.create'))
        <a href="{{ route('admin.maintenance.amcs.create') }}" class="btn btn-primary m-btn-add"><i class="bx bx-plus"></i> Add AMC</a>
        @endif
    </div>

    <div class="card">
        <div class="card-body">
            <table id="amcs-table" class="table table-bordered table-striped" style="width:100%">
                <thead><tr><th>#</th><th>Contract #</th><th>Asset</th><th>Vendor</th><th>Start Date</th><th>End Date</th><th>Cost</th><th>Status</th><th>Actions</th></tr></thead>
            </table>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
var statusColors = { active: 'success', expired: 'warning', cancelled: 'danger' };

function amcStatusBadge(status, statusUrl) {
    if (!status) return '<span class="badge bg-label-secondary">-</span>';
    var label = status.charAt(0).toUpperCase() + status.slice(1);
    @if(auth()->user()->hasPermission('maintenance_amcs.edit'))
    if (status !== 'cancelled') {
        return '<button type="button" class="btn btn-sm btn-status-toggle btn-' + (statusColors[status] || 'secondary') + '" data-url="' + statusUrl + '">' + label + '</button>';
    }
    @endif
    return '<span class="badge bg-label-' + (statusColors[status] || 'secondary') + '">' + label + '</span>';
}

function formatCurrency(data) {
    return parseFloat(data).toLocaleString('en', {minimumFractionDigits: 2});
}

function amcActions(row) {
    var html = '<div class="btn-group btn-group-sm">';
    @if(auth()->user()->hasPermission('maintenance_amcs.view'))
    html += '<a href="' + row.show_url + '" class="btn btn-outline-info"><i class="bx bx-show"></i></a>';
    @endif
    @if(auth()->user()->hasPermission('maintenance_amcs.edit'))
    html += '<a href="' + row.edit_url + '" class="btn btn-outline-primary"><i class="bx bx-edit"></i></a>';
    @endif
    @if(auth()->user()->hasPermission('maintenance_amcs.delete'))
    html += '<button type="button" class="btn btn-outline-danger btn-delete-item" data-url="' + row.delete_url + '" data-name="' + row.contract_number + '"><i class="bx bx-trash"></i></button>';
    @endif
    html += '</div>';
    return html;
}

var table = $('#amcs-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: "{{ route('admin.maintenance.amcs.data') }}",
    columns: [
        { data: 'id', name: 'id', orderable: false, searchable: false },
        { data: 'contract_number', name: 'contract_number' },
        { data: 'asset_name', name: 'asset_name', orderable: false, searchable: false },
        { data: 'vendor_name', name: 'vendor_name', orderable: false, searchable: false },
        { data: 'start_date', name: 'start_date', render: function(data) { if (!data) return '-'; var d = new Date(data); return ('0' + d.getDate()).slice(-2) + '-' + ('0' + (d.getMonth()+1)).slice(-2) + '-' + d.getFullYear(); } },
        { data: 'end_date', name: 'end_date', render: function(data) { if (!data) return '-'; var d = new Date(data); return ('0' + d.getDate()).slice(-2) + '-' + ('0' + (d.getMonth()+1)).slice(-2) + '-' + d.getFullYear(); } },
        { data: 'cost', name: 'cost', render: function(data) { return '₹' + formatCurrency(data); } },
        { data: 'status', name: 'status', orderable: false, searchable: false, render: function(data, type, row) {
            return amcStatusBadge(data, row.status_url);
        }},
        { data: 'actions', name: 'actions', orderable: false, searchable: false, render: function(data, type, row) {
            return amcActions(row);
        }}
    ],
    order: [[0, 'desc']]
});
</script>
@endsection
