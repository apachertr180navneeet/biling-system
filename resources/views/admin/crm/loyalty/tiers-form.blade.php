@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-medal"></i></div>
            <div>
                <h4 class="m-page-title">{{ isset($tier) ? 'Edit Loyalty Tier' : 'Create Loyalty Tier' }}</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Guest CRM</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="{{ route('admin.crm.loyalty.tiers.index') }}">Loyalty Tiers</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">{{ isset($tier) ? 'Edit' : 'Create' }}</li>
                </ul>
            </div>
        </div>
        <a href="{{ route('admin.crm.loyalty.tiers.index') }}" class="btn btn-outline-secondary"><i class="bx bx-left-arrow-alt me-1"></i> Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ isset($tier) ? route('admin.crm.loyalty.tiers.update', $tier) : route('admin.crm.loyalty.tiers.store') }}" method="POST">
                @csrf
                @if(isset($tier)) @method('PUT') @endif

                <div class="m-section-divider">Tier Details</div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Tier Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $tier?->name) }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Minimum Points <span class="text-danger">*</span></label>
                        <input type="number" name="min_points" class="form-control" min="0" value="{{ old('min_points', $tier?->min_points ?? 0) }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Tier Color</label>
                        <input type="color" name="color" class="form-control form-control-color" value="{{ old('color', $tier?->color ?? '#6c757d') }}">
                    </div>
                </div>

                <div class="m-section-divider mt-2">Benefits</div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Discount Percentage <span class="text-danger">*</span></label>
                        <input type="number" name="discount_percentage" class="form-control" step="0.01" min="0" max="100" value="{{ old('discount_percentage', $tier?->discount_percentage ?? 0) }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Points Multiplier <span class="text-danger">*</span></label>
                        <input type="number" name="points_multiplier" class="form-control" step="0.01" min="0.1" max="10" value="{{ old('points_multiplier', $tier?->points_multiplier ?? 1.00) }}" required>
                    </div>
                </div>

                <div class="m-section-divider mt-2">Additional</div>
                <div class="row">
                    <div class="col-md-8 mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description', $tier?->description) }}</textarea>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="active" {{ old('status', $tier?->status ?? 'active') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $tier?->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="m-form-actions mt-3">
                    <button type="submit" class="btn btn-primary"><i class="bx bx-check me-1"></i> {{ isset($tier) ? 'Update' : 'Create' }} Tier</button>
                    <a href="{{ route('admin.crm.loyalty.tiers.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
