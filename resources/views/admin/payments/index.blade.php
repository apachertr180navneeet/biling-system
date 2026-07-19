@extends('admin.layouts.app')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold">Payments</h4>
        <a href="{{ route('admin.payments.create') }}" class="btn btn-primary"><i class="bx bx-plus"></i> New Payment</a>
    </div>

    @include('admin.layouts.elements.sweet_alerts')

    <div class="card">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead><tr><th>#</th><th>Payment No</th><th>Customer</th><th>Invoice</th><th>Date</th><th>Mode</th><th>Amount</th><th>Actions</th></tr></thead>
                <tbody>
                    @forelse($payments as $payment)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $payment->payment_number }}</td>
                        <td>{{ $payment->customer->first_name ?? '' }} {{ $payment->customer->last_name ?? '' }}</td>
                        <td>{{ $payment->invoice->invoice_number ?? '-' }}</td>
                        <td>{{ $payment->payment_date->format('d-m-Y') }}</td>
                        <td><span class="badge bg-label-info">{{ ucfirst(str_replace('_', ' ', $payment->payment_mode)) }}</span></td>
                        <td>{{ number_format($payment->amount, 2) }}</td>
                        <td>
                            <button class="btn btn-sm btn-danger delete-btn" data-id="{{ $payment->id }}" data-url="{{ route('admin.payments.destroy', $payment) }}"><i class="bx bx-trash"></i></button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center text-muted">No payments found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $payments->links() }}</div>
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
            text: "Delete this payment?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#696cff',
            cancelButtonColor: '#8592a3',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                form.attr('action',url);
                $.post(url,form.serialize()).done(function(r){
                    if(r.success) {
                        location.reload();
                    } else {
                        Swal.fire('Error', r.message || 'Error', 'error');
                    }
                }).fail(function(){
                    Swal.fire('Error', 'Error deleting payment.', 'error');
                });
            }
        });
    });
});
</script>
@endsection
@endsection
