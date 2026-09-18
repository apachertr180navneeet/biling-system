@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-door-open"></i></div>
            <div>
                <h4 class="m-page-title">Room Types</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Room Master</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">Room Types</li>
                </ul>
            </div>
        </div>
        @if(auth()->user()->hasPermission('room_types.create'))
        <a href="{{ route('admin.property.room-types.create') }}" class="btn btn-primary m-btn-add"><i class="bx bx-plus"></i> Add Room Type</a>
        @endif
    </div>

    <div class="card">
        <div class="card-body">
            <table id="room-types-table" class="table table-bordered table-striped" style="width:100%">
                <thead><tr><th>#</th><th>Name</th><th>Base Rate</th><th>Max Occupancy</th><th>Description</th><th>Status</th><th>Actions</th></tr></thead>
            </table>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
function roomTypesStatusBadge(status, statusUrl) {
    @if(auth()->user()->hasPermission('room_types.edit'))
    return '<button type="button" class="btn btn-sm btn-status-toggle btn-' + (status === 'active' ? 'success' : 'warning') + '" data-url="' + statusUrl + '">' + status.charAt(0).toUpperCase() + status.slice(1) + '</button>';
    @else
    return '<span class="badge bg-label-' + (status === 'active' ? 'success' : 'warning') + '">' + status.charAt(0).toUpperCase() + status.slice(1) + '</span>';
    @endif
}

function roomTypesActions(row) {
    var html = '<div class="btn-group btn-group-sm">';
    @if(auth()->user()->hasPermission('room_types.edit'))
    html += '<a href="' + row.edit_url + '" class="btn btn-outline-primary"><i class="bx bx-edit"></i></a>';
    @endif
    @if(auth()->user()->hasPermission('room_types.delete'))
    html += '<button type="button" class="btn btn-outline-danger btn-delete-item" data-url="' + row.delete_url + '" data-name="' + row.name + '"><i class="bx bx-trash"></i></button>';
    @endif
    html += '</div>';
    return html;
}

var table = $('#room-types-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: "{{ route('admin.property.room-types.data') }}",
    columns: [
        { data: 'id', name: 'id', orderable: false, searchable: false },
        { data: 'name', name: 'name' },
        { data: 'base_rate', name: 'base_rate', render: function(data) { return parseFloat(data).toFixed(2); }},
        { data: 'max_occupancy', name: 'max_occupancy' },
        { data: 'description', name: 'description' },
        { data: 'status', name: 'status', orderable: false, searchable: false, render: function(data, type, row) {
            return roomTypesStatusBadge(data, row.status_url);
        }},
        { data: 'actions', name: 'actions', orderable: false, searchable: false, render: function(data, type, row) {
            return roomTypesActions(row);
        }}
    ],
    order: [[0, 'desc']]
});
</script>
@endsection