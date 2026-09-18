@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-building-house"></i></div>
            <div>
                <h4 class="m-page-title">{{ isset($hotel) ? 'Edit Hotel' : 'Create Hotel' }}</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Property</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="{{ route('admin.property.hotels.index') }}">Hotels</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">{{ isset($hotel) ? 'Edit' : 'Create' }}</li>
                </ul>
            </div>
        </div>
        <a href="{{ route('admin.property.hotels.index') }}" class="btn btn-outline-secondary"><i class="bx bx-left-arrow-alt me-1"></i> Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ isset($hotel) ? route('admin.property.hotels.update', $hotel) : route('admin.property.hotels.store') }}" method="POST">
                @csrf
                @if(isset($hotel)) @method('PUT') @endif

                <div class="m-section-divider">Hotel Details</div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Company <span class="text-danger">*</span></label>
                        <select name="company_id" class="form-select" required>
                            <option value="">Select Company</option>
                            @foreach($companies as $company)
                            <option value="{{ $company->id }}" {{ old('company_id', $hotel?->company_id) == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $hotel?->name) }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $hotel?->email) }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $hotel?->phone) }}">
                    </div>
                </div>

                <div class="m-section-divider mt-2">Location</div>
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Address</label>
                        <textarea name="address" class="form-control" rows="2">{{ old('address', $hotel?->address) }}</textarea>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">City</label>
                        <input type="text" name="city" class="form-control" value="{{ old('city', $hotel?->city) }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">State</label>
                        <input type="text" name="state" class="form-control" value="{{ old('state', $hotel?->state) }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Country</label>
                        <input type="text" name="country" class="form-control" value="{{ old('country', $hotel?->country) }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Zipcode</label>
                        <input type="text" name="zipcode" class="form-control" value="{{ old('zipcode', $hotel?->zipcode) }}">
                    </div>
                </div>

                <div class="m-section-divider mt-2">Additional</div>
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description', $hotel?->description) }}</textarea>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="active" {{ old('status', $hotel?->status ?? 'active') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $hotel?->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="m-form-actions mt-3">
                    <button type="submit" class="btn btn-primary"><i class="bx bx-check me-1"></i> {{ isset($hotel) ? 'Update' : 'Create' }} Hotel</button>
                    <a href="{{ route('admin.property.hotels.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
