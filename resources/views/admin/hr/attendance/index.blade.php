@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-calendar-check"></i></div>
            <div>
                <h4 class="m-page-title">Attendance</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">HR</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">Attendance</li>
                </ul>
            </div>
        </div>
        @if(auth()->user()->hasPermission('hr_attendance.create'))
        <a href="{{ route('admin.hr.attendance.create') }}" class="btn btn-primary m-btn-add"><i class="bx bx-plus"></i> Add Attendance</a>
        @endif
    </div>

    <div class="card">
        <div class="card-body">
            <table id="attendance-table" class="table table-bordered table-striped" style="width:100%">
                <thead><tr><th>#</th><th>Employee</th><th>Date</th><th>Check In</th><th>Check Out</th><th>Status</th><th>Hours Worked</th><th>Actions</th></tr></thead>
            </table>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
var statusColors = { present: 'success', absent: 'danger', half_day: 'warning', late: 'info', leave: 'primary', holiday: 'secondary' };

function attendanceStatusBadge(status) {
    var label = status.replace('_', ' ');
    label = label.charAt(0).toUpperCase() + label.slice(1);
    return '<span class="badge bg-label-' + (statusColors[status] || 'secondary') + '">' + label + '</span>';
}

function attendanceActions(row) {
    var html = '<div class="btn-group btn-group-sm">';
    @if(auth()->user()->hasPermission('hr_attendance.edit'))
    html += '<a href="' + row.edit_url + '" class="btn btn-outline-primary"><i class="bx bx-edit"></i></a>';
    @endif
    @if(auth()->user()->hasPermission('hr_attendance.delete'))
    html += '<button type="button" class="btn btn-outline-danger btn-delete-item" data-url="' + row.delete_url + '" data-name="' + row.employee_name + '"><i class="bx bx-trash"></i></button>';
    @endif
    html += '</div>';
    return html;
}

var table = $('#attendance-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: "{{ route('admin.hr.attendance.data') }}",
    columns: [
        { data: 'id', name: 'id', orderable: false, searchable: false },
        { data: 'employee_name', name: 'employee_name' },
        { data: 'date', name: 'date', render: function(data) { if (!data) return '-'; var d = new Date(data); return ('0' + d.getDate()).slice(-2) + '-' + ('0' + (d.getMonth()+1)).slice(-2) + '-' + d.getFullYear(); } },
        { data: 'check_in', name: 'check_in', render: function(data) { return data ? data.substring(0, 5) : '-'; } },
        { data: 'check_out', name: 'check_out', render: function(data) { return data ? data.substring(0, 5) : '-'; } },
        { data: 'status', name: 'status', orderable: false, searchable: false, render: function(data) {
            return attendanceStatusBadge(data);
        }},
        { data: 'hours_worked', name: 'hours_worked', render: function(data) { return data ? parseFloat(data).toFixed(2) : '-'; } },
        { data: 'actions', name: 'actions', orderable: false, searchable: false, render: function(data, type, row) {
            return attendanceActions(row);
        }}
    ],
    order: [[2, 'desc']]
});
</script>
@endsection