@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-group"></i></div>
            <div>
                <h4 class="m-page-title">{{ isset($reservation) ? 'Edit Group Booking' : 'Create Group Booking' }}</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Reservations</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="{{ route('admin.reservation.group-bookings.index') }}">Group Bookings</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">{{ isset($reservation) ? 'Edit' : 'Create' }}</li>
                </ul>
            </div>
        </div>
        <a href="{{ route('admin.reservation.group-bookings.index') }}" class="btn btn-outline-secondary"><i class="bx bx-left-arrow-alt me-1"></i> Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ isset($reservation) ? route('admin.reservation.group-bookings.update', $reservation) : route('admin.reservation.group-bookings.store') }}" method="POST">
                @csrf
                @if(isset($reservation)) @method('PUT') @endif

                <div class="m-section-divider">Group Details</div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Group Name <span class="text-danger">*</span></label>
                        <input type="text" name="group_name" class="form-control" value="{{ old('group_name', $reservation?->group_name) }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Corporate Name</label>
                        <input type="text" name="corporate_name" class="form-control" value="{{ old('corporate_name', $reservation?->corporate_name) }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Hotel <span class="text-danger">*</span></label>
                        <select name="hotel_id" class="form-select" required>
                            <option value="">Select Hotel</option>
                            @foreach($hotels as $hotel)
                            <option value="{{ $hotel->id }}" {{ old('hotel_id', $reservation?->hotel_id) == $hotel->id ? 'selected' : '' }}>{{ $hotel->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Contact Person (Guest) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <select name="guest_id" class="form-select" id="guest-select" required>
                                <option value="">Select Guest</option>
                                @foreach($guests as $guest)
                                <option value="{{ $guest->id }}" {{ old('guest_id', $reservation?->guest_id) == $guest->id ? 'selected' : '' }}>{{ $guest->full_name }}</option>
                                @endforeach
                            </select>
                            <button type="button" class="btn btn-primary" id="btn-quick-add-guest" title="Quick Add Guest"><i class="bx bx-plus"></i></button>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Booking Source</label>
                        <select name="booking_source" class="form-select">
                            <option value="walk-in" {{ old('booking_source', $reservation?->booking_source ?? 'walk-in') == 'walk-in' ? 'selected' : '' }}>Walk-in</option>
                            <option value="online" {{ old('booking_source', $reservation?->booking_source) == 'online' ? 'selected' : '' }}>Online</option>
                            <option value="ota" {{ old('booking_source', $reservation?->booking_source) == 'ota' ? 'selected' : '' }}>OTA</option>
                        </select>
                    </div>
                </div>

                <div class="m-section-divider mt-2">Stay Details</div>
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Check-in Date <span class="text-danger">*</span></label>
                        <input type="date" name="check_in_date" class="form-control" value="{{ old('check_in_date', $reservation?->check_in_date?->format('Y-m-d')) }}" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Check-out Date <span class="text-danger">*</span></label>
                        <input type="date" name="check_out_date" class="form-control" value="{{ old('check_out_date', $reservation?->check_out_date?->format('Y-m-d')) }}" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Adults <span class="text-danger">*</span></label>
                        <input type="number" name="adults" class="form-control" value="{{ old('adults', $reservation?->adults ?? 1) }}" min="1" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Children</label>
                        <input type="number" name="children" class="form-control" value="{{ old('children', $reservation?->children ?? 0) }}" min="0">
                    </div>
                </div>

                <div class="m-section-divider mt-2">Room Assignments</div>
                <div id="room-entries">
                    @if(isset($reservation) && $reservation->rooms->count() > 0)
                        @foreach($reservation->rooms as $index => $rr)
                        <div class="row room-entry mb-2" data-index="{{ $index }}">
                            <div class="col-md-4">
                                <label class="form-label">Room</label>
                                <select name="room_ids[]" class="form-select room-select">
                                    <option value="">Select Room</option>
                                    @foreach($rooms as $room)
                                    <option value="{{ $room->id }}" data-type="{{ $room->room_type_id }}" data-rate="{{ $room->roomType->base_rate ?? 0 }}" {{ $rr->room_id == $room->id ? 'selected' : '' }}>{{ $room->room_number }} - {{ $room->roomType->name ?? '' }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Room Type</label>
                                <select name="room_type_ids[]" class="form-select room-type-select">
                                    <option value="">Select Type</option>
                                    @foreach($roomTypes as $roomType)
                                    <option value="{{ $roomType->id }}" data-rate="{{ $roomType->base_rate }}" {{ $rr->room_type_id == $roomType->id ? 'selected' : '' }}>{{ $roomType->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Rate/Night</label>
                                <input type="number" name="rates[]" class="form-control rate-input" value="{{ $rr->rate_per_night }}" step="0.01" min="0">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="button" class="btn btn-outline-danger btn-sm remove-room"><i class="bx bx-trash"></i> Remove</button>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="row room-entry mb-2" data-index="0">
                            <div class="col-md-4">
                                <label class="form-label">Room</label>
                                <select name="room_ids[]" class="form-select room-select">
                                    <option value="">Select Room</option>
                                    @foreach($rooms as $room)
                                    <option value="{{ $room->id }}" data-type="{{ $room->room_type_id }}" data-rate="{{ $room->roomType->base_rate ?? 0 }}">{{ $room->room_number }} - {{ $room->roomType->name ?? '' }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Room Type</label>
                                <select name="room_type_ids[]" class="form-select room-type-select">
                                    <option value="">Select Type</option>
                                    @foreach($roomTypes as $roomType)
                                    <option value="{{ $roomType->id }}" data-rate="{{ $roomType->base_rate }}">{{ $roomType->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Rate/Night</label>
                                <input type="number" name="rates[]" class="form-control rate-input" value="0" step="0.01" min="0">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="button" class="btn btn-outline-danger btn-sm remove-room"><i class="bx bx-trash"></i> Remove</button>
                            </div>
                        </div>
                    @endif
                </div>
                <button type="button" id="add-room" class="btn btn-outline-primary btn-sm mt-2"><i class="bx bx-plus"></i> Add Room</button>

                <div class="m-section-divider mt-3">Additional</div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="pending" {{ old('status', $reservation?->status ?? 'pending') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="confirmed" {{ old('status', $reservation?->status) == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                        </select>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="3">{{ old('notes', $reservation?->notes) }}</textarea>
                    </div>
                </div>

                <div class="m-form-actions mt-3">
                    <button type="submit" class="btn btn-primary"><i class="bx bx-check me-1"></i> {{ isset($reservation) ? 'Update' : 'Create' }} Group Booking</button>
                    <a href="{{ route('admin.reservation.group-bookings.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Quick Add Guest Modal -->
<div class="modal fade" id="quickAddGuestModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bx bx-user-plus me-1"></i> Quick Add Guest</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="quick-guest-form">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">First Name <span class="text-danger">*</span></label>
                            <input type="text" name="first_name" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Last Name</label>
                            <input type="text" name="last_name" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nationality</label>
                            <input type="text" name="nationality" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Company Name</label>
                            <input type="text" name="company_name" class="form-control">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="btn-save-guest"><i class="bx bx-check me-1"></i> Save Guest</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
$(document).ready(function() {
    var roomIndex = {{ isset($reservation) ? $reservation->rooms->count() : 1 }};

    $('#add-room').on('click', function() {
        var template = `
        <div class="row room-entry mb-2" data-index="${roomIndex}">
            <div class="col-md-4">
                <label class="form-label">Room</label>
                <select name="room_ids[]" class="form-select room-select">
                    <option value="">Select Room</option>
                    @foreach($rooms as $room)
                    <option value="{{ $room->id }}" data-type="{{ $room->room_type_id }}" data-rate="{{ $room->roomType->base_rate ?? 0 }}">{{ $room->room_number }} - {{ $room->roomType->name ?? '' }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Room Type</label>
                <select name="room_type_ids[]" class="form-select room-type-select">
                    <option value="">Select Type</option>
                    @foreach($roomTypes as $roomType)
                    <option value="{{ $roomType->id }}" data-rate="{{ $roomType->base_rate }}">{{ $roomType->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Rate/Night</label>
                <input type="number" name="rates[]" class="form-control rate-input" value="0" step="0.01" min="0">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="button" class="btn btn-outline-danger btn-sm remove-room"><i class="bx bx-trash"></i> Remove</button>
            </div>
        </div>`;
        $('#room-entries').append(template);
        roomIndex++;
    });

    $(document).on('click', '.remove-room', function() {
        if ($('.room-entry').length > 1) {
            $(this).closest('.room-entry').remove();
        }
    });

    $(document).on('change', '.room-select', function() {
        var selected = $(this).find(':selected');
        var row = $(this).closest('.room-entry');
        var typeId = selected.data('type');
        var rate = selected.data('rate');
        if (typeId) row.find('.room-type-select').val(typeId);
        if (rate) row.find('.rate-input').val(rate);
    });

    $(document).on('change', '.room-type-select', function() {
        var rate = $(this).find(':selected').data('rate');
        if (rate) $(this).closest('.room-entry').find('.rate-input').val(rate);
    });

    $('#btn-quick-add-guest').on('click', function() {
        $('#quick-guest-form')[0].reset();
        $('#quickAddGuestModal').modal('show');
    });

    $('#btn-save-guest').on('click', function() {
        var $btn = $(this);
        var $form = $('#quick-guest-form');

        if (!$form.find('input[name="first_name"]').val().trim()) {
            Toast.fire({ icon: 'error', title: 'First name is required!' });
            return;
        }

        $.ajax({
            url: "{{ route('admin.reservation.guests.quick-store') }}",
            type: 'POST',
            data: $form.serialize(),
            beforeSend: function() {
                $btn.prop('disabled', true).html('<i class="bx bx-loader-alt bx-spin me-1"></i> Saving...');
            },
            success: function(res) {
                if (res.success && res.guest) {
                    var newOption = $('<option>', {
                        value: res.guest.id,
                        text: res.guest.full_name,
                        selected: true
                    });
                    $('#guest-select').append(newOption).trigger('change');
                    $('#quickAddGuestModal').modal('hide');
                    Toast.fire({ icon: 'success', title: res.message || 'Guest added!' });
                }
            },
            error: function(xhr) {
                var msg = xhr.responseJSON ? xhr.responseJSON.message : 'Something went wrong.';
                Toast.fire({ icon: 'error', title: msg });
            },
            complete: function() {
                $btn.prop('disabled', false).html('<i class="bx bx-check me-1"></i> Save Guest');
            }
        });
    });
});
</script>
@endsection


