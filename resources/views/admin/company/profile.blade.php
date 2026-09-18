@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-buildings"></i></div>
            <div>
                <h4 class="m-page-title">Company Profile</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Company Setup</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">Profile</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.company.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="m-section-divider">General Information</div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Company Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $company?->name) }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $company?->email) }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $company?->phone) }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Website</label>
                        <input type="url" name="website" class="form-control" value="{{ old('website', $company?->website) }}">
                    </div>
                </div>

                <div class="m-section-divider mt-2">Location</div>
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Address</label>
                        <textarea name="address" class="form-control" rows="2">{{ old('address', $company?->address) }}</textarea>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">City</label>
                        <input type="text" name="city" class="form-control" value="{{ old('city', $company?->city) }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">State</label>
                        <input type="text" name="state" class="form-control" value="{{ old('state', $company?->state) }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Country</label>
                        <input type="text" name="country" class="form-control" value="{{ old('country', $company?->country) }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Zipcode</label>
                        <input type="text" name="zipcode" class="form-control" value="{{ old('zipcode', $company?->zipcode) }}">
                    </div>
                </div>

                <div class="m-section-divider mt-2">Configuration</div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Default Currency</label>
                        <select name="currency_id" class="form-select">
                            <option value="">Select Currency</option>
                            @foreach($currencies as $currency)
                            <option value="{{ $currency->id }}" {{ old('currency_id', $company?->currency_id) == $currency->id ? 'selected' : '' }}>{{ $currency->code }} - {{ $currency->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Timezone</label>
                        <input type="text" name="timezone" class="form-control" value="{{ old('timezone', $company?->timezone ?? 'Australia/Sydney') }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Company Logo</label>
                        @if($company?->logo)
                        <div class="mb-2">
                            <img src="{{ asset($company->logo) }}" alt="Logo" class="rounded border p-1" height="60">
                        </div>
                        @endif
                        <input type="file" name="logo" class="form-control" accept="image/*">
                    </div>
                </div>

                <div class="m-section-divider mt-2">GST Information</div>
                <div class="row">
                    <div class="col-md-2 mb-3">
                        <div class="form-check form-switch mt-4">
                            <input type="checkbox" name="is_gst_registered" class="form-check-input" value="1" id="isGstRegistered" {{ old('is_gst_registered', $company?->is_gst_registered) ? 'checked' : '' }}>
                            <label class="form-check-label" for="isGstRegistered">GST Registered</label>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">GSTIN</label>
                        <input type="text" name="gstin" class="form-control" placeholder="22AAAAA0000A1Z5" maxlength="15" value="{{ old('gstin', $company?->gstin) }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">PAN</label>
                        <input type="text" name="pan" class="form-control" placeholder="AAAAA0000A" maxlength="10" value="{{ old('pan', $company?->pan) }}">
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label">State Code</label>
                        <input type="text" name="state_code" class="form-control" placeholder="27" maxlength="2" value="{{ old('state_code', $company?->state_code) }}">
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label">State Name</label>
                        <input type="text" name="state_name" class="form-control" placeholder="Maharashtra" value="{{ old('state_name', $company?->state_name) }}">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-2 mb-3">
                        <div class="form-check form-switch">
                            <input type="checkbox" name="is_einvoice_enabled" class="form-check-input" value="1" id="isEinvoiceEnabled" {{ old('is_einvoice_enabled', $company?->is_einvoice_enabled) ? 'checked' : '' }}>
                            <label class="form-check-label" for="isEinvoiceEnabled">Enable e-Invoice</label>
                        </div>
                    </div>
                </div>

                <div class="m-form-actions mt-3">
                    <button type="submit" class="btn btn-primary"><i class="bx bx-check me-1"></i> Save Changes</button>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
