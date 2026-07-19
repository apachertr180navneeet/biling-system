@extends('admin.layouts.app')
@section('style')
@endsection
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Admin /</span> Suppliers
    </h4>
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="mb-0">All Suppliers</h5>
            <a href="{{ route('admin.suppliers.create') }}" class="btn btn-primary">Add New</a>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Contact Person</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suppliers as $supplier)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $supplier->name }}</td>
                        <td>{{ $supplier->type }}</td>
                        <td>{{ $supplier->contact_person ?? '-' }}</td>
                        <td>{{ $supplier->phone ?? '-' }}</td>
                        <td>{{ $supplier->email ?? '-' }}</td>
                        <td>
                            <label class="switch switch-success">
                                <input type="checkbox" class="toggle-status" data-url="{{ route('admin.suppliers.toggle-status', $supplier) }}" {{ $supplier->is_active ? 'checked' : '' }}>
                                <span class="slider round"></span>
                            </label>
                        </td>
                        <td>
                            <a href="{{ route('admin.suppliers.edit', $supplier) }}" class="btn btn-sm btn-primary">Edit</a>
                            <button class="btn btn-sm btn-danger btn-delete" data-url="{{ route('admin.suppliers.destroy', $supplier) }}">Delete</button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center">No suppliers found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $suppliers->links() }}</div>
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
