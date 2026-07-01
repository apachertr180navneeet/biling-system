@extends('admin.layouts.app')
@section('style')
@endsection
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Admin /</span> HSN/SAC Master
    </h4>
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="mb-0">All HSN/SAC Codes</h5>
            <a href="{{ route('admin.hsn-sac-master.create') }}" class="btn btn-primary">Add New</a>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Code</th>
                        <th>Description</th>
                        <th>GST Rate (%)</th>
                        <th>Cess Rate (%)</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($hsnCodes as $hsn)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $hsn->code }}</td>
                        <td>{{ $hsn->description ?? '-' }}</td>
                        <td>{{ $hsn->gst_rate }}%</td>
                        <td>{{ $hsn->cess_rate }}%</td>
                        <td>
                            <div class="form-check form-switch mb-0">
                                <input type="checkbox" class="form-check-input toggle-status" data-url="{{ route('admin.hsn-sac-master.toggle-status', $hsn) }}" {{ $hsn->is_active ? 'checked' : '' }}>
                            </div>
                        </td>
                        <td>
                            <a href="{{ route('admin.hsn-sac-master.edit', $hsn) }}" class="btn btn-sm btn-primary">Edit</a>
                            <button class="btn btn-sm btn-danger btn-delete" data-url="{{ route('admin.hsn-sac-master.destroy', $hsn) }}">Delete</button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center">No HSN/SAC codes found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $hsnCodes->links() }}</div>
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
