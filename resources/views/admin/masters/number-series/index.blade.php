@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-barcode"></i></div>
            <div>
                <h4 class="m-page-title">Number Series</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Masters</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">Number Series</li>
                </ul>
            </div>
        </div>
        @if(auth()->user()->hasPermission('number_series.create'))
        <a href="{{ route('admin.masters.number-series.create') }}" class="btn btn-primary m-btn-add"><i class="bx bx-plus"></i> Add Number Series</a>
        @endif
    </div>

    <div class="card">
        <div class="card-body">
            <table id="number-series-table" class="table table-bordered table-striped" style="width:100%">
                <thead><tr><th>#</th><th>Module</th><th>Prefix</th><th>Suffix</th><th>Next Number</th><th>Padding</th><th>Preview</th><th>Status</th><th>Actions</th></tr></thead>
            </table>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
function nsModuleBadge(module) {
    var label = module.replace(/_/g, ' ').replace(/\b\w/g, function(l) { return l.toUpperCase(); });
    return '<span class="badge bg-label-primary">' + label + '</span>';
}

function nsPreview(code) {
    return '<code>' + code + '</code>';
}

function nsOptional(data) {
    return data || '-';
}

function nsStatusBadge(status) {
    return '<span class="badge bg-label-' + (status === 'active' ? 'success' : 'warning') + '">' + status.charAt(0).toUpperCase() + status.slice(1) + '</span>';
}

function nsActions(row) {
    var html = '<div class="btn-group btn-group-sm">';
    @if(auth()->user()->hasPermission('number_series.edit'))
    html += '<a href="' + row.edit_url + '" class="btn btn-outline-primary"><i class="bx bx-edit"></i></a>';
    @endif
    @if(auth()->user()->hasPermission('number_series.delete'))
    html += '<button type="button" class="btn btn-outline-danger btn-delete-item" data-url="' + row.delete_url + '" data-name="' + row.module + '"><i class="bx bx-trash"></i></button>';
    @endif
    html += '</div>';
    return html;
}

var table = $('#number-series-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: "{{ route('admin.masters.number-series.data') }}",
    columns: [
        { data: 'id', name: 'id', orderable: false, searchable: false },
        { data: 'module', name: 'module', render: function(data) { return nsModuleBadge(data); }},
        { data: 'prefix', name: 'prefix', render: function(data) { return nsOptional(data); }},
        { data: 'suffix', name: 'suffix', render: function(data) { return nsOptional(data); }},
        { data: 'next_number', name: 'next_number' },
        { data: 'padding', name: 'padding' },
        { data: 'preview', name: 'preview', orderable: false, searchable: false, render: function(data) {
            return nsPreview(data);
        }},
        { data: 'status', name: 'status', orderable: false, searchable: false, render: function(data) {
            return nsStatusBadge(data);
        }},
        { data: 'actions', name: 'actions', orderable: false, searchable: false, render: function(data, type, row) {
            return nsActions(row);
        }}
    ],
    order: [[0, 'desc']]
});
</script>
@endsection
