@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-shield"></i></div>
            <div>
                <h4 class="m-page-title">Roles & Permissions</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">Roles</li>
                </ul>
            </div>
        </div>
        @if(auth()->user()->hasPermission('roles.create'))
        <a href="{{ route('admin.roles.create') }}" class="btn btn-primary m-btn-add"><i class="bx bx-plus"></i> Add Role</a>
        @endif
    </div>

    <div class="card">
        <div class="card-body">
            <table id="roles-table" class="table table-bordered table-striped" style="width:100%">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Slug</th>
                        <th>Description</th>
                        <th>Permissions</th>
                        <th>System</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
function rolesSlug(slug) {
    return '<code>' + slug + '</code>';
}

function rolesDescription(data) {
    return data || '-';
}

function rolesPermissionsCount(count) {
    return '<span class="badge bg-info">' + count + '</span>';
}

function rolesSystemBadge(isSystem) {
    return isSystem ? '<span class="badge bg-label-warning">System</span>' : '<span class="badge bg-label-secondary">Custom</span>';
}

function rolesActions(row) {
    var html = '';
    @if(auth()->user()->hasPermission('roles.edit'))
    html += '<a href="' + row.edit_url + '" class="btn btn-sm btn-outline-primary"><i class="bx bx-edit"></i></a> ';
    @endif
    @if(auth()->user()->hasPermission('roles.delete'))
    if (!row.is_system) {
        html += '<button type="button" class="btn btn-sm btn-outline-danger btn-delete-item" data-url="' + row.delete_url + '" data-name="' + row.name + '"><i class="bx bx-trash"></i></button>';
    }
    @endif
    return html;
}

var table = $('#roles-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: "{{ route('admin.roles.data') }}",
    columns: [
        { data: 'id', name: 'id', orderable: false, searchable: false },
        { data: 'name', name: 'name' },
        { data: 'slug', name: 'slug', render: function(data) { return rolesSlug(data); }},
        { data: 'description', name: 'description', orderable: false, searchable: false, render: function(data) {
            return rolesDescription(data);
        }},
        { data: 'permissions_count', name: 'permissions_count', orderable: false, searchable: false, render: function(data) {
            return rolesPermissionsCount(data);
        }},
        { data: 'is_system', name: 'is_system', orderable: false, searchable: false, render: function(data) {
            return rolesSystemBadge(data);
        }},
        { data: 'actions', name: 'actions', orderable: false, searchable: false, render: function(data, type, row) {
            return rolesActions(row);
        }}
    ],
    order: [[0, 'desc']]
});
</script>
@endsection
