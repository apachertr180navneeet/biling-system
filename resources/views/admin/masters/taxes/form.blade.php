@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-receipt"></i></div>
            <div>
                <h4 class="m-page-title">{{ isset($tax) ? 'Edit Tax' : 'Create Tax' }}</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Masters</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="{{ route('admin.masters.taxes.index') }}">Taxes</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">{{ isset($tax) ? 'Edit' : 'Create' }}</li>
                </ul>
            </div>
        </div>
        <a href="{{ route('admin.masters.taxes.index') }}" class="btn btn-outline-secondary"><i class="bx bx-left-arrow-alt me-1"></i> Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ isset($tax) ? route('admin.masters.taxes.update', $tax) : route('admin.masters.taxes.store') }}" method="POST">
                @csrf
                @if(isset($tax)) @method('PUT') @endif
                
                <div class="m-section-divider">Tax Details</div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $tax?->name) }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Rate (%) <span class="text-danger">*</span></label>
                        <input type="number" name="rate" class="form-control" value="{{ old('rate', $tax?->rate ?? 0) }}" step="0.01" min="0" max="100" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Type <span class="text-danger">*</span></label>
                        <select name="type" class="form-select" required>
                            <option value="percentage" {{ old('type', $tax?->type) == 'percentage' ? 'selected' : '' }}>Percentage</option>
                            <option value="fixed" {{ old('type', $tax?->type) == 'fixed' ? 'selected' : '' }}>Fixed</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Default</label>
                        <select name="is_default" class="form-select">
                            <option value="0" {{ old('is_default', $tax?->is_default) ? '' : 'selected' }}>No</option>
                            <option value="1" {{ old('is_default', $tax?->is_default) ? 'selected' : '' }}>Yes</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="active" {{ old('status', $tax?->status ?? 'active') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $tax?->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>
                
                <div class="m-form-actions mt-3">
                    <button type="submit" class="btn btn-primary"><i class="bx bx-check me-1"></i> {{ isset($tax) ? 'Update' : 'Create' }} Tax</button>
                    <a href="{{ route('admin.masters.taxes.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

