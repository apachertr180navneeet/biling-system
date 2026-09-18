@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-layer"></i></div>
            <div>
                <h4 class="m-page-title">Tax Groups</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Masters</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">Tax Groups</li>
                </ul>
            </div>
        </div>
        @if(auth()->user()->hasPermission('tax_groups.create'))
        <a href="{{ route('admin.masters.tax-groups.create') }}" class="btn btn-primary m-btn-add"><i class="bx bx-plus"></i> Add Tax Group</a>
        @endif
    </div>

    <div class="card">
        <div class="card-body">
            <table id="tax-groups-table" class="table table-bordered table-striped" style="width:100%">
                <thead><tr><th>#</th><th>Name</th><th>Description</th><th>Taxes</th><th>Total Rate</th><th>Status</th><th>Actions</th></tr></thead>
            </table>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
function taxGroupsDescription(data) {
    if (!data) return '-';
    return data.length > 50 ? data.substring(0, 50) + '...' : data;
}

function taxGroupsTotalRate(rate) {
    return rate + '%';
}

function taxGroupsStatusBadge(status) {
    return '<span class="badge bg-label-' + (status === 'active' ? 'success' : 'warning') + '">' + status.charAt(0).toUpperCase() + status.slice(1) + '</span>';
}

function taxGroupsActions(row) {
    var html = '<div class="btn-group btn-group-sm">';
    @if(auth()->user()->hasPermission('tax_groups.edit'))
    html += '<a href="' + row.edit_url + '" class="btn btn-outline-primary"><i class="bx bx-edit"></i></a>';
    @endif
    @if(auth()->user()->hasPermission('tax_groups.delete'))
    html += '<button type="button" class="btn btn-outline-danger btn-delete-item" data-url="' + row.delete_url + '" data-name="' + row.name + '"><i class="bx bx-trash"></i></button>';
    @endif
    html += '</div>';
    return html;
}

var table = $('#tax-groups-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: "{{ route('admin.masters.tax-groups.data') }}",
    columns: [
        { data: 'id', name: 'id', orderable: false, searchable: false },
        { data: 'name', name: 'name' },
        { data: 'description', name: 'description', orderable: false, searchable: false, render: function(data) {
            return taxGroupsDescription(data);
        }},
        { data: 'taxes_names', name: 'taxes_names', orderable: false, searchable: false },
        { data: 'total_rate', name: 'total_rate', orderable: false, searchable: false, render: function(data) {
            return taxGroupsTotalRate(data);
        }},
        { data: 'status', name: 'status', orderable: false, searchable: false, render: function(data) {
            return taxGroupsStatusBadge(data);
        }},
        { data: 'actions', name: 'actions', orderable: false, searchable: false, render: function(data, type, row) {
            return taxGroupsActions(row);
        }}
    ],
    order: [[0, 'desc']]
});
</script>
@endsection
