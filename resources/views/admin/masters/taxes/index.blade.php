@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-receipt"></i></div>
            <div>
                <h4 class="m-page-title">Taxes</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Masters</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">Taxes</li>
                </ul>
            </div>
        </div>
        @if(auth()->user()->hasPermission('taxes.create'))
        <a href="{{ route('admin.masters.taxes.create') }}" class="btn btn-primary m-btn-add"><i class="bx bx-plus"></i> Add Tax</a>
        @endif
    </div>

    <div class="card">
        <div class="card-body">
            <table id="taxes-table" class="table table-bordered table-striped" style="width:100%">
                <thead><tr><th>#</th><th>Name</th><th>Rate</th><th>Type</th><th>Default</th><th>Status</th><th>Actions</th></tr></thead>
            </table>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
function taxesRateDisplay(rate, type) {
    return rate + (type === 'percentage' ? '%' : '');
}

function taxesTypeBadge(type) {
    return '<span class="badge bg-label-info">' + type.charAt(0).toUpperCase() + type.slice(1) + '</span>';
}

function taxesDefaultBadge(isDefault) {
    return '<span class="badge bg-' + (isDefault ? 'success' : 'secondary') + '">' + (isDefault ? 'Yes' : 'No') + '</span>';
}

function taxesStatusBadge(status, statusUrl) {
    @if(auth()->user()->hasPermission('taxes.edit'))
    return '<button type="button" class="btn btn-sm btn-status-toggle btn-' + (status === 'active' ? 'success' : 'warning') + '" data-url="' + statusUrl + '">' + status.charAt(0).toUpperCase() + status.slice(1) + '</button>';
    @else
    return '<span class="badge bg-label-' + (status === 'active' ? 'success' : 'warning') + '">' + status.charAt(0).toUpperCase() + status.slice(1) + '</span>';
    @endif
}

function taxesActions(row) {
    var html = '<div class="btn-group btn-group-sm">';
    @if(auth()->user()->hasPermission('taxes.edit'))
    html += '<a href="' + row.edit_url + '" class="btn btn-outline-primary"><i class="bx bx-edit"></i></a>';
    @endif
    @if(auth()->user()->hasPermission('taxes.delete'))
    html += '<button type="button" class="btn btn-outline-danger btn-delete-item" data-url="' + row.delete_url + '" data-name="' + row.name + '"><i class="bx bx-trash"></i></button>';
    @endif
    html += '</div>';
    return html;
}

var table = $('#taxes-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: "{{ route('admin.masters.taxes.data') }}",
    columns: [
        { data: 'id', name: 'id', orderable: false, searchable: false },
        { data: 'name', name: 'name' },
        { data: 'rate', name: 'rate', render: function(data, type, row) { return taxesRateDisplay(data, row.type); }},
        { data: 'type', name: 'type', orderable: false, searchable: false, render: function(data) {
            return taxesTypeBadge(data);
        }},
        { data: 'is_default', name: 'is_default', orderable: false, searchable: false, render: function(data) {
            return taxesDefaultBadge(data);
        }},
        { data: 'status', name: 'status', orderable: false, searchable: false, render: function(data, type, row) {
            return taxesStatusBadge(data, row.status_url);
        }},
        { data: 'actions', name: 'actions', orderable: false, searchable: false, render: function(data, type, row) {
            return taxesActions(row);
        }}
    ],
    order: [[0, 'desc']]
});
</script>
@endsection
