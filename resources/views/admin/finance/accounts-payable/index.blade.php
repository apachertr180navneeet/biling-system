@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-wallet"></i></div>
            <div>
                <h4 class="m-page-title">Accounts Payable</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Finance</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">Accounts Payable</li>
                </ul>
            </div>
        </div>
        @if(auth()->user()->hasPermission('accounts_payable.create'))
        <a href="{{ route('admin.finance.accounts-payable.create') }}" class="btn btn-primary m-btn-add"><i class="bx bx-plus"></i> Add Bill</a>
        @endif
    </div>

    <div class="card">
        <div class="card-body">
            <table id="ap-table" class="table table-bordered table-striped" style="width:100%">
                <thead><tr><th>#</th><th>Bill Number</th><th>Vendor / Supplier</th><th>Bill Date</th><th>Due Date</th><th>Total Amount</th><th>Paid Amount</th><th>Balance</th><th>Status</th><th>Actions</th></tr></thead>
            </table>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
function apStatusBadge(status, statusUrl) {
    @if(auth()->user()->hasPermission('accounts_payable.edit'))
    var colors = { draft: 'warning', pending: 'info', partial: 'primary', paid: 'success', overdue: 'danger' };
    return '<button type="button" class="btn btn-sm btn-status-toggle bg-label-' + (colors[status] || 'secondary') + ' border-0" data-url="' + statusUrl + '">' + status.charAt(0).toUpperCase() + status.slice(1) + '</button>';
    @else
    var colors = { draft: 'warning', pending: 'info', partial: 'primary', paid: 'success', overdue: 'danger' };
    return '<span class="badge bg-label-' + (colors[status] || 'secondary') + '">' + status.charAt(0).toUpperCase() + status.slice(1) + '</span>';
    @endif
}

function apActions(row) {
    var html = '<div class="btn-group btn-group-sm">';
    @if(auth()->user()->hasPermission('accounts_payable.view'))
    html += '<a href="' + row.show_url + '" class="btn btn-outline-info"><i class="bx bx-show"></i></a>';
    @endif
    @if(auth()->user()->hasPermission('accounts_payable.edit'))
    html += '<a href="' + row.edit_url + '" class="btn btn-outline-primary"><i class="bx bx-edit"></i></a>';
    @endif
    @if(auth()->user()->hasPermission('accounts_payable.delete'))
    html += '<button type="button" class="btn btn-outline-danger btn-delete-item" data-url="' + row.delete_url + '" data-name="' + row.bill_number + '"><i class="bx bx-trash"></i></button>';
    @endif
    html += '</div>';
    return html;
}

var table = $('#ap-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: "{{ route('admin.finance.accounts-payable.data') }}",
    columns: [
        { data: 'id', name: 'id', orderable: false, searchable: false },
        { data: 'bill_number', name: 'bill_number' },
        { data: 'vendor_supplier', name: 'vendor_supplier' },
        { data: 'bill_date', name: 'bill_date', render: function(data) { if (!data) return '-'; var d = new Date(data); return ('0' + d.getDate()).slice(-2) + '-' + ('0' + (d.getMonth()+1)).slice(-2) + '-' + d.getFullYear(); } },
        { data: 'due_date', name: 'due_date', render: function(data) { if (!data) return '-'; var d = new Date(data); return ('0' + d.getDate()).slice(-2) + '-' + ('0' + (d.getMonth()+1)).slice(-2) + '-' + d.getFullYear(); } },
        { data: 'total_amount', name: 'total_amount', render: function(data) { return parseFloat(data).toFixed(2); } },
        { data: 'paid_amount', name: 'paid_amount', render: function(data) { return parseFloat(data).toFixed(2); } },
        { data: 'balance', name: 'balance', render: function(data) { return parseFloat(data).toFixed(2); } },
        { data: 'status', name: 'status', orderable: false, searchable: false, render: function(data, type, row) {
            return apStatusBadge(data, row.status_url);
        }},
        { data: 'actions', name: 'actions', orderable: false, searchable: false, render: function(data, type, row) {
            return apActions(row);
        }}
    ],
    order: [[0, 'desc']]
});
</script>
@endsection
