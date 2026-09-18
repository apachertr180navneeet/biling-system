@extends('admin.layouts.app')

@section('style')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
<style>
.fc { font-family: 'Inter', sans-serif !important; }
.fc .fc-toolbar-title { font-size: 1.25rem !important; font-weight: 600 !important; }
.fc .fc-button { border-radius: 0.5rem !important; font-size: 0.8125rem !important; font-weight: 500 !important; }
.fc .fc-button-primary { background: var(--m-primary) !important; border-color: var(--m-primary) !important; }
.fc .fc-button-primary:hover { background: var(--m-primary-hover) !important; }
.fc .fc-daygrid-day-number { font-size: 0.8125rem !important; padding: 0.25rem 0.5rem !important; }
.fc .fc-event { border-radius: 0.25rem !important; font-size: 0.75rem !important; padding: 1px 4px !important; }
.fc .fc-col-header-cell-cushion { font-weight: 600 !important; font-size: 0.8125rem !important; }
.room-legend { display: inline-flex; align-items: center; gap: 0.375rem; margin-right: 1rem; font-size: 0.8125rem; }
.room-legend-dot { width: 10px; height: 10px; border-radius: 50%; }
</style>
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-calendar"></i></div>
            <div>
                <h4 class="m-page-title">Reservation Calendar</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Reservations</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">Calendar</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-3 mb-2">
                            <label class="form-label">Hotel</label>
                            <select id="filter-hotel" class="form-select">
                                <option value="">All Hotels</option>
                                @foreach($hotels as $hotel)
                                <option value="{{ $hotel->id }}">{{ $hotel->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-2">
                            <div class="mt-4">
                                <span class="room-legend"><span class="room-legend-dot" style="background:#f59e0b"></span> Pending</span>
                                <span class="room-legend"><span class="room-legend-dot" style="background:#3b82f6"></span> Confirmed</span>
                                <span class="room-legend"><span class="room-legend-dot" style="background:#10b981"></span> Checked-in</span>
                                <span class="room-legend"><span class="room-legend-dot" style="background:#6b7280"></span> Checked-out</span>
                                <span class="room-legend"><span class="room-legend-dot" style="background:#ef4444"></span> No-show</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div id="calendar"></div>
        </div>
    </div>

    <!-- Reservation Detail Modal -->
    <div class="modal fade" id="reservationModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Reservation Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="reservationModalBody">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek'
        },
        events: function(info, successCallback, failureCallback) {
            var hotelId = document.getElementById('filter-hotel').value;
            var url = "{{ route('admin.reservation.calendar.events') }}" +
                "?start=" + info.startStr +
                "&end=" + info.endStr +
                (hotelId ? "&hotel_id=" + hotelId : "");

            fetch(url)
                .then(function(response) { return response.json(); })
                .then(function(data) { successCallback(data); })
                .catch(function(error) { failureCallback(error); });
        },
        eventClick: function(info) {
            var props = info.event.extendedProps;
            var html = '<table class="table table-borderless mb-0">';
            html += '<tr><td class="fw-semibold" style="width:140px">Reservation #</td><td>' + (props.reservation_number || '') + '</td></tr>';
            html += '<tr><td class="fw-semibold">Guest</td><td>' + (props.guest_name || '') + '</td></tr>';
            html += '<tr><td class="fw-semibold">Hotel</td><td>' + (props.hotel_name || '') + '</td></tr>';
            html += '<tr><td class="fw-semibold">Source</td><td>' + (props.booking_source || '') + '</td></tr>';
            html += '<tr><td class="fw-semibold">Status</td><td><span class="badge bg-label-' + (props.status === 'checked-in' ? 'success' : props.status === 'confirmed' ? 'info' : props.status === 'pending' ? 'warning' : 'secondary') + '">' + (props.status || '') + '</span></td></tr>';
            html += '<tr><td class="fw-semibold">Guests</td><td>' + (props.adults || 0) + ' Adults, ' + (props.children || 0) + ' Children</td></tr>';
            html += '<tr><td class="fw-semibold">Amount</td><td>' + parseFloat(props.total_amount || 0).toFixed(2) + '</td></tr>';
            if (props.rooms && props.rooms.length > 0) {
                html += '<tr><td class="fw-semibold">Rooms</td><td>';
                props.rooms.forEach(function(r) {
                    html += '<span class="badge bg-label-primary me-1">' + r.room_number + ' (' + r.room_type + ')</span>';
                });
                html += '</td></tr>';
            }
            html += '</table>';
            document.getElementById('reservationModalBody').innerHTML = html;
            new bootstrap.Modal(document.getElementById('reservationModal')).show();
        },
        eventDidMount: function(info) {
            info.el.title = info.event.title;
        }
    });
    calendar.render();

    document.getElementById('filter-hotel').addEventListener('change', function() {
        calendar.refetchEvents();
    });
});
</script>
@endsection

