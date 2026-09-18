@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-user"></i></div>
            <div>
                <h4 class="m-page-title">Guest Profiles</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Guest CRM</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">Guest Profiles</li>
                </ul>
            </div>
        </div>
        @if(auth()->user()->hasPermission('guest_profiles.create'))
        <a href="{{ route('admin.crm.guest-profiles.create') }}" class="btn btn-primary m-btn-add"><i class="bx bx-plus"></i> Add Profile</a>
        @endif
    </div>

    <div class="card">
        <div class="card-body">
            <table id="profiles-table" class="table table-bordered table-striped" style="width:100%">
                <thead><tr><th>#</th><th>Guest</th><th>Email</th><th>Phone</th><th>Gender</th><th>ID Type</th><th>ID Number</th><th>VIP</th><th>Status</th><th>Actions</th></tr></thead>
            </table>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
function profilesStatusBadge(status, statusUrl) {
    @if(auth()->user()->hasPermission('guest_profiles.edit'))
    return '<button type="button" class="btn btn-sm btn-status-toggle btn-' + (status === 'active' ? 'success' : 'warning') + '" data-url="' + statusUrl + '">' + status.charAt(0).toUpperCase() + status.slice(1) + '</button>';
    @else
    return '<span class="badge bg-label-' + (status === 'active' ? 'success' : 'warning') + '">' + status.charAt(0).toUpperCase() + status.slice(1) + '</span>';
    @endif
}

function profilesActions(row) {
    var html = '<div class="btn-group btn-group-sm">';
    @if(auth()->user()->hasPermission('guest_profiles.edit'))
    html += '<a href="' + row.edit_url + '" class="btn btn-outline-primary"><i class="bx bx-edit"></i></a>';
    @endif
    @if(auth()->user()->hasPermission('guest_profiles.delete'))
    html += '<button type="button" class="btn btn-outline-danger btn-delete-item" data-url="' + row.delete_url + '" data-name="' + row.guest_name + '"><i class="bx bx-trash"></i></button>';
    @endif
    html += '</div>';
    return html;
}

var table = $('#profiles-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: "{{ route('admin.crm.guest-profiles.data') }}",
    columns: [
        { data: 'id', name: 'id', orderable: false, searchable: false },
        { data: 'guest_name', name: 'guest_name' },
        { data: 'guest_email', name: 'guest_email', orderable: false, searchable: false },
        { data: 'guest_phone', name: 'guest_phone', orderable: false, searchable: false },
        { data: 'gender', name: 'gender', orderable: false, searchable: false },
        { data: 'id_type', name: 'id_type', orderable: false, searchable: false },
        { data: 'id_number', name: 'id_number' },
        { data: 'vip_badge', name: 'vip_status', orderable: false, searchable: false },
        { data: 'status', name: 'status', orderable: false, searchable: false, render: function(data, type, row) {
            return profilesStatusBadge(data, row.status_url);
        }},
        { data: 'actions', name: 'actions', orderable: false, searchable: false, render: function(data, type, row) {
            return profilesActions(row);
        }}
    ],
    order: [[0, 'desc']]
});
</script>
@endsection
