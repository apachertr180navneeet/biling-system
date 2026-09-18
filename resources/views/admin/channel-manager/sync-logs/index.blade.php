@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-history"></i></div>
            <div>
                <h4 class="m-page-title">Sync Logs</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Channel Manager</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">Sync Logs</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <table id="sync-logs-table" class="table table-bordered table-striped" style="width:100%">
                <thead><tr><th>#</th><th>Channel</th><th>Reservation</th><th>Direction</th><th>Action</th><th>Status</th><th>Error</th><th>Created At</th></tr></thead>
            </table>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
var table = $('#sync-logs-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: "{{ route('admin.channel-manager.sync-logs.data') }}",
    columns: [
        { data: 'id', name: 'id', orderable: false, searchable: false },
        { data: 'ota_channel_name', name: 'ota_channel_name', orderable: false, searchable: false },
        { data: 'reservation_number', name: 'reservation_number', orderable: false, searchable: false },
        { data: 'direction_badge', name: 'direction', orderable: false, searchable: false },
        { data: 'action', name: 'action', orderable: false, searchable: false },
        { data: 'status_badge', name: 'status', orderable: false, searchable: false },
        { data: 'error_message', name: 'error_message', orderable: false, searchable: false, render: function(data) {
            if (!data) return '-';
            return data.length > 50 ? data.substring(0, 50) + '...' : data;
        }},
        { data: 'created_at', name: 'created_at', orderable: true, searchable: false }
    ],
    order: [[0, 'desc']]
});
</script>
@endsection
