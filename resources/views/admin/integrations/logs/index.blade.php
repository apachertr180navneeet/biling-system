@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-list-ul"></i></div>
            <div>
                <h4 class="m-page-title">Device Activity Logs</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Integrations</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">Device Logs</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <table id="logs-table" class="table table-bordered table-striped" style="width:100%">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Device</th>
                        <th>Event</th>
                        <th>Status</th>
                        <th>User</th>
                        <th>Employee</th>
                        <th>Message</th>
                        <th>Date & Time</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
$('#logs-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: "{{ route('admin.integrations.logs.data') }}",
    columns: [
        { data: 'id', name: 'id', orderable: false, searchable: false },
        { data: 'device_name', name: 'device_name' },
        { data: 'event_badge', name: 'event_type', orderable: false, searchable: false },
        { data: 'status_badge', name: 'status', orderable: false, searchable: false },
        { data: 'user_name', name: 'user_name', orderable: false, searchable: false },
        { data: 'employee_name', name: 'employee_name', orderable: false, searchable: false },
        { data: 'message', name: 'message' },
        { data: 'created_at', name: 'created_at', render: function(data) {
            if (!data) return '-';
            var d = new Date(data);
            return d.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' }) + ' ' + d.toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit' });
        }}
    ],
    order: [[0, 'desc']]
});
</script>
@endsection
