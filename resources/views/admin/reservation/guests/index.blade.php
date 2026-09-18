@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-user"></i></div>
            <div>
                <h4 class="m-page-title">Guests</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Reservation Setup</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">Guests</li>
                </ul>
            </div>
        </div>
        @if(auth()->user()->hasPermission('guests.create'))
        <a href="{{ route('admin.reservation.guests.create') }}" class="btn btn-primary m-btn-add"><i class="bx bx-plus"></i> Add Guest</a>
        @endif
    </div>

    <div class="card">
        <div class="card-body">
            <table id="guests-table" class="table table-bordered table-striped" style="width:100%">
                <thead><tr><th>#</th><th>Name</th><th>Email</th><th>Phone</th><th>City</th><th>Nationality</th><th>Company</th><th>Status</th><th>Actions</th></tr></thead>
            </table>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
function guestsStatusBadge(status, statusUrl) {
    @if(auth()->user()->hasPermission('guests.edit'))
    return '<button type="button" class="btn btn-sm btn-status-toggle btn-' + (status === 'active' ? 'success' : 'warning') + '" data-url="' + statusUrl + '">' + status.charAt(0).toUpperCase() + status.slice(1) + '</button>';
    @else
    return '<span class="badge bg-label-' + (status === 'active' ? 'success' : 'warning') + '">' + status.charAt(0).toUpperCase() + status.slice(1) + '</span>';
    @endif
}

function guestsActions(row) {
    var html = '<div class="btn-group btn-group-sm">';
    @if(auth()->user()->hasPermission('guests.edit'))
    html += '<a href="' + row.edit_url + '" class="btn btn-outline-primary"><i class="bx bx-edit"></i></a>';
    @endif
    @if(auth()->user()->hasPermission('guests.delete'))
    html += '<button type="button" class="btn btn-outline-danger btn-delete-item" data-url="' + row.delete_url + '" data-name="' + row.full_name + '"><i class="bx bx-trash"></i></button>';
    @endif
    html += '</div>';
    return html;
}

var table = $('#guests-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: "{{ route('admin.reservation.guests.data') }}",
    columns: [
        { data: 'id', name: 'id', orderable: false, searchable: false },
        { data: 'full_name', name: 'full_name' },
        { data: 'email', name: 'email' },
        { data: 'phone', name: 'phone' },
        { data: 'city', name: 'city' },
        { data: 'nationality', name: 'nationality' },
        { data: 'company_name', name: 'company_name' },
        { data: 'status', name: 'status', orderable: false, searchable: false, render: function(data, type, row) {
            return guestsStatusBadge(data, row.status_url);
        }},
        { data: 'actions', name: 'actions', orderable: false, searchable: false, render: function(data, type, row) {
            return guestsActions(row);
        }}
    ],
    order: [[0, 'desc']]
});
</script>
@endsection

