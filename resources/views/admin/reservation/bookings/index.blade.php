@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-calendar"></i></div>
            <div>
                <h4 class="m-page-title">Bookings</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Reservations</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">Bookings</li>
                </ul>
            </div>
        </div>
        @if(auth()->user()->hasPermission('bookings.create'))
        <a href="{{ route('admin.reservation.bookings.create') }}" class="btn btn-primary m-btn-add"><i class="bx bx-plus"></i> Add Booking</a>
        @endif
    </div>

    <div class="card">
        <div class="card-body">
            <table id="bookings-table" class="table table-bordered table-striped" style="width:100%">
                <thead><tr><th>#</th><th>Reservation #</th><th>Guest</th><th>Hotel</th><th>Check-In</th><th>Check-Out</th><th>Nights</th><th>Amount</th><th>Status</th><th>Actions</th></tr></thead>
            </table>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
var statusColors = {
    'pending': 'warning',
    'confirmed': 'info',
    'checked-in': 'success',
    'checked-out': 'secondary',
    'cancelled': 'danger',
    'no-show': 'danger'
};

function bookingStatusBadge(status, statusUrl) {
    var color = statusColors[status] || 'secondary';
    @if(auth()->user()->hasPermission('bookings.edit'))
    return '<button type="button" class="btn btn-sm btn-status-toggle btn-' + color + '" data-url="' + statusUrl + '">' + status.charAt(0).toUpperCase() + status.slice(1) + '</button>';
    @else
    return '<span class="badge bg-label-' + color + '">' + status.charAt(0).toUpperCase() + status.slice(1) + '</span>';
    @endif
}

function bookingActions(row) {
    var html = '<div class="btn-group btn-group-sm">';
    @if(auth()->user()->hasPermission('bookings.edit'))
    html += '<a href="' + row.edit_url + '" class="btn btn-outline-primary" title="Edit"><i class="bx bx-edit"></i></a>';
    @endif
    @if(auth()->user()->hasPermission('payments.view'))
    html += '<a href="' + '{{ url("admin/reservation") }}/' + row.id + '/payments" class="btn btn-outline-success" title="Payments"><i class="bx bx-dollar"></i></a>';
    @endif
    @if(auth()->user()->hasPermission('bookings.delete'))
    html += '<button type="button" class="btn btn-outline-danger btn-delete-item" data-url="' + row.delete_url + '" data-name="' + row.reservation_number + '"><i class="bx bx-trash"></i></button>';
    @endif
    html += '</div>';
    return html;
}

var table = $('#bookings-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: "{{ route('admin.reservation.bookings.data') }}",
    columns: [
        { data: 'id', name: 'id', orderable: false, searchable: false },
        { data: 'reservation_number', name: 'reservation_number' },
        { data: 'guest_name', name: 'guest_name', orderable: false, searchable: false },
        { data: 'hotel_name', name: 'hotel_name', orderable: false, searchable: false },
{ data: 'check_in_date', name: 'check_in_date', render: function(data) { return data ? moment(data).format('DD-MM-YYYY') : ''; }},
        { data: 'check_out_date', name: 'check_out_date', render: function(data) { return data ? moment(data).format('DD-MM-YYYY') : ''; }},
        { data: 'nights', name: 'nights', orderable: false, searchable: false },
        { data: 'total_amount', name: 'total_amount', render: function(data) { return parseFloat(data).toFixed(2); }},
        { data: 'status', name: 'status', orderable: false, searchable: false, render: function(data, type, row) {
            return bookingStatusBadge(data, row.status_url);
        }},
        { data: 'actions', name: 'actions', orderable: false, searchable: false, render: function(data, type, row) {
            return bookingActions(row);
        }}
    ],
    order: [[0, 'desc']]
});
</script>
@endsection

