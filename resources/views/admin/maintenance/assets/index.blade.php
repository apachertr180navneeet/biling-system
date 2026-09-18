@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-wrench"></i></div>
            <div>
                <h4 class="m-page-title">Assets</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Maintenance</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">Assets</li>
                </ul>
            </div>
        </div>
        @if(auth()->user()->hasPermission('maintenance_assets.create'))
        <a href="{{ route('admin.maintenance.assets.create') }}" class="btn btn-primary m-btn-add"><i class="bx bx-plus"></i> Add Asset</a>
        @endif
    </div>

    <div class="card">
        <div class="card-body">
            <table id="assets-table" class="table table-bordered table-striped" style="width:100%">
                <thead><tr><th>#</th><th>Asset Code</th><th>Name</th><th>Category</th><th>Brand</th><th>Location</th><th>Status</th><th>Actions</th></tr></thead>
            </table>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
var statusColors = { active: 'success', inactive: 'secondary', under_maintenance: 'warning', disposed: 'danger' };

function assetStatusBadge(status) {
    if (!status) return '<span class="badge bg-label-secondary">-</span>';
    var label = status.replace(/_/g, ' ');
    label = label.charAt(0).toUpperCase() + label.slice(1);
    return '<span class="badge bg-label-' + (statusColors[status] || 'secondary') + '">' + label + '</span>';
}

function assetActions(row) {
    var html = '<div class="btn-group btn-group-sm">';
    @if(auth()->user()->hasPermission('maintenance_assets.view'))
    html += '<a href="' + row.show_url + '" class="btn btn-outline-info"><i class="bx bx-show"></i></a>';
    @endif
    @if(auth()->user()->hasPermission('maintenance_assets.edit'))
    html += '<a href="' + row.edit_url + '" class="btn btn-outline-primary"><i class="bx bx-edit"></i></a>';
    @endif
    @if(auth()->user()->hasPermission('maintenance_assets.delete'))
    html += '<button type="button" class="btn btn-outline-danger btn-delete-item" data-url="' + row.delete_url + '" data-name="' + row.name + '"><i class="bx bx-trash"></i></button>';
    @endif
    html += '</div>';
    return html;
}

var table = $('#assets-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: "{{ route('admin.maintenance.assets.data') }}",
    columns: [
        { data: 'id', name: 'id', orderable: false, searchable: false },
        { data: 'asset_code', name: 'asset_code' },
        { data: 'name', name: 'name' },
        { data: 'category', name: 'category' },
        { data: 'brand', name: 'brand', render: function(data) { return data || '-'; } },
        { data: 'location', name: 'location', render: function(data) { return data || '-'; } },
        { data: 'status', name: 'status', orderable: false, searchable: false, render: function(data) {
            return assetStatusBadge(data);
        }},
        { data: 'actions', name: 'actions', orderable: false, searchable: false, render: function(data, type, row) {
            return assetActions(row);
        }}
    ],
    order: [[0, 'desc']]
});
</script>
@endsection
