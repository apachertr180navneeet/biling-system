@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-money"></i></div>
            <div>
                <h4 class="m-page-title">{{ isset($currency) ? 'Edit Currency' : 'Create Currency' }}</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Masters</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="{{ route('admin.masters.currencies.index') }}">Currencies</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">{{ isset($currency) ? 'Edit' : 'Create' }}</li>
                </ul>
            </div>
        </div>
        <a href="{{ route('admin.masters.currencies.index') }}" class="btn btn-outline-secondary"><i class="bx bx-left-arrow-alt me-1"></i> Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ isset($currency) ? route('admin.masters.currencies.update', $currency) : route('admin.masters.currencies.store') }}" method="POST">
                @csrf
                @if(isset($currency)) @method('PUT') @endif
                
                <div class="m-section-divider">Currency Details</div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Code <span class="text-danger">*</span></label>
                        <input type="text" name="code" class="form-control" value="{{ old('code', $currency?->code) }}" placeholder="e.g. USD" maxlength="10" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $currency?->name) }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Symbol <span class="text-danger">*</span></label>
                        <input type="text" name="symbol" class="form-control" value="{{ old('symbol', $currency?->symbol) }}" maxlength="10" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Exchange Rate <span class="text-danger">*</span></label>
                        <input type="number" name="exchange_rate" class="form-control" value="{{ old('exchange_rate', $currency?->exchange_rate ?? 1) }}" step="0.000001" min="0" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Default</label>
                        <select name="is_default" class="form-select">
                            <option value="0" {{ old('is_default', $currency?->is_default) ? '' : 'selected' }}>No</option>
                            <option value="1" {{ old('is_default', $currency?->is_default) ? 'selected' : '' }}>Yes</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="active" {{ old('status', $currency?->status ?? 'active') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $currency?->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>
                
                <div class="m-form-actions mt-3">
                    <button type="submit" class="btn btn-primary"><i class="bx bx-check me-1"></i> {{ isset($currency) ? 'Update' : 'Create' }} Currency</button>
                    <a href="{{ route('admin.masters.currencies.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

