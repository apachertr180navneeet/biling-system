@extends('admin.layouts.app')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold">Service Reminders</h4>
        <a href="{{ route('admin.service-reminders.create') }}" class="btn btn-primary"><i class="bx bx-plus"></i> New</a>
    </div>
    @include('admin.layouts.elements.sweet_alerts')
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
        if(!confirm('Delete this reminder?'))return;
        var form=$('#deleteForm'),url=$(this).data('url');
        form.attr('action',url);$.post(url,form.serialize()).done(function(r){if(r.success)location.reload();}).fail(function(){alert('Error');});
    });
});
</script>
@endsection
@endsection
