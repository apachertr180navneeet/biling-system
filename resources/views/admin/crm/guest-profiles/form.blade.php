@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-user"></i></div>
            <div>
                <h4 class="m-page-title">{{ isset($guestProfile) ? 'Edit Guest Profile' : 'Create Guest Profile' }}</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Guest CRM</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="{{ route('admin.crm.guest-profiles.index') }}">Guest Profiles</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">{{ isset($guestProfile) ? 'Edit' : 'Create' }}</li>
                </ul>
            </div>
        </div>
        <a href="{{ route('admin.crm.guest-profiles.index') }}" class="btn btn-outline-secondary"><i class="bx bx-left-arrow-alt me-1"></i> Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ isset($guestProfile) ? route('admin.crm.guest-profiles.update', $guestProfile) : route('admin.crm.guest-profiles.store') }}" method="POST">
                @csrf
                @if(isset($guestProfile)) @method('PUT') @endif

                <div class="m-section-divider">Guest Selection</div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Guest <span class="text-danger">*</span></label>
                        <select name="guest_id" class="form-select" required>
                            <option value="">Select Guest</option>
                            @foreach($guests as $guest)
                            <option value="{{ $guest->id }}" {{ old('guest_id', $guestProfile?->guest_id) == $guest->id ? 'selected' : '' }}>{{ $guest->full_name }} ({{ $guest->email }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="m-section-divider mt-2">Personal Details</div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Date of Birth</label>
                        <input type="date" name="date_of_birth" class="form-control" value="{{ old('date_of_birth', $guestProfile?->date_of_birth?->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Gender</label>
                        <select name="gender" class="form-select">
                            <option value="">Select Gender</option>
                            <option value="male" {{ old('gender', $guestProfile?->gender) == 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender', $guestProfile?->gender) == 'female' ? 'selected' : '' }}>Female</option>
                            <option value="other" {{ old('gender', $guestProfile?->gender) == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Occupation</label>
                        <input type="text" name="occupation" class="form-control" value="{{ old('occupation', $guestProfile?->occupation) }}">
                    </div>
                </div>

                <div class="m-section-divider mt-2">Identity Verification (KYC)</div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">ID Type</label>
                        <select name="id_type" class="form-select">
                            <option value="">Select ID Type</option>
                            <option value="passport" {{ old('id_type', $guestProfile?->id_type) == 'passport' ? 'selected' : '' }}>Passport</option>
                            <option value="drivers_license" {{ old('id_type', $guestProfile?->id_type) == 'drivers_license' ? 'selected' : '' }}>Driver's License</option>
                            <option value="national_id" {{ old('id_type', $guestProfile?->id_type) == 'national_id' ? 'selected' : '' }}>National ID</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">ID Number</label>
                        <input type="text" name="id_number" class="form-control" value="{{ old('id_number', $guestProfile?->id_number) }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">ID Expiry Date</label>
                        <input type="date" name="id_expiry_date" class="form-control" value="{{ old('id_expiry_date', $guestProfile?->id_expiry_date?->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Address Proof Type</label>
                        <select name="address_proof_type" class="form-select">
                            <option value="">Select Type</option>
                            <option value="utility_bill" {{ old('address_proof_type', $guestProfile?->address_proof_type) == 'utility_bill' ? 'selected' : '' }}>Utility Bill</option>
                            <option value="bank_statement" {{ old('address_proof_type', $guestProfile?->address_proof_type) == 'bank_statement' ? 'selected' : '' }}>Bank Statement</option>
                            <option value="rental_agreement" {{ old('address_proof_type', $guestProfile?->address_proof_type) == 'rental_agreement' ? 'selected' : '' }}>Rental Agreement</option>
                        </select>
                    </div>
                </div>

                <div class="m-section-divider mt-2">Preferences</div>
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Dietary Preference</label>
                        <select name="dietary_preference" class="form-select">
                            <option value="">None</option>
                            <option value="vegetarian" {{ old('dietary_preference', $guestProfile?->dietary_preference) == 'vegetarian' ? 'selected' : '' }}>Vegetarian</option>
                            <option value="vegan" {{ old('dietary_preference', $guestProfile?->dietary_preference) == 'vegan' ? 'selected' : '' }}>Vegan</option>
                            <option value="halal" {{ old('dietary_preference', $guestProfile?->dietary_preference) == 'halal' ? 'selected' : '' }}>Halal</option>
                            <option value="kosher" {{ old('dietary_preference', $guestProfile?->dietary_preference) == 'kosher' ? 'selected' : '' }}>Kosher</option>
                            <option value="gluten_free" {{ old('dietary_preference', $guestProfile?->dietary_preference) == 'gluten_free' ? 'selected' : '' }}>Gluten Free</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Room Preference</label>
                        <select name="room_preference" class="form-select">
                            <option value="">None</option>
                            <option value="non_smoking" {{ old('room_preference', $guestProfile?->room_preference) == 'non_smoking' ? 'selected' : '' }}>Non-Smoking</option>
                            <option value="smoking" {{ old('room_preference', $guestProfile?->room_preference) == 'smoking' ? 'selected' : '' }}>Smoking</option>
                            <option value="high_floor" {{ old('room_preference', $guestProfile?->room_preference) == 'high_floor' ? 'selected' : '' }}>High Floor</option>
                            <option value="low_floor" {{ old('room_preference', $guestProfile?->room_preference) == 'low_floor' ? 'selected' : '' }}>Low Floor</option>
                            <option value="city_view" {{ old('room_preference', $guestProfile?->room_preference) == 'city_view' ? 'selected' : '' }}>City View</option>
                            <option value="sea_view" {{ old('room_preference', $guestProfile?->room_preference) == 'sea_view' ? 'selected' : '' }}>Sea View</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Bed Preference</label>
                        <select name="bed_preference" class="form-select">
                            <option value="">None</option>
                            <option value="single" {{ old('bed_preference', $guestProfile?->bed_preference) == 'single' ? 'selected' : '' }}>Single</option>
                            <option value="twin" {{ old('bed_preference', $guestProfile?->bed_preference) == 'twin' ? 'selected' : '' }}>Twin</option>
                            <option value="king" {{ old('bed_preference', $guestProfile?->bed_preference) == 'king' ? 'selected' : '' }}>King</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Pillow Preference</label>
                        <select name="pillow_preference" class="form-select">
                            <option value="">None</option>
                            <option value="soft" {{ old('pillow_preference', $guestProfile?->pillow_preference) == 'soft' ? 'selected' : '' }}>Soft</option>
                            <option value="firm" {{ old('pillow_preference', $guestProfile?->pillow_preference) == 'firm' ? 'selected' : '' }}>Firm</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Arrival Preference</label>
                        <select name="arrival_preference" class="form-select">
                            <option value="">Standard</option>
                            <option value="early_checkin" {{ old('arrival_preference', $guestProfile?->arrival_preference) == 'early_checkin' ? 'selected' : '' }}>Early Check-in</option>
                            <option value="late_checkin" {{ old('arrival_preference', $guestProfile?->arrival_preference) == 'late_checkin' ? 'selected' : '' }}>Late Check-in</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Communication Preference</label>
                        <select name="communication_preference" class="form-select">
                            <option value="">None</option>
                            <option value="email" {{ old('communication_preference', $guestProfile?->communication_preference) == 'email' ? 'selected' : '' }}>Email</option>
                            <option value="phone" {{ old('communication_preference', $guestProfile?->communication_preference) == 'phone' ? 'selected' : '' }}>Phone</option>
                            <option value="sms" {{ old('communication_preference', $guestProfile?->communication_preference) == 'sms' ? 'selected' : '' }}>SMS</option>
                            <option value="whatsapp" {{ old('communication_preference', $guestProfile?->communication_preference) == 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                        </select>
                    </div>
                </div>

                <div class="m-section-divider mt-2">VIP & Notes</div>
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label">VIP Status</label>
                        <select name="vip_status" class="form-select">
                            <option value="0" {{ old('vip_status', $guestProfile?->vip_status ?? 0) == 0 ? 'selected' : '' }}>No</option>
                            <option value="1" {{ old('vip_status', $guestProfile?->vip_status ?? 0) == 1 ? 'selected' : '' }}>Yes</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">VIP Level</label>
                        <select name="vip_level" class="form-select">
                            <option value="">None</option>
                            <option value="gold" {{ old('vip_level', $guestProfile?->vip_level) == 'gold' ? 'selected' : '' }}>Gold</option>
                            <option value="platinum" {{ old('vip_level', $guestProfile?->vip_level) == 'platinum' ? 'selected' : '' }}>Platinum</option>
                            <option value="diamond" {{ old('vip_level', $guestProfile?->vip_level) == 'diamond' ? 'selected' : '' }}>Diamond</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="active" {{ old('status', $guestProfile?->status ?? 'active') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $guestProfile?->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Special Notes</label>
                        <textarea name="special_notes" class="form-control" rows="3">{{ old('special_notes', $guestProfile?->special_notes) }}</textarea>
                    </div>
                </div>

                <div class="m-form-actions mt-3">
                    <button type="submit" class="btn btn-primary"><i class="bx bx-check me-1"></i> {{ isset($guestProfile) ? 'Update' : 'Create' }} Profile</button>
                    <a href="{{ route('admin.crm.guest-profiles.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
