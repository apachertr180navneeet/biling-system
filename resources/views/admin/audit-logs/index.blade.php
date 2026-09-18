@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-data"></i></div>
            <div>
                <h4 class="m-page-title">Audit Logs</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">Audit Logs</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-3">
                    <select id="module-filter" class="form-select">
                        <option value="">All Modules</option>
                        <option value="reservation">Reservation</option>
                        <option value="check_in">Check In</option>
                        <option value="check_out">Check Out</option>
                        <option value="employee">Employee</option>
                        <option value="room">Room</option>
                        <option value="expense">Expense</option>
                        <option value="purchase_order">Purchase Order</option>
                        <option value="device">Device</option>
                        <option value="user">User</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select id="event-filter" class="form-select">
                        <option value="">All Events</option>
                        <option value="created">Created</option>
                        <option value="updated">Updated</option>
                        <option value="deleted">Deleted</option>
                    </select>
                </div>
            </div>
            <table id="audit-table" class="table table-bordered table-striped" style="width:100%">
                <thead><tr><th>#</th><th>User</th><th>Module</th><th>Event</th><th>Description</th><th>IP Address</th><th>Date</th><th></th></tr></thead>
            </table>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
var table = $('#audit-table').DataTable({
    processing: true, serverSide: true,
    ajax: { url: "{{ route('admin.audit-logs.data') }}", data: function(d) { d.module = $('#module-filter').val(); d.event = $('#event-filter').val(); } },
    columns: [
        { data: 'id', name: 'id', orderable: false, searchable: false },
        { data: 'causer_name', name: 'causer_name' },
        { data: 'module_badge', name: 'module_badge', orderable: false, searchable: false },
        { data: 'event_badge', name: 'event', orderable: false, searchable: false },
        { data: 'description', name: 'description' },
        { data: 'ip_address', name: 'ip_address' },
        { data: 'formatted_date', name: 'created_at', orderable: false, searchable: false },
        { data: 'actions', name: 'actions', orderable: false, searchable: false, render: function(data, type, row) {
            return '<a href="' + row.show_url + '" class="btn btn-sm btn-info"><i class="bx bx-show"></i></a>';
        }}
    ], order: [[0, 'desc']]
});
$('#module-filter, #event-filter').on('change', function() { table.ajax.reload(); });
</script>
@endsection
