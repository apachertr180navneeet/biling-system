@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-car"></i></div>
            <div>
                <h4 class="m-page-title">{{ isset($type) ? 'Edit Transport Type' : 'Create Transport Type' }}</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Travel Desk</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="{{ route('admin.travel-desk.types.index') }}">Transport Types</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">{{ isset($type) ? 'Edit' : 'Create' }}</li>
                </ul>
            </div>
        </div>
        <a href="{{ route('admin.travel-desk.types.index') }}" class="btn btn-outline-secondary"><i class="bx bx-left-arrow-alt me-1"></i> Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ isset($type) ? route('admin.travel-desk.types.update', $type) : route('admin.travel-desk.types.store') }}" method="POST">
                @csrf
                @if(isset($type)) @method('PUT') @endif

                <div class="m-section-divider">Basic Information</div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Type Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $type?->name) }}" required placeholder="e.g. Sedan, SUV, Tempo Traveller">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Max Passengers <span class="text-danger">*</span></label>
                        <input type="number" name="max_passengers" class="form-control" value="{{ old('max_passengers', $type?->max_passengers ?? 4) }}" required min="1">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select" required>
                            <option value="active" {{ old('status', $type?->status ?? 'active') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $type?->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="2">{{ old('description', $type?->description) }}</textarea>
                    </div>
                </div>

                <div class="m-section-divider">Pricing</div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Base Price <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="base_price" class="form-control" value="{{ old('base_price', $type?->base_price ?? 0) }}" required min="0">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Per KM Rate</label>
                        <input type="number" step="0.01" name="per_km_rate" class="form-control" value="{{ old('per_km_rate', $type?->per_km_rate ?? 0) }}" min="0">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Per Hour Rate</label>
                        <input type="number" step="0.01" name="per_hour_rate" class="form-control" value="{{ old('per_hour_rate', $type?->per_hour_rate ?? 0) }}" min="0">
                    </div>
                </div>

                <div class="m-form-actions mt-3">
                    <button type="submit" class="btn btn-primary"><i class="bx bx-check me-1"></i> {{ isset($type) ? 'Update' : 'Create' }} Type</button>
                    <a href="{{ route('admin.travel-desk.types.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
