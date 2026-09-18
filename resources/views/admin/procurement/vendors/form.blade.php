@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-user"></i></div>
            <div>
                <h4 class="m-page-title">{{ isset($vendor) ? 'Edit Vendor' : 'Create Vendor' }}</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Procurement</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="{{ route('admin.procurement.vendors.index') }}">Vendors</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">{{ isset($vendor) ? 'Edit' : 'Create' }}</li>
                </ul>
            </div>
        </div>
        <a href="{{ route('admin.procurement.vendors.index') }}" class="btn btn-outline-secondary"><i class="bx bx-left-arrow-alt me-1"></i> Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ isset($vendor) ? route('admin.procurement.vendors.update', $vendor) : route('admin.procurement.vendors.store') }}" method="POST">
                @csrf
                @if(isset($vendor)) @method('PUT') @endif

                <div class="m-section-divider">Basic Information</div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Hotel <span class="text-danger">*</span></label>
                        <select name="hotel_id" class="form-select" required>
                            <option value="">Select Hotel</option>
                            @foreach($hotels as $hotel)
                            <option value="{{ $hotel->id }}" {{ old('hotel_id', $vendor?->hotel_id) == $hotel->id ? 'selected' : '' }}>{{ $hotel->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Category</label>
                        <select name="vendor_category_id" class="form-select">
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('vendor_category_id', $vendor?->vendor_category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Vendor Type <span class="text-danger">*</span></label>
                        <select name="vendor_type" class="form-select" required>
                            <option value="material" {{ old('vendor_type', $vendor?->vendor_type) == 'material' ? 'selected' : '' }}>Material</option>
                            <option value="service" {{ old('vendor_type', $vendor?->vendor_type) == 'service' ? 'selected' : '' }}>Service</option>
                            <option value="both" {{ old('vendor_type', $vendor?->vendor_type) == 'both' ? 'selected' : '' }}>Both</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Company Name <span class="text-danger">*</span></label>
                        <input type="text" name="company_name" class="form-control" value="{{ old('company_name', $vendor?->company_name) }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Contact Person</label>
                        <input type="text" name="contact_person" class="form-control" value="{{ old('contact_person', $vendor?->contact_person) }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Rating</label>
                        <select name="rating" class="form-select">
                            <option value="">Select Rating</option>
                            <option value="excellent" {{ old('rating', $vendor?->rating) == 'excellent' ? 'selected' : '' }}>Excellent</option>
                            <option value="good" {{ old('rating', $vendor?->rating) == 'good' ? 'selected' : '' }}>Good</option>
                            <option value="average" {{ old('rating', $vendor?->rating) == 'average' ? 'selected' : '' }}>Average</option>
                            <option value="poor" {{ old('rating', $vendor?->rating) == 'poor' ? 'selected' : '' }}>Poor</option>
                        </select>
                    </div>
                </div>

                <div class="m-section-divider">Contact Details</div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $vendor?->email) }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $vendor?->phone) }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">City</label>
                        <input type="text" name="city" class="form-control" value="{{ old('city', $vendor?->city) }}">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Address</label>
                        <textarea name="address" class="form-control" rows="2">{{ old('address', $vendor?->address) }}</textarea>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">State</label>
                        <input type="text" name="state" class="form-control" value="{{ old('state', $vendor?->state) }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Pincode</label>
                        <input type="text" name="pincode" class="form-control" value="{{ old('pincode', $vendor?->pincode) }}">
                    </div>
                </div>

                <div class="m-section-divider">Tax & Compliance</div>
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label">GSTIN</label>
                        <input type="text" name="gstin" class="form-control" value="{{ old('gstin', $vendor?->gstin) }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">PAN</label>
                        <input type="text" name="pan" class="form-control" value="{{ old('pan', $vendor?->pan) }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">CIN</label>
                        <input type="text" name="cin" class="form-control" value="{{ old('cin', $vendor?->cin) }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">TAN</label>
                        <input type="text" name="tan" class="form-control" value="{{ old('tan', $vendor?->tan) }}">
                    </div>
                </div>

                <div class="m-section-divider">Bank Details</div>
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Bank Name</label>
                        <input type="text" name="bank_name" class="form-control" value="{{ old('bank_name', $vendor?->bank_name) }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Account Number</label>
                        <input type="text" name="bank_account_number" class="form-control" value="{{ old('bank_account_number', $vendor?->bank_account_number) }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">IFSC</label>
                        <input type="text" name="bank_ifsc" class="form-control" value="{{ old('bank_ifsc', $vendor?->bank_ifsc) }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Branch</label>
                        <input type="text" name="bank_branch" class="form-control" value="{{ old('bank_branch', $vendor?->bank_branch) }}">
                    </div>
                </div>

                <div class="m-section-divider">Terms & Agreement</div>
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Credit Limit</label>
                        <input type="number" step="0.01" name="credit_limit" class="form-control" value="{{ old('credit_limit', $vendor?->credit_limit ?? 0) }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Payment Terms</label>
                        <input type="text" name="payment_terms" class="form-control" value="{{ old('payment_terms', $vendor?->payment_terms) }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Agreement Start</label>
                        <input type="date" name="agreement_start_date" class="form-control" value="{{ old('agreement_start_date', $vendor?->agreement_start_date?->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Agreement End</label>
                        <input type="date" name="agreement_end_date" class="form-control" value="{{ old('agreement_end_date', $vendor?->agreement_end_date?->format('Y-m-d')) }}">
                    </div>
                </div>

                <div class="m-section-divider">Other</div>
                <div class="row">
                    <div class="col-md-9 mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="2">{{ old('notes', $vendor?->notes) }}</textarea>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select" required>
                            <option value="active" {{ old('status', $vendor?->status ?? 'active') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $vendor?->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="blacklisted" {{ old('status', $vendor?->status) == 'blacklisted' ? 'selected' : '' }}>Blacklisted</option>
                        </select>
                    </div>
                </div>

                <div class="m-form-actions mt-3">
                    <button type="submit" class="btn btn-primary"><i class="bx bx-check me-1"></i> {{ isset($vendor) ? 'Update' : 'Create' }} Vendor</button>
                    <a href="{{ route('admin.procurement.vendors.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
