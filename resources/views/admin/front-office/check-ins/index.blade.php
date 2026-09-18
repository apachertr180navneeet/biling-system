@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-log-in"></i></div>
            <div>
                <h4 class="m-page-title">Check In</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Front Office</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">Check In</li>
                </ul>
            </div>
        </div>
        @if(auth()->user()->hasPermission('check_ins.create'))
        <a href="{{ route('admin.front-office.check-ins.create') }}" class="btn btn-primary m-btn-add"><i class="bx bx-plus"></i> Check In Guest</a>
        @endif
    </div>

    <div class="card">
        <div class="card-body">
            <table id="checkins-table" class="table table-bordered table-striped" style="width:100%">
                <thead><tr><th>#</th><th>Reservation</th><th>Guest</th><th>Hotel</th><th>Room</th><th>Check-in Date</th><th>Arrival Time</th><th>ID Verified</th><th>Status</th><th>Actions</th></tr></thead>
            </table>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
function checkinsStatusBadge(status, statusUrl) {
    @if(auth()->user()->hasPermission('check_ins.edit'))
    return '<button type="button" class="btn btn-sm btn-status-toggle btn-' + (status === 'active' ? 'success' : 'warning') + '" data-url="' + statusUrl + '">' + status.charAt(0).toUpperCase() + status.slice(1) + '</button>';
    @else
    return '<span class="badge bg-label-' + (status === 'active' ? 'success' : 'warning') + '">' + status.charAt(0).toUpperCase() + status.slice(1) + '</span>';
    @endif
}

function checkinsActions(row) {
    var html = '<div class="btn-group btn-group-sm">';
    @if(auth()->user()->hasPermission('check_ins.edit'))
    html += '<a href="' + row.edit_url + '" class="btn btn-outline-primary"><i class="bx bx-edit"></i></a>';
    @endif
    @if(auth()->user()->hasPermission('check_ins.delete'))
    html += '<button type="button" class="btn btn-outline-danger btn-delete-item" data-url="' + row.delete_url + '" data-name="' + row.reservation_number + '"><i class="bx bx-trash"></i></button>';
    @endif
    html += '</div>';
    return html;
}

var table = $('#checkins-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: "{{ route('admin.front-office.check-ins.data') }}",
    columns: [
        { data: 'id', name: 'id', orderable: false, searchable: false },
        { data: 'reservation_number', name: 'reservation_number' },
        { data: 'guest_name', name: 'guest_name' },
        { data: 'hotel_name', name: 'hotel_name', orderable: false, searchable: false },
        { data: 'room_number', name: 'room_number', orderable: false, searchable: false },
        { data: 'check_in_date', name: 'check_in_date', orderable: false, searchable: false },
        { data: 'arrival_time', name: 'arrival_time', orderable: false, searchable: false },
        { data: 'id_verified', name: 'id_verified', orderable: false, searchable: false, render: function(data) {
            return data ? '<span class="badge bg-label-success"><i class="bx bx-check"></i> Verified</span>' : '<span class="badge bg-label-secondary">Pending</span>';
        }},
        { data: 'status', name: 'status', orderable: false, searchable: false, render: function(data, type, row) {
            return checkinsStatusBadge(data, row.status_url);
        }},
        { data: 'actions', name: 'actions', orderable: false, searchable: false, render: function(data, type, row) {
            return checkinsActions(row);
        }}
    ],
    order: [[0, 'desc']]
});
</script>
@endsection
