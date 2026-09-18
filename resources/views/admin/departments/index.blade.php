@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-sitemap"></i></div>
            <div>
                <h4 class="m-page-title">Departments</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Company Setup</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">Departments</li>
                </ul>
            </div>
        </div>
        @if(auth()->user()->hasPermission('departments.create'))
        <a href="{{ route('admin.departments.create') }}" class="btn btn-primary m-btn-add"><i class="bx bx-plus"></i> Add Department</a>
        @endif
    </div>

    <div class="card">
        <div class="card-body">
            <table id="departments-table" class="table table-bordered table-striped" style="width:100%">
                <thead><tr><th>#</th><th>Name</th><th>Company</th><th>Description</th><th>Status</th><th>Actions</th></tr></thead>
            </table>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
function departmentsDescription(data) {
    if (!data) return '-';
    return data.length > 50 ? data.substring(0, 50) + '...' : data;
}

function departmentsStatusBadge(status, statusUrl) {
    @if(auth()->user()->hasPermission('departments.edit'))
    return '<button type="button" class="btn btn-sm btn-status-toggle btn-' + (status === 'active' ? 'success' : 'warning') + '" data-url="' + statusUrl + '">' + status.charAt(0).toUpperCase() + status.slice(1) + '</button>';
    @else
    return '<span class="badge bg-label-' + (status === 'active' ? 'success' : 'warning') + '">' + status.charAt(0).toUpperCase() + status.slice(1) + '</span>';
    @endif
}

function departmentsActions(row) {
    var html = '<div class="btn-group btn-group-sm">';
    @if(auth()->user()->hasPermission('departments.edit'))
    html += '<a href="' + row.edit_url + '" class="btn btn-outline-primary"><i class="bx bx-edit"></i></a>';
    @endif
    @if(auth()->user()->hasPermission('departments.delete'))
    html += '<button type="button" class="btn btn-outline-danger btn-delete-item" data-url="' + row.delete_url + '" data-name="' + row.name + '"><i class="bx bx-trash"></i></button>';
    @endif
    html += '</div>';
    return html;
}

var table = $('#departments-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: "{{ route('admin.departments.data') }}",
    columns: [
        { data: 'id', name: 'id', orderable: false, searchable: false },
        { data: 'name', name: 'name' },
        { data: 'company_name', name: 'company_name', orderable: false, searchable: false },
        { data: 'description', name: 'description', orderable: false, searchable: false, render: function(data) {
            return departmentsDescription(data);
        }},
        { data: 'status', name: 'status', orderable: false, searchable: false, render: function(data, type, row) {
            return departmentsStatusBadge(data, row.status_url);
        }},
        { data: 'actions', name: 'actions', orderable: false, searchable: false, render: function(data, type, row) {
            return departmentsActions(row);
        }}
    ],
    order: [[0, 'desc']]
});
</script>
@endsection
