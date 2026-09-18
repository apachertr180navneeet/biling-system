@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-book"></i></div>
            <div>
                <h4 class="m-page-title">Journal Entries</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Finance</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">Journal Entries</li>
                </ul>
            </div>
        </div>
        @if(auth()->user()->hasPermission('journal_entries.create'))
        <a href="{{ route('admin.finance.journal-entries.create') }}" class="btn btn-primary m-btn-add"><i class="bx bx-plus"></i> New Journal Entry</a>
        @endif
    </div>

    <div class="card">
        <div class="card-body">
            <table id="je-table" class="table table-bordered table-striped" style="width:100%">
                <thead><tr><th>#</th><th>Entry No.</th><th>Date</th><th>Type</th><th>Description</th><th>Debit</th><th>Credit</th><th>Status</th><th>Actions</th></tr></thead>
            </table>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
function jeStatusBadge(status, statusUrl) {
    var colors = { 'draft': 'warning', 'posted': 'success', 'void': 'danger' };
    @if(auth()->user()->hasPermission('journal_entries.edit'))
    return '<button type="button" class="btn btn-sm btn-status-toggle bg-label-' + (colors[status] || 'secondary') + ' border-0" data-url="' + statusUrl + '">' + status.charAt(0).toUpperCase() + status.slice(1) + '</button>';
    @else
    return '<span class="badge bg-label-' + (colors[status] || 'secondary') + '">' + status.charAt(0).toUpperCase() + status.slice(1) + '</span>';
    @endif
}

function jeActions(row) {
    var html = '<div class="btn-group btn-group-sm">';
    @if(auth()->user()->hasPermission('journal_entries.view'))
    html += '<a href="' + row.view_url + '" class="btn btn-outline-info"><i class="bx bx-show"></i></a>';
    @endif
    @if(auth()->user()->hasPermission('journal_entries.edit'))
    if (row.status === 'draft') html += '<a href="' + row.edit_url + '" class="btn btn-outline-primary"><i class="bx bx-edit"></i></a>';
    @endif
    @if(auth()->user()->hasPermission('journal_entries.delete'))
    if (row.status === 'draft') html += '<button type="button" class="btn btn-outline-danger btn-delete-item" data-url="' + row.delete_url + '" data-name="' + row.entry_number + '"><i class="bx bx-trash"></i></button>';
    @endif
    html += '</div>';
    return html;
}

var table = $('#je-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: "{{ route('admin.finance.journal-entries.data') }}",
    columns: [
        { data: 'id', name: 'id', orderable: false, searchable: false },
        { data: 'entry_number', name: 'entry_number' },
        { data: 'entry_date', name: 'entry_date', render: function(data) { if (!data) return '-'; var d = new Date(data); return ('0' + d.getDate()).slice(-2) + '-' + ('0' + (d.getMonth()+1)).slice(-2) + '-' + d.getFullYear(); }},
        { data: 'type', name: 'type', render: function(data) { return data ? data.charAt(0).toUpperCase() + data.slice(1) : '-'; }},
        { data: 'description', name: 'description' },
        { data: 'total_debit', name: 'total_debit', render: function(data) { return parseFloat(data).toFixed(2); }},
        { data: 'total_credit', name: 'total_credit', render: function(data) { return parseFloat(data).toFixed(2); }},
        { data: 'status', name: 'status', orderable: false, searchable: false, render: function(data, type, row) { return jeStatusBadge(data, row.status_url); }},
        { data: 'actions', name: 'actions', orderable: false, searchable: false, render: function(data, type, row) { return jeActions(row); }}
    ],
    order: [[0, 'desc']]
});
</script>
@endsection
