@extends('admin.layouts.app')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold">Vehicle Sales</h4>
        <a href="{{ route('admin.sales.create') }}" class="btn btn-primary"><i class="bx bx-plus"></i> New Sale</a>
    </div>
    @include('admin.layouts.elements.sweet_alerts')
    <div class="card">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead><tr><th>#</th><th>Sale No</th><th>Customer</th><th>Vehicle</th><th>Sale Price</th><th>Status</th><th>Booking Date</th><th>Actions</th></tr></thead>
                <tbody>
                    @forelse($sales as $s)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $s->sale_number }}</td>
                        <td>{{ $s->customer->first_name ?? '' }} {{ $s->customer->last_name ?? '' }}</td>
                        <td>{{ $s->vehicle_description ?? '-' }}</td>
                        <td>{{ number_format($s->sale_price, 2) }}</td>
                        <td><span class="badge bg-{{ $s->status=='booking'?'secondary':($s->status=='allotment'?'info':($s->status=='registration'?'primary':($s->status=='delivery'?'success':'dark'))) }}">{{ ucfirst($s->status) }}</span></td>
                        <td>{{ $s->booking_date->format('d-m-Y') }}</td>
                        <td>
                            <a href="{{ route('admin.sales.show', $s) }}" class="btn btn-sm btn-info"><i class="bx bx-show"></i></a>
                            <a href="{{ route('admin.sales.edit', $s) }}" class="btn btn-sm btn-primary"><i class="bx bx-edit"></i></a>
                            <button class="btn btn-sm btn-danger delete-btn" data-url="{{ route('admin.sales.destroy', $s) }}"><i class="bx bx-trash"></i></button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center text-muted">No sales.</td></tr>
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
        if(!confirm('Delete this sale?'))return;
        var form=$('#deleteForm'),url=$(this).data('url');
        form.attr('action',url);$.post(url,form.serialize()).done(function(r){if(r.success)location.reload();}).fail(function(){alert('Error');});
    });
});
</script>
@endsection
@endsection
