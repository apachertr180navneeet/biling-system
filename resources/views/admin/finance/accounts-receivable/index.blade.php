@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-dollar"></i></div>
            <div>
                <h4 class="m-page-title">Accounts Receivable</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Finance</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">Accounts Receivable</li>
                </ul>
            </div>
        </div>
        @if(auth()->user()->hasPermission('accounts_receivable.create'))
        <a href="{{ route('admin.finance.accounts-receivable.create') }}" class="btn btn-primary m-btn-add"><i class="bx bx-plus"></i> Add Invoice</a>
        @endif
    </div>

    <div class="card">
        <div class="card-body">
            <table id="ar-table" class="table table-bordered table-striped" style="width:100%">
                <thead><tr><th>#</th><th>Invoice Number</th><th>Guest</th><th>Invoice Date</th><th>Due Date</th><th>Total Amount</th><th>Received Amount</th><th>Balance</th><th>Status</th><th>Actions</th></tr></thead>
            </table>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
function arStatusBadge(status, statusUrl) {
    @if(auth()->user()->hasPermission('accounts_receivable.edit'))
    var colors = { draft: 'warning', pending: 'info', partial: 'primary', paid: 'success', overdue: 'danger' };
    return '<button type="button" class="btn btn-sm btn-status-toggle bg-label-' + (colors[status] || 'secondary') + ' border-0" data-url="' + statusUrl + '">' + status.charAt(0).toUpperCase() + status.slice(1) + '</button>';
    @else
    var colors = { draft: 'warning', pending: 'info', partial: 'primary', paid: 'success', overdue: 'danger' };
    return '<span class="badge bg-label-' + (colors[status] || 'secondary') + '">' + status.charAt(0).toUpperCase() + status.slice(1) + '</span>';
    @endif
}

function arActions(row) {
    var html = '<div class="btn-group btn-group-sm">';
    @if(auth()->user()->hasPermission('accounts_receivable.view'))
    html += '<a href="' + row.show_url + '" class="btn btn-outline-info"><i class="bx bx-show"></i></a>';
    @endif
    @if(auth()->user()->hasPermission('accounts_receivable.edit'))
    html += '<a href="' + row.edit_url + '" class="btn btn-outline-primary"><i class="bx bx-edit"></i></a>';
    @endif
    @if(auth()->user()->hasPermission('accounts_receivable.delete'))
    html += '<button type="button" class="btn btn-outline-danger btn-delete-item" data-url="' + row.delete_url + '" data-name="' + row.invoice_number + '"><i class="bx bx-trash"></i></button>';
    @endif
    html += '</div>';
    return html;
}

var table = $('#ar-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: "{{ route('admin.finance.accounts-receivable.data') }}",
    columns: [
        { data: 'id', name: 'id', orderable: false, searchable: false },
        { data: 'invoice_number', name: 'invoice_number' },
        { data: 'guest_name', name: 'guest_name' },
        { data: 'invoice_date', name: 'invoice_date', render: function(data) { if (!data) return '-'; var d = new Date(data); return ('0' + d.getDate()).slice(-2) + '-' + ('0' + (d.getMonth()+1)).slice(-2) + '-' + d.getFullYear(); } },
        { data: 'due_date', name: 'due_date', render: function(data) { if (!data) return '-'; var d = new Date(data); return ('0' + d.getDate()).slice(-2) + '-' + ('0' + (d.getMonth()+1)).slice(-2) + '-' + d.getFullYear(); } },
        { data: 'total_amount', name: 'total_amount', render: function(data) { return parseFloat(data).toFixed(2); } },
        { data: 'received_amount', name: 'received_amount', render: function(data) { return parseFloat(data).toFixed(2); } },
        { data: 'balance', name: 'balance', render: function(data) { return parseFloat(data).toFixed(2); } },
        { data: 'status', name: 'status', orderable: false, searchable: false, render: function(data, type, row) {
            return arStatusBadge(data, row.status_url);
        }},
        { data: 'actions', name: 'actions', orderable: false, searchable: false, render: function(data, type, row) {
            return arActions(row);
        }}
    ],
    order: [[0, 'desc']]
});
</script>
@endsection
