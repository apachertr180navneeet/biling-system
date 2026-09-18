@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-paper-plane"></i></div>
            <div>
                <h4 class="m-page-title">Marketing Campaigns</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Guest CRM</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">Campaigns</li>
                </ul>
            </div>
        </div>
        @if(auth()->user()->hasPermission('marketing_campaigns.create'))
        <a href="{{ route('admin.crm.campaigns.create') }}" class="btn btn-primary m-btn-add"><i class="bx bx-plus"></i> Add Campaign</a>
        @endif
    </div>

    <div class="card">
        <div class="card-body">
            <table id="campaigns-table" class="table table-bordered table-striped" style="width:100%">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Campaign Name</th>
                        <th>Channel</th>
                        <th>Target Audience</th>
                        <th>Scheduled At</th>
                        <th>Sent At</th>
                        <th>Metrics</th>
                        <th>Status</th>
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
var statusColors = {
    draft: 'secondary',
    scheduled: 'warning',
    sending: 'info',
    sent: 'success',
    failed: 'danger'
};

var channelBadges = {
    email: '<span class="badge bg-label-info"><i class="bx bx-envelope me-1"></i> Email</span>',
    sms: '<span class="badge bg-label-primary"><i class="bx bx-message-detail me-1"></i> SMS</span>',
    whatsapp: '<span class="badge bg-label-success"><i class="bx bxl-whatsapp me-1"></i> WhatsApp</span>'
};

var audienceLabels = {
    all_guests: 'All Guests',
    loyalty_members: 'Loyalty Members',
    recent_guests: 'Recent Guests'
};

function statusBadge(status) {
    if (!status) return '<span class="badge bg-label-secondary">-</span>';
    var label = status.charAt(0).toUpperCase() + status.slice(1);
    return '<span class="badge bg-label-' + (statusColors[status] || 'secondary') + '">' + label + '</span>';
}

function formatDate(dateString) {
    if (!dateString) return '-';
    var d = new Date(dateString);
    var pad = function(n) { return ('0' + n).slice(-2); };
    return pad(d.getDate()) + '-' + pad(d.getMonth() + 1) + '-' + d.getFullYear() + ' ' + pad(d.getHours()) + ':' + pad(d.getMinutes());
}

function campaignActions(row) {
    var html = '<div class="btn-group btn-group-sm">';
    @if(auth()->user()->hasPermission('marketing_campaigns.view'))
    html += '<a href="' + row.show_url + '" class="btn btn-outline-info" title="View Details"><i class="bx bx-show"></i></a>';
    @endif
    
    if (row.status !== 'sent') {
        @if(auth()->user()->hasPermission('marketing_campaigns.edit'))
        html += '<a href="' + row.edit_url + '" class="btn btn-outline-primary" title="Edit"><i class="bx bx-edit"></i></a>';
        @endif
        @if(auth()->user()->hasPermission('marketing_campaigns.edit'))
        html += '<a href="' + row.send_url + '" class="btn btn-outline-success" title="Send Now"><i class="bx bx-send"></i></a>';
        @endif
    }
    
    @if(auth()->user()->hasPermission('marketing_campaigns.delete'))
    html += '<button type="button" class="btn btn-outline-danger btn-delete-item" data-url="' + row.delete_url + '" data-name="' + row.name + '" title="Delete"><i class="bx bx-trash"></i></button>';
    @endif
    html += '</div>';
    return html;
}

var table = $('#campaigns-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: "{{ route('admin.crm.campaigns.data') }}",
    columns: [
        { data: 'id', name: 'id', orderable: false, searchable: false },
        { data: 'name', name: 'name' },
        { data: 'channel', name: 'channel', render: function(data) { return channelBadges[data] || data; } },
        { data: 'target_audience', name: 'target_audience', render: function(data) { return audienceLabels[data] || data; } },
        { data: 'scheduled_at', name: 'scheduled_at', render: function(data) { return formatDate(data); } },
        { data: 'sent_at', name: 'sent_at', render: function(data) { return formatDate(data); } },
        { data: 'total_recipients', name: 'total_recipients', render: function(data, type, row) {
            if (row.status === 'draft' || row.status === 'scheduled') return '-';
            return row.successful_deliveries + ' / ' + row.total_recipients;
        }},
        { data: 'status', name: 'status', orderable: false, searchable: false, render: function(data) {
            return statusBadge(data);
        }},
        { data: 'actions', name: 'actions', orderable: false, searchable: false, render: function(data, type, row) {
            return campaignActions(row);
        }}
    ],
    order: [[0, 'desc']]
});
</script>
@endsection
