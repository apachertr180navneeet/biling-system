@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-user"></i></div>
            <div>
                <h4 class="m-page-title">Employees</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">HR</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">Employees</li>
                </ul>
            </div>
        </div>
        @if(auth()->user()->hasPermission('hr_employees.create'))
        <a href="{{ route('admin.hr.employees.create') }}" class="btn btn-primary m-btn-add"><i class="bx bx-plus"></i> Add Employee</a>
        @endif
    </div>

    <div class="card">
        <div class="card-body">
            <table id="employee-table" class="table table-bordered table-striped" style="width:100%">
                <thead><tr><th>#</th><th>Employee ID</th><th>Full Name</th><th>Email</th><th>Department</th><th>Designation</th><th>Basic Salary</th><th>Status</th><th>Actions</th></tr></thead>
            </table>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
function employeeStatusBadge(status, statusUrl) {
    @if(auth()->user()->hasPermission('hr_employees.edit'))
    return '<button type="button" class="btn btn-sm btn-status-toggle btn-' + (status === 'active' ? 'success' : 'warning') + '" data-url="' + statusUrl + '">' + status.charAt(0).toUpperCase() + status.slice(1) + '</button>';
    @else
    return '<span class="badge bg-label-' + (status === 'active' ? 'success' : 'warning') + '">' + status.charAt(0).toUpperCase() + status.slice(1) + '</span>';
    @endif
}

function employeeActions(row) {
    var html = '<div class="btn-group btn-group-sm">';
    @if(auth()->user()->hasPermission('hr_employees.edit'))
    html += '<a href="' + row.edit_url + '" class="btn btn-outline-primary"><i class="bx bx-edit"></i></a>';
    @endif
    @if(auth()->user()->hasPermission('hr_employees.delete'))
    html += '<button type="button" class="btn btn-outline-danger btn-delete-item" data-url="' + row.delete_url + '" data-name="' + row.full_name + '"><i class="bx bx-trash"></i></button>';
    @endif
    html += '</div>';
    return html;
}

var table = $('#employee-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: "{{ route('admin.hr.employees.data') }}",
    columns: [
        { data: 'id', name: 'id', orderable: true, searchable: false },
        { data: 'employee_id', name: 'employee_id' },
        { data: 'full_name', name: 'full_name' },
        { data: 'email', name: 'email' },
        { 
            data: 'department.name', 
            name: 'department.name', 
            defaultContent: '-',
            render: function(data, type, row) { 
                return row.department ? row.department.name : '-'; 
            } 
        },
        { 
            data: 'designation.name', 
            name: 'designation.name', 
            defaultContent: '-',
            render: function(data, type, row) { 
                return row.designation ? row.designation.name : '-'; 
            } 
        },
        { data: 'basic_salary', name: 'basic_salary', render: function(data) { return parseFloat(data).toLocaleString('en', {minimumFractionDigits: 2}); } },
        { data: 'status', name: 'status', orderable: false, searchable: false, render: function(data, type, row) {
            return employeeStatusBadge(data, row.status_url);
        }},
        { data: 'actions', name: 'actions', orderable: false, searchable: false, render: function(data, type, row) {
            return employeeActions(row);
        }}
    ],
    order: [[0, 'desc']]
});
</script>
@endsection