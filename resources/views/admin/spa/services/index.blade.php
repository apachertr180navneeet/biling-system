@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-spa"></i></div>
            <div>
                <h4 class="m-page-title">Spa Services</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Spa</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">Services</li>
                </ul>
            </div>
        </div>
        @if(auth()->user()->hasPermission('spa_services.create'))
        <a href="{{ route('admin.spa.services.create') }}" class="btn btn-primary m-btn-add"><i class="bx bx-plus"></i> Add Service</a>
        @endif
    </div>

    <div class="card">
        <div class="card-body">
            <table id="spa-service-table" class="table table-bordered table-striped" style="width:100%">
                <thead><tr><th>#</th><th>Service Name</th><th>Category</th><th>Duration</th><th>Price</th><th>Gender</th><th>Status</th><th>Actions</th></tr></thead>
            </table>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
function spaServiceStatusBadge(status, statusUrl) {
    @if(auth()->user()->hasPermission('spa_services.edit'))
    return '<button type="button" class="btn btn-sm btn-status-toggle btn-' + (status === 'active' ? 'success' : 'warning') + '" data-url="' + statusUrl + '">' + status.charAt(0).toUpperCase() + status.slice(1) + '</button>';
    @else
    return '<span class="badge bg-label-' + (status === 'active' ? 'success' : 'warning') + '">' + status.charAt(0).toUpperCase() + status.slice(1) + '</span>';
    @endif
}

function spaServiceActions(row) {
    var html = '<div class="btn-group btn-group-sm">';
    @if(auth()->user()->hasPermission('spa_services.edit'))
    html += '<a href="' + row.edit_url + '" class="btn btn-outline-primary"><i class="bx bx-edit"></i></a>';
    @endif
    @if(auth()->user()->hasPermission('spa_services.delete'))
    html += '<button type="button" class="btn btn-outline-danger btn-delete" data-url="' + row.delete_url + '" data-name="' + row.name + '"><i class="bx bx-trash"></i></button>';
    @endif
    html += '</div>';
    return html;
}

var table = $('#spa-service-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: "{{ route('admin.spa.services.data') }}",
    columns: [
        { data: 'id', name: 'id', orderable: false, searchable: false },
        { data: 'name', name: 'name' },
        { data: 'category', name: 'category' },
        { data: 'duration_minutes', name: 'duration_minutes', render: function(data) { return data + ' min'; }},
        { data: 'price', name: 'price', render: function(data) { return '₹' + parseFloat(data).toLocaleString('en-IN', {minimumFractionDigits: 2}); }},
        { data: 'gender', name: 'gender', render: function(data) { return data.charAt(0).toUpperCase() + data.slice(1); }},
        { data: 'status', name: 'status', orderable: false, searchable: false, render: function(data, type, row) { return spaServiceStatusBadge(data, row.status_url); }},
        { data: 'actions', name: 'actions', orderable: false, searchable: false, render: function(data, type, row) { return spaServiceActions(row); }}
    ],
    order: [[0, 'desc']]
});

$(document).on('click', '.btn-status-toggle', function() {
    var btn = $(this);
    $.ajax({ url: btn.data('url'), type: 'PATCH', headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success: function(res) { btn.text(res.status.charAt(0).toUpperCase() + res.status.slice(1)); }
    });
});

$(document).on('click', '.btn-delete', function() {
    var btn = $(this);
    if (confirm('Are you sure you want to delete "' + btn.data('name') + '"?')) {
        $.ajax({ url: btn.data('url'), type: 'DELETE', headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success: function() { table.ajax.reload(); }
        });
    }
});
</script>
@endsection
