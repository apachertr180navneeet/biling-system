@extends('admin.layouts.app')

@section('style')
<style>
    .ledger-summary-card {
        border-radius: 12px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .ledger-summary-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
    }
    .badge-paid {
        background-color: #71dd37;
        color: #fff;
    }
    .badge-partial {
        background-color: #ffab00;
        color: #fff;
    }
    .badge-unpaid {
        background-color: #ff3e1d;
        color: #fff;
    }
</style>
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header Title -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">
                <span class="text-muted fw-light">Admin / Customers /</span> Complete Ledger History
            </h4>
            <p class="text-muted mb-0">Showing full statement up to {{ date('d M Y', strtotime($toDate ?? date('Y-m-d'))) }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.customers.ledger.export', ['customer' => $customer->id, 'from_date' => $fromDate, 'to_date' => $toDate]) }}" class="btn btn-outline-success">
                <i class="bx bx-file-export me-1"></i> Export Excel
            </a>
            <button onclick="window.print()" class="btn btn-outline-secondary">
                <i class="bx bx-printer me-1"></i> Print Statement
            </button>
            <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary">
                <i class="bx bx-arrow-back me-1"></i> Back to Customers
            </a>
        </div>
    </div>

    <!-- Customer Profile Card -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-7">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-xl me-3 bg-label-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 54px; height: 54px; font-size: 1.6rem; font-weight: bold;">
                            {{ strtoupper(substr($customer->name, 0, 1)) }}
                        </div>
                        <div>
                            <h4 class="mb-1 text-primary fw-bold">{{ $customer->name }}</h4>
                            <div class="d-flex flex-wrap gap-3 text-muted small">
                                <span><i class="bx bx-phone me-1"></i>{{ $customer->phone ?? 'N/A' }}</span>
                                <span><i class="bx bx-envelope me-1"></i>{{ $customer->email ?? 'N/A' }}</span>
                                <span><i class="bx bx-building me-1"></i>GSTIN: {{ $customer->gstin ?? 'N/A' }}</span>
                                <span><i class="bx bx-map me-1"></i>{{ $customer->address ?? 'N/A' }} {{ $customer->state ? '('.$customer->state.')' : '' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-5 text-md-end mt-3 mt-md-0">
                    <span class="badge bg-label-secondary px-3 py-2 fs-6">Type: {{ ucfirst($customer->type) }}</span>
                    @if($customer->company_name)
                        <div class="mt-2 text-muted fw-semibold"><i class="bx bx-briefcase me-1"></i>Company: {{ $customer->company_name }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Summary KPI Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-sm-6">
            <div class="card ledger-summary-card bg-primary text-white border-0 shadow-sm">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-white-50 text-uppercase fw-semibold small">Total Invoiced</span>
                        <i class="bx bx-receipt fs-4 text-white-50"></i>
                    </div>
                    <h3 class="text-white mb-0 fw-bold">₹{{ number_format($totalBilled, 2) }}</h3>
                    <small class="text-white-50">{{ $totalInvoices }} Invoices Total</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card ledger-summary-card bg-success text-white border-0 shadow-sm">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-white-50 text-uppercase fw-semibold small">Total Received</span>
                        <i class="bx bx-check-circle fs-4 text-white-50"></i>
                    </div>
                    <h3 class="text-white mb-0 fw-bold">₹{{ number_format($totalPaid, 2) }}</h3>
                    <small class="text-white-50">Paid up to today</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card ledger-summary-card {{ $totalOutstanding > 0 ? 'bg-danger' : 'bg-info' }} text-white border-0 shadow-sm">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-white-50 text-uppercase fw-semibold small">Net Outstanding</span>
                        <i class="bx bx-error-circle fs-4 text-white-50"></i>
                    </div>
                    <h3 class="text-white mb-0 fw-bold">₹{{ number_format($totalOutstanding, 2) }}</h3>
                    <small class="text-white-50">{{ $totalOutstanding > 0 ? 'Balance Pending' : 'Clear / Settled' }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card ledger-summary-card bg-dark text-white border-0 shadow-sm">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-white-50 text-uppercase fw-semibold small">Filtered Count</span>
                        <i class="bx bx-list-ul fs-4 text-white-50"></i>
                    </div>
                    <h3 class="text-white mb-0 fw-bold">{{ $transactions->count() }}</h3>
                    <small class="text-white-50">Transactions Shown</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.customers.ledger', $customer) }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">From Date</label>
                        <input type="date" name="from_date" class="form-control" value="{{ $fromDate ?? '' }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">To Date (Up to)</label>
                        <input type="date" name="to_date" class="form-control" value="{{ $toDate ?? date('Y-m-d') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Doc Type</label>
                        <select name="type" class="form-select no-select2">
                            <option value="all" {{ ($type ?? 'all') === 'all' ? 'selected' : '' }}>All Invoices</option>
                            <option value="vehicle" {{ ($type ?? '') === 'vehicle' ? 'selected' : '' }}>Vehicle Invoices</option>
                            <option value="part" {{ ($type ?? '') === 'part' ? 'selected' : '' }}>Part Invoices</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Status</label>
                        <select name="payment_status" class="form-select no-select2">
                            <option value="all" {{ ($paymentStatus ?? 'all') === 'all' ? 'selected' : '' }}>All Status</option>
                            <option value="paid" {{ ($paymentStatus ?? '') === 'paid' ? 'selected' : '' }}>Paid Only</option>
                            <option value="pending" {{ ($paymentStatus ?? '') === 'pending' ? 'selected' : '' }}>Pending Only</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1"><i class="bx bx-filter-alt"></i> Filter</button>
                        <a href="{{ route('admin.customers.ledger', $customer) }}" class="btn btn-outline-secondary" title="Reset Filters"><i class="bx bx-reset"></i></a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Complete Ledger Transactions Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white d-flex align-items-center justify-content-between py-3">
            <h5 class="mb-0 fw-bold text-dark"><i class="bx bx-list-check me-2 text-primary"></i>Transaction History</h5>
            <span class="text-muted small">All records up to today</span>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>Invoice #</th>
                        <th>Type</th>
                        <th>Particulars / Details</th>
                        <th>Payment Mode</th>
                        <th class="text-end">Invoice Amount</th>
                        <th class="text-end">Paid Amount</th>
                        <th class="text-end">Balance Amount</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $t)
                    @php
                        $statusClass = $t['balance'] <= 0 ? 'badge-paid' : ($t['received_amount'] > 0 ? 'badge-partial' : 'badge-unpaid');
                        $statusText  = $t['balance'] <= 0 ? 'PAID' : ($t['received_amount'] > 0 ? 'PARTIAL' : 'UNPAID');
                    @endphp
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td><span class="fw-semibold">{{ date('d M Y', strtotime($t['doc_date'])) }}</span></td>
                        <td>
                            <a href="{{ $t['show_url'] }}" class="fw-bold text-primary" target="_blank">{{ $t['doc_number'] }}</a>
                        </td>
                        <td>
                            @if($t['doc_type'] === 'vehicle')
                                <span class="badge bg-label-info"><i class="bx bx-car me-1"></i>Vehicle</span>
                            @else
                                <span class="badge bg-label-warning"><i class="bx bx-wrench me-1"></i>Part</span>
                            @endif
                        </td>
                        <td style="max-width: 250px; white-space: normal;">
                            <span class="text-dark fw-semibold">{{ $t['particulars'] }}</span>
                        </td>
                        <td><span class="badge bg-label-secondary">{{ $t['payment_mode'] }}</span></td>
                        <td class="text-end fw-bold text-dark">₹{{ number_format($t['total_amount'], 2) }}</td>
                        <td class="text-end fw-bold text-success">₹{{ number_format($t['received_amount'], 2) }}</td>
                        <td class="text-end fw-bold {{ $t['balance'] > 0 ? 'text-danger' : 'text-muted' }}">₹{{ number_format($t['balance'], 2) }}</td>
                        <td class="text-center">
                            <span class="badge {{ $statusClass }} px-2 py-1">{{ $statusText }}</span>
                        </td>
                        <td class="text-center">
                            <div class="btn-group">
                                <a href="{{ $t['pdf_url'] }}" class="btn btn-sm btn-outline-primary" target="_blank" title="View PDF">
                                    <i class="bx bx-file"></i> PDF
                                </a>
                                @if($t['balance'] > 0)
                                <button type="button" 
                                        class="btn btn-sm btn-success btn-receive-payment" 
                                        data-url="{{ $t['payment_url'] }}" 
                                        data-doc="{{ $t['doc_number'] }}" 
                                        data-balance="{{ number_format($t['balance'], 2, '.', '') }}">
                                    <i class="bx bx-dollar"></i> Pay
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="text-center py-5">
                            <div class="text-muted">
                                <i class="bx bx-folder-open fs-1 mb-2 d-block text-secondary"></i>
                                <h5>No transactions found for this customer.</h5>
                                <p class="mb-0">Try adjusting your filters or date range.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if($transactions->count() > 0)
                <tfoot class="table-light fw-bold">
                    <tr>
                        <td colspan="6" class="text-end text-uppercase">Subtotal (Shown Transactions):</td>
                        <td class="text-end text-dark">₹{{ number_format($transactions->sum('total_amount'), 2) }}</td>
                        <td class="text-end text-success">₹{{ number_format($transactions->sum('received_amount'), 2) }}</td>
                        <td class="text-end text-danger">₹{{ number_format($transactions->sum('balance'), 2) }}</td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>

<!-- Receive Payment Modal -->
<div class="modal fade" id="receivePaymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form id="receivePaymentForm">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title text-white" id="paymentModalTitle">Receive Payment</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Pending Outstanding Balance</label>
                        <input type="text" id="modal_balance_display" class="form-control bg-light text-danger fw-bold fs-5" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Payment Amount to Receive (₹) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="amount" id="modal_amount_input" class="form-control form-control-lg fw-bold" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success"><i class="bx bx-check"></i> Submit Payment</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

@section('script')
<script>
$(function(){
    var activePaymentUrl = '';

    $('.btn-receive-payment').click(function(){
        activePaymentUrl = $(this).data('url');
        var docNo = $(this).data('doc');
        var balance = $(this).data('balance');

        $('#paymentModalTitle').text('Receive Payment for ' + docNo);
        $('#modal_balance_display').val('₹' + parseFloat(balance).toLocaleString('en-IN', {minimumFractionDigits: 2}));
        $('#modal_amount_input').val(balance).attr('max', balance);
        
        var modal = new bootstrap.Modal(document.getElementById('receivePaymentModal'));
        modal.show();
    });

    $('#receivePaymentForm').submit(function(e){
        e.preventDefault();
        if (!activePaymentUrl) return;

        var btn = $(this).find('button[type="submit"]');
        btn.prop('disabled', true).html('<i class="bx bx-loader-alt bx-spin"></i> Processing...');

        $.ajax({
            url: activePaymentUrl,
            type: 'POST',
            data: $(this).serialize(),
            success: function(resp) {
                if (resp.success) {
                    Swal.fire('Success', resp.message, 'success').then(function(){
                        location.reload();
                    });
                } else {
                    Swal.fire('Error', resp.message || 'Payment failed.', 'error');
                    btn.prop('disabled', false).html('<i class="bx bx-check"></i> Submit Payment');
                }
            },
            error: function(xhr) {
                var msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'An error occurred.';
                Swal.fire('Error', msg, 'error');
                btn.prop('disabled', false).html('<i class="bx bx-check"></i> Submit Payment');
            }
        });
    });
});
</script>
@endsection
