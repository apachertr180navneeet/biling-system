@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-spray-can"></i></div>
            <div>
                <h4 class="m-page-title">Room Cleaning Schedule</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Housekeeping</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">Cleaning Schedules</li>
                </ul>
            </div>
        </div>
        @if(auth()->user()->hasPermission('cleaning_schedules.create'))
        <a href="{{ route('admin.housekeeping.cleaning-schedules.create') }}" class="btn btn-primary m-btn-add"><i class="bx bx-plus"></i> Add Schedule</a>
        @endif
    </div>

    <div class="card">
        <div class="card-body">
            <table id="cleaning-schedule-table" class="table table-bordered table-striped" style="width:100%">
                <thead><tr><th>#</th><th>Hotel</th><th>Room</th><th>Cleaning Type</th><th>Scheduled Date</th><th>Assigned To</th><th>Priority</th><th>Status</th><th>Actions</th></tr></thead>
            </table>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
var table = $('#cleaning-schedule-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: "{{ route('admin.housekeeping.cleaning-schedules.data') }}",
    columns: [
        { data: 'id', name: 'id', orderable: false, searchable: false },
        { data: 'hotel_name', name: 'hotel_name' },
        { data: 'room_number', name: 'room_number' },
        { data: 'cleaning_type', name: 'cleaning_type', orderable: false, searchable: false, render: function(data) {
            return '<span class="badge bg-label-info">' + data.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase()) + '</span>';
        }},
        { data: 'scheduled_date_formatted', name: 'scheduled_date' },
        { data: 'assigned_name', name: 'assigned_name' },
        { data: 'priority_badge', name: 'priority', orderable: false, searchable: false },
        { data: 'status_badge', name: 'status', orderable: false, searchable: false },
        { data: 'actions', name: 'actions', orderable: false, searchable: false, render: function(data, type, row) {
            var html = '<div class="btn-group btn-group-sm">';
            @if(auth()->user()->hasPermission('cleaning_schedules.edit'))
            html += '<a href="' + row.edit_url + '" class="btn btn-outline-primary"><i class="bx bx-edit"></i></a>';
            @endif
            @if(auth()->user()->hasPermission('cleaning_schedules.delete'))
            html += '<button type="button" class="btn btn-outline-danger btn-delete-item" data-url="' + row.delete_url + '" data-name="Cleaning Schedule"><i class="bx bx-trash"></i></button>';
            @endif
            html += '</div>';
            return html;
        }}
    ],
    order: [[0, 'desc']]
});
</script>
@endsection
