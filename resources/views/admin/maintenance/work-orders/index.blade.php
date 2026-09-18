@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-task"></i></div>
            <div>
                <h4 class="m-page-title">Work Orders</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Maintenance</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">Work Orders</li>
                </ul>
            </div>
        </div>
        @if(auth()->user()->hasPermission('maintenance_work_orders.create'))
        <a href="{{ route('admin.maintenance.work-orders.create') }}" class="btn btn-primary m-btn-add"><i class="bx bx-plus"></i> Create Work Order</a>
        @endif
    </div>

    <div class="card">
        <div class="card-body">
            <table id="work-orders-table" class="table table-bordered table-striped" style="width:100%">
                <thead><tr><th>#</th><th>WO Number</th><th>Title</th><th>Asset</th><th>Type</th><th>Priority</th><th>Assigned To</th><th>Schedule</th><th>Status</th><th>Actions</th></tr></thead>
            </table>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
var statusColors = { pending: 'warning', in_progress: 'info', completed: 'success', cancelled: 'danger' };
var priorityColors = { low: 'info', medium: 'warning', high: 'danger' };

function woStatusBadge(status, statusUrl) {
    if (!status) return '<span class="badge bg-label-secondary">-</span>';
    var label = status.replace(/_/g, ' ');
    label = label.charAt(0).toUpperCase() + label.slice(1);
    @if(auth()->user()->hasPermission('maintenance_work_orders.edit'))
    if (status !== 'completed' && status !== 'cancelled') {
        return '<button type="button" class="btn btn-sm btn-status-toggle btn-' + (statusColors[status] || 'secondary') + '" data-url="' + statusUrl + '">' + label + '</button>';
    }
    @endif
    return '<span class="badge bg-label-' + (statusColors[status] || 'secondary') + '">' + label + '</span>';
}

function priorityBadge(priority) {
    if (!priority) return '-';
    return '<span class="badge bg-label-' + (priorityColors[priority] || 'secondary') + '">' + priority.charAt(0).toUpperCase() + priority.slice(1) + '</span>';
}

function woActions(row) {
    var html = '<div class="btn-group btn-group-sm">';
    @if(auth()->user()->hasPermission('maintenance_work_orders.view'))
    html += '<a href="' + row.show_url + '" class="btn btn-outline-info"><i class="bx bx-show"></i></a>';
    @endif
    @if(auth()->user()->hasPermission('maintenance_work_orders.edit'))
    html += '<a href="' + row.edit_url + '" class="btn btn-outline-primary"><i class="bx bx-edit"></i></a>';
    @endif
    @if(auth()->user()->hasPermission('maintenance_work_orders.delete'))
    html += '<button type="button" class="btn btn-outline-danger btn-delete-item" data-url="' + row.delete_url + '" data-name="' + row.work_order_number + '"><i class="bx bx-trash"></i></button>';
    @endif
    html += '</div>';
    return html;
}

var table = $('#work-orders-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: "{{ route('admin.maintenance.work-orders.data') }}",
    columns: [
        { data: 'id', name: 'id', orderable: false, searchable: false },
        { data: 'work_order_number', name: 'work_order_number' },
        { data: 'title', name: 'title' },
        { data: 'asset_name', name: 'asset_name', orderable: false, searchable: false },
        { data: 'type', name: 'type', render: function(data) { return data ? data.charAt(0).toUpperCase() + data.slice(1) : '-'; } },
        { data: 'priority', name: 'priority', orderable: false, searchable: false, render: function(data) { return priorityBadge(data); } },
        { data: 'employee_name', name: 'employee_name', orderable: false, searchable: false },
        { data: 'schedule_date', name: 'schedule_date', render: function(data) { if (!data) return '-'; var d = new Date(data); return ('0' + d.getDate()).slice(-2) + '-' + ('0' + (d.getMonth()+1)).slice(-2) + '-' + d.getFullYear(); } },
        { data: 'status', name: 'status', orderable: false, searchable: false, render: function(data, type, row) {
            return woStatusBadge(data, row.status_url);
        }},
        { data: 'actions', name: 'actions', orderable: false, searchable: false, render: function(data, type, row) {
            return woActions(row);
        }}
    ],
    order: [[0, 'desc']]
});
</script>
@endsection
