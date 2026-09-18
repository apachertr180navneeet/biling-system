@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-file"></i></div>
            <div>
                <h4 class="m-page-title">GST Returns</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Finance</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">GST Returns</li>
                </ul>
            </div>
        </div>
        @if(auth()->user()->hasPermission('gst_returns.create'))
        <a href="{{ route('admin.finance.gst.create') }}" class="btn btn-primary m-btn-add"><i class="bx bx-plus"></i> Add Return</a>
        @endif
    </div>

    <div class="card">
        <div class="card-body">
            <table id="gst-table" class="table table-bordered table-striped" style="width:100%">
                <thead><tr><th>#</th><th>Return Number</th><th>Period</th><th>Return Type</th><th>Total Tax</th><th>Status</th><th>Actions</th></tr></thead>
            </table>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
function gstStatusBadge(status, statusUrl) {
    @if(auth()->user()->hasPermission('gst_returns.edit'))
    var colors = { draft: 'warning', filed: 'success', pending: 'info', overdue: 'danger' };
    return '<button type="button" class="btn btn-sm btn-status-toggle bg-label-' + (colors[status] || 'secondary') + ' border-0" data-url="' + statusUrl + '">' + status.charAt(0).toUpperCase() + status.slice(1) + '</button>';
    @else
    var colors = { draft: 'warning', filed: 'success', pending: 'info', overdue: 'danger' };
    return '<span class="badge bg-label-' + (colors[status] || 'secondary') + '">' + status.charAt(0).toUpperCase() + status.slice(1) + '</span>';
    @endif
}

function gstActions(row) {
    var html = '<div class="btn-group btn-group-sm">';
    @if(auth()->user()->hasPermission('gst_returns.view'))
    html += '<a href="' + row.show_url + '" class="btn btn-outline-info"><i class="bx bx-show"></i></a>';
    @endif
    @if(auth()->user()->hasPermission('gst_returns.edit'))
    html += '<a href="' + row.edit_url + '" class="btn btn-outline-primary"><i class="bx bx-edit"></i></a>';
    @endif
    @if(auth()->user()->hasPermission('gst_returns.delete'))
    html += '<button type="button" class="btn btn-outline-danger btn-delete-item" data-url="' + row.delete_url + '" data-name="' + row.return_number + '"><i class="bx bx-trash"></i></button>';
    @endif
    html += '</div>';
    return html;
}

var table = $('#gst-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: "{{ route('admin.finance.gst.data') }}",
    columns: [
        { data: 'id', name: 'id', orderable: false, searchable: false },
        { data: 'return_number', name: 'return_number' },
        { data: 'period', name: 'period' },
        { data: 'return_type', name: 'return_type', render: function(data) { return data ? data.toUpperCase() : ''; } },
        { data: 'total_tax', name: 'total_tax', render: function(data) { return parseFloat(data).toFixed(2); } },
        { data: 'status', name: 'status', orderable: false, searchable: false, render: function(data, type, row) {
            return gstStatusBadge(data, row.status_url);
        }},
        { data: 'actions', name: 'actions', orderable: false, searchable: false, render: function(data, type, row) {
            return gstActions(row);
        }}
    ],
    order: [[0, 'desc']]
});
</script>
@endsection
