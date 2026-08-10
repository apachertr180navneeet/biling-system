@extends('admin.layouts.master')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold py-1 mb-0">
                <span class="text-muted fw-light">Reports /</span> Payment Audit Log
            </h4>
            <p class="text-muted small mb-0">Complete audit trail of all payments received and payment rollbacks across Sales & Purchases.</p>
        </div>
    </div>

    <!-- Summary Metrics -->
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm border-start border-success border-4 h-100">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted text-uppercase mb-1 small">Total Payments</h6>
                            <h4 class="mb-0 text-success fw-bold">₹{{ number_format($totalPayments, 2) }}</h4>
                        </div>
                        <div class="avatar bg-light-success p-2 rounded">
                            <i class="bx bx-down-arrow-circle text-success fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm border-start border-danger border-4 h-100">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted text-uppercase mb-1 small">Total Rolled Back</h6>
                            <h4 class="mb-0 text-danger fw-bold">₹{{ number_format($totalRollbacks, 2) }}</h4>
                        </div>
                        <div class="avatar bg-light-danger p-2 rounded">
                            <i class="bx bx-undo text-danger fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm border-start border-primary border-4 h-100">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted text-uppercase mb-1 small">Net Realized</h6>
                            <h4 class="mb-0 text-primary fw-bold">₹{{ number_format($totalPayments - $totalRollbacks, 2) }}</h4>
                        </div>
                        <div class="avatar bg-light-primary p-2 rounded">
                            <i class="bx bx-wallet text-primary fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm border-start border-dark border-4 h-100">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted text-uppercase mb-1 small">Total Audit Records</h6>
                            <h4 class="mb-0 text-dark fw-bold">{{ number_format($transactions->total()) }}</h4>
                        </div>
                        <div class="avatar bg-light p-2 rounded">
                            <i class="bx bx-receipt text-dark fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body py-3">
            <form action="{{ route('admin.reports.payment-audit-log') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-2">
                    <label class="form-label small fw-bold">From Date</label>
                    <input type="date" name="from_date" value="{{ $fromDate }}" class="form-control form-control-sm">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold">To Date</label>
                    <input type="date" name="to_date" value="{{ $toDate }}" class="form-control form-control-sm">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold">Transaction Type</label>
                    <select name="transaction_type" class="form-select form-select-sm">
                        <option value="all" {{ $type === 'all' ? 'selected' : '' }}>All Events</option>
                        <option value="payment" {{ $type === 'payment' ? 'selected' : '' }}>Payments Received</option>
                        <option value="rollback" {{ $type === 'rollback' ? 'selected' : '' }}>Payment Rollbacks</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold">Document Type</label>
                    <select name="doc_type" class="form-select form-select-sm">
                        <option value="all" {{ $docType === 'all' ? 'selected' : '' }}>All Modules</option>
                        <option value="vehicle_sale" {{ $docType === 'vehicle_sale' ? 'selected' : '' }}>Vehicle Sales</option>
                        <option value="part_sale" {{ $docType === 'part_sale' ? 'selected' : '' }}>Parts Sales</option>
                        <option value="vehicle_purchase" {{ $docType === 'vehicle_purchase' ? 'selected' : '' }}>Vehicle Purchases</option>
                        <option value="part_purchase" {{ $docType === 'part_purchase' ? 'selected' : '' }}>Parts Purchases</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Search</label>
                    <input type="text" name="search" value="{{ $search }}" class="form-control form-control-sm" placeholder="Doc No, Party Name, Reason, Ref...">
                </div>
                <div class="col-md-1 d-flex gap-1">
                    <button type="submit" class="btn btn-primary btn-sm w-100" title="Filter"><i class="bx bx-filter-alt"></i></button>
                    <a href="{{ route('admin.reports.payment-audit-log') }}" class="btn btn-outline-secondary btn-sm" title="Reset"><i class="bx bx-refresh"></i></a>
                </div>
            </form>
        </div>
    </div>

    <!-- Audit Log Table -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Date & Time</th>
                            <th>Type</th>
                            <th>Document Reference</th>
                            <th>Party Name</th>
                            <th class="text-end">Amount</th>
                            <th>Mode / Ref</th>
                            <th>Reason / Note</th>
                            <th>Performed By</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $tx)
                            @php
                                $payable = $tx->payable;
                                $docNo = $payable ? ($payable->invoice_number ?? $payable->po_number ?? $payable->order_number ?? ('#' . $payable->id)) : 'Deleted Doc';
                                $partyName = $payable ? (optional($payable->customer)->name ?? $payable->customer_name ?? optional($payable->supplier)->name ?? 'N/A') : 'N/A';
                                
                                $moduleAlias = '';
                                $moduleLabel = '';
                                if ($tx->payable_type === 'App\Models\VehicleSalesInvoice') {
                                    $moduleAlias = 'vehicle-sales-invoice';
                                    $moduleLabel = 'Vehicle Sale';
                                } elseif ($tx->payable_type === 'App\Models\PartSalesInvoice') {
                                    $moduleAlias = 'part-sales-invoice';
                                    $moduleLabel = 'Parts Sale';
                                } elseif ($tx->payable_type === 'App\Models\VehiclePurchaseOrder') {
                                    $moduleAlias = 'vehicle-purchase-order';
                                    $moduleLabel = 'Vehicle PO';
                                } elseif ($tx->payable_type === 'App\Models\PurchaseOrder') {
                                    $moduleAlias = 'purchase-order';
                                    $moduleLabel = 'Parts PO';
                                }
                            @endphp
                            <tr>
                                <td>
                                    <small class="fw-semibold">{{ $tx->created_at ? $tx->created_at->format('d-m-Y h:i A') : '-' }}</small>
                                </td>
                                <td>
                                    @if($tx->transaction_type === 'payment')
                                        <span class="badge bg-success"><i class="bx bx-down-arrow-alt"></i> PAYMENT</span>
                                    @else
                                        <span class="badge bg-danger"><i class="bx bx-undo"></i> ROLLBACK</span>
                                    @endif
                                </td>
                                <td>
                                    <strong class="text-dark">{{ $docNo }}</strong>
                                    <span class="badge bg-label-secondary ms-1">{{ $moduleLabel }}</span>
                                </td>
                                <td>{{ $partyName }}</td>
                                <td class="text-end fw-bold {{ $tx->transaction_type === 'payment' ? 'text-success' : 'text-danger' }}">
                                    {{ $tx->transaction_type === 'payment' ? '+' : '-' }}₹{{ number_format($tx->amount, 2) }}
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $tx->payment_mode ?? 'Cash' }}</span>
                                    @if($tx->reference_no)
                                        <small class="d-block text-muted">Ref: {{ $tx->reference_no }}</small>
                                    @endif
                                </td>
                                <td>
                                    <small class="text-wrap" style="max-width: 250px;">{{ $tx->note ?? '-' }}</small>
                                </td>
                                <td>
                                    <small class="fw-semibold text-muted">{{ optional($tx->createdBy)->name ?? 'System' }}</small>
                                </td>
                                <td class="text-center">
                                    @if($payable && $moduleAlias)
                                        <button class="btn btn-xs btn-outline-info" 
                                                onclick="openPaymentHistoryModal('{{ $moduleAlias }}', {{ $payable->id }})" 
                                                title="View Document Payment History">
                                            <i class="bx bx-history"></i> History
                                        </button>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-5">
                                    <i class="bx bx-receipt fs-1 d-block mb-2"></i>
                                    No payment or rollback audit records found matching your filters.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($transactions->hasPages())
                <div class="card-footer bg-light py-2">
                    {{ $transactions->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

@include('admin.payment_transactions.history_modal')
@endsection
