@extends('admin.layouts.app')
@section('style')
@endsection
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Admin / Customers /</span> Details
    </h4>
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="mb-0">{{ $customer->name }}</h5>
            <div>
                <a href="{{ route('admin.customers.ledger', $customer) }}" class="btn btn-dark me-2"><i class="bx bx-book-content me-1"></i> View Complete Ledger</a>
                <a href="{{ route('admin.customers.edit', $customer) }}" class="btn btn-primary">Edit</a>
                <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary">Back</a>
            </div>
        </div>
        <div class="card-body">
            <!-- Live Ledger Summary Box -->
            <div id="customer_show_ledger_box" class="alert alert-light border border-primary-subtle p-3 mb-4 rounded shadow-xs" style="background-color: #f8fafc;">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0 text-primary fw-bold"><i class="bx bx-receipt me-1"></i> Ledger Financial Overview</h6>
                    <a href="{{ route('admin.customers.ledger', $customer) }}" class="btn btn-sm btn-outline-primary">
                        Open Full History <i class="bx bx-right-arrow-alt"></i>
                    </a>
                </div>
                <div class="row g-2 text-center" id="show_ledger_metrics">
                    <div class="col-md-4">
                        <div class="p-2 bg-white rounded border">
                            <small class="text-muted d-block text-uppercase fw-semibold">Total Invoiced</small>
                            <span id="show_lbl_total" class="h6 mb-0 text-dark fw-bold">Loading...</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-2 bg-white rounded border">
                            <small class="text-muted d-block text-uppercase fw-semibold">Total Paid</small>
                            <span id="show_lbl_paid" class="h6 mb-0 text-success fw-bold">Loading...</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-2 bg-white rounded border">
                            <small class="text-muted d-block text-uppercase fw-semibold">Net Outstanding</small>
                            <span id="show_lbl_bal" class="h6 mb-0 text-danger fw-bold">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
            <table class="table table-bordered">
                <tr><th style="width:35%">Type</th><td>{{ ucfirst($customer->type) }}</td></tr>
                <tr><th>Name</th><td>{{ $customer->name }}</td></tr>
                <tr><th>Company Name</th><td>{{ $customer->company_name ?? '-' }}</td></tr>
                <tr><th>Phone</th><td>{{ $customer->phone }}</td></tr>
                <tr><th>Email</th><td>{{ $customer->email ?? '-' }}</td></tr>
                <tr><th>Address</th><td>{{ $customer->address ?? '-' }}</td></tr>
                <tr><th>State</th><td>{{ $customer->state ?? '-' }}</td></tr>
                <tr><th>GSTIN</th><td>{{ $customer->gstin ?? '-' }}</td></tr>
                <tr><th>PAN No</th><td>{{ $customer->pan_no ?? '-' }}</td></tr>
                <tr><th>Aadhaar No</th><td>{{ $customer->aadhaar_no ?? '-' }}</td></tr>
            </table>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script>
$(function(){
    $.ajax({
        url: "{{ route('admin.customers.ledger-summary') }}",
        type: "GET",
        data: { customer_id: "{{ $customer->id }}" },
        success: function(resp) {
            if (resp.success) {
                $('#show_lbl_total').text('₹' + parseFloat(resp.total_amount).toLocaleString('en-IN', {minimumFractionDigits: 2}));
                $('#show_lbl_paid').text('₹' + parseFloat(resp.paid_amount).toLocaleString('en-IN', {minimumFractionDigits: 2}));
                var bal = parseFloat(resp.outstanding_balance);
                $('#show_lbl_bal').text('₹' + bal.toLocaleString('en-IN', {minimumFractionDigits: 2}));
                if (bal > 0) {
                    $('#show_lbl_bal').removeClass('text-success text-muted').addClass('text-danger');
                } else {
                    $('#show_lbl_bal').removeClass('text-danger').addClass('text-success');
                }
            }
        }
    });
});
</script>
@endsection
