@extends('admin.layouts.app')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold">Service Reminders</h4>
        <a href="{{ route('admin.service-reminders.create') }}" class="btn btn-primary"><i class="bx bx-plus"></i> New</a>
    </div>
        <div class="card">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead><tr><th>#</th><th>Customer</th><th>Vehicle</th><th>Last Service</th><th>Next Service</th><th>Status</th><th>Actions</th></tr></thead>
                <tbody>
                    @forelse($reminders as $r)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $r->customer->first_name ?? '' }} {{ $r->customer->last_name ?? '' }}</td>
                        <td>{{ $r->vehicle_number ?? '-' }}</td>
                        <td>{{ $r->last_service_date ? $r->last_service_date->format('d-m-Y') : '-' }}</td>
                        <td>{{ $r->next_service_date->format('d-m-Y') }}</td>
                        <td><span class="badge bg-{{ $r->status=='pending'?'warning':($r->status=='sent'?'info':'success') }}">{{ ucfirst($r->status) }}</span></td>
                        <td>
                            <a href="{{ route('admin.service-reminders.edit', $r) }}" class="btn btn-sm btn-primary"><i class="bx bx-edit"></i></a>
                            <button class="btn btn-sm btn-danger delete-btn" data-url="{{ route('admin.service-reminders.destroy', $r) }}"><i class="bx bx-trash"></i></button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center text-muted">No reminders.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $reminders->links() }}</div>
    </div>
</div>
<form id="deleteForm" method="POST">@csrf</form>
@section('script')
<script>
$(function(){
    $('.delete-btn').click(function(){
        var form=$('#deleteForm'),url=$(this).data('url');
        Swal.fire({
            title: 'Are you sure?',
            text: "Delete this reminder?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#696cff',
            cancelButtonColor: '#8592a3',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                form.attr('action',url);
                $.post(url, form.serialize() + '&_method=DELETE').done(function(r){
                    if(r.success) location.reload();
                }).fail(function(){
                    Swal.fire('Error', 'Something went wrong!', 'error');
                });
            }
        });
    });
});
</script>
@endsection
@endsection
