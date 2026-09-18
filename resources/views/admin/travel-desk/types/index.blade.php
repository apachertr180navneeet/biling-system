@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-car"></i></div>
            <div>
                <h4 class="m-page-title">Transport Types</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Travel Desk</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">Transport Types</li>
                </ul>
            </div>
        </div>
        @if(auth()->user()->hasPermission('transport_types.create'))
        <a href="{{ route('admin.travel-desk.types.create') }}" class="btn btn-primary m-btn-add"><i class="bx bx-plus"></i> Add Type</a>
        @endif
    </div>

    <div class="card">
        <div class="card-body">
            <table id="transport-type-table" class="table table-bordered table-striped" style="width:100%">
                <thead><tr><th>#</th><th>Name</th><th>Base Price</th><th>Per KM</th><th>Per Hour</th><th>Max Passengers</th><th>Status</th><th>Actions</th></tr></thead>
            </table>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
function transportTypeStatusBadge(status, statusUrl) {
    @if(auth()->user()->hasPermission('transport_types.edit'))
    return '<button type="button" class="btn btn-sm btn-status-toggle btn-' + (status === 'active' ? 'success' : 'warning') + '" data-url="' + statusUrl + '">' + status.charAt(0).toUpperCase() + status.slice(1) + '</button>';
    @else
    return '<span class="badge bg-label-' + (status === 'active' ? 'success' : 'warning') + '">' + status.charAt(0).toUpperCase() + status.slice(1) + '</span>';
    @endif
}

function transportTypeActions(row) {
    var html = '<div class="btn-group btn-group-sm">';
    @if(auth()->user()->hasPermission('transport_types.edit'))
    html += '<a href="' + row.edit_url + '" class="btn btn-outline-primary"><i class="bx bx-edit"></i></a>';
    @endif
    @if(auth()->user()->hasPermission('transport_types.delete'))
    html += '<button type="button" class="btn btn-outline-danger btn-delete" data-url="' + row.delete_url + '" data-name="' + row.name + '"><i class="bx bx-trash"></i></button>';
    @endif
    html += '</div>';
    return html;
}

var table = $('#transport-type-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: "{{ route('admin.travel-desk.types.data') }}",
    columns: [
        { data: 'id', name: 'id', orderable: false, searchable: false },
        { data: 'name', name: 'name' },
        { data: 'base_price', name: 'base_price', render: function(data) { return '₹' + parseFloat(data).toLocaleString('en-IN', {minimumFractionDigits: 2}); }},
        { data: 'per_km_rate', name: 'per_km_rate', render: function(data) { return '₹' + parseFloat(data).toLocaleString('en-IN', {minimumFractionDigits: 2}); }},
        { data: 'per_hour_rate', name: 'per_hour_rate', render: function(data) { return '₹' + parseFloat(data).toLocaleString('en-IN', {minimumFractionDigits: 2}); }},
        { data: 'max_passengers', name: 'max_passengers' },
        { data: 'status', name: 'status', orderable: false, searchable: false, render: function(data, type, row) { return transportTypeStatusBadge(data, row.status_url); }},
        { data: 'actions', name: 'actions', orderable: false, searchable: false, render: function(data, type, row) { return transportTypeActions(row); }}
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
