@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-user"></i></div>
            <div>
                <h4 class="m-page-title">{{ isset($guest) ? 'Edit Guest' : 'Create Guest' }}</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Reservation Setup</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="{{ route('admin.reservation.guests.index') }}">Guests</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">{{ isset($guest) ? 'Edit' : 'Create' }}</li>
                </ul>
            </div>
        </div>
        <a href="{{ route('admin.reservation.guests.index') }}" class="btn btn-outline-secondary"><i class="bx bx-left-arrow-alt me-1"></i> Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ isset($guest) ? route('admin.reservation.guests.update', $guest) : route('admin.reservation.guests.store') }}" method="POST">
                @csrf
                @if(isset($guest)) @method('PUT') @endif

                <div class="m-section-divider">Personal Information</div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">First Name <span class="text-danger">*</span></label>
                        <input type="text" name="first_name" class="form-control" value="{{ old('first_name', $guest?->first_name) }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Last Name</label>
                        <input type="text" name="last_name" class="form-control" value="{{ old('last_name', $guest?->last_name) }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $guest?->email) }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $guest?->phone) }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Nationality</label>
                        <input type="text" name="nationality" class="form-control" value="{{ old('nationality', $guest?->nationality) }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Company Name</label>
                        <input type="text" name="company_name" class="form-control" value="{{ old('company_name', $guest?->company_name) }}">
                    </div>
                </div>

                <div class="m-section-divider mt-2">Identity</div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">ID Type</label>
                        <select name="id_type" class="form-select">
                            <option value="">Select ID Type</option>
                            <option value="passport" {{ old('id_type', $guest?->id_type) == 'passport' ? 'selected' : '' }}>Passport</option>
                            <option value="license" {{ old('id_type', $guest?->id_type) == 'license' ? 'selected' : '' }}>License</option>
                            <option value="aadhar" {{ old('id_type', $guest?->id_type) == 'aadhar' ? 'selected' : '' }}>Aadhar</option>
                            <option value="other" {{ old('id_type', $guest?->id_type) == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">ID Number</label>
                        <input type="text" name="id_number" class="form-control" value="{{ old('id_number', $guest?->id_number) }}">
                    </div>
                </div>

                <div class="m-section-divider mt-2">Address</div>
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Address</label>
                        <textarea name="address" class="form-control" rows="2">{{ old('address', $guest?->address) }}</textarea>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">City</label>
                        <input type="text" name="city" class="form-control" value="{{ old('city', $guest?->city) }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">State</label>
                        <input type="text" name="state" class="form-control" value="{{ old('state', $guest?->state) }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Country</label>
                        <input type="text" name="country" class="form-control" value="{{ old('country', $guest?->country) }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Zipcode</label>
                        <input type="text" name="zipcode" class="form-control" value="{{ old('zipcode', $guest?->zipcode) }}">
                    </div>
                </div>

                <div class="m-section-divider mt-2">Additional</div>
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="3">{{ old('notes', $guest?->notes) }}</textarea>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="active" {{ old('status', $guest?->status ?? 'active') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $guest?->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="m-form-actions mt-3">
                    <button type="submit" class="btn btn-primary"><i class="bx bx-check me-1"></i> {{ isset($guest) ? 'Update' : 'Create' }} Guest</button>
                    <a href="{{ route('admin.reservation.guests.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection


