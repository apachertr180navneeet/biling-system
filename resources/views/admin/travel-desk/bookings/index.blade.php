@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-car"></i></div>
            <div>
                <h4 class="m-page-title">Transport Bookings</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Travel Desk</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">Bookings</li>
                </ul>
            </div>
        </div>
        @if(auth()->user()->hasPermission('transport_bookings.create'))
        <a href="{{ route('admin.travel-desk.bookings.create') }}" class="btn btn-primary m-btn-add"><i class="bx bx-plus"></i> New Booking</a>
        @endif
    </div>

    <div class="card">
        <div class="card-body">
            <table id="transport-booking-table" class="table table-bordered table-striped" style="width:100%">
                <thead><tr><th>#</th><th>Booking #</th><th>Guest</th><th>Type</th><th>Trip</th><th>Pickup</th><th>Drop</th><th>Date/Time</th><th>Total</th><th>Status</th><th>Payment</th><th>Actions</th></tr></thead>
            </table>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
function trvBookingStatusBadge(status, statusUrl) {
    @if(auth()->user()->hasPermission('transport_bookings.edit'))
    return '<button type="button" class="btn btn-sm btn-status-toggle btn-' + (status === 'completed' ? 'success' : (status === 'confirmed' ? 'primary' : (status === 'cancelled' ? 'danger' : 'warning'))) + '" data-url="' + statusUrl + '">' + status.replace('_', ' ').charAt(0).toUpperCase() + status.replace('_', ' ').slice(1) + '</button>';
    @else
    return '<span class="badge bg-label-' + (status === 'completed' ? 'success' : (status === 'confirmed' ? 'primary' : (status === 'cancelled' ? 'danger' : 'warning'))) + '">' + status.replace('_', ' ').charAt(0).toUpperCase() + status.replace('_', ' ').slice(1) + '</span>';
    @endif
}

function trvBookingActions(row) {
    var html = '<div class="btn-group btn-group-sm">';
    html += '<a href="' + row.show_url + '" class="btn btn-outline-info"><i class="bx bx-show"></i></a>';
    @if(auth()->user()->hasPermission('transport_bookings.edit'))
    html += '<a href="' + row.edit_url + '" class="btn btn-outline-primary"><i class="bx bx-edit"></i></a>';
    @endif
    @if(auth()->user()->hasPermission('transport_bookings.delete'))
    html += '<button type="button" class="btn btn-outline-danger btn-delete" data-url="' + row.delete_url + '" data-name="' + row.booking_number + '"><i class="bx bx-trash"></i></button>';
    @endif
    html += '</div>';
    return html;
}

var table = $('#transport-booking-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: "{{ route('admin.travel-desk.bookings.data') }}",
    columns: [
        { data: 'id', name: 'id', orderable: false, searchable: false },
        { data: 'booking_number', name: 'booking_number' },
        { data: 'guest_name', name: 'guest_name' },
        { data: 'type_name', name: 'transportType.name' },
        { data: 'trip_type', name: 'trip_type', orderable: false, searchable: false, render: function(data, type, row) { return row.trip_type_label; }},
        { data: 'pickup_location', name: 'pickup_location' },
        { data: 'drop_location', name: 'drop_location' },
        { data: 'pickup_datetime_formatted', name: 'pickup_datetime' },
        { data: 'total_amount', name: 'total_amount', render: function(data) { return '₹' + parseFloat(data).toLocaleString('en-IN', {minimumFractionDigits: 2}); }},
        { data: 'status', name: 'status', orderable: false, searchable: false, render: function(data, type, row) { return trvBookingStatusBadge(data, row.status_url); }},
        { data: 'payment_status', name: 'payment_status', orderable: false, searchable: false, render: function(data, type, row) { return row.payment_status_badge; }},
        { data: 'actions', name: 'actions', orderable: false, searchable: false, render: function(data, type, row) { return trvBookingActions(row); }}
    ],
    order: [[0, 'desc']]
});

$(document).on('click', '.btn-status-toggle', function() {
    var btn = $(this);
    $.ajax({ url: btn.data('url'), type: 'PATCH', headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success: function() { table.ajax.reload(); }
    });
});

$(document).on('click', '.btn-delete', function() {
    var btn = $(this);
    if (confirm('Are you sure you want to delete "' + btn.data('name') + '"?')) {
        $.ajax({ url: btn.data('url'), type: 'DELETE', headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success: function() { table.ajax.reload(); }
        });
    }
});
</script>
@endsection
