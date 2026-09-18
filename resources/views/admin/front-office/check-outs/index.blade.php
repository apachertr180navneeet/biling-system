@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-log-out"></i></div>
            <div>
                <h4 class="m-page-title">Check Out</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Front Office</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">Check Out</li>
                </ul>
            </div>
        </div>
        @if(auth()->user()->hasPermission('check_outs.create'))
        <a href="{{ route('admin.front-office.check-outs.create') }}" class="btn btn-primary m-btn-add"><i class="bx bx-plus"></i> Check Out Guest</a>
        @endif
    </div>

    <div class="card">
        <div class="card-body">
            <table id="checkouts-table" class="table table-bordered table-striped" style="width:100%">
                <thead><tr><th>#</th><th>Reservation</th><th>Guest</th><th>Hotel</th><th>Room</th><th>Check-out Time</th><th>Final Bill</th><th>Balance Due</th><th>Status</th><th>Actions</th></tr></thead>
            </table>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
function checkoutsStatusBadge(status, statusUrl) {
    @if(auth()->user()->hasPermission('check_outs.edit'))
    return '<button type="button" class="btn btn-sm btn-status-toggle btn-' + (status === 'active' ? 'success' : 'warning') + '" data-url="' + statusUrl + '">' + status.charAt(0).toUpperCase() + status.slice(1) + '</button>';
    @else
    return '<span class="badge bg-label-' + (status === 'active' ? 'success' : 'warning') + '">' + status.charAt(0).toUpperCase() + status.slice(1) + '</span>';
    @endif
}

function checkoutsActions(row) {
    var html = '<div class="btn-group btn-group-sm">';
    @if(auth()->user()->hasPermission('check_outs.edit'))
    html += '<a href="' + row.edit_url + '" class="btn btn-outline-primary"><i class="bx bx-edit"></i></a>';
    @endif
    @if(auth()->user()->hasPermission('check_outs.delete'))
    html += '<button type="button" class="btn btn-outline-danger btn-delete-item" data-url="' + row.delete_url + '" data-name="' + row.reservation_number + '"><i class="bx bx-trash"></i></button>';
    @endif
    html += '</div>';
    return html;
}

var table = $('#checkouts-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: "{{ route('admin.front-office.check-outs.data') }}",
    columns: [
        { data: 'id', name: 'id', orderable: false, searchable: false },
        { data: 'reservation_number', name: 'reservation_number' },
        { data: 'guest_name', name: 'guest_name' },
        { data: 'hotel_name', name: 'hotel_name', orderable: false, searchable: false },
        { data: 'room_number', name: 'room_number', orderable: false, searchable: false },
        { data: 'check_out_time', name: 'check_out_time', orderable: false, searchable: false },
        { data: 'final_bill_amount', name: 'final_bill_amount', orderable: false, searchable: false, render: function(data) {
            return parseFloat(data).toLocaleString('en-US', {minimumFractionDigits: 2});
        }},
        { data: 'balance_due_formatted', name: 'balance_due', orderable: false, searchable: false, render: function(data, type, row) {
            var val = parseFloat(row.balance_due);
            var cls = val > 0 ? 'text-danger' : 'text-success';
            return '<span class="' + cls + '">' + data + '</span>';
        }},
        { data: 'status', name: 'status', orderable: false, searchable: false, render: function(data, type, row) {
            return checkoutsStatusBadge(data, row.status_url);
        }},
        { data: 'actions', name: 'actions', orderable: false, searchable: false, render: function(data, type, row) {
            return checkoutsActions(row);
        }}
    ],
    order: [[0, 'desc']]
});
</script>
@endsection
