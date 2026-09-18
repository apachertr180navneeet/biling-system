@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-dollar"></i></div>
            <div>
                <h4 class="m-page-title">{{ isset($account) ? 'Edit Invoice' : 'Create Invoice' }}</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Finance</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="{{ route('admin.finance.accounts-receivable.index') }}">Accounts Receivable</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">{{ isset($account) ? 'Edit' : 'Create' }}</li>
                </ul>
            </div>
        </div>
        <a href="{{ route('admin.finance.accounts-receivable.index') }}" class="btn btn-outline-secondary"><i class="bx bx-left-arrow-alt me-1"></i> Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ isset($account) ? route('admin.finance.accounts-receivable.update', $account) : route('admin.finance.accounts-receivable.store') }}" method="POST">
                @csrf
                @if(isset($account)) @method('PUT') @endif

                <div class="m-section-divider">Invoice Details</div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Invoice Date <span class="text-danger">*</span></label>
                        <input type="date" name="invoice_date" class="form-control" value="{{ old('invoice_date', $account?->invoice_date?->format('Y-m-d')) }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Due Date <span class="text-danger">*</span></label>
                        <input type="date" name="due_date" class="form-control" value="{{ old('due_date', $account?->due_date?->format('Y-m-d')) }}" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Guest <span class="text-danger">*</span></label>
                        <select name="guest_id" class="form-select" required>
                            <option value="">-- Select Guest --</option>
                            @foreach($guests as $guest)
                            <option value="{{ $guest->id }}" {{ old('guest_id', $account?->guest_id) == $guest->id ? 'selected' : '' }}>{{ $guest->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="m-section-divider">Amounts</div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Subtotal <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="subtotal" class="form-control" value="{{ old('subtotal', $account?->subtotal) }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tax Amount</label>
                        <input type="number" step="0.01" name="tax_amount" class="form-control" value="{{ old('tax_amount', $account?->tax_amount ?? 0) }}">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="3">{{ old('notes', $account?->notes) }}</textarea>
                    </div>
                </div>

                <div class="m-form-actions mt-3">
                    <button type="submit" class="btn btn-primary"><i class="bx bx-check me-1"></i> {{ isset($account) ? 'Update' : 'Create' }} Invoice</button>
                    <a href="{{ route('admin.finance.accounts-receivable.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
