@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-door-open"></i></div>
            <div>
                <h4 class="m-page-title">Rooms</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Property</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">Rooms</li>
                </ul>
            </div>
        </div>
        @if(auth()->user()->hasPermission('rooms.create'))
        <a href="{{ route('admin.property.rooms.create') }}" class="btn btn-primary m-btn-add"><i class="bx bx-plus"></i> Add Room</a>
        @endif
    </div>

    <div class="card">
        <div class="card-body">
            <table id="rooms-table" class="table table-bordered table-striped" style="width:100%">
                <thead><tr><th>#</th><th>Room No</th><th>Hotel</th><th>Building</th><th>Floor</th><th>Wing</th><th>Room Type</th><th>Bed Type</th><th>Status</th><th>Actions</th></tr></thead>
            </table>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
function roomsStatusBadge(status, statusUrl) {
    @if(auth()->user()->hasPermission('rooms.edit'))
    return '<button type="button" class="btn btn-sm btn-status-toggle btn-' + (status === 'active' ? 'success' : 'warning') + '" data-url="' + statusUrl + '">' + status.charAt(0).toUpperCase() + status.slice(1) + '</button>';
    @else
    return '<span class="badge bg-label-' + (status === 'active' ? 'success' : 'warning') + '">' + status.charAt(0).toUpperCase() + status.slice(1) + '</span>';
    @endif
}

function roomsActions(row) {
    var html = '<div class="btn-group btn-group-sm">';
    @if(auth()->user()->hasPermission('rooms.edit'))
    html += '<a href="' + row.edit_url + '" class="btn btn-outline-primary"><i class="bx bx-edit"></i></a>';
    @endif
    @if(auth()->user()->hasPermission('rooms.delete'))
    html += '<button type="button" class="btn btn-outline-danger btn-delete-item" data-url="' + row.delete_url + '" data-name="' + row.room_number + '"><i class="bx bx-trash"></i></button>';
    @endif
    html += '</div>';
    return html;
}

var table = $('#rooms-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: "{{ route('admin.property.rooms.data') }}",
    columns: [
        { data: 'id', name: 'id', orderable: false, searchable: false },
        { data: 'room_number', name: 'room_number' },
        { data: 'hotel_name', name: 'hotel_name', orderable: false, searchable: false },
        { data: 'building_name', name: 'building_name', orderable: false, searchable: false },
        { data: 'floor_name', name: 'floor_name', orderable: false, searchable: false },
        { data: 'wing_name', name: 'wing_name', orderable: false, searchable: false },
        { data: 'room_type_name', name: 'room_type_name', orderable: false, searchable: false },
        { data: 'bed_type_name', name: 'bed_type_name', orderable: false, searchable: false },
        { data: 'status', name: 'status', orderable: false, searchable: false, render: function(data, type, row) {
            return roomsStatusBadge(data, row.status_url);
        }},
        { data: 'actions', name: 'actions', orderable: false, searchable: false, render: function(data, type, row) {
            return roomsActions(row);
        }}
    ],
    order: [[0, 'desc']]
});
</script>
@endsection
