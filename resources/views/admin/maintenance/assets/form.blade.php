@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-wrench"></i></div>
            <div>
                <h4 class="m-page-title">{{ isset($asset) ? 'Edit Asset' : 'Create Asset' }}</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Maintenance</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="{{ route('admin.maintenance.assets.index') }}">Assets</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">{{ isset($asset) ? 'Edit' : 'Create' }}</li>
                </ul>
            </div>
        </div>
        <a href="{{ route('admin.maintenance.assets.index') }}" class="btn btn-outline-secondary"><i class="bx bx-left-arrow-alt me-1"></i> Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ isset($asset) ? route('admin.maintenance.assets.update', $asset) : route('admin.maintenance.assets.store') }}" method="POST">
                @csrf
                @if(isset($asset)) @method('PUT') @endif

                <div class="m-section-divider">Asset Details</div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Asset Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $asset?->name) }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Category <span class="text-danger">*</span></label>
                        <select name="category" class="form-select" required>
                            <option value="">Select Category</option>
                            @foreach(['Electrical', 'Plumbing', 'HVAC', 'Furniture', 'IT Hardware', 'Kitchen', 'Elevator', 'Generator', 'Fire Safety', 'Other'] as $cat)
                            <option value="{{ $cat }}" {{ old('category', $asset?->category) == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Location</label>
                        <input type="text" name="location" class="form-control" placeholder="e.g. Room 101, Lobby, Kitchen" value="{{ old('location', $asset?->location) }}">
                    </div>
                </div>

                <div class="m-section-divider">Identification</div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Brand</label>
                        <input type="text" name="brand" class="form-control" value="{{ old('brand', $asset?->brand) }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Model</label>
                        <input type="text" name="model" class="form-control" value="{{ old('model', $asset?->model) }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Serial Number</label>
                        <input type="text" name="serial_number" class="form-control" value="{{ old('serial_number', $asset?->serial_number) }}">
                    </div>
                </div>

                <div class="m-section-divider">Purchase & Warranty</div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Purchase Date</label>
                        <input type="date" name="purchase_date" class="form-control" value="{{ old('purchase_date', $asset?->purchase_date?->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Purchase Cost (₹)</label>
                        <input type="number" step="0.01" name="purchase_cost" class="form-control" value="{{ old('purchase_cost', $asset?->purchase_cost) }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Warranty Expiry</label>
                        <input type="date" name="warranty_expiry_date" class="form-control" value="{{ old('warranty_expiry_date', $asset?->warranty_expiry_date?->format('Y-m-d')) }}">
                    </div>
                </div>

                <div class="m-section-divider">Status & Notes</div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select" required>
                            @foreach(['active' => 'Active', 'inactive' => 'Inactive', 'under_maintenance' => 'Under Maintenance', 'disposed' => 'Disposed'] as $val => $lbl)
                            <option value="{{ $val }}" {{ old('status', $asset?->status ?? 'active') == $val ? 'selected' : '' }}>{{ $lbl }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-8 mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description', $asset?->description) }}</textarea>
                    </div>
                </div>

                <div class="m-form-actions mt-3">
                    <button type="submit" class="btn btn-primary"><i class="bx bx-check me-1"></i> {{ isset($asset) ? 'Update' : 'Create' }} Asset</button>
                    <a href="{{ route('admin.maintenance.assets.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
