@extends('admin.layouts.app')
@section('style')
@endsection
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Admin /</span> Spare Parts
    </h4>
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="mb-0">All Spare Parts</h5>
            <a href="{{ route('admin.spare-parts.create') }}" class="btn btn-primary">Add New</a>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Part No</th>
                        <th>Name</th>
                        <th>MRP</th>
                        <th>Selling Price</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($parts as $part)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $part->part_no }}</td>
                        <td>{{ $part->name }}</td>
                        <td>{{ number_format($part->mrp, 2) }}</td>
                        <td>{{ number_format($part->selling_price, 2) }}</td>
                        <td>
                            <label class="switch switch-success">
                                <input type="checkbox" class="toggle-status" data-url="{{ route('admin.spare-parts.toggle-status', $part) }}" {{ $part->is_active ? 'checked' : '' }}>
                                <span class="slider round"></span>
                            </label>
                        </td>
                        <td>
                            <a href="{{ route('admin.spare-parts.edit', $part) }}" class="btn btn-sm btn-primary">Edit</a>
                            <button class="btn btn-sm btn-danger btn-delete" data-url="{{ route('admin.spare-parts.destroy', $part) }}">Delete</button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center">No spare parts found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $parts->links() }}</div>
    </div>
</div>
@endsection
@section('script')
<script>
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
