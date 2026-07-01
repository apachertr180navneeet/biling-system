@extends('admin.layouts.app')
@section('style')
@endsection
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Admin /</span> Goods Receipt Notes
    </h4>
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="mb-0">All GRNs</h5>
            <a href="{{ route('admin.goods-receipt-notes.create') }}" class="btn btn-primary">Add New</a>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>GRN No.</th>
                        <th>PO Reference</th>
                        <th>Received Date</th>
                        <th>Items</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($grns as $grn)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $grn->grn_number }}</td>
                        <td>{{ $grn->purchaseOrder->order_number ?? '-' }}</td>
                        <td>{{ $grn->received_date->format('d-m-Y') }}</td>
                        <td>{{ $grn->items->count() }}</td>
                        <td>
                            @if($grn->status == 'completed')
                            <span class="badge bg-success">Completed</span>
                            @else
                            <span class="badge bg-warning">Pending</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.goods-receipt-notes.show', $grn) }}" class="btn btn-sm btn-info">View</a>
                            <button class="btn btn-sm btn-danger btn-delete" data-url="{{ route('admin.goods-receipt-notes.destroy', $grn) }}">Delete</button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center">No GRNs found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $grns->links() }}</div>
    </div>
</div>
@endsection
@section('script')
<script>
$(document).ready(function() {
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
