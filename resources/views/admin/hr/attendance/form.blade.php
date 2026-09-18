@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-calendar-check"></i></div>
            <div>
                <h4 class="m-page-title">{{ isset($attendance) ? 'Edit Attendance' : 'Create Attendance' }}</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">HR</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="{{ route('admin.hr.attendance.index') }}">Attendance</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">{{ isset($attendance) ? 'Edit' : 'Create' }}</li>
                </ul>
            </div>
        </div>
        <a href="{{ route('admin.hr.attendance.index') }}" class="btn btn-outline-secondary"><i class="bx bx-left-arrow-alt me-1"></i> Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ isset($attendance) ? route('admin.hr.attendance.update', $attendance) : route('admin.hr.attendance.store') }}" method="POST">
                @csrf
                @if(isset($attendance)) @method('PUT') @endif

                <div class="m-section-divider">Attendance Details</div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Employee <span class="text-danger">*</span></label>
                        <select name="employee_id" class="form-select" required>
                            <option value="">Select Employee</option>
                            @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" {{ old('employee_id', $attendance?->employee_id) == $emp->id ? 'selected' : '' }}>{{ $emp->first_name }} {{ $emp->last_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Date <span class="text-danger">*</span></label>
                        <input type="date" name="date" class="form-control" value="{{ old('date', $attendance?->date?->format('Y-m-d')) }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select" required>
                            <option value="present" {{ old('status', $attendance?->status) == 'present' ? 'selected' : '' }}>Present</option>
                            <option value="absent" {{ old('status', $attendance?->status) == 'absent' ? 'selected' : '' }}>Absent</option>
                            <option value="half_day" {{ old('status', $attendance?->status) == 'half_day' ? 'selected' : '' }}>Half Day</option>
                            <option value="late" {{ old('status', $attendance?->status) == 'late' ? 'selected' : '' }}>Late</option>
                            <option value="leave" {{ old('status', $attendance?->status) == 'leave' ? 'selected' : '' }}>Leave</option>
                            <option value="holiday" {{ old('status', $attendance?->status) == 'holiday' ? 'selected' : '' }}>Holiday</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Check In</label>
                        <input type="time" name="check_in" class="form-control" value="{{ old('check_in', $attendance?->check_in) }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Check Out</label>
                        <input type="time" name="check_out" class="form-control" value="{{ old('check_out', $attendance?->check_out) }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Hours Worked</label>
                        <input type="number" step="0.01" name="hours_worked" class="form-control" value="{{ old('hours_worked', $attendance?->hours_worked) }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Overtime Hours</label>
                        <input type="number" step="0.01" name="overtime_hours" class="form-control" value="{{ old('overtime_hours', $attendance?->overtime_hours) }}">
                    </div>
                    <div class="col-md-8 mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="3">{{ old('notes', $attendance?->notes) }}</textarea>
                    </div>
                </div>

                <div class="m-form-actions mt-3">
                    <button type="submit" class="btn btn-primary"><i class="bx bx-check me-1"></i> {{ isset($attendance) ? 'Update' : 'Create' }} Attendance</button>
                    <a href="{{ route('admin.hr.attendance.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection