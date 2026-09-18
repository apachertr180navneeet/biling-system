@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-dollar"></i></div>
            <div>
                <h4 class="m-page-title">{{ isset($ratePlan) ? 'Edit Rate Plan' : 'Create Rate Plan' }}</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Reservation Setup</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="{{ route('admin.reservation.rate-plans.index') }}">Rate Plans</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">{{ isset($ratePlan) ? 'Edit' : 'Create' }}</li>
                </ul>
            </div>
        </div>
        <a href="{{ route('admin.reservation.rate-plans.index') }}" class="btn btn-outline-secondary"><i class="bx bx-left-arrow-alt me-1"></i> Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ isset($ratePlan) ? route('admin.reservation.rate-plans.update', $ratePlan) : route('admin.reservation.rate-plans.store') }}" method="POST">
                @csrf
                @if(isset($ratePlan)) @method('PUT') @endif

                <div class="m-section-divider">Rate Plan Details</div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Hotel <span class="text-danger">*</span></label>
                        <select name="hotel_id" class="form-select" required>
                            <option value="">Select Hotel</option>
                            @foreach($hotels as $hotel)
                            <option value="{{ $hotel->id }}" {{ old('hotel_id', $ratePlan?->hotel_id) == $hotel->id ? 'selected' : '' }}>{{ $hotel->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Room Type <span class="text-danger">*</span></label>
                        <select name="room_type_id" class="form-select" required>
                            <option value="">Select Room Type</option>
                            @foreach($roomTypes as $roomType)
                            <option value="{{ $roomType->id }}" {{ old('room_type_id', $ratePlan?->room_type_id) == $roomType->id ? 'selected' : '' }}>{{ $roomType->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Plan Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $ratePlan?->name) }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Rate Per Night <span class="text-danger">*</span></label>
                        <input type="number" name="rate_per_night" class="form-control" value="{{ old('rate_per_night', $ratePlan?->rate_per_night) }}" step="0.01" min="0" required>
                    </div>
                </div>

                <div class="m-section-divider mt-2">Validity</div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Effective From <span class="text-danger">*</span></label>
                        <input type="date" name="effective_from" class="form-control" value="{{ old('effective_from', $ratePlan?->effective_from?->format('Y-m-d')) }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Effective To <span class="text-danger">*</span></label>
                        <input type="date" name="effective_to" class="form-control" value="{{ old('effective_to', $ratePlan?->effective_to?->format('Y-m-d')) }}" required>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label">Min Stay</label>
                        <input type="number" name="min_stay" class="form-control" value="{{ old('min_stay', $ratePlan?->min_stay ?? 1) }}" min="1">
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label">Max Stay</label>
                        <input type="number" name="max_stay" class="form-control" value="{{ old('max_stay', $ratePlan?->max_stay ?? 30) }}" min="1">
                    </div>
                </div>

                <div class="m-section-divider mt-2">Additional</div>
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description', $ratePlan?->description) }}</textarea>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="active" {{ old('status', $ratePlan?->status ?? 'active') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $ratePlan?->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="m-form-actions mt-3">
                    <button type="submit" class="btn btn-primary"><i class="bx bx-check me-1"></i> {{ isset($ratePlan) ? 'Update' : 'Create' }} Rate Plan</button>
                    <a href="{{ route('admin.reservation.rate-plans.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection


