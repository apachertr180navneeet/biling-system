@extends('admin.layouts.app')
@section('style')
@endsection
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Admin /</span> Invoices
    </h4>
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="mb-0">All Invoices</h5>
            <div>
                <a href="{{ route('admin.invoices.create-vehicle') }}" class="btn btn-primary">Vehicle Invoice</a>
                <a href="{{ route('admin.invoices.create-parts') }}" class="btn btn-primary">Parts Invoice</a>
            </div>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Invoice No.</th>
                        <th>Date</th>
                        <th>Customer</th>
                        <th>Type</th>
                        <th>GST</th>
                        <th>Grand Total</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $inv)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $inv->invoice_number }}</td>
                        <td>{{ $inv->invoice_date->format('d-m-Y') }}</td>
                        <td>{{ $inv->customer->first_name ?? '' }} {{ $inv->customer->last_name ?? '' }}</td>
                        <td>{{ ucfirst($inv->invoice_type) }}</td>
                        <td>@if($inv->is_gst)<span class="badge bg-info">{{ strtoupper($inv->gst_type ?? 'gst') }}</span>@else<span class="badge bg-secondary">Non-GST</span>@endif</td>
                        <td>{{ number_format($inv->grand_total, 2) }}</td>
                        <td>
                            @if($inv->status == 'confirmed')
                            <span class="badge bg-success">Confirmed</span>
                            @elseif($inv->status == 'draft')
                            <span class="badge bg-warning">Draft</span>
                            @else
                            <span class="badge bg-secondary">{{ ucfirst($inv->status) }}</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.invoices.show', $inv) }}" class="btn btn-sm btn-info">View</a>
                            <button class="btn btn-sm btn-danger btn-delete" data-url="{{ route('admin.invoices.destroy', $inv) }}">Delete</button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="text-center">No invoices found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $invoices->links() }}</div>
    </div>
</div>
@endsection
@section('script')
<script>
$(document).ready(function() {
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
