@extends('admin.layouts.app')
@section('style')
<style>
.item-row { border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 10px; }
</style>
@endsection
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Admin / Vehicle Purchase Orders /</span> All
    </h4>
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="mb-0">Vehicle Purchase Orders</h5>
            <a href="{{ route('admin.vehicle-purchase-orders.create') }}" class="btn btn-primary">Add New</a>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>PO No.</th>
                        <th>Supplier</th>
                        <th>Order Date</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Active</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $order->po_number }}</td>
                        <td>{{ $order->supplier->name ?? '-' }}</td>
                        <td>{{ $order->order_date->format('d-m-Y') }}</td>
                        <td>{{ $order->items_count }}</td>
                        <td>{{ number_format($order->total_amount, 2) }}</td>
                        <td>
                            @if($order->status == 'pending')
                            <span class="badge bg-warning">Pending</span>
                            @elseif($order->status == 'partial')
                            <span class="badge bg-info">Partial</span>
                            @elseif($order->status == 'received')
                            <span class="badge bg-success">Received</span>
                            @else
                            <span class="badge bg-secondary">{{ ucfirst($order->status) }}</span>
                            @endif
                        </td>
                        <td>
                            <div class="form-check form-switch mb-0">
                                <input type="checkbox" class="form-check-input toggle-status" data-url="{{ route('admin.vehicle-purchase-orders.toggle-status', $order) }}" {{ $order->is_active ? 'checked' : '' }}>
                            </div>
                        </td>
                        <td>
                            <a href="{{ route('admin.vehicle-purchase-orders.show', $order) }}" class="btn btn-sm btn-info">View</a>
                            @if($order->status == 'pending')
                            <a href="{{ route('admin.vehicle-purchase-orders.edit', $order) }}" class="btn btn-sm btn-primary">Edit</a>
                            @endif
                            <button class="btn btn-sm btn-danger btn-delete" data-url="{{ route('admin.vehicle-purchase-orders.destroy', $order) }}">Delete</button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="text-center">No vehicle purchase orders found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $orders->links() }}</div>
    </div>
</div>
@endsection
@section('script')
<script>
$(document).ready(function() {
    $('.toggle-status').change(function() {
        var url = $(this).data('url');
        $.post(url, { _token: '{{ csrf_token() }}' }, function(resp) {
            if (resp.success) { setFlesh('success', 'Status updated.'); }
        });
    });
    $('.btn-delete').click(function() {
        var url = $(this).data('url');
        var btn = $(this);
        Swal.fire({ title: 'Are you sure?', text: 'This will be soft deleted.', icon: 'warning', showCancelButton: true, confirmButtonText: 'Yes, delete!' }).then(function(result) {
            if (result.isConfirmed) {
                $.ajax({ url: url, type: 'POST', data: { _token: '{{ csrf_token() }}', _method: 'DELETE' }, success: function(resp) {
                    if (resp.success) { btn.closest('tr').remove(); setFlesh('success', resp.message); }
                }});
            }
        });
    });
});
</script>
@endsection
