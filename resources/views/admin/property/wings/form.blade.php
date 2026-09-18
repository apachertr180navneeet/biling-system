@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-directions"></i></div>
            <div>
                <h4 class="m-page-title">{{ isset($wing) ? 'Edit Wing' : 'Create Wing' }}</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Property</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="{{ route('admin.property.wings.index') }}">Wings</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">{{ isset($wing) ? 'Edit' : 'Create' }}</li>
                </ul>
            </div>
        </div>
        <a href="{{ route('admin.property.wings.index') }}" class="btn btn-outline-secondary"><i class="bx bx-left-arrow-alt me-1"></i> Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ isset($wing) ? route('admin.property.wings.update', $wing) : route('admin.property.wings.store') }}" method="POST">
                @csrf
                @if(isset($wing)) @method('PUT') @endif

                <div class="m-section-divider">Wing Details</div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Floor <span class="text-danger">*</span></label>
                        <select name="floor_id" class="form-select" required>
                            <option value="">Select Floor</option>
                            @foreach($floors as $floor)
                            <option value="{{ $floor->id }}" {{ old('floor_id', $wing?->floor_id) == $floor->id ? 'selected' : '' }}>{{ $floor->building_name }} - {{ $floor->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $wing?->name) }}" required>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description', $wing?->description) }}</textarea>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="active" {{ old('status', $wing?->status ?? 'active') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $wing?->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="m-form-actions mt-3">
                    <button type="submit" class="btn btn-primary"><i class="bx bx-check me-1"></i> {{ isset($wing) ? 'Update' : 'Create' }} Wing</button>
                    <a href="{{ route('admin.property.wings.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
