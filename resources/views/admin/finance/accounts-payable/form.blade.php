@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-wallet"></i></div>
            <div>
                <h4 class="m-page-title">{{ isset($account) ? 'Edit Bill' : 'Create Bill' }}</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Finance</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="{{ route('admin.finance.accounts-payable.index') }}">Accounts Payable</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">{{ isset($account) ? 'Edit' : 'Create' }}</li>
                </ul>
            </div>
        </div>
        <a href="{{ route('admin.finance.accounts-payable.index') }}" class="btn btn-outline-secondary"><i class="bx bx-left-arrow-alt me-1"></i> Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ isset($account) ? route('admin.finance.accounts-payable.update', $account) : route('admin.finance.accounts-payable.store') }}" method="POST">
                @csrf
                @if(isset($account)) @method('PUT') @endif

                <div class="m-section-divider">Bill Details</div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Bill Date <span class="text-danger">*</span></label>
                        <input type="date" name="bill_date" class="form-control" value="{{ old('bill_date', $account?->bill_date?->format('Y-m-d')) }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Due Date <span class="text-danger">*</span></label>
                        <input type="date" name="due_date" class="form-control" value="{{ old('due_date', $account?->due_date?->format('Y-m-d')) }}" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Vendor</label>
                        <select name="vendor_id" class="form-select">
                            <option value="">-- Select Vendor --</option>
                            @foreach($vendors as $vendor)
                            <option value="{{ $vendor->id }}" {{ old('vendor_id', $account?->vendor_id) == $vendor->id ? 'selected' : '' }}>{{ $vendor->company_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Supplier</label>
                        <select name="supplier_id" class="form-select">
                            <option value="">-- Select Supplier --</option>
                            @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ old('supplier_id', $account?->supplier_id) == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
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
                    <button type="submit" class="btn btn-primary"><i class="bx bx-check me-1"></i> {{ isset($account) ? 'Update' : 'Create' }} Bill</button>
                    <a href="{{ route('admin.finance.accounts-payable.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
