@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-credit-card"></i></div>
            <div>
                <h4 class="m-page-title">{{ isset($expense) ? 'Edit Expense' : 'Create Expense' }}</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Finance</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="{{ route('admin.finance.expenses.index') }}">Expenses</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">{{ isset($expense) ? 'Edit' : 'Create' }}</li>
                </ul>
            </div>
        </div>
        <a href="{{ route('admin.finance.expenses.index') }}" class="btn btn-outline-secondary"><i class="bx bx-left-arrow-alt me-1"></i> Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ isset($expense) ? route('admin.finance.expenses.update', $expense) : route('admin.finance.expenses.store') }}" method="POST">
                @csrf
                @if(isset($expense)) @method('PUT') @endif

                <div class="m-section-divider">Expense Details</div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Expense Date <span class="text-danger">*</span></label>
                        <input type="date" name="expense_date" class="form-control" value="{{ old('expense_date', $expense?->expense_date?->format('Y-m-d')) }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Payment Method <span class="text-danger">*</span></label>
                        <select name="payment_method" class="form-select" required>
                            <option value="">-- Select --</option>
                            <option value="cash" {{ old('payment_method', $expense?->payment_method) == 'cash' ? 'selected' : '' }}>Cash</option>
                            <option value="bank_transfer" {{ old('payment_method', $expense?->payment_method) == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                            <option value="cheque" {{ old('payment_method', $expense?->payment_method) == 'cheque' ? 'selected' : '' }}>Cheque</option>
                            <option value="upi" {{ old('payment_method', $expense?->payment_method) == 'upi' ? 'selected' : '' }}>UPI</option>
                            <option value="card" {{ old('payment_method', $expense?->payment_method) == 'card' ? 'selected' : '' }}>Card</option>
                            <option value="online" {{ old('payment_method', $expense?->payment_method) == 'online' ? 'selected' : '' }}>Online</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Account <span class="text-danger">*</span></label>
                        <select name="account_id" class="form-select" required>
                            <option value="">-- Select Account --</option>
                            @foreach($accounts as $account)
                            <option value="{{ $account->id }}" {{ old('account_id', $expense?->account_id) == $account->id ? 'selected' : '' }}>{{ $account->code }} - {{ $account->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Vendor</label>
                        <select name="vendor_id" class="form-select">
                            <option value="">-- Select Vendor --</option>
                            @foreach($vendors as $vendor)
                            <option value="{{ $vendor->id }}" {{ old('vendor_id', $expense?->vendor_id) == $vendor->id ? 'selected' : '' }}>{{ $vendor->company_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="m-section-divider">Amounts & Status</div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Amount <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="amount" class="form-control" value="{{ old('amount', $expense?->amount) }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Tax Amount</label>
                        <input type="number" step="0.01" name="tax_amount" class="form-control" value="{{ old('tax_amount', $expense?->tax_amount ?? 0) }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select" required>
                            <option value="draft" {{ old('status', $expense?->status ?? 'draft') == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="pending" {{ old('status', $expense?->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ old('status', $expense?->status) == 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="paid" {{ old('status', $expense?->status) == 'paid' ? 'selected' : '' }}>Paid</option>
                            <option value="rejected" {{ old('status', $expense?->status) == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Description <span class="text-danger">*</span></label>
                        <textarea name="description" class="form-control" rows="2" required>{{ old('description', $expense?->description) }}</textarea>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="2">{{ old('notes', $expense?->notes) }}</textarea>
                    </div>
                </div>

                <div class="m-form-actions mt-3">
                    <button type="submit" class="btn btn-primary"><i class="bx bx-check me-1"></i> {{ isset($expense) ? 'Update' : 'Create' }} Expense</button>
                    <a href="{{ route('admin.finance.expenses.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
