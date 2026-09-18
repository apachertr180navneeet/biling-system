@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-file"></i></div>
            <div>
                <h4 class="m-page-title">{{ isset($amc) ? 'Edit AMC' : 'Create AMC' }}</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Maintenance</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="{{ route('admin.maintenance.amcs.index') }}">AMC</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">{{ isset($amc) ? 'Edit' : 'Create' }}</li>
                </ul>
            </div>
        </div>
        <a href="{{ route('admin.maintenance.amcs.index') }}" class="btn btn-outline-secondary"><i class="bx bx-left-arrow-alt me-1"></i> Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ isset($amc) ? route('admin.maintenance.amcs.update', $amc) : route('admin.maintenance.amcs.store') }}" method="POST">
                @csrf
                @if(isset($amc)) @method('PUT') @endif

                <div class="m-section-divider">Contract Details</div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Asset <span class="text-danger">*</span></label>
                        <select name="asset_id" class="form-select" required>
                            <option value="">Select Asset</option>
                            @foreach($assets as $a)
                            <option value="{{ $a->id }}" {{ old('asset_id', $amc?->asset_id) == $a->id ? 'selected' : '' }}>{{ $a->asset_code }} - {{ $a->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Vendor</label>
                        <select name="vendor_id" class="form-select">
                            <option value="">Select Vendor</option>
                            @foreach($vendors as $v)
                            <option value="{{ $v->id }}" {{ old('vendor_id', $amc?->vendor_id) == $v->id ? 'selected' : '' }}>{{ $v->company_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select" required>
                            @foreach(['active' => 'Active', 'expired' => 'Expired', 'cancelled' => 'Cancelled'] as $val => $lbl)
                            <option value="{{ $val }}" {{ old('status', $amc?->status ?? 'active') == $val ? 'selected' : '' }}>{{ $lbl }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="m-section-divider">Duration & Cost</div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Start Date <span class="text-danger">*</span></label>
                        <input type="date" name="start_date" class="form-control" value="{{ old('start_date', $amc?->start_date?->format('Y-m-d')) }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">End Date <span class="text-danger">*</span></label>
                        <input type="date" name="end_date" class="form-control" value="{{ old('end_date', $amc?->end_date?->format('Y-m-d')) }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Cost (₹) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="cost" class="form-control" value="{{ old('cost', $amc?->cost) }}" required>
                    </div>
                </div>

                <div class="m-section-divider">Contact Information</div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Contact Person</label>
                        <input type="text" name="contact_person" class="form-control" value="{{ old('contact_person', $amc?->contact_person) }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Contact Phone</label>
                        <input type="text" name="contact_phone" class="form-control" value="{{ old('contact_phone', $amc?->contact_phone) }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description', $amc?->description) }}</textarea>
                    </div>
                </div>

                <div class="m-form-actions mt-3">
                    <button type="submit" class="btn btn-primary"><i class="bx bx-check me-1"></i> {{ isset($amc) ? 'Update' : 'Create' }} AMC</button>
                    <a href="{{ route('admin.maintenance.amcs.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
