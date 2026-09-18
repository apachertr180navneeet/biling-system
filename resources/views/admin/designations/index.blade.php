@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-id-card"></i></div>
            <div>
                <h4 class="m-page-title">Designations</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Company Setup</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">Designations</li>
                </ul>
            </div>
        </div>
        @if(auth()->user()->hasPermission('designations.create'))
        <a href="{{ route('admin.designations.create') }}" class="btn btn-primary m-btn-add"><i class="bx bx-plus"></i> Add Designation</a>
        @endif
    </div>

    <div class="card">
        <div class="card-body">
            <table id="designations-table" class="table table-bordered table-striped" style="width:100%">
                <thead><tr><th>#</th><th>Name</th><th>Department</th><th>Description</th><th>Status</th><th>Actions</th></tr></thead>
            </table>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
function designationsDescription(data) {
    if (!data) return '-';
    return data.length > 50 ? data.substring(0, 50) + '...' : data;
}

function designationsStatusBadge(status, statusUrl) {
    @if(auth()->user()->hasPermission('designations.edit'))
    return '<button type="button" class="btn btn-sm btn-status-toggle btn-' + (status === 'active' ? 'success' : 'warning') + '" data-url="' + statusUrl + '">' + status.charAt(0).toUpperCase() + status.slice(1) + '</button>';
    @else
    return '<span class="badge bg-label-' + (status === 'active' ? 'success' : 'warning') + '">' + status.charAt(0).toUpperCase() + status.slice(1) + '</span>';
    @endif
}

function designationsActions(row) {
    var html = '<div class="btn-group btn-group-sm">';
    @if(auth()->user()->hasPermission('designations.edit'))
    html += '<a href="' + row.edit_url + '" class="btn btn-outline-primary"><i class="bx bx-edit"></i></a>';
    @endif
    @if(auth()->user()->hasPermission('designations.delete'))
    html += '<button type="button" class="btn btn-outline-danger btn-delete-item" data-url="' + row.delete_url + '" data-name="' + row.name + '"><i class="bx bx-trash"></i></button>';
    @endif
    html += '</div>';
    return html;
}

var table = $('#designations-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: "{{ route('admin.designations.data') }}",
    columns: [
        { data: 'id', name: 'id', orderable: false, searchable: false },
        { data: 'name', name: 'name' },
        { data: 'department_name', name: 'department_name', orderable: false, searchable: false },
        { data: 'description', name: 'description', orderable: false, searchable: false, render: function(data) {
            return designationsDescription(data);
        }},
        { data: 'status', name: 'status', orderable: false, searchable: false, render: function(data, type, row) {
            return designationsStatusBadge(data, row.status_url);
        }},
        { data: 'actions', name: 'actions', orderable: false, searchable: false, render: function(data, type, row) {
            return designationsActions(row);
        }}
    ],
    order: [[0, 'desc']]
});
</script>
@endsection
