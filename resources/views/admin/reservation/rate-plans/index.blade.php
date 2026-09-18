@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-dollar"></i></div>
            <div>
                <h4 class="m-page-title">Rate Plans</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Reservation Setup</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">Rate Plans</li>
                </ul>
            </div>
        </div>
        @if(auth()->user()->hasPermission('rate_plans.create'))
        <a href="{{ route('admin.reservation.rate-plans.create') }}" class="btn btn-primary m-btn-add"><i class="bx bx-plus"></i> Add Rate Plan</a>
        @endif
    </div>

    <div class="card">
        <div class="card-body">
            <table id="rate-plans-table" class="table table-bordered table-striped" style="width:100%">
                <thead><tr><th>#</th><th>Name</th><th>Hotel</th><th>Room Type</th><th>Rate/Night</th><th>Valid From</th><th>Valid To</th><th>Status</th><th>Actions</th></tr></thead>
            </table>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
function ratePlansStatusBadge(status, statusUrl) {
    @if(auth()->user()->hasPermission('rate_plans.edit'))
    return '<button type="button" class="btn btn-sm btn-status-toggle btn-' + (status === 'active' ? 'success' : 'warning') + '" data-url="' + statusUrl + '">' + status.charAt(0).toUpperCase() + status.slice(1) + '</button>';
    @else
    return '<span class="badge bg-label-' + (status === 'active' ? 'success' : 'warning') + '">' + status.charAt(0).toUpperCase() + status.slice(1) + '</span>';
    @endif
}

function ratePlansActions(row) {
    var html = '<div class="btn-group btn-group-sm">';
    @if(auth()->user()->hasPermission('rate_plans.edit'))
    html += '<a href="' + row.edit_url + '" class="btn btn-outline-primary"><i class="bx bx-edit"></i></a>';
    @endif
    @if(auth()->user()->hasPermission('rate_plans.delete'))
    html += '<button type="button" class="btn btn-outline-danger btn-delete-item" data-url="' + row.delete_url + '" data-name="' + row.name + '"><i class="bx bx-trash"></i></button>';
    @endif
    html += '</div>';
    return html;
}

var table = $('#rate-plans-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: "{{ route('admin.reservation.rate-plans.data') }}",
    columns: [
        { data: 'id', name: 'id', orderable: false, searchable: false },
        { data: 'name', name: 'name' },
        { data: 'hotel_name', name: 'hotel_name', orderable: false, searchable: false },
        { data: 'room_type_name', name: 'room_type_name', orderable: false, searchable: false },
        { data: 'rate_per_night', name: 'rate_per_night', render: function(data) { return parseFloat(data).toFixed(2); }},
        { data: 'effective_from_formatted', name: 'effective_from', orderable: false, searchable: false },
        { data: 'effective_to_formatted', name: 'effective_to', orderable: false, searchable: false },
        { data: 'status', name: 'status', orderable: false, searchable: false, render: function(data, type, row) {
            return ratePlansStatusBadge(data, row.status_url);
        }},
        { data: 'actions', name: 'actions', orderable: false, searchable: false, render: function(data, type, row) {
            return ratePlansActions(row);
        }}
    ],
    order: [[0, 'desc']]
});
</script>
@endsection

