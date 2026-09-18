@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-moon"></i></div>
            <div>
                <h4 class="m-page-title">{{ isset($nightAudit) ? 'Edit Night Audit' : 'Run Night Audit' }}</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Front Office</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="{{ route('admin.front-office.night-audits.index') }}">Night Audit</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">{{ isset($nightAudit) ? 'Edit' : 'Run' }}</li>
                </ul>
            </div>
        </div>
        <a href="{{ route('admin.front-office.night-audits.index') }}" class="btn btn-outline-secondary"><i class="bx bx-left-arrow-alt me-1"></i> Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ isset($nightAudit) ? route('admin.front-office.night-audits.update', $nightAudit) : route('admin.front-office.night-audits.store') }}" method="POST">
                @csrf
                @if(isset($nightAudit)) @method('PUT') @endif

                <div class="m-section-divider">Audit Details</div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Audit Date <span class="text-danger">*</span></label>
                        <input type="date" name="audit_date" class="form-control" value="{{ old('audit_date', $nightAudit?->audit_date?->format('Y-m-d') ?? date('Y-m-d')) }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Hotel <span class="text-danger">*</span></label>
                        <select name="hotel_id" class="form-select" required>
                            <option value="">Select Hotel</option>
                            @foreach($hotels as $hotel)
                            <option value="{{ $hotel->id }}" {{ old('hotel_id', $nightAudit?->hotel_id) == $hotel->id ? 'selected' : '' }}>{{ $hotel->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                @if(isset($nightAudit))
                <div class="m-section-divider mt-2">Audit Summary</div>
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Rooms Occupied</label>
                        <input type="text" class="form-control" value="{{ $nightAudit->total_rooms_occupied }}" readonly>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Rooms Available</label>
                        <input type="text" class="form-control" value="{{ $nightAudit->total_rooms_available }}" readonly>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Total Revenue</label>
                        <input type="text" class="form-control" value="{{ number_format($nightAudit->total_revenue, 2) }}" readonly>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Payments Received</label>
                        <input type="text" class="form-control" value="{{ number_format($nightAudit->total_payments_received, 2) }}" readonly>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Outstanding</label>
                        <input type="text" class="form-control text-danger" value="{{ number_format($nightAudit->total_outstanding, 2) }}" readonly>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="pending" {{ old('status', $nightAudit->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="completed" {{ old('status', $nightAudit->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ old('status', $nightAudit->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                </div>
                @endif

                <div class="m-section-divider mt-2">Notes</div>
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <textarea name="notes" class="form-control" rows="3" placeholder="Audit notes...">{{ old('notes', $nightAudit?->notes) }}</textarea>
                    </div>
                </div>

                <div class="m-form-actions mt-3">
                    <button type="submit" class="btn btn-primary"><i class="bx bx-check me-1"></i> {{ isset($nightAudit) ? 'Update' : 'Run Night Audit' }}</button>
                    <a href="{{ route('admin.front-office.night-audits.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
