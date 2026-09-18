@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-dish"></i></div>
            <div>
                <h4 class="m-page-title">Menu Items</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Restaurant POS</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">Menu Items</li>
                </ul>
            </div>
        </div>
        @if(auth()->user()->hasPermission('menu_items.create'))
        <a href="{{ route('admin.restaurant.menu-items.create') }}" class="btn btn-primary m-btn-add"><i class="bx bx-plus"></i> Add Menu Item</a>
        @endif
    </div>

    <div class="card">
        <div class="card-body">
            <table id="menu-items-table" class="table table-bordered table-striped" style="width:100%">
                <thead><tr><th>#</th><th>Hotel</th><th>Name</th><th>Category</th><th>Price</th><th>Tax Rate</th><th>Available</th><th>Status</th><th>Actions</th></tr></thead>
            </table>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
var table = $('#menu-items-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: "{{ route('admin.restaurant.menu-items.data') }}",
    columns: [
        { data: 'id', name: 'id', orderable: false, searchable: false },
        { data: 'hotel_name', name: 'hotel_name' },
        { data: 'name', name: 'name' },
        { data: 'category_badge', name: 'category', orderable: false, searchable: false },
        { data: 'price', name: 'price', render: function(data) {
            return parseFloat(data).toFixed(2);
        }},
        { data: 'tax_rate', name: 'tax_rate', render: function(data) {
            return parseFloat(data).toFixed(2) + '%';
        }},
        { data: 'is_available', name: 'is_available', orderable: false, searchable: false, render: function(data) {
            return data ? '<span class="badge bg-label-success">Yes</span>' : '<span class="badge bg-label-danger">No</span>';
        }},
        { data: 'status', name: 'status', orderable: false, searchable: false, render: function(data, type, row) {
            return '<button type="button" class="btn btn-sm btn-status-toggle btn-' + (data === 'active' ? 'success' : 'warning') + '" data-url="' + row.status_url + '">' + data.charAt(0).toUpperCase() + data.slice(1) + '</button>';
        }},
        { data: 'actions', name: 'actions', orderable: false, searchable: false, render: function(data, type, row) {
            var html = '<div class="btn-group btn-group-sm">';
            @if(auth()->user()->hasPermission('menu_items.edit'))
            html += '<a href="' + row.edit_url + '" class="btn btn-outline-primary"><i class="bx bx-edit"></i></a>';
            @endif
            @if(auth()->user()->hasPermission('menu_items.delete'))
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
