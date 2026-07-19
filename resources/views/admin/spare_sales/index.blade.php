@extends('admin.layouts.app')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold">Spare Parts Counter Sales</h4>
        <a href="{{ route('admin.spare-sales.create') }}" class="btn btn-primary"><i class="bx bx-plus"></i> New Sale</a>
    </div>
        <div class="card">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead><tr><th>#</th><th>Sale No</th><th>Customer</th><th>Date</th><th>Mode</th><th>Total</th><th>Actions</th></tr></thead>
                <tbody>
                    @forelse($sales as $s)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $s->sale_number }}</td>
                        <td>{{ $s->customer->first_name ?? '' }} {{ $s->customer->last_name ?? '' }}</td>
                        <td>{{ $s->sale_date->format('d-m-Y') }}</td>
                        <td><span class="badge bg-label-info">{{ ucfirst(str_replace('_', ' ', $s->payment_mode)) }}</span></td>
                        <td>{{ number_format($s->grand_total, 2) }}</td>
                        <td>
                            <a href="{{ route('admin.spare-sales.show', $s) }}" class="btn btn-sm btn-info"><i class="bx bx-show"></i></a>
                            <a href="{{ route('admin.spare-sales.print', $s) }}" class="btn btn-sm btn-secondary" target="_blank"><i class="bx bx-printer"></i></a>
                            <button class="btn btn-sm btn-danger delete-btn" data-url="{{ route('admin.spare-sales.destroy', $s) }}"><i class="bx bx-trash"></i></button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center text-muted">No sales.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $sales->links() }}</div>
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
            text: "Delete this sale?",
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
