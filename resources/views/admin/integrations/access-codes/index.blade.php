@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-key"></i></div>
            <div>
                <h4 class="m-page-title">Smart Lock Access Codes</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Integrations</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">Access Codes</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <table id="access-codes-table" class="table table-bordered table-striped" style="width:100%">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Device</th>
                        <th>Guest</th>
                        <th>Room</th>
                        <th>PIN</th>
                        <th>Type</th>
                        <th>Valid From</th>
                        <th>Valid Until</th>
                        <th>Status</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
$('#access-codes-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: "{{ route('admin.integrations.access-codes.data') }}",
    columns: [
        { data: 'id', name: 'id', orderable: false, searchable: false },
        { data: 'device_name', name: 'device_name', orderable: false, searchable: false },
        { data: 'guest_name', name: 'guest_name' },
        { data: 'room_number', name: 'room_number', orderable: false, searchable: false },
        { data: 'pin_code', name: 'pin_code', orderable: false, searchable: false, render: function(data) {
            return '<code>' + data + '</code>';
        }},
        { data: 'access_type', name: 'access_type', render: function(data) { return data.toUpperCase(); } },
        { data: 'valid_from_fmt', name: 'valid_from', orderable: false, searchable: false },
        { data: 'valid_until_fmt', name: 'valid_until', orderable: false, searchable: false },
        { data: 'status_badge', name: 'status_badge', orderable: false, searchable: false }
    ],
    order: [[0, 'desc']]
});
</script>
@endsection
