@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-dollar"></i></div>
            <div>
                <h4 class="m-page-title">Payroll</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">HR</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">Payroll</li>
                </ul>
            </div>
        </div>
        @if(auth()->user()->hasPermission('hr_payroll.create'))
        <a href="{{ route('admin.hr.payroll.create') }}" class="btn btn-primary m-btn-add"><i class="bx bx-plus"></i> Create Payroll</a>
        @endif
    </div>

    <div class="card">
        <div class="card-body">
            <table id="payroll-table" class="table table-bordered table-striped" style="width:100%">
                <thead><tr><th>#</th><th>Payroll Number</th><th>Period</th><th>Start Date</th><th>End Date</th><th>Total Employees</th><th>Total Earnings</th><th>Total Deductions</th><th>Net Pay</th><th>Status</th><th>Actions</th></tr></thead>
            </table>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
var statusColors = { draft: 'warning', processing: 'info', approved: 'primary', paid: 'success', cancelled: 'danger' };

function payrollStatusBadge(status, statusUrl) {
    var label = status.charAt(0).toUpperCase() + status.slice(1);
    @if(auth()->user()->hasPermission('hr_payroll.edit'))
    if (status !== 'paid' && status !== 'cancelled') {
        return '<button type="button" class="btn btn-sm btn-status-toggle btn-' + (statusColors[status] || 'secondary') + '" data-url="' + statusUrl + '">' + label + '</button>';
    }
    @endif
    return '<span class="badge bg-label-' + (statusColors[status] || 'secondary') + '">' + label + '</span>';
}

function formatCurrency(data) {
    return parseFloat(data).toLocaleString('en', {minimumFractionDigits: 2});
}

function payrollActions(row) {
    var html = '<div class="btn-group btn-group-sm">';
    @if(auth()->user()->hasPermission('hr_payroll.view'))
    html += '<a href="' + row.show_url + '" class="btn btn-outline-info"><i class="bx bx-show"></i></a>';
    @endif
    @if(auth()->user()->hasPermission('hr_payroll.edit'))
    html += '<a href="' + row.edit_url + '" class="btn btn-outline-primary"><i class="bx bx-edit"></i></a>';
    @endif
    @if(auth()->user()->hasPermission('hr_payroll.delete'))
    html += '<button type="button" class="btn btn-outline-danger btn-delete-item" data-url="' + row.delete_url + '" data-name="' + row.payroll_number + '"><i class="bx bx-trash"></i></button>';
    @endif
    html += '</div>';
    return html;
}

var table = $('#payroll-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: "{{ route('admin.hr.payroll.data') }}",
    columns: [
        { data: 'id', name: 'id', orderable: false, searchable: false },
        { data: 'payroll_number', name: 'payroll_number' },
        { data: 'period', name: 'period' },
        { data: 'start_date', name: 'start_date', render: function(data) { if (!data) return '-'; var d = new Date(data); return ('0' + d.getDate()).slice(-2) + '-' + ('0' + (d.getMonth()+1)).slice(-2) + '-' + d.getFullYear(); } },
        { data: 'end_date', name: 'end_date', render: function(data) { if (!data) return '-'; var d = new Date(data); return ('0' + d.getDate()).slice(-2) + '-' + ('0' + (d.getMonth()+1)).slice(-2) + '-' + d.getFullYear(); } },
        { data: 'total_employees', name: 'total_employees' },
        { data: 'total_earnings', name: 'total_earnings', render: function(data) { return formatCurrency(data); } },
        { data: 'total_deductions', name: 'total_deductions', render: function(data) { return formatCurrency(data); } },
        { data: 'total_net_pay', name: 'total_net_pay', render: function(data) { return formatCurrency(data); } },
        { data: 'status', name: 'status', orderable: false, searchable: false, render: function(data, type, row) {
            return payrollStatusBadge(data, row.status_url);
        }},
        { data: 'actions', name: 'actions', orderable: false, searchable: false, render: function(data, type, row) {
            return payrollActions(row);
        }}
    ],
    order: [[0, 'desc']]
});
</script>
@endsection