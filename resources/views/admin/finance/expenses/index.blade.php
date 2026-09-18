@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-credit-card"></i></div>
            <div>
                <h4 class="m-page-title">Expenses</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Finance</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">Expenses</li>
                </ul>
            </div>
        </div>
        @if(auth()->user()->hasPermission('expenses.create'))
        <a href="{{ route('admin.finance.expenses.create') }}" class="btn btn-primary m-btn-add"><i class="bx bx-plus"></i> Add Expense</a>
        @endif
    </div>

    <div class="card">
        <div class="card-body">
            <table id="expense-table" class="table table-bordered table-striped" style="width:100%">
                <thead><tr><th>#</th><th>Expense Number</th><th>Expense Date</th><th>Account</th><th>Vendor</th><th>Total Amount</th><th>Payment Method</th><th>Status</th><th>Actions</th></tr></thead>
            </table>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
function expenseStatusBadge(status, statusUrl) {
    if (!status) return '<span class="badge bg-label-secondary">N/A</span>';
    @if(auth()->user()->hasPermission('expenses.edit'))
    var colors = { draft: 'warning', approved: 'success', pending: 'info', rejected: 'danger', paid: 'primary' };
    return '<button type="button" class="btn btn-sm btn-status-toggle bg-label-' + (colors[status] || 'secondary') + ' border-0" data-url="' + statusUrl + '">' + status.charAt(0).toUpperCase() + status.slice(1) + '</button>';
    @else
    var colors = { draft: 'warning', approved: 'success', pending: 'info', rejected: 'danger', paid: 'primary' };
    return '<span class="badge bg-label-' + (colors[status] || 'secondary') + '">' + status.charAt(0).toUpperCase() + status.slice(1) + '</span>';
    @endif
}

function expenseActions(row) {
    var html = '<div class="btn-group btn-group-sm">';
    @if(auth()->user()->hasPermission('expenses.view'))
    html += '<a href="' + row.show_url + '" class="btn btn-outline-info"><i class="bx bx-show"></i></a>';
    @endif
    @if(auth()->user()->hasPermission('expenses.edit'))
    html += '<a href="' + row.edit_url + '" class="btn btn-outline-primary"><i class="bx bx-edit"></i></a>';
    @endif
    @if(auth()->user()->hasPermission('expenses.delete'))
    html += '<button type="button" class="btn btn-outline-danger btn-delete-item" data-url="' + row.delete_url + '" data-name="' + row.expense_number + '"><i class="bx bx-trash"></i></button>';
    @endif
    html += '</div>';
    return html;
}

var table = $('#expense-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: "{{ route('admin.finance.expenses.data') }}",
    columns: [
        { data: 'id', name: 'id', orderable: false, searchable: false },
        { data: 'expense_number', name: 'expense_number' },
        { data: 'expense_date', name: 'expense_date', render: function(data) { if (!data) return '-'; var d = new Date(data); return ('0' + d.getDate()).slice(-2) + '-' + ('0' + (d.getMonth()+1)).slice(-2) + '-' + d.getFullYear(); } },
        { 
            data: 'account.name', 
            name: 'account.name', 
            render: function(data, type, row) { 
                return row.account ? (row.account.code + ' - ' + row.account.name) : '-'; 
            } 
        },
        { 
            data: 'vendor.company_name', 
            name: 'vendor.company_name', 
            render: function(data, type, row) { 
                return row.vendor ? row.vendor.company_name : '-'; 
            } 
        },
        { data: 'total_amount', name: 'total_amount', render: function(data) { return parseFloat(data).toFixed(2); } },
        { data: 'payment_method', name: 'payment_method', render: function(data) { return data ? data.replace('_', ' ').replace(/\b\w/g, function(l){ return l.toUpperCase(); }) : '-'; } },
        { data: 'status', name: 'status', orderable: false, searchable: false, render: function(data, type, row) {
            return expenseStatusBadge(data, row.status_url);
        }},
        { data: 'actions', name: 'actions', orderable: false, searchable: false, render: function(data, type, row) {
            return expenseActions(row);
        }}
    ],
    order: [[0, 'desc']]
});
</script>
@endsection
