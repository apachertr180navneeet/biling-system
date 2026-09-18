@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-log-in"></i></div>
            <div>
                <h4 class="m-page-title">{{ isset($checkIn) ? 'Edit Check In' : 'Check In Guest' }}</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Front Office</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="{{ route('admin.front-office.check-ins.index') }}">Check In</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">{{ isset($checkIn) ? 'Edit' : 'Create' }}</li>
                </ul>
            </div>
        </div>
        <a href="{{ route('admin.front-office.check-ins.index') }}" class="btn btn-outline-secondary"><i class="bx bx-left-arrow-alt me-1"></i> Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ isset($checkIn) ? route('admin.front-office.check-ins.update', $checkIn) : route('admin.front-office.check-ins.store') }}" method="POST">
                @csrf
                @if(isset($checkIn)) @method('PUT') @endif

                <div class="m-section-divider">Reservation & Room</div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Reservation <span class="text-danger">*</span></label>
                        <select name="reservation_id" class="form-select" required>
                            <option value="">Select Reservation</option>
                            @foreach($reservations as $reservation)
                            <option value="{{ $reservation->id }}" {{ old('reservation_id', $checkIn?->reservation_id) == $reservation->id ? 'selected' : '' }}>
                                {{ $reservation->reservation_number }} - {{ $reservation->guest->full_name }} ({{ $reservation->hotel->name }})
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Hotel <span class="text-danger">*</span></label>
                        <select name="hotel_id" class="form-select" required>
                            <option value="">Select Hotel</option>
                            @foreach($hotels as $hotel)
                            <option value="{{ $hotel->id }}" {{ old('hotel_id', $checkIn?->hotel_id) == $hotel->id ? 'selected' : '' }}>{{ $hotel->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Room</label>
                        <select name="room_id" class="form-select">
                            <option value="">Select Room</option>
                            @foreach($rooms as $room)
                            <option value="{{ $room->id }}" {{ old('room_id', $checkIn?->room_id) == $room->id ? 'selected' : '' }}>{{ $room->room_number }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="m-section-divider mt-2">ID Verification</div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">ID Document Type</label>
                        <select name="id_document_type" class="form-select">
                            <option value="">Select Type</option>
                            <option value="passport" {{ old('id_document_type', $checkIn?->id_document_type) == 'passport' ? 'selected' : '' }}>Passport</option>
                            <option value="drivers_license" {{ old('id_document_type', $checkIn?->id_document_type) == 'drivers_license' ? 'selected' : '' }}>Driver's License</option>
                            <option value="national_id" {{ old('id_document_type', $checkIn?->id_document_type) == 'national_id' ? 'selected' : '' }}>National ID</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">ID Document Number</label>
                        <input type="text" name="id_document_number" class="form-control" value="{{ old('id_document_number', $checkIn?->id_document_number) }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">ID Expiry Date</label>
                        <input type="date" name="id_document_expiry" class="form-control" value="{{ old('id_document_expiry', $checkIn?->id_document_expiry?->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Key Card Numbers</label>
                        <input type="text" name="key_card_numbers" class="form-control" value="{{ old('key_card_numbers', $checkIn?->key_card_numbers) }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">ID Verified</label>
                        <select name="id_verified" class="form-select">
                            <option value="0" {{ old('id_verified', $checkIn?->id_verified ?? 0) == 0 ? 'selected' : '' }}>No</option>
                            <option value="1" {{ old('id_verified', $checkIn?->id_verified ?? 0) == 1 ? 'selected' : '' }}>Yes</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Arrival Time</label>
                        <input type="datetime-local" name="arrival_time" class="form-control" value="{{ old('arrival_time', $checkIn?->arrival_time?->format('Y-m-d\TH:i')) }}">
                    </div>
                </div>

                <div class="m-section-divider mt-2">Additional Details</div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Special Requests</label>
                        <textarea name="special_requests" class="form-control" rows="2">{{ old('special_requests', $checkIn?->special_requests) }}</textarea>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="2">{{ old('notes', $checkIn?->notes) }}</textarea>
                    </div>
                </div>

                <div class="m-form-actions mt-3">
                    <button type="submit" class="btn btn-primary"><i class="bx bx-check me-1"></i> {{ isset($checkIn) ? 'Update' : 'Check In' }}</button>
                    <a href="{{ route('admin.front-office.check-ins.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
