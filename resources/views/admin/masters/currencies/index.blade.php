@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-money"></i></div>
            <div>
                <h4 class="m-page-title">Currencies</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Masters</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">Currencies</li>
                </ul>
            </div>
        </div>
        @if(auth()->user()->hasPermission('currencies.create'))
        <a href="{{ route('admin.masters.currencies.create') }}" class="btn btn-primary m-btn-add"><i class="bx bx-plus"></i> Add Currency</a>
        @endif
    </div>

    <div class="card">
        <div class="card-body">
            <table id="currencies-table" class="table table-bordered table-striped" style="width:100%">
                <thead><tr><th>#</th><th>Code</th><th>Name</th><th>Symbol</th><th>Exchange Rate</th><th>Default</th><th>Status</th><th>Actions</th></tr></thead>
            </table>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
function currenciesCode(data) {
    return '<strong>' + data + '</strong>';
}

function currenciesDefaultBadge(isDefault) {
    return '<span class="badge bg-' + (isDefault ? 'success' : 'secondary') + '">' + (isDefault ? 'Yes' : 'No') + '</span>';
}

function currenciesStatusBadge(status, statusUrl) {
    @if(auth()->user()->hasPermission('currencies.edit'))
    return '<button type="button" class="btn btn-sm btn-status-toggle btn-' + (status === 'active' ? 'success' : 'warning') + '" data-url="' + statusUrl + '">' + status.charAt(0).toUpperCase() + status.slice(1) + '</button>';
    @else
    return '<span class="badge bg-label-' + (status === 'active' ? 'success' : 'warning') + '">' + status.charAt(0).toUpperCase() + status.slice(1) + '</span>';
    @endif
}

function currenciesActions(row) {
    var html = '<div class="btn-group btn-group-sm">';
    @if(auth()->user()->hasPermission('currencies.edit'))
    html += '<a href="' + row.edit_url + '" class="btn btn-outline-primary"><i class="bx bx-edit"></i></a>';
    @endif
    @if(auth()->user()->hasPermission('currencies.delete'))
    html += '<button type="button" class="btn btn-outline-danger btn-delete-item" data-url="' + row.delete_url + '" data-name="' + row.name + '"><i class="bx bx-trash"></i></button>';
    @endif
    html += '</div>';
    return html;
}

var table = $('#currencies-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: "{{ route('admin.masters.currencies.data') }}",
    columns: [
        { data: 'id', name: 'id', orderable: false, searchable: false },
        { data: 'code', name: 'code', render: function(data) { return currenciesCode(data); }},
        { data: 'name', name: 'name' },
        { data: 'symbol', name: 'symbol' },
        { data: 'exchange_rate', name: 'exchange_rate' },
        { data: 'is_default', name: 'is_default', orderable: false, searchable: false, render: function(data) {
            return currenciesDefaultBadge(data);
        }},
        { data: 'status', name: 'status', orderable: false, searchable: false, render: function(data, type, row) {
            return currenciesStatusBadge(data, row.status_url);
        }},
        { data: 'actions', name: 'actions', orderable: false, searchable: false, render: function(data, type, row) {
            return currenciesActions(row);
        }}
    ],
    order: [[0, 'desc']]
});
</script>
@endsection
