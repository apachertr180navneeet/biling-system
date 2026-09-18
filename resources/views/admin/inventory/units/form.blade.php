@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-ruler"></i></div>
            <div>
                <h4 class="m-page-title">{{ isset($unit) ? 'Edit Inventory Unit' : 'Create Inventory Unit' }}</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Inventory</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="{{ route('admin.inventory.units.index') }}">Units</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">{{ isset($unit) ? 'Edit' : 'Create' }}</li>
                </ul>
            </div>
        </div>
        <a href="{{ route('admin.inventory.units.index') }}" class="btn btn-outline-secondary"><i class="bx bx-left-arrow-alt me-1"></i> Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ isset($unit) ? route('admin.inventory.units.update', $unit) : route('admin.inventory.units.store') }}" method="POST">
                @csrf
                @if(isset($unit)) @method('PUT') @endif

                <div class="m-section-divider">Unit Details</div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $unit?->name) }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Short Name <span class="text-danger">*</span></label>
                        <input type="text" name="short_name" class="form-control" value="{{ old('short_name', $unit?->short_name) }}" maxlength="20" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="active" {{ old('status', $unit?->status ?? 'active') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $unit?->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="m-form-actions mt-3">
                    <button type="submit" class="btn btn-primary"><i class="bx bx-check me-1"></i> {{ isset($unit) ? 'Update' : 'Create' }} Unit</button>
                    <a href="{{ route('admin.inventory.units.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
