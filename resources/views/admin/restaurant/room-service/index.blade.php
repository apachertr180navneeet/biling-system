@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-bed"></i></div>
            <div>
                <h4 class="m-page-title">Room Service - Post to Folio</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Restaurant POS</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">Room Service</li>
                </ul>
            </div>
        </div>
        @if(auth()->user()->hasPermission('room_service.create'))
        <a href="{{ route('admin.restaurant.room-service.create') }}" class="btn btn-primary m-btn-add"><i class="bx bx-plus"></i> Post Charge</a>
        @endif
    </div>

    <div class="card">
        <div class="card-body">
            <table id="room-service-table" class="table table-bordered table-striped" style="width:100%">
                <thead><tr><th>#</th><th>Charge No</th><th>Hotel</th><th>Reservation</th><th>Guest</th><th>Order No</th><th>Amount</th><th>Total</th><th>Posted By</th><th>Status</th><th>Actions</th></tr></thead>
            </table>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
var table = $('#room-service-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: "{{ route('admin.restaurant.room-service.data') }}",
    columns: [
        { data: 'id', name: 'id', orderable: false, searchable: false },
        { data: 'charge_number', name: 'charge_number' },
        { data: 'hotel_name', name: 'hotel_name' },
        { data: 'reservation_number', name: 'reservation_number' },
        { data: 'guest_name', name: 'guest_name' },
        { data: 'order_number', name: 'order_number' },
        { data: 'amount', name: 'amount', render: function(data) {
            return parseFloat(data).toFixed(2);
        }},
        { data: 'total_amount', name: 'total_amount', render: function(data) {
            return parseFloat(data).toFixed(2);
        }},
        { data: 'posted_by_name', name: 'posted_by_name' },
        { data: 'charge_status_badge', name: 'charge_status', orderable: false, searchable: false },
        { data: 'actions', name: 'actions', orderable: false, searchable: false, render: function(data, type, row) {
            var html = '<div class="btn-group btn-group-sm">';
            @if(auth()->user()->hasPermission('room_service.edit'))
            html += '<a href="' + row.edit_url + '" class="btn btn-outline-primary"><i class="bx bx-edit"></i></a>';
            @endif
            @if(auth()->user()->hasPermission('room_service.delete'))
            html += '<button type="button" class="btn btn-outline-danger btn-delete-item" data-url="' + row.delete_url + '" data-name="' + row.charge_number + '"><i class="bx bx-trash"></i></button>';
            @endif
            html += '</div>';
            return html;
        }}
    ],
    order: [[0, 'desc']]
});
</script>
@endsection
