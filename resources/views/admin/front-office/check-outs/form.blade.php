@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-log-out"></i></div>
            <div>
                <h4 class="m-page-title">{{ isset($checkOut) ? 'Edit Check Out' : 'Check Out Guest' }}</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Front Office</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="{{ route('admin.front-office.check-outs.index') }}">Check Out</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">{{ isset($checkOut) ? 'Edit' : 'Create' }}</li>
                </ul>
            </div>
        </div>
        <a href="{{ route('admin.front-office.check-outs.index') }}" class="btn btn-outline-secondary"><i class="bx bx-left-arrow-alt me-1"></i> Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ isset($checkOut) ? route('admin.front-office.check-outs.update', $checkOut) : route('admin.front-office.check-outs.store') }}" method="POST">
                @csrf
                @if(isset($checkOut)) @method('PUT') @endif

                <div class="m-section-divider">Reservation & Room</div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Reservation <span class="text-danger">*</span></label>
                        <select name="reservation_id" class="form-select" required>
                            <option value="">Select Reservation</option>
                            @foreach($reservations as $reservation)
                            <option value="{{ $reservation->id }}" data-total="{{ $reservation->total_amount }}" data-paid="{{ $reservation->paid_amount }}" {{ old('reservation_id', $checkOut?->reservation_id) == $reservation->id ? 'selected' : '' }}>
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
                            <option value="{{ $hotel->id }}" {{ old('hotel_id', $checkOut?->hotel_id) == $hotel->id ? 'selected' : '' }}>{{ $hotel->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Room</label>
                        <select name="room_id" class="form-select">
                            <option value="">Select Room</option>
                            @foreach($rooms as $room)
                            <option value="{{ $room->id }}" {{ old('room_id', $checkOut?->room_id) == $room->id ? 'selected' : '' }}>{{ $room->room_number }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="m-section-divider mt-2">Billing</div>
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Total Charges <span class="text-danger">*</span></label>
                        <input type="number" name="total_charges" class="form-control" step="0.01" min="0" value="{{ old('total_charges', $checkOut?->total_charges ?? '0') }}" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Total Payments <span class="text-danger">*</span></label>
                        <input type="number" name="total_payments" class="form-control" step="0.01" min="0" value="{{ old('total_payments', $checkOut?->total_payments ?? '0') }}" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Damage Charges</label>
                        <input type="number" name="damage_charges" class="form-control" step="0.01" min="0" value="{{ old('damage_charges', $checkOut?->damage_charges ?? '0') }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Final Bill Amount <span class="text-danger">*</span></label>
                        <input type="number" name="final_bill_amount" class="form-control" step="0.01" min="0" value="{{ old('final_bill_amount', $checkOut?->final_bill_amount ?? '0') }}" required>
                    </div>
                </div>

                <div class="m-section-divider mt-2">Room Condition & Feedback</div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Room Condition</label>
                        <select name="room_condition" class="form-select">
                            <option value="">Select Condition</option>
                            <option value="good" {{ old('room_condition', $checkOut?->room_condition) == 'good' ? 'selected' : '' }}>Good</option>
                            <option value="damaged" {{ old('room_condition', $checkOut?->room_condition) == 'damaged' ? 'selected' : '' }}>Damaged</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Damage Notes</label>
                        <textarea name="damage_notes" class="form-control" rows="2">{{ old('damage_notes', $checkOut?->damage_notes) }}</textarea>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Feedback Rating</label>
                        <select name="feedback_rating" class="form-select">
                            <option value="">Select Rating</option>
                            @for($i = 1; $i <= 5; $i++)
                            <option value="{{ $i }}" {{ old('feedback_rating', $checkOut?->feedback_rating) == $i ? 'selected' : '' }}>{{ $i }} Star{{ $i > 1 ? 's' : '' }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Feedback Notes</label>
                        <textarea name="feedback_notes" class="form-control" rows="2">{{ old('feedback_notes', $checkOut?->feedback_notes) }}</textarea>
                    </div>
                </div>

                <div class="m-form-actions mt-3">
                    <button type="submit" class="btn btn-primary"><i class="bx bx-check me-1"></i> {{ isset($checkOut) ? 'Update' : 'Check Out' }}</button>
                    <a href="{{ route('admin.front-office.check-outs.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
