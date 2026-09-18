@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-door-open"></i></div>
            <div>
                <h4 class="m-page-title">{{ isset($room) ? 'Edit Room' : 'Create Room' }}</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Property</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="{{ route('admin.property.rooms.index') }}">Rooms</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">{{ isset($room) ? 'Edit' : 'Create' }}</li>
                </ul>
            </div>
        </div>
        <a href="{{ route('admin.property.rooms.index') }}" class="btn btn-outline-secondary"><i class="bx bx-left-arrow-alt me-1"></i> Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ isset($room) ? route('admin.property.rooms.update', $room) : route('admin.property.rooms.store') }}" method="POST">
                @csrf
                @if(isset($room)) @method('PUT') @endif

                <div class="m-section-divider">Room Details</div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Hotel <span class="text-danger">*</span></label>
                        <select name="hotel_id" class="form-select" required>
                            <option value="">Select Hotel</option>
                            @foreach($hotels as $hotel)
                            <option value="{{ $hotel->id }}" {{ old('hotel_id', $room?->hotel_id) == $hotel->id ? 'selected' : '' }}>{{ $hotel->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Building <span class="text-danger">*</span></label>
                        <select name="building_id" class="form-select" required>
                            <option value="">Select Building</option>
                            @foreach($buildings as $building)
                            <option value="{{ $building->id }}" {{ old('building_id', $room?->building_id) == $building->id ? 'selected' : '' }}>{{ $building->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Floor <span class="text-danger">*</span></label>
                        <select name="floor_id" class="form-select" required>
                            <option value="">Select Floor</option>
                            @foreach($floors as $floor)
                            <option value="{{ $floor->id }}" {{ old('floor_id', $room?->floor_id) == $floor->id ? 'selected' : '' }}>{{ $floor->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Wing <span class="text-danger">*</span></label>
                        <select name="wing_id" class="form-select" required>
                            <option value="">Select Wing</option>
                            @foreach($wings as $wing)
                            <option value="{{ $wing->id }}" {{ old('wing_id', $room?->wing_id) == $wing->id ? 'selected' : '' }}>{{ $wing->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Room Number <span class="text-danger">*</span></label>
                        <input type="text" name="room_number" class="form-control" value="{{ old('room_number', $room?->room_number) }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Floor Label</label>
                        <input type="text" name="floor_label" class="form-control" value="{{ old('floor_label', $room?->floor_label) }}">
                    </div>
                </div>

                <div class="m-section-divider mt-2">Room Configuration</div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Room Type <span class="text-danger">*</span></label>
                        <select name="room_type_id" class="form-select" required>
                            <option value="">Select Room Type</option>
                            @foreach($roomTypes as $roomType)
                            <option value="{{ $roomType->id }}" {{ old('room_type_id', $room?->room_type_id) == $roomType->id ? 'selected' : '' }}>{{ $roomType->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Bed Type <span class="text-danger">*</span></label>
                        <select name="bed_type_id" class="form-select" required>
                            <option value="">Select Bed Type</option>
                            @foreach($bedTypes as $bedType)
                            <option value="{{ $bedType->id }}" {{ old('bed_type_id', $room?->bed_type_id) == $bedType->id ? 'selected' : '' }}>{{ $bedType->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Room Status</label>
                        <select name="room_status_id" class="form-select">
                            <option value="">Select Status</option>
                            @foreach($roomStatuses as $roomStatus)
                            <option value="{{ $roomStatus->id }}" {{ old('room_status_id', $room?->room_status_id) == $roomStatus->id ? 'selected' : '' }}>{{ $roomStatus->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="m-section-divider mt-2">Additional</div>
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description', $room?->description) }}</textarea>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="active" {{ old('status', $room?->status ?? 'active') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $room?->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="m-form-actions mt-3">
                    <button type="submit" class="btn btn-primary"><i class="bx bx-check me-1"></i> {{ isset($room) ? 'Update' : 'Create' }} Room</button>
                    <a href="{{ route('admin.property.rooms.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

