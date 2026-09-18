@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-receipt"></i></div>
            <div>
                <h4 class="m-page-title">Restaurant Orders</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Restaurant POS</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">Orders</li>
                </ul>
            </div>
        </div>
        @if(auth()->user()->hasPermission('restaurant_orders.create'))
        <a href="{{ route('admin.restaurant.orders.create') }}" class="btn btn-primary m-btn-add"><i class="bx bx-plus"></i> New Order</a>
        @endif
    </div>

    <div class="card">
        <div class="card-body">
            <table id="orders-table" class="table table-bordered table-striped" style="width:100%">
                <thead><tr><th>#</th><th>Order No</th><th>Hotel</th><th>Type</th><th>Table</th><th>Guest</th><th>Amount</th><th>Payment</th><th>Order Status</th><th>Actions</th></tr></thead>
            </table>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
var table = $('#orders-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: "{{ route('admin.restaurant.orders.data') }}",
    columns: [
        { data: 'id', name: 'id', orderable: false, searchable: false },
        { data: 'order_number', name: 'order_number' },
        { data: 'hotel_name', name: 'hotel_name' },
        { data: 'order_type_badge', name: 'order_type', orderable: false, searchable: false },
        { data: 'table_number', name: 'table_number' },
        { data: 'guest_name', name: 'guest_name' },
        { data: 'net_amount', name: 'net_amount', render: function(data) {
            return parseFloat(data).toFixed(2);
        }},
        { data: 'payment_status_badge', name: 'payment_status', orderable: false, searchable: false },
        { data: 'order_status_badge', name: 'order_status', orderable: false, searchable: false },
        { data: 'actions', name: 'actions', orderable: false, searchable: false, render: function(data, type, row) {
            var html = '<div class="btn-group btn-group-sm">';
            @if(auth()->user()->hasPermission('restaurant_orders.edit'))
            html += '<a href="' + row.edit_url + '" class="btn btn-outline-primary"><i class="bx bx-edit"></i></a>';
            @endif
            @if(auth()->user()->hasPermission('restaurant_orders.delete'))
            html += '<button type="button" class="btn btn-outline-danger btn-delete-item" data-url="' + row.delete_url + '" data-name="' + row.order_number + '"><i class="bx bx-trash"></i></button>';
            @endif
            html += '</div>';
            return html;
        }}
    ],
    order: [[0, 'desc']]
});
</script>
@endsection
