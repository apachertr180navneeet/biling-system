@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-door-open"></i></div>
            <div>
                <h4 class="m-page-title">{{ isset($roomType) ? 'Edit Room Type' : 'Create Room Type' }}</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Room Master</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="{{ route('admin.property.room-types.index') }}">Room Types</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">{{ isset($roomType) ? 'Edit' : 'Create' }}</li>
                </ul>
            </div>
        </div>
        <a href="{{ route('admin.property.room-types.index') }}" class="btn btn-outline-secondary"><i class="bx bx-left-arrow-alt me-1"></i> Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ isset($roomType) ? route('admin.property.room-types.update', $roomType) : route('admin.property.room-types.store') }}" method="POST">
                @csrf
                @if(isset($roomType)) @method('PUT') @endif

                <div class="m-section-divider">Room Type Details</div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $roomType?->name) }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Base Rate <span class="text-danger">*</span></label>
                        <input type="number" name="base_rate" class="form-control" value="{{ old('base_rate', $roomType?->base_rate ?? 0) }}" step="0.01" min="0" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Max Occupancy <span class="text-danger">*</span></label>
                        <input type="number" name="max_occupancy" class="form-control" value="{{ old('max_occupancy', $roomType?->max_occupancy ?? 1) }}" min="1" required>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description', $roomType?->description) }}</textarea>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="active" {{ old('status', $roomType?->status ?? 'active') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $roomType?->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="m-form-actions mt-3">
                    <button type="submit" class="btn btn-primary"><i class="bx bx-check me-1"></i> {{ isset($roomType) ? 'Update' : 'Create' }} Room Type</button>
                    <a href="{{ route('admin.property.room-types.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
