@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-directions"></i></div>
            <div>
                <h4 class="m-page-title">Wings</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Property</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">Wings</li>
                </ul>
            </div>
        </div>
        @if(auth()->user()->hasPermission('wings.create'))
        <a href="{{ route('admin.property.wings.create') }}" class="btn btn-primary m-btn-add"><i class="bx bx-plus"></i> Add Wing</a>
        @endif
    </div>

    <div class="card">
        <div class="card-body">
            <table id="wings-table" class="table table-bordered table-striped" style="width:100%">
                <thead><tr><th>#</th><th>Name</th><th>Floor</th><th>Description</th><th>Status</th><th>Actions</th></tr></thead>
            </table>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
function wingsStatusBadge(status, statusUrl) {
    @if(auth()->user()->hasPermission('wings.edit'))
    return '<button type="button" class="btn btn-sm btn-status-toggle btn-' + (status === 'active' ? 'success' : 'warning') + '" data-url="' + statusUrl + '">' + status.charAt(0).toUpperCase() + status.slice(1) + '</button>';
    @else
    return '<span class="badge bg-label-' + (status === 'active' ? 'success' : 'warning') + '">' + status.charAt(0).toUpperCase() + status.slice(1) + '</span>';
    @endif
}

function wingsActions(row) {
    var html = '<div class="btn-group btn-group-sm">';
    @if(auth()->user()->hasPermission('wings.edit'))
    html += '<a href="' + row.edit_url + '" class="btn btn-outline-primary"><i class="bx bx-edit"></i></a>';
    @endif
    @if(auth()->user()->hasPermission('wings.delete'))
    html += '<button type="button" class="btn btn-outline-danger btn-delete-item" data-url="' + row.delete_url + '" data-name="' + row.name + '"><i class="bx bx-trash"></i></button>';
    @endif
    html += '</div>';
    return html;
}

var table = $('#wings-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: "{{ route('admin.property.wings.data') }}",
    columns: [
        { data: 'id', name: 'id', orderable: false, searchable: false },
        { data: 'name', name: 'name' },
        { data: 'floor_name', name: 'floor_name', orderable: false, searchable: false },
        { data: 'description', name: 'description' },
        { data: 'status', name: 'status', orderable: false, searchable: false, render: function(data, type, row) {
            return wingsStatusBadge(data, row.status_url);
        }},
        { data: 'actions', name: 'actions', orderable: false, searchable: false, render: function(data, type, row) {
            return wingsActions(row);
        }}
    ],
    order: [[0, 'desc']]
});
</script>
@endsection