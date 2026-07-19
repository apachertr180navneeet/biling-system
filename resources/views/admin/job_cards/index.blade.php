@extends('admin.layouts.app')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold">Job Cards</h4>
        <a href="{{ route('admin.job-cards.create') }}" class="btn btn-primary"><i class="bx bx-plus"></i> New Job Card</a>
    </div>
        <div class="card">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead><tr><th>#</th><th>Job Card No</th><th>Customer</th><th>Vehicle</th><th>Status</th><th>Total</th><th>Date</th><th>Actions</th></tr></thead>
                <tbody>
                    @forelse($jobCards as $j)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $j->job_card_number }}</td>
                        <td>{{ $j->customer->first_name ?? '' }} {{ $j->customer->last_name ?? '' }}</td>
                        <td>{{ $j->vehicle_number ?? ($j->vehicle_model ?? '-') }}</td>
                        <td>
                            <span class="badge bg-{{ $j->status == 'pending' ? 'secondary' : ($j->status == 'in_progress' ? 'info' : ($j->status == 'completed' ? 'success' : 'primary')) }}">
                                {{ ucfirst(str_replace('_', ' ', $j->status)) }}
                            </span>
                        </td>
                        <td>{{ number_format($j->grand_total, 2) }}</td>
                        <td>{{ $j->service_date->format('d-m-Y') }}</td>
                        <td>
                            <a href="{{ route('admin.job-cards.show', $j) }}" class="btn btn-sm btn-info"><i class="bx bx-show"></i></a>
                            <a href="{{ route('admin.job-cards.edit', $j) }}" class="btn btn-sm btn-primary"><i class="bx bx-edit"></i></a>
                            <button class="btn btn-sm btn-danger delete-btn" data-url="{{ route('admin.job-cards.destroy', $j) }}"><i class="bx bx-trash"></i></button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center text-muted">No job cards.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $jobCards->links() }}</div>
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
            text: "Delete this job card?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#696cff',
            cancelButtonColor: '#8592a3',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                form.attr('action',url);
                $.post(url,form.serialize()).done(function(r){
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
