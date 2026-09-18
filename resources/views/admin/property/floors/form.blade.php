@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-layer"></i></div>
            <div>
                <h4 class="m-page-title">{{ isset($floor) ? 'Edit Floor' : 'Create Floor' }}</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Property</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="{{ route('admin.property.floors.index') }}">Floors</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">{{ isset($floor) ? 'Edit' : 'Create' }}</li>
                </ul>
            </div>
        </div>
        <a href="{{ route('admin.property.floors.index') }}" class="btn btn-outline-secondary"><i class="bx bx-left-arrow-alt me-1"></i> Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ isset($floor) ? route('admin.property.floors.update', $floor) : route('admin.property.floors.store') }}" method="POST">
                @csrf
                @if(isset($floor)) @method('PUT') @endif

                <div class="m-section-divider">Floor Details</div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Building <span class="text-danger">*</span></label>
                        <select name="building_id" class="form-select" required>
                            <option value="">Select Building</option>
                            @foreach($buildings as $building)
                            <option value="{{ $building->id }}" {{ old('building_id', $floor?->building_id) == $building->id ? 'selected' : '' }}>{{ $building->hotel_name }} - {{ $building->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $floor?->name) }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Floor Number <span class="text-danger">*</span></label>
                        <input type="number" name="floor_number" class="form-control" value="{{ old('floor_number', $floor?->floor_number) }}" required>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description', $floor?->description) }}</textarea>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="active" {{ old('status', $floor?->status ?? 'active') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $floor?->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="m-form-actions mt-3">
                    <button type="submit" class="btn btn-primary"><i class="bx bx-check me-1"></i> {{ isset($floor) ? 'Update' : 'Create' }} Floor</button>
                    <a href="{{ route('admin.property.floors.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
