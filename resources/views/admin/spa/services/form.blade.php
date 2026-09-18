@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-spa"></i></div>
            <div>
                <h4 class="m-page-title">{{ isset($service) ? 'Edit Spa Service' : 'Create Spa Service' }}</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Spa</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="{{ route('admin.spa.services.index') }}">Services</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">{{ isset($service) ? 'Edit' : 'Create' }}</li>
                </ul>
            </div>
        </div>
        <a href="{{ route('admin.spa.services.index') }}" class="btn btn-outline-secondary"><i class="bx bx-left-arrow-alt me-1"></i> Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ isset($service) ? route('admin.spa.services.update', $service) : route('admin.spa.services.store') }}" method="POST">
                @csrf
                @if(isset($service)) @method('PUT') @endif

                <div class="m-section-divider">Basic Information</div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Hotel <span class="text-danger">*</span></label>
                        <select name="hotel_id" class="form-select" required>
                            <option value="">Select Hotel</option>
                            @foreach($hotels as $hotel)
                            <option value="{{ $hotel->id }}" {{ old('hotel_id', $service?->hotel_id) == $hotel->id ? 'selected' : '' }}>{{ $hotel->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Service Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $service?->name) }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Category</label>
                        <input type="text" name="category" class="form-control" value="{{ old('category', $service?->category) }}" placeholder="e.g. Massage, Facial, Body Treatment">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="2">{{ old('description', $service?->description) }}</textarea>
                    </div>
                </div>

                <div class="m-section-divider">Pricing & Duration</div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Duration (minutes) <span class="text-danger">*</span></label>
                        <input type="number" name="duration_minutes" class="form-control" value="{{ old('duration_minutes', $service?->duration_minutes ?? 60) }}" required min="1">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Price <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="price" class="form-control" value="{{ old('price', $service?->price ?? 0) }}" required min="0">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Gender</label>
                        <select name="gender" class="form-select">
                            <option value="unisex" {{ old('gender', $service?->gender ?? 'unisex') == 'unisex' ? 'selected' : '' }}>Unisex</option>
                            <option value="male" {{ old('gender', $service?->gender) == 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender', $service?->gender) == 'female' ? 'selected' : '' }}>Female</option>
                        </select>
                    </div>
                </div>

                <div class="m-section-divider">Status</div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select" required>
                            <option value="active" {{ old('status', $service?->status ?? 'active') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $service?->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="m-form-actions mt-3">
                    <button type="submit" class="btn btn-primary"><i class="bx bx-check me-1"></i> {{ isset($service) ? 'Update' : 'Create' }} Service</button>
                    <a href="{{ route('admin.spa.services.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
