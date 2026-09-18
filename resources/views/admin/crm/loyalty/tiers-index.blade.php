@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-medal"></i></div>
            <div>
                <h4 class="m-page-title">Loyalty Tiers</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Guest CRM</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="{{ route('admin.crm.loyalty.index') }}">Loyalty</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">Tiers</li>
                </ul>
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.crm.loyalty.index') }}" class="btn btn-outline-secondary"><i class="bx bx-left-arrow-alt me-1"></i> Members</a>
            @if(auth()->user()->hasPermission('loyalty_tiers.create'))
            <a href="{{ route('admin.crm.loyalty.tiers.create') }}" class="btn btn-primary m-btn-add"><i class="bx bx-plus"></i> Add Tier</a>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <table id="tiers-table" class="table table-bordered table-striped" style="width:100%">
                <thead><tr><th>#</th><th>Tier</th><th>Min Points</th><th>Discount</th><th>Points Multiplier</th><th>Status</th><th>Actions</th></tr></thead>
            </table>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
function tiersStatusBadge(status, statusUrl) {
    @if(auth()->user()->hasPermission('loyalty_tiers.edit'))
    return '<button type="button" class="btn btn-sm btn-status-toggle btn-' + (status === 'active' ? 'success' : 'warning') + '" data-url="' + statusUrl + '">' + status.charAt(0).toUpperCase() + status.slice(1) + '</button>';
    @else
    return '<span class="badge bg-label-' + (status === 'active' ? 'success' : 'warning') + '">' + status.charAt(0).toUpperCase() + status.slice(1) + '</span>';
    @endif
}

function tiersActions(row) {
    var html = '<div class="btn-group btn-group-sm">';
    @if(auth()->user()->hasPermission('loyalty_tiers.edit'))
    html += '<a href="' + row.edit_url + '" class="btn btn-outline-primary"><i class="bx bx-edit"></i></a>';
    @endif
    @if(auth()->user()->hasPermission('loyalty_tiers.delete'))
    html += '<button type="button" class="btn btn-outline-danger btn-delete-item" data-url="' + row.delete_url + '" data-name="' + row.color_badge + '"><i class="bx bx-trash"></i></button>';
    @endif
    html += '</div>';
    return html;
}

var table = $('#tiers-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: "{{ route('admin.crm.loyalty.tiers.data') }}",
    columns: [
        { data: 'id', name: 'id', orderable: false, searchable: false },
        { data: 'color_badge', name: 'name', orderable: false, searchable: false },
        { data: 'min_points_formatted', name: 'min_points', orderable: false, searchable: false },
        { data: 'discount_badge', name: 'discount_percentage', orderable: false, searchable: false },
        { data: 'multiplier_badge', name: 'points_multiplier', orderable: false, searchable: false },
        { data: 'status', name: 'status', orderable: false, searchable: false, render: function(data, type, row) {
            return tiersStatusBadge(data, row.status_url);
        }},
        { data: 'actions', name: 'actions', orderable: false, searchable: false, render: function(data, type, row) {
            return tiersActions(row);
        }}
    ],
    order: [[0, 'asc']]
});
</script>
@endsection
