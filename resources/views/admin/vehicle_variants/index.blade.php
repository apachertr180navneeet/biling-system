@extends('admin.layouts.app')
@section('style')
@endsection
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Admin /</span> Vehicle Variants
    </h4>
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="mb-0">All Variants</h5>
            <a href="{{ route('admin.vehicle-variants.create') }}" class="btn btn-primary">Add New</a>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Model</th>
                        <th>Name</th>
                        <th>Fuel Type</th>
                        <th>Transmission</th>
                        <th>Ex-Showroom Price</th>
                        <th>HSN Code</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($variants as $variant)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $variant->model->brand->name ?? '' }} {{ $variant->model->name ?? '' }}</td>
                        <td>{{ $variant->name }}</td>
                        <td>{{ $variant->fuel_type ?? '-' }}</td>
                        <td>{{ $variant->transmission ?? '-' }}</td>
                        <td>{{ number_format($variant->ex_showroom_price, 2) }}</td>
                        <td>{{ $variant->hsn_code ?? '-' }}</td>
                        <td>
                            <div class="form-check form-switch mb-0">
                                <input type="checkbox" class="form-check-input toggle-status" data-url="{{ route('admin.vehicle-variants.toggle-status', $variant) }}" {{ $variant->is_active ? 'checked' : '' }}>
                            </div>
                        </td>
                        <td>
                            <a href="{{ route('admin.vehicle-variants.edit', $variant) }}" class="btn btn-sm btn-primary">Edit</a>
                            <button class="btn btn-sm btn-danger btn-delete" data-url="{{ route('admin.vehicle-variants.destroy', $variant) }}">Delete</button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="text-center">No variants found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $variants->links() }}</div>
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
