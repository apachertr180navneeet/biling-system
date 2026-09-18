@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-duplicate"></i></div>
            <div>
                <h4 class="m-page-title">Linen & Laundry Items</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Housekeeping</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">Laundry Items</li>
                </ul>
            </div>
        </div>
        @if(auth()->user()->hasPermission('laundry_items.create'))
        <a href="{{ route('admin.housekeeping.laundry-items.create') }}" class="btn btn-primary m-btn-add"><i class="bx bx-plus"></i> Add Item</a>
        @endif
    </div>

    <div class="card">
        <div class="card-body">
            <table id="laundry-items-table" class="table table-bordered table-striped" style="width:100%">
                <thead><tr><th>#</th><th>Hotel</th><th>Name</th><th>Type</th><th>Quantity</th><th>Unit</th><th>Status</th><th>Actions</th></tr></thead>
            </table>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
var table = $('#laundry-items-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: "{{ route('admin.housekeeping.laundry-items.data') }}",
    columns: [
        { data: 'id', name: 'id', orderable: false, searchable: false },
        { data: 'hotel_name', name: 'hotel_name' },
        { data: 'name', name: 'name' },
        { data: 'item_type_badge', name: 'item_type', orderable: false, searchable: false },
        { data: 'quantity', name: 'quantity' },
        { data: 'unit', name: 'unit', orderable: false, searchable: false },
        { data: 'status', name: 'status', orderable: false, searchable: false, render: function(data, type, row) {
            return '<button type="button" class="btn btn-sm btn-status-toggle btn-' + (data === 'active' ? 'success' : 'warning') + '" data-url="' + row.status_url + '">' + data.charAt(0).toUpperCase() + data.slice(1) + '</button>';
        }},
        { data: 'actions', name: 'actions', orderable: false, searchable: false, render: function(data, type, row) {
            var html = '<div class="btn-group btn-group-sm">';
            @if(auth()->user()->hasPermission('laundry_items.edit'))
            html += '<a href="' + row.edit_url + '" class="btn btn-outline-primary"><i class="bx bx-edit"></i></a>';
            @endif
            @if(auth()->user()->hasPermission('laundry_items.delete'))
            html += '<button type="button" class="btn btn-outline-danger btn-delete-item" data-url="' + row.delete_url + '" data-name="' + row.name + '"><i class="bx bx-trash"></i></button>';
            @endif
            html += '</div>';
            return html;
        }}
    ],
    order: [[0, 'desc']]
});
</script>
@endsection
