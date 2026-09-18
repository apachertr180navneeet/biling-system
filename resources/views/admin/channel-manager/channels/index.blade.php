@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-link"></i></div>
            <div>
                <h4 class="m-page-title">OTA Channels</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Channel Manager</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">OTA Channels</li>
                </ul>
            </div>
        </div>
        <div class="d-flex gap-2">
            <form action="{{ route('admin.channel-manager.sync-all') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-success"><i class="bx bx-sync"></i> Sync All</button>
            </form>
            @if(auth()->user()->hasPermission('channel_manager.create'))
            <a href="{{ route('admin.channel-manager.channels.create') }}" class="btn btn-primary m-btn-add"><i class="bx bx-plus"></i> Add Channel</a>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <table id="channels-table" class="table table-bordered table-striped" style="width:100%">
                <thead><tr><th>#</th><th>Name</th><th>Provider</th><th>Hotel</th><th>Property ID</th><th>Last Sync</th><th>Status</th><th>Actions</th></tr></thead>
            </table>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
function channelsStatusBadge(status, statusUrl) {
    @if(auth()->user()->hasPermission('channel_manager.edit'))
    return '<button type="button" class="btn btn-sm btn-status-toggle btn-' + (status === 'active' ? 'success' : 'warning') + '" data-url="' + statusUrl + '">' + status.charAt(0).toUpperCase() + status.slice(1) + '</button>';
    @else
    return '<span class="badge bg-label-' + (status === 'active' ? 'success' : 'warning') + '">' + status.charAt(0).toUpperCase() + status.slice(1) + '</span>';
    @endif
}

function channelsActions(row) {
    var html = '<div class="btn-group btn-group-sm">';
    @if(auth()->user()->hasPermission('channel_manager.edit'))
    html += '<a href="' + row.edit_url + '" class="btn btn-outline-primary"><i class="bx bx-edit"></i></a>';
    html += '<form action="' + row.sync_url + '" method="POST" class="d-inline">@csrf<button type="submit" class="btn btn-outline-success" title="Sync"><i class="bx bx-sync"></i></button></form>';
    @endif
    @if(auth()->user()->hasPermission('channel_manager.delete'))
    html += '<button type="button" class="btn btn-outline-danger btn-delete-item" data-url="' + row.delete_url + '" data-name="' + row.name + '"><i class="bx bx-trash"></i></button>';
    @endif
    html += '</div>';
    return html;
}

var table = $('#channels-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: "{{ route('admin.channel-manager.channels.data') }}",
    columns: [
        { data: 'id', name: 'id', orderable: false, searchable: false },
        { data: 'name', name: 'name' },
        { data: 'provider_label', name: 'provider', orderable: false, searchable: false },
        { data: 'hotel_name', name: 'hotel_name', orderable: false, searchable: false },
        { data: 'property_id_on_ota', name: 'property_id_on_ota', orderable: false, searchable: false },
        { data: 'last_sync', name: 'last_synced_at', orderable: false, searchable: false },
        { data: 'status', name: 'status', orderable: false, searchable: false, render: function(data, type, row) {
            return channelsStatusBadge(data, row.status_url);
        }},
        { data: 'actions', name: 'actions', orderable: false, searchable: false, render: function(data, type, row) {
            return channelsActions(row);
        }}
    ],
    order: [[0, 'desc']]
});
</script>
@endsection
