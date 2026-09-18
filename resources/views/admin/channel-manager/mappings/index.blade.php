@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-network-chart"></i></div>
            <div>
                <h4 class="m-page-title">Room Type Mappings</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Channel Manager</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">Room Mappings</li>
                </ul>
            </div>
        </div>
        @if(auth()->user()->hasPermission('channel_manager.create'))
        <a href="{{ route('admin.channel-manager.mappings.create') }}" class="btn btn-primary m-btn-add"><i class="bx bx-plus"></i> Add Mapping</a>
        @endif
    </div>

    <div class="card">
        <div class="card-body">
            <table id="mappings-table" class="table table-bordered table-striped" style="width:100%">
                <thead><tr><th>#</th><th>OTA Channel</th><th>Room Type</th><th>OTA Room Type ID</th><th>OTA Room Name</th><th>Rate Multiplier</th><th>Status</th><th>Actions</th></tr></thead>
            </table>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
function mappingsActions(row) {
    var html = '<div class="btn-group btn-group-sm">';
    @if(auth()->user()->hasPermission('channel_manager.edit'))
    html += '<a href="' + row.edit_url + '" class="btn btn-outline-primary"><i class="bx bx-edit"></i></a>';
    @endif
    @if(auth()->user()->hasPermission('channel_manager.delete'))
    html += '<button type="button" class="btn btn-outline-danger btn-delete-item" data-url="' + row.delete_url + '" data-name="' + row.room_type_name + '"><i class="bx bx-trash"></i></button>';
    @endif
    html += '</div>';
    return html;
}

var table = $('#mappings-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: "{{ route('admin.channel-manager.mappings.data') }}",
    columns: [
        { data: 'id', name: 'id', orderable: false, searchable: false },
        { data: 'ota_channel_name', name: 'ota_channel_name', orderable: false, searchable: false },
        { data: 'room_type_name', name: 'room_type_name', orderable: false, searchable: false },
        { data: 'ota_room_type_id', name: 'ota_room_type_id', orderable: false, searchable: false },
        { data: 'ota_room_name', name: 'ota_room_name', orderable: false, searchable: false },
        { data: 'rate_multiplier', name: 'rate_multiplier', orderable: false, searchable: false },
        { data: 'status', name: 'status', orderable: false, searchable: false, render: function(data) {
            return '<span class="badge bg-label-' + (data === 'active' ? 'success' : 'warning') + '">' + data.charAt(0).toUpperCase() + data.slice(1) + '</span>';
        }},
        { data: 'actions', name: 'actions', orderable: false, searchable: false, render: function(data, type, row) {
            return mappingsActions(row);
        }}
    ],
    order: [[0, 'desc']]
});
</script>
@endsection
