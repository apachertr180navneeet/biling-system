@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-grid"></i></div>
            <div>
                <h4 class="m-page-title">{{ isset($restaurantTable) ? 'Edit Restaurant Table' : 'Create Restaurant Table' }}</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Restaurant POS</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="{{ route('admin.restaurant.tables.index') }}">Tables</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">{{ isset($restaurantTable) ? 'Edit' : 'Create' }}</li>
                </ul>
            </div>
        </div>
        <a href="{{ route('admin.restaurant.tables.index') }}" class="btn btn-outline-secondary"><i class="bx bx-left-arrow-alt me-1"></i> Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ isset($restaurantTable) ? route('admin.restaurant.tables.update', $restaurantTable) : route('admin.restaurant.tables.store') }}" method="POST">
                @csrf
                @if(isset($restaurantTable)) @method('PUT') @endif

                <div class="m-section-divider">Table Details</div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Hotel <span class="text-danger">*</span></label>
                        <select name="hotel_id" class="form-select" required>
                            <option value="">Select Hotel</option>
                            @foreach($hotels as $hotel)
                            <option value="{{ $hotel->id }}" {{ old('hotel_id', $restaurantTable?->hotel_id) == $hotel->id ? 'selected' : '' }}>{{ $hotel->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Table Number <span class="text-danger">*</span></label>
                        <input type="text" name="table_number" class="form-control" value="{{ old('table_number', $restaurantTable?->table_number) }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Capacity <span class="text-danger">*</span></label>
                        <input type="number" name="capacity" class="form-control" value="{{ old('capacity', $restaurantTable?->capacity ?? 4) }}" min="1" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Floor Number</label>
                        <input type="number" name="floor_number" class="form-control" value="{{ old('floor_number', $restaurantTable?->floor_number) }}" min="0">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Section</label>
                        <input type="text" name="section" class="form-control" value="{{ old('section', $restaurantTable?->section) }}" placeholder="e.g., Indoor, Outdoor, VIP">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Table Status <span class="text-danger">*</span></label>
                        <select name="table_status" class="form-select" required>
                            <option value="available" {{ old('table_status', $restaurantTable?->table_status ?? 'available') == 'available' ? 'selected' : '' }}>Available</option>
                            <option value="occupied" {{ old('table_status', $restaurantTable?->table_status) == 'occupied' ? 'selected' : '' }}>Occupied</option>
                            <option value="reserved" {{ old('table_status', $restaurantTable?->table_status) == 'reserved' ? 'selected' : '' }}>Reserved</option>
                            <option value="maintenance" {{ old('table_status', $restaurantTable?->table_status) == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="active" {{ old('status', $restaurantTable?->status ?? 'active') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $restaurantTable?->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="m-form-actions mt-3">
                    <button type="submit" class="btn btn-primary"><i class="bx bx-check me-1"></i> {{ isset($restaurantTable) ? 'Update' : 'Create' }} Table</button>
                    <a href="{{ route('admin.restaurant.tables.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
