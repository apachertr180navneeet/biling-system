@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-star"></i></div>
            <div>
                <h4 class="m-page-title">{{ isset($member) ? 'Edit Loyalty Member' : 'Enroll Loyalty Member' }}</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Guest CRM</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="{{ route('admin.crm.loyalty.index') }}">Loyalty</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">{{ isset($member) ? 'Edit' : 'Enroll' }}</li>
                </ul>
            </div>
        </div>
        <a href="{{ route('admin.crm.loyalty.index') }}" class="btn btn-outline-secondary"><i class="bx bx-left-arrow-alt me-1"></i> Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ isset($member) ? route('admin.crm.loyalty.update', $member) : route('admin.crm.loyalty.store') }}" method="POST">
                @csrf
                @if(isset($member)) @method('PUT') @endif

                <div class="m-section-divider">Member Details</div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Guest <span class="text-danger">*</span></label>
                        <select name="guest_id" class="form-select" required>
                            <option value="">Select Guest</option>
                            @foreach($guests as $guest)
                            <option value="{{ $guest->id }}" {{ old('guest_id', $member?->guest_id) == $guest->id ? 'selected' : '' }}>{{ $guest->full_name }} ({{ $guest->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Loyalty Tier <span class="text-danger">*</span></label>
                        <select name="loyalty_tier_id" class="form-select" required>
                            <option value="">Select Tier</option>
                            @foreach($tiers as $tier)
                            <option value="{{ $tier->id }}" {{ old('loyalty_tier_id', $member?->loyalty_tier_id) == $tier->id ? 'selected' : '' }}>{{ $tier->name }} (Min: {{ number_format($tier->min_points) }} pts)</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                @if(isset($member))
                <div class="m-section-divider mt-2">Member Stats</div>
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Member Number</label>
                        <input type="text" class="form-control" value="{{ $member->member_number }}" readonly>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Total Points</label>
                        <input type="text" class="form-control" value="{{ number_format($member->total_points) }}" readonly>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Total Stays</label>
                        <input type="text" class="form-control" value="{{ $member->total_stays }}" readonly>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Total Spent</label>
                        <input type="text" class="form-control" value="{{ number_format($member->total_spent, 2) }}" readonly>
                    </div>
                </div>
                @endif

                <div class="m-form-actions mt-3">
                    <button type="submit" class="btn btn-primary"><i class="bx bx-check me-1"></i> {{ isset($member) ? 'Update' : 'Enroll' }} Member</button>
                    <a href="{{ route('admin.crm.loyalty.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
