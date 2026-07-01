@extends('admin.layouts.app')
@section('style')
@endsection
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Admin /</span> Vehicle Stock
    </h4>
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="mb-0">All Stock</h5>
            <a href="{{ route('admin.vehicle-stocks.create') }}" class="btn btn-primary">Add New</a>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Chassis No.</th>
                        <th>Engine No.</th>
                        <th>Vehicle</th>
                        <th>Color</th>
                        <th>Mfg Year</th>
                        <th>Purchase Price</th>
                        <th>Status</th>
                        <th>Active</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stocks as $stock)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $stock->chassis_number }}</td>
                        <td>{{ $stock->engine_number ?? '-' }}</td>
                        <td>{{ $stock->color->variant->model->brand->name ?? '' }} {{ $stock->color->variant->model->name ?? '' }} {{ $stock->color->variant->name ?? '' }}</td>
                        <td>
                            @if($stock->color->color_code)
                            <span class="d-inline-block rounded" style="width:14px;height:14px;background:{{ $stock->color->color_code }};vertical-align:middle"></span>
                            @endif
                            {{ $stock->color->color_name ?? '-' }}
                        </td>
                        <td>{{ $stock->mfg_year ?? '-' }}</td>
                        <td>{{ number_format($stock->purchase_price, 2) }}</td>
                        <td>
                            @if($stock->status == 'available')
                            <span class="badge bg-success">Available</span>
                            @elseif($stock->status == 'sold')
                            <span class="badge bg-secondary">Sold</span>
                            @else
                            <span class="badge bg-info">Transferred</span>
                            @endif
                        </td>
                        <td>
                            <div class="form-check form-switch mb-0">
                                <input type="checkbox" class="form-check-input toggle-status" data-url="{{ route('admin.vehicle-stocks.toggle-status', $stock) }}" {{ $stock->is_active ? 'checked' : '' }}>
                            </div>
                        </td>
                        <td>
                            <a href="{{ route('admin.vehicle-stocks.edit', $stock) }}" class="btn btn-sm btn-primary">Edit</a>
                            <button class="btn btn-sm btn-danger btn-delete" data-url="{{ route('admin.vehicle-stocks.destroy', $stock) }}">Delete</button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="10" class="text-center">No stock found.</td></tr>
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
