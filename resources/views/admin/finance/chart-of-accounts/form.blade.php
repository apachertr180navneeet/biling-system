@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-dollar"></i></div>
            <div>
                <h4 class="m-page-title">{{ isset($account) ? 'Edit Account' : 'Create Account' }}</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Finance</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="{{ route('admin.finance.chart-of-accounts.index') }}">Chart of Accounts</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">{{ isset($account) ? 'Edit' : 'Create' }}</li>
                </ul>
            </div>
        </div>
        <a href="{{ route('admin.finance.chart-of-accounts.index') }}" class="btn btn-outline-secondary"><i class="bx bx-left-arrow-alt me-1"></i> Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ isset($account) ? route('admin.finance.chart-of-accounts.update', ['chart_of_account' => $account]) : route('admin.finance.chart-of-accounts.store') }}" method="POST">
                @csrf
                @if(isset($account)) @method('PUT') @endif

                <div class="m-section-divider">Account Details</div>
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Code <span class="text-danger">*</span></label>
                        <input type="text" name="code" class="form-control" value="{{ old('code', $account?->code) }}" required>
                    </div>
                    <div class="col-md-5 mb-3">
                        <label class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $account?->name) }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Type <span class="text-danger">*</span></label>
                        <select name="type" class="form-select" required>
                            <option value="asset" {{ old('type', $account?->type) == 'asset' ? 'selected' : '' }}>Asset</option>
                            <option value="liability" {{ old('type', $account?->type) == 'liability' ? 'selected' : '' }}>Liability</option>
                            <option value="equity" {{ old('type', $account?->type) == 'equity' ? 'selected' : '' }}>Equity</option>
                            <option value="revenue" {{ old('type', $account?->type) == 'revenue' ? 'selected' : '' }}>Revenue</option>
                            <option value="expense" {{ old('type', $account?->type) == 'expense' ? 'selected' : '' }}>Expense</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Sub Type</label>
                        <select name="sub_type" class="form-select">
                            <option value="">-- None --</option>
                            <option value="current_asset" {{ old('sub_type', $account?->sub_type) == 'current_asset' ? 'selected' : '' }}>Current Asset</option>
                            <option value="fixed_asset" {{ old('sub_type', $account?->sub_type) == 'fixed_asset' ? 'selected' : '' }}>Fixed Asset</option>
                            <option value="current_liability" {{ old('sub_type', $account?->sub_type) == 'current_liability' ? 'selected' : '' }}>Current Liability</option>
                            <option value="long_term_liability" {{ old('sub_type', $account?->sub_type) == 'long_term_liability' ? 'selected' : '' }}>Long Term Liability</option>
                            <option value="equity" {{ old('sub_type', $account?->sub_type) == 'equity' ? 'selected' : '' }}>Equity</option>
                            <option value="revenue" {{ old('sub_type', $account?->sub_type) == 'revenue' ? 'selected' : '' }}>Revenue</option>
                            <option value="cost_of_goods" {{ old('sub_type', $account?->sub_type) == 'cost_of_goods' ? 'selected' : '' }}>Cost of Goods</option>
                            <option value="operating_expense" {{ old('sub_type', $account?->sub_type) == 'operating_expense' ? 'selected' : '' }}>Operating Expense</option>
                            <option value="non_operating_expense" {{ old('sub_type', $account?->sub_type) == 'non_operating_expense' ? 'selected' : '' }}>Non-Operating Expense</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Parent Account</label>
                        <select name="parent_id" class="form-select">
                            <option value="">-- None (Top Level) --</option>
                            @foreach($parents as $parent)
                            <option value="{{ $parent->id }}" {{ old('parent_id', $account?->parent_id) == $parent->id ? 'selected' : '' }}>{{ $parent->code }} - {{ $parent->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Is Group Account?</label>
                        <select name="is_group" class="form-select">
                            <option value="0" {{ old('is_group', $account?->is_group) == 0 ? 'selected' : '' }}>No</option>
                            <option value="1" {{ old('is_group', $account?->is_group) == 1 ? 'selected' : '' }}>Yes</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="active" {{ old('status', $account?->status ?? 'active') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $account?->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="2">{{ old('description', $account?->description) }}</textarea>
                    </div>
                </div>

                <div class="m-section-divider">Bank Details (Optional)</div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Is Bank Account?</label>
                        <select name="is_bank_account" class="form-select">
                            <option value="0" {{ old('is_bank_account', $account?->is_bank_account) == 0 ? 'selected' : '' }}>No</option>
                            <option value="1" {{ old('is_bank_account', $account?->is_bank_account) == 1 ? 'selected' : '' }}>Yes</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Bank Name</label>
                        <input type="text" name="bank_name" class="form-control" value="{{ old('bank_name', $account?->bank_name) }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Bank Account Number</label>
                        <input type="text" name="bank_account_number" class="form-control" value="{{ old('bank_account_number', $account?->bank_account_number) }}">
                    </div>
                </div>

                <div class="m-form-actions mt-3">
                    <button type="submit" class="btn btn-primary"><i class="bx bx-check me-1"></i> {{ isset($account) ? 'Update' : 'Create' }} Account</button>
                    <a href="{{ route('admin.finance.chart-of-accounts.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
