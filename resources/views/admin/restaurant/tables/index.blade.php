@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-grid"></i></div>
            <div>
                <h4 class="m-page-title">Restaurant Tables</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Restaurant POS</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">Tables</li>
                </ul>
            </div>
        </div>
        @if(auth()->user()->hasPermission('restaurant_tables.create'))
        <a href="{{ route('admin.restaurant.tables.create') }}" class="btn btn-primary m-btn-add"><i class="bx bx-plus"></i> Add Table</a>
        @endif
    </div>

    <div class="card">
        <div class="card-body">
            <table id="restaurant-tables-table" class="table table-bordered table-striped" style="width:100%">
                <thead><tr><th>#</th><th>Hotel</th><th>Table No</th><th>Capacity</th><th>Floor</th><th>Section</th><th>Status</th><th>Actions</th></tr></thead>
            </table>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
var table = $('#restaurant-tables-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: "{{ route('admin.restaurant.tables.data') }}",
    columns: [
        { data: 'id', name: 'id', orderable: false, searchable: false },
        { data: 'hotel_name', name: 'hotel_name' },
        { data: 'table_number', name: 'table_number' },
        { data: 'capacity', name: 'capacity' },
        { data: 'floor_number', name: 'floor_number' },
        { data: 'section', name: 'section' },
        { data: 'table_status_badge', name: 'table_status', orderable: false, searchable: false },
        { data: 'actions', name: 'actions', orderable: false, searchable: false, render: function(data, type, row) {
            var html = '<div class="btn-group btn-group-sm">';
            @if(auth()->user()->hasPermission('restaurant_tables.edit'))
            html += '<a href="' + row.edit_url + '" class="btn btn-outline-primary"><i class="bx bx-edit"></i></a>';
            @endif
            @if(auth()->user()->hasPermission('restaurant_tables.delete'))
            html += '<button type="button" class="btn btn-outline-danger btn-delete-item" data-url="' + row.delete_url + '" data-name="Table ' + row.table_number + '"><i class="bx bx-trash"></i></button>';
            @endif
            html += '</div>';
            return html;
        }}
    ],
    order: [[0, 'desc']]
});
</script>
@endsection
