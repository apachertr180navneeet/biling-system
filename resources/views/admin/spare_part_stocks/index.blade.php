@extends('admin.layouts.app')
@section('style')
@endsection
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Admin /</span> Spare Part Stock
    </h4>
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="mb-0">All Stock</h5>
            <a href="{{ route('admin.spare-part-stocks.create') }}" class="btn btn-primary">Add New</a>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Part</th>
                        <th>Category</th>
                        <th>Quantity</th>
                        <th>Min Qty</th>
                        <th>Location</th>
                        <th>Status</th>
                        <th>Active</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stocks as $stock)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $stock->sparePart->part_no ?? '-' }} - {{ $stock->sparePart->name ?? '-' }}</td>
                        <td>{{ $stock->sparePart->category->name ?? '-' }}</td>
                        <td>{{ $stock->quantity }}</td>
                        <td>{{ $stock->min_quantity }}</td>
                        <td>{{ $stock->location ?? '-' }}</td>
                        <td>
                            @if($stock->quantity <= 0)
                            <span class="badge bg-danger">Out of Stock</span>
                            @elseif($stock->quantity <= $stock->min_quantity)
                            <span class="badge bg-warning">Low Stock</span>
                            @else
                            <span class="badge bg-success">In Stock</span>
                            @endif
                        </td>
                        <td>
                            <div class="form-check form-switch mb-0">
                                <input type="checkbox" class="form-check-input toggle-status" data-url="{{ route('admin.spare-part-stocks.toggle-status', $stock) }}" {{ $stock->is_active ? 'checked' : '' }}>
                            </div>
                        </td>
                        <td>
                            <a href="{{ route('admin.spare-part-stocks.edit', $stock) }}" class="btn btn-sm btn-primary">Edit</a>
                            <button class="btn btn-sm btn-danger btn-delete" data-url="{{ route('admin.spare-part-stocks.destroy', $stock) }}">Delete</button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="text-center">No stock found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $stocks->links() }}</div>
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
