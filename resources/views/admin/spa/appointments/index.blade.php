@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-spa"></i></div>
            <div>
                <h4 class="m-page-title">Spa Appointments</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Spa</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">Appointments</li>
                </ul>
            </div>
        </div>
        @if(auth()->user()->hasPermission('spa_appointments.create'))
        <a href="{{ route('admin.spa.appointments.create') }}" class="btn btn-primary m-btn-add"><i class="bx bx-plus"></i> Book Appointment</a>
        @endif
    </div>

    <div class="card">
        <div class="card-body">
            <table id="spa-appt-table" class="table table-bordered table-striped" style="width:100%">
                <thead><tr><th>#</th><th>Appointment #</th><th>Guest</th><th>Service</th><th>Date</th><th>Time</th><th>Duration</th><th>Total</th><th>Status</th><th>Payment</th><th>Actions</th></tr></thead>
            </table>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
function spaApptStatusBadge(status, statusUrl) {
    @if(auth()->user()->hasPermission('spa_appointments.edit'))
    return '<button type="button" class="btn btn-sm btn-status-toggle btn-' + (status === 'completed' ? 'success' : (status === 'confirmed' ? 'primary' : (status === 'cancelled' ? 'danger' : 'warning'))) + '" data-url="' + statusUrl + '">' + status.replace('_', ' ').charAt(0).toUpperCase() + status.replace('_', ' ').slice(1) + '</button>';
    @else
    return '<span class="badge bg-label-' + (status === 'completed' ? 'success' : (status === 'confirmed' ? 'primary' : (status === 'cancelled' ? 'danger' : 'warning'))) + '">' + status.replace('_', ' ').charAt(0).toUpperCase() + status.replace('_', ' ').slice(1) + '</span>';
    @endif
}

function spaApptActions(row) {
    var html = '<div class="btn-group btn-group-sm">';
    html += '<a href="' + row.show_url + '" class="btn btn-outline-info"><i class="bx bx-show"></i></a>';
    @if(auth()->user()->hasPermission('spa_appointments.edit'))
    html += '<a href="' + row.edit_url + '" class="btn btn-outline-primary"><i class="bx bx-edit"></i></a>';
    @endif
    @if(auth()->user()->hasPermission('spa_appointments.delete'))
    html += '<button type="button" class="btn btn-outline-danger btn-delete" data-url="' + row.delete_url + '" data-name="' + row.appointment_number + '"><i class="bx bx-trash"></i></button>';
    @endif
    html += '</div>';
    return html;
}

var table = $('#spa-appt-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: "{{ route('admin.spa.appointments.data') }}",
    columns: [
        { data: 'id', name: 'id', orderable: false, searchable: false },
        { data: 'appointment_number', name: 'appointment_number' },
        { data: 'guest_name', name: 'guest_name' },
        { data: 'service_name', name: 'spaService.name' },
        { data: 'appointment_date_formatted', name: 'appointment_date' },
        { data: 'appointment_time', name: 'appointment_time' },
        { data: 'duration_minutes', name: 'duration_minutes', render: function(data) { return data + ' min'; }},
        { data: 'total_amount', name: 'total_amount', render: function(data) { return '₹' + parseFloat(data).toLocaleString('en-IN', {minimumFractionDigits: 2}); }},
        { data: 'status', name: 'status', orderable: false, searchable: false, render: function(data, type, row) { return spaApptStatusBadge(data, row.status_url); }},
        { data: 'payment_status', name: 'payment_status', orderable: false, searchable: false, render: function(data, type, row) { return row.payment_status_badge; }},
        { data: 'actions', name: 'actions', orderable: false, searchable: false, render: function(data, type, row) { return spaApptActions(row); }}
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
