@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-calendar-event"></i></div>
            <div>
                <h4 class="m-page-title">Events</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Banquet</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">Events</li>
                </ul>
            </div>
        </div>
        @if(auth()->user()->hasPermission('events.create'))
        <a href="{{ route('admin.banquet.events.create') }}" class="btn btn-primary m-btn-add"><i class="bx bx-plus"></i> Book Event</a>
        @endif
    </div>

    <div class="card">
        <div class="card-body">
            <table id="event-table" class="table table-bordered table-striped" style="width:100%">
                <thead><tr><th>#</th><th>Event No</th><th>Event Name</th><th>Hall</th><th>Type</th><th>Date</th><th>Contact</th><th>Total</th><th>Booking</th><th>Payment</th><th>Actions</th></tr></thead>
            </table>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
var typeLabels = { wedding:'Wedding', conference:'Conference', seminar:'Seminar', exhibition:'Exhibition', corporate:'Corporate', social:'Social', birthday:'Birthday', anniversary:'Anniversary', other:'Other' };

function eventActions(row) {
    var html = '<div class="btn-group btn-group-sm">';
    @if(auth()->user()->hasPermission('events.view'))
    html += '<a href="' + row.edit_url.replace('/edit', '') + '" class="btn btn-outline-info"><i class="bx bx-show"></i></a>';
    @endif
    @if(auth()->user()->hasPermission('events.edit'))
    html += '<a href="' + row.edit_url + '" class="btn btn-outline-primary"><i class="bx bx-edit"></i></a>';
    @endif
    @if(auth()->user()->hasPermission('events.delete'))
    html += '<button type="button" class="btn btn-outline-danger btn-delete" data-url="' + row.delete_url + '" data-name="' + row.event_name + '"><i class="bx bx-trash"></i></button>';
    @endif
    html += '</div>';
    return html;
}

var table = $('#event-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: "{{ route('admin.banquet.events.data') }}",
    columns: [
        { data: 'id', name: 'id', orderable: false, searchable: false },
        { data: 'event_number', name: 'event_number' },
        { data: 'event_name', name: 'event_name' },
        { data: 'hall_name', name: 'hall.name' },
        { data: 'event_type', name: 'event_type', orderable: false, searchable: false, render: function(data) { return typeLabels[data] || data; }},
        { data: 'event_date_formatted', name: 'event_date' },
        { data: 'contact_name', name: 'contact_name' },
        { data: 'total_amount', name: 'total_amount', render: function(data) { return '₹' + parseFloat(data).toLocaleString('en-IN', {minimumFractionDigits: 2}); }},
        { data: 'booking_status_badge', name: 'booking_status', orderable: false, searchable: false },
        { data: 'payment_status_badge', name: 'payment_status', orderable: false, searchable: false },
        { data: 'actions', name: 'actions', orderable: false, searchable: false, render: function(data, type, row) { return eventActions(row); }}
    ],
    order: [[0, 'desc']]
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
