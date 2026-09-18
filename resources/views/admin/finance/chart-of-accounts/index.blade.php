@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-dollar"></i></div>
            <div>
                <h4 class="m-page-title">Chart of Accounts</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Finance</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">Chart of Accounts</li>
                </ul>
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.finance.chart-of-accounts.tree') }}" class="btn btn-outline-secondary"><i class="bx bx-tree"></i> Tree View</a>
            @if(auth()->user()->hasPermission('chart_of_accounts.create'))
            <a href="{{ route('admin.finance.chart-of-accounts.create') }}" class="btn btn-primary m-btn-add"><i class="bx bx-plus"></i> Add Account</a>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <table id="coa-table" class="table table-bordered table-striped" style="width:100%">
                <thead><tr><th>#</th><th>Code</th><th>Name</th><th>Type</th><th>Sub Type</th><th>Parent</th><th>Status</th><th>Actions</th></tr></thead>
            </table>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
function coaStatusBadge(status, statusUrl) {
    @if(auth()->user()->hasPermission('chart_of_accounts.edit'))
    return '<button type="button" class="btn btn-sm btn-status-toggle btn-' + (status === 'active' ? 'success' : 'warning') + '" data-url="' + statusUrl + '">' + status.charAt(0).toUpperCase() + status.slice(1) + '</button>';
    @else
    return '<span class="badge bg-label-' + (status === 'active' ? 'success' : 'warning') + '">' + status.charAt(0).toUpperCase() + status.slice(1) + '</span>';
    @endif
}

function coaActions(row) {
    var html = '<div class="btn-group btn-group-sm">';
    @if(auth()->user()->hasPermission('chart_of_accounts.edit'))
    html += '<a href="' + row.edit_url + '" class="btn btn-outline-primary"><i class="bx bx-edit"></i></a>';
    @endif
    @if(auth()->user()->hasPermission('chart_of_accounts.delete'))
    html += '<button type="button" class="btn btn-outline-danger btn-delete-item" data-url="' + row.delete_url + '" data-name="' + row.name + '"><i class="bx bx-trash"></i></button>';
    @endif
    html += '</div>';
    return html;
}

var table = $('#coa-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: "{{ route('admin.finance.chart-of-accounts.data') }}",
    columns: [
        { data: 'id', name: 'id', orderable: false, searchable: false },
        { data: 'code', name: 'code' },
        { data: 'name', name: 'name' },
        { data: 'type', name: 'type', render: function(data) { return '<span class="badge bg-label-info">' + data.charAt(0).toUpperCase() + data.slice(1) + '</span>'; }},
        { data: 'sub_type', name: 'sub_type', render: function(data) { return data ? data.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()) : '-'; }},
        { data: 'parent_name', name: 'parent_name' },
        { data: 'status', name: 'status', orderable: false, searchable: false, render: function(data, type, row) { return coaStatusBadge(data, row.status_url); }},
        { data: 'actions', name: 'actions', orderable: false, searchable: false, render: function(data, type, row) { return coaActions(row); }}
    ],
    order: [[2, 'asc']]
});
</script>
@endsection
