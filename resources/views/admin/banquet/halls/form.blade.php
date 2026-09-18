@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-building"></i></div>
            <div>
                <h4 class="m-page-title">{{ isset($hall) ? 'Edit Hall' : 'Create Hall' }}</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Banquet</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="{{ route('admin.banquet.halls.index') }}">Halls</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">{{ isset($hall) ? 'Edit' : 'Create' }}</li>
                </ul>
            </div>
        </div>
        <a href="{{ route('admin.banquet.halls.index') }}" class="btn btn-outline-secondary"><i class="bx bx-left-arrow-alt me-1"></i> Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ isset($hall) ? route('admin.banquet.halls.update', $hall) : route('admin.banquet.halls.store') }}" method="POST">
                @csrf
                @if(isset($hall)) @method('PUT') @endif

                <div class="m-section-divider">Basic Information</div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Hotel <span class="text-danger">*</span></label>
                        <select name="hotel_id" class="form-select" required>
                            <option value="">Select Hotel</option>
                            @foreach($hotels as $hotel)
                            <option value="{{ $hotel->id }}" {{ old('hotel_id', $hall?->hotel_id) == $hotel->id ? 'selected' : '' }}>{{ $hotel->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Hall Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $hall?->name) }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Floor</label>
                        <input type="text" name="floor" class="form-control" value="{{ old('floor', $hall?->floor) }}">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="2">{{ old('description', $hall?->description) }}</textarea>
                    </div>
                </div>

                <div class="m-section-divider">Capacity & Pricing</div>
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Capacity (persons) <span class="text-danger">*</span></label>
                        <input type="number" name="capacity" class="form-control" value="{{ old('capacity', $hall?->capacity) }}" required min="1">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Area (sqft)</label>
                        <input type="number" step="0.01" name="area_sqft" class="form-control" value="{{ old('area_sqft', $hall?->area_sqft) }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Base Price <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="base_price" class="form-control" value="{{ old('base_price', $hall?->base_price) }}" required min="0">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Price Unit <span class="text-danger">*</span></label>
                        <select name="price_unit" class="form-select" required>
                            <option value="per_event" {{ old('price_unit', $hall?->price_unit ?? 'per_event') == 'per_event' ? 'selected' : '' }}>Per Event</option>
                            <option value="per_hour" {{ old('price_unit', $hall?->price_unit) == 'per_hour' ? 'selected' : '' }}>Per Hour</option>
                            <option value="per_day" {{ old('price_unit', $hall?->price_unit) == 'per_day' ? 'selected' : '' }}>Per Day</option>
                        </select>
                    </div>
                </div>

                <div class="m-section-divider">Facilities</div>
                <div class="row">
                    <div class="col-md-2 mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_ac" value="1" id="isAc" {{ old('is_ac', $hall?->is_ac) ? 'checked' : '' }}>
                            <label class="form-check-label" for="isAc">AC</label>
                        </div>
                    </div>
                    <div class="col-md-2 mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="has_projector" value="1" id="hasProjector" {{ old('has_projector', $hall?->has_projector) ? 'checked' : '' }}>
                            <label class="form-check-label" for="hasProjector">Projector</label>
                        </div>
                    </div>
                    <div class="col-md-2 mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="has_stage" value="1" id="hasStage" {{ old('has_stage', $hall?->has_stage) ? 'checked' : '' }}>
                            <label class="form-check-label" for="hasStage">Stage</label>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="has_sound_system" value="1" id="hasSound" {{ old('has_sound_system', $hall?->has_sound_system) ? 'checked' : '' }}>
                            <label class="form-check-label" for="hasSound">Sound System</label>
                        </div>
                    </div>
                    <div class="col-md-2 mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="has_parking" value="1" id="hasParking" {{ old('has_parking', $hall?->has_parking) ? 'checked' : '' }}>
                            <label class="form-check-label" for="hasParking">Parking</label>
                        </div>
                    </div>
                </div>

                <div class="m-section-divider">Amenities</div>
                <div class="row">
                    @foreach($amenities as $amenity)
                    <div class="col-md-3 mb-2">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="amenities[]" value="{{ $amenity->id }}" id="amenity{{ $amenity->id }}" {{ in_array($amenity->id, old('amenities', $hallAmenityIds ?? [])) ? 'checked' : '' }}>
                            <label class="form-check-label" for="amenity{{ $amenity->id }}">{{ $amenity->name }}</label>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="m-section-divider">Status</div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select" required>
                            <option value="active" {{ old('status', $hall?->status ?? 'active') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $hall?->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="maintenance" {{ old('status', $hall?->status) == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                        </select>
                    </div>
                </div>

                <div class="m-form-actions mt-3">
                    <button type="submit" class="btn btn-primary"><i class="bx bx-check me-1"></i> {{ isset($hall) ? 'Update' : 'Create' }} Hall</button>
                    <a href="{{ route('admin.banquet.halls.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
