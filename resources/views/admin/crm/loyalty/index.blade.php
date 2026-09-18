@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-star"></i></div>
            <div>
                <h4 class="m-page-title">Loyalty Members</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Guest CRM</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">Loyalty</li>
                </ul>
            </div>
        </div>
        <div class="d-flex gap-2">
            @if(auth()->user()->hasPermission('loyalty_tiers.view'))
            <a href="{{ route('admin.crm.loyalty.tiers.index') }}" class="btn btn-outline-primary"><i class="bx bx-cog me-1"></i> Manage Tiers</a>
            @endif
            @if(auth()->user()->hasPermission('loyalty_members.create'))
            <a href="{{ route('admin.crm.loyalty.create') }}" class="btn btn-primary m-btn-add"><i class="bx bx-plus"></i> Enroll Member</a>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <table id="loyalty-table" class="table table-bordered table-striped" style="width:100%">
                <thead><tr><th>#</th><th>Member No</th><th>Guest</th><th>Tier</th><th>Points</th><th>Total Stays</th><th>Total Spent</th><th>Enrolled</th><th>Status</th><th>Actions</th></tr></thead>
            </table>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
function loyaltyStatusBadge(status, statusUrl) {
    @if(auth()->user()->hasPermission('loyalty_members.edit'))
    return '<button type="button" class="btn btn-sm btn-status-toggle btn-' + (status === 'active' ? 'success' : 'warning') + '" data-url="' + statusUrl + '">' + status.charAt(0).toUpperCase() + status.slice(1) + '</button>';
    @else
    return '<span class="badge bg-label-' + (status === 'active' ? 'success' : 'warning') + '">' + status.charAt(0).toUpperCase() + status.slice(1) + '</span>';
    @endif
}

function loyaltyActions(row) {
    var html = '<div class="btn-group btn-group-sm">';
    @if(auth()->user()->hasPermission('loyalty_members.view'))
    html += '<a href="' + row.transactions_url + '" class="btn btn-outline-info" title="Transactions"><i class="bx bx-history"></i></a>';
    @endif
    @if(auth()->user()->hasPermission('loyalty_members.edit'))
    html += '<a href="' + row.edit_url + '" class="btn btn-outline-primary"><i class="bx bx-edit"></i></a>';
    @endif
    @if(auth()->user()->hasPermission('loyalty_members.delete'))
    html += '<button type="button" class="btn btn-outline-danger btn-delete-item" data-url="' + row.delete_url + '" data-name="' + row.member_number + '"><i class="bx bx-trash"></i></button>';
    @endif
    html += '</div>';
    return html;
}

var table = $('#loyalty-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: "{{ route('admin.crm.loyalty.data') }}",
    columns: [
        { data: 'id', name: 'id', orderable: false, searchable: false },
        { data: 'member_number', name: 'member_number' },
        { data: 'guest_name', name: 'guest_name' },
        { data: 'tier_name', name: 'tier_name', orderable: false, searchable: false, render: function(data, type, row) {
            return '<span class="badge" style="background-color:' + row.tier_color + '">' + data + '</span>';
        }},
        { data: 'total_points', name: 'total_points' },
        { data: 'total_stays', name: 'total_stays', orderable: false, searchable: false },
        { data: 'total_spent_formatted', name: 'total_spent', orderable: false, searchable: false },
        { data: 'enrolled_date', name: 'enrolled_date', orderable: false, searchable: false, render: function(data) {
            return data ? new Date(data).toLocaleDateString('en-GB', {day: '2-digit', month: '2-digit', year: 'numeric'}) : '';
        }},
        { data: 'status', name: 'status', orderable: false, searchable: false, render: function(data, type, row) {
            return loyaltyStatusBadge(data, row.status_url);
        }},
        { data: 'actions', name: 'actions', orderable: false, searchable: false, render: function(data, type, row) {
            return loyaltyActions(row);
        }}
    ],
    order: [[0, 'desc']]
});
</script>
@endsection
