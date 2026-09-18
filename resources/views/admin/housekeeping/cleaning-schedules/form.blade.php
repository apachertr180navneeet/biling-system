@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-spray-can"></i></div>
            <div>
                <h4 class="m-page-title">{{ isset($cleaningSchedule) ? 'Edit Cleaning Schedule' : 'Create Cleaning Schedule' }}</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Housekeeping</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="{{ route('admin.housekeeping.cleaning-schedules.index') }}">Cleaning Schedules</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">{{ isset($cleaningSchedule) ? 'Edit' : 'Create' }}</li>
                </ul>
            </div>
        </div>
        <a href="{{ route('admin.housekeeping.cleaning-schedules.index') }}" class="btn btn-outline-secondary"><i class="bx bx-left-arrow-alt me-1"></i> Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ isset($cleaningSchedule) ? route('admin.housekeeping.cleaning-schedules.update', $cleaningSchedule) : route('admin.housekeeping.cleaning-schedules.store') }}" method="POST">
                @csrf
                @if(isset($cleaningSchedule)) @method('PUT') @endif

                <div class="m-section-divider">Cleaning Schedule Details</div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Hotel <span class="text-danger">*</span></label>
                        <select name="hotel_id" class="form-select" required>
                            <option value="">Select Hotel</option>
                            @foreach($hotels as $hotel)
                            <option value="{{ $hotel->id }}" {{ old('hotel_id', $cleaningSchedule?->hotel_id) == $hotel->id ? 'selected' : '' }}>{{ $hotel->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Room <span class="text-danger">*</span></label>
                        <select name="room_id" class="form-select" required>
                            <option value="">Select Room</option>
                            @foreach($rooms as $room)
                            <option value="{{ $room->id }}" {{ old('room_id', $cleaningSchedule?->room_id) == $room->id ? 'selected' : '' }}>{{ $room->room_number }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Cleaning Type <span class="text-danger">*</span></label>
                        <select name="cleaning_type" class="form-select" required>
                            <option value="stay_over" {{ old('cleaning_type', $cleaningSchedule?->cleaning_type) == 'stay_over' ? 'selected' : '' }}>Stay Over</option>
                            <option value="checkout" {{ old('cleaning_type', $cleaningSchedule?->cleaning_type) == 'checkout' ? 'selected' : '' }}>Checkout</option>
                            <option value="deep_cleaning" {{ old('cleaning_type', $cleaningSchedule?->cleaning_type) == 'deep_cleaning' ? 'selected' : '' }}>Deep Cleaning</option>
                            <option value="turndown" {{ old('cleaning_type', $cleaningSchedule?->cleaning_type) == 'turndown' ? 'selected' : '' }}>Turndown</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Priority <span class="text-danger">*</span></label>
                        <select name="priority" class="form-select" required>
                            <option value="medium" {{ old('priority', $cleaningSchedule?->priority ?? 'medium') == 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="high" {{ old('priority', $cleaningSchedule?->priority) == 'high' ? 'selected' : '' }}>High</option>
                            <option value="low" {{ old('priority', $cleaningSchedule?->priority) == 'low' ? 'selected' : '' }}>Low</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Scheduled Date <span class="text-danger">*</span></label>
                        <input type="date" name="scheduled_date" class="form-control" value="{{ old('scheduled_date', $cleaningSchedule?->scheduled_date?->format('Y-m-d') ?? date('Y-m-d')) }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Scheduled Time</label>
                        <input type="time" name="scheduled_time" class="form-control" value="{{ old('scheduled_time', $cleaningSchedule?->scheduled_time) }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Assigned To</label>
                        <select name="assigned_to" class="form-select">
                            <option value="">Unassigned</option>
                            @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('assigned_to', $cleaningSchedule?->assigned_to) == $user->id ? 'selected' : '' }}>{{ $user->first_name }} {{ $user->last_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="pending" {{ old('status', $cleaningSchedule?->status ?? 'pending') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="in_progress" {{ old('status', $cleaningSchedule?->status) == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="completed" {{ old('status', $cleaningSchedule?->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ old('status', $cleaningSchedule?->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="3">{{ old('notes', $cleaningSchedule?->notes) }}</textarea>
                    </div>
                </div>

                <div class="m-form-actions mt-3">
                    <button type="submit" class="btn btn-primary"><i class="bx bx-check me-1"></i> {{ isset($cleaningSchedule) ? 'Update' : 'Create' }} Schedule</button>
                    <a href="{{ route('admin.housekeeping.cleaning-schedules.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
