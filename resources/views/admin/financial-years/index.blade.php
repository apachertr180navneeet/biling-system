@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-calendar"></i></div>
            <div>
                <h4 class="m-page-title">Financial Years</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Company Setup</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">Financial Years</li>
                </ul>
            </div>
        </div>
        @if(auth()->user()->hasPermission('financial_years.create'))
        <a href="{{ route('admin.financial-years.create') }}" class="btn btn-primary m-btn-add"><i class="bx bx-plus"></i> Add Financial Year</a>
        @endif
    </div>

    <div class="card">
        <div class="card-body">
            <table id="financial-years-table" class="table table-bordered table-striped" style="width:100%">
                <thead>
                    <tr><th>#</th><th>Name</th><th>Company</th><th>Start Date</th><th>End Date</th><th>Current</th><th>Status</th><th>Actions</th></tr>
                </thead>
            </table>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
function fyFormatDate(data) {
    return data ? moment(data).format('DD-MM-YYYY') : '-';
}

function fyCurrentBadge(isCurrent, setActiveUrl) {
    if (isCurrent) {
        return '<span class="badge bg-success">Current</span>';
    }
    @if(auth()->user()->hasPermission('financial_years.edit'))
    return '<button type="button" class="btn btn-sm btn-outline-success btn-set-active" data-url="' + setActiveUrl + '">Set Current</button>';
    @else
    return '-';
    @endif
}

function fyStatusBadge(status) {
    return '<span class="badge bg-label-' + (status === 'active' ? 'success' : 'warning') + '">' + status.charAt(0).toUpperCase() + status.slice(1) + '</span>';
}

function fyActions(row) {
    var html = '<div class="btn-group btn-group-sm">';
    @if(auth()->user()->hasPermission('financial_years.edit'))
    html += '<a href="' + row.edit_url + '" class="btn btn-outline-primary"><i class="bx bx-edit"></i></a>';
    @endif
    @if(auth()->user()->hasPermission('financial_years.delete'))
    html += '<button type="button" class="btn btn-outline-danger btn-delete-item" data-url="' + row.delete_url + '" data-name="' + row.name + '"><i class="bx bx-trash"></i></button>';
    @endif
    html += '</div>';
    return html;
}

var table = $('#financial-years-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: "{{ route('admin.financial-years.data') }}",
    columns: [
        { data: 'id', name: 'id', orderable: false, searchable: false },
        { data: 'name', name: 'name' },
        { data: 'company_name', name: 'company_name', orderable: false, searchable: false },
        { data: 'start_date', name: 'start_date', render: function(data) { return fyFormatDate(data); }},
        { data: 'end_date', name: 'end_date', render: function(data) { return fyFormatDate(data); }},
        { data: 'is_current', name: 'is_current', orderable: false, searchable: false, render: function(data, type, row) {
            return fyCurrentBadge(data, row.set_active_url);
        }},
        { data: 'status', name: 'status', orderable: false, searchable: false, render: function(data) {
            return fyStatusBadge(data);
        }},
        { data: 'actions', name: 'actions', orderable: false, searchable: false, render: function(data, type, row) {
            return fyActions(row);
        }}
    ],
    order: [[0, 'desc']]
});
</script>
@endsection
