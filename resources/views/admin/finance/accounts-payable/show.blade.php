@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-wallet"></i></div>
            <div>
                <h4 class="m-page-title">Bill #{{ $account->bill_number }}</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Finance</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="{{ route('admin.finance.accounts-payable.index') }}">Accounts Payable</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">View</li>
                </ul>
            </div>
        </div>
        <a href="{{ route('admin.finance.accounts-payable.index') }}" class="btn btn-outline-secondary"><i class="bx bx-left-arrow-alt me-1"></i> Back</a>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3"><strong>Bill Number:</strong><br>{{ $account->bill_number }}</div>
                <div class="col-md-3"><strong>Bill Date:</strong><br>{{ $account->bill_date->format('d-m-Y') }}</div>
                <div class="col-md-3"><strong>Due Date:</strong><br>{{ $account->due_date->format('d-m-Y') }}</div>
                <div class="col-md-3"><strong>Status:</strong><br>
                    @php $colors = ['draft' => 'warning', 'pending' => 'info', 'partial' => 'primary', 'paid' => 'success', 'overdue' => 'danger']; @endphp
                    <span class="badge bg-label-{{ $colors[$account->status] ?? 'secondary' }}">{{ ucfirst($account->status) }}</span>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-3"><strong>Vendor:</strong><br>{{ $account->vendor?->name ?? '-' }}</div>
                <div class="col-md-3"><strong>Supplier:</strong><br>{{ $account->supplier?->name ?? '-' }}</div>
                <div class="col-md-2"><strong>Subtotal:</strong><br>{{ number_format($account->subtotal, 2) }}</div>
                <div class="col-md-2"><strong>Tax:</strong><br>{{ number_format($account->tax_amount, 2) }}</div>
                <div class="col-md-2"><strong>Total:</strong><br>{{ number_format($account->total_amount, 2) }}</div>
            </div>
            <div class="row mt-3">
                <div class="col-md-2"><strong>Paid:</strong><br>{{ number_format($account->paid_amount, 2) }}</div>
                <div class="col-md-2"><strong>Balance:</strong><br>{{ number_format($account->balance, 2) }}</div>
                <div class="col-md-8"><strong>Notes:</strong><br>{{ $account->notes ?? '-' }}</div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <h5>Payments</h5>
            <table class="table table-bordered table-striped">
                <thead><tr><th>#</th><th>Payment Date</th><th class="text-end">Amount</th><th>Method</th><th>Reference</th><th>Notes</th></tr></thead>
                <tbody>
                    @forelse($account->payments as $index => $payment)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $payment->payment_date->format('d-m-Y') }}</td>
                        <td class="text-end">{{ number_format($payment->amount, 2) }}</td>
                        <td>{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</td>
                        <td>{{ $payment->reference_number ?? '-' }}</td>
                        <td>{{ $payment->notes ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted">No payments recorded</td></tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="table-active">
                        <td colspan="2"><strong>Total Paid</strong></td>
                        <td class="text-end"><strong>{{ number_format($account->paid_amount, 2) }}</strong></td>
                        <td colspan="3"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    @if(auth()->user()->hasPermission('accounts_payable.edit'))
    <div class="card">
        <div class="card-body">
            <h5>Record Payment</h5>
            <form action="{{ route('admin.finance.accounts-payable.payment', $account) }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Payment Date <span class="text-danger">*</span></label>
                        <input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Amount <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="amount" class="form-control" max="{{ $account->balance }}" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Payment Method <span class="text-danger">*</span></label>
                        <select name="payment_method" class="form-select" required>
                            <option value="">-- Select --</option>
                            <option value="cash">Cash</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="cheque">Cheque</option>
                            <option value="upi">UPI</option>
                            <option value="card">Card</option>
                            <option value="online">Online</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Reference Number</label>
                        <input type="text" name="reference_number" class="form-control">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <button type="submit" class="btn btn-success"><i class="bx bx-check me-1"></i> Record Payment</button>
            </form>
        </div>
    </div>
    @endif
</div>
@endsection
