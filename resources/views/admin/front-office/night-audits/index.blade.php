@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-moon"></i></div>
            <div>
                <h4 class="m-page-title">Night Audit</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Front Office</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">Night Audit</li>
                </ul>
            </div>
        </div>
        @if(auth()->user()->hasPermission('night_audits.create'))
        <a href="{{ route('admin.front-office.night-audits.create') }}" class="btn btn-primary m-btn-add"><i class="bx bx-plus"></i> Run Night Audit</a>
        @endif
    </div>

    <div class="card">
        <div class="card-body">
            <table id="nightaudits-table" class="table table-bordered table-striped" style="width:100%">
                <thead><tr><th>#</th><th>Audit Date</th><th>Hotel</th><th>Rooms Occupied</th><th>Revenue</th><th>Payments</th><th>Outstanding</th><th>Status</th><th>Audited By</th><th>Actions</th></tr></thead>
            </table>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
function nightAuditsStatusBadge(status, statusUrl) {
    var colors = { 'pending': 'warning', 'completed': 'success', 'cancelled': 'danger' };
    return '<span class="badge bg-label-' + (colors[status] || 'secondary') + '">' + status.charAt(0).toUpperCase() + status.slice(1) + '</span>';
}

function nightAuditsActions(row) {
    var html = '<div class="btn-group btn-group-sm">';
    @if(auth()->user()->hasPermission('night_audits.edit'))
    html += '<a href="' + row.edit_url + '" class="btn btn-outline-primary"><i class="bx bx-edit"></i></a>';
    @endif
    @if(auth()->user()->hasPermission('night_audits.delete'))
    html += '<button type="button" class="btn btn-outline-danger btn-delete-item" data-url="' + row.delete_url + '" data-name="Night Audit ' + row.audit_date_formatted + '"><i class="bx bx-trash"></i></button>';
    @endif
    html += '</div>';
    return html;
}

var table = $('#nightaudits-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: "{{ route('admin.front-office.night-audits.data') }}",
    columns: [
        { data: 'id', name: 'id', orderable: false, searchable: false },
        { data: 'audit_date_formatted', name: 'audit_date' },
        { data: 'hotel_name', name: 'hotel_name', orderable: false, searchable: false },
        { data: 'total_rooms_occupied', name: 'total_rooms_occupied', orderable: false, searchable: false },
        { data: 'total_revenue_formatted', name: 'total_revenue', orderable: false, searchable: false },
        { data: 'total_payments_formatted', name: 'total_payments_received', orderable: false, searchable: false },
        { data: 'total_outstanding_formatted', name: 'total_outstanding', orderable: false, searchable: false },
        { data: 'status', name: 'status', orderable: false, searchable: false, render: function(data) {
            return nightAuditsStatusBadge(data);
        }},
        { data: 'audited_by_name', name: 'audited_by_name', orderable: false, searchable: false },
        { data: 'actions', name: 'actions', orderable: false, searchable: false, render: function(data, type, row) {
            return nightAuditsActions(row);
        }}
    ],
    order: [[0, 'desc']]
});
</script>
@endsection
