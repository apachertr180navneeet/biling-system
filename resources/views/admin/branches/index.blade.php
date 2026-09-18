@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-map-pin"></i></div>
            <div>
                <h4 class="m-page-title">Branches</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Company Setup</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">Branches</li>
                </ul>
            </div>
        </div>
        @if(auth()->user()->hasPermission('branches.create'))
        <a href="{{ route('admin.branches.create') }}" class="btn btn-primary m-btn-add"><i class="bx bx-plus"></i> Add Branch</a>
        @endif
    </div>

    <div class="card">
        <div class="card-body">
            <table id="branches-table" class="table table-bordered table-striped" style="width:100%">
                <thead>
                    <tr><th>#</th><th>Name</th><th>Company</th><th>Email</th><th>Phone</th><th>Head Office</th><th>Status</th><th>Actions</th></tr>
                </thead>
            </table>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
function branchesHeadOfficeBadge(isHead) {
    return '<span class="badge bg-' + (isHead ? 'success' : 'secondary') + '">' + (isHead ? 'Yes' : 'No') + '</span>';
}

function branchesStatusBadge(status, statusUrl) {
    @if(auth()->user()->hasPermission('branches.edit'))
    return '<button type="button" class="btn btn-sm btn-status-toggle btn-' + (status === 'active' ? 'success' : 'warning') + '" data-url="' + statusUrl + '">' + status.charAt(0).toUpperCase() + status.slice(1) + '</button>';
    @else
    return '<span class="badge bg-label-' + (status === 'active' ? 'success' : 'warning') + '">' + status.charAt(0).toUpperCase() + status.slice(1) + '</span>';
    @endif
}

function branchesActions(row) {
    var html = '<div class="btn-group btn-group-sm">';
    @if(auth()->user()->hasPermission('branches.edit'))
    html += '<a href="' + row.edit_url + '" class="btn btn-outline-primary"><i class="bx bx-edit"></i></a>';
    @endif
    @if(auth()->user()->hasPermission('branches.delete'))
    html += '<button type="button" class="btn btn-outline-danger btn-delete-item" data-url="' + row.delete_url + '" data-name="' + row.name + '"><i class="bx bx-trash"></i></button>';
    @endif
    html += '</div>';
    return html;
}

var table = $('#branches-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: "{{ route('admin.branches.data') }}",
    columns: [
        { data: 'id', name: 'id', orderable: false, searchable: false },
        { data: 'name', name: 'name' },
        { data: 'company_name', name: 'company_name', orderable: false, searchable: false },
        { data: 'email', name: 'email' },
        { data: 'phone', name: 'phone' },
        { data: 'is_head_office', name: 'is_head_office', orderable: false, searchable: false, render: function(data) {
            return branchesHeadOfficeBadge(data);
        }},
        { data: 'status', name: 'status', orderable: false, searchable: false, render: function(data, type, row) {
            return branchesStatusBadge(data, row.status_url);
        }},
        { data: 'actions', name: 'actions', orderable: false, searchable: false, render: function(data, type, row) {
            return branchesActions(row);
        }}
    ],
    order: [[0, 'desc']]
});
</script>
@endsection
