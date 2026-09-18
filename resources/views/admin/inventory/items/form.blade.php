@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-package"></i></div>
            <div>
                <h4 class="m-page-title">{{ isset($item) ? 'Edit Inventory Item' : 'Create Inventory Item' }}</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Inventory</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="{{ route('admin.inventory.items.index') }}">Items</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">{{ isset($item) ? 'Edit' : 'Create' }}</li>
                </ul>
            </div>
        </div>
        <a href="{{ route('admin.inventory.items.index') }}" class="btn btn-outline-secondary"><i class="bx bx-left-arrow-alt me-1"></i> Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ isset($item) ? route('admin.inventory.items.update', $item) : route('admin.inventory.items.store') }}" method="POST">
                @csrf
                @if(isset($item)) @method('PUT') @endif

                <div class="m-section-divider">Basic Information</div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Hotel <span class="text-danger">*</span></label>
                        <select name="hotel_id" class="form-select" required>
                            <option value="">Select Hotel</option>
                            @foreach($hotels as $hotel)
                            <option value="{{ $hotel->id }}" {{ old('hotel_id', $item?->hotel_id) == $hotel->id ? 'selected' : '' }}>{{ $hotel->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Category <span class="text-danger">*</span></label>
                        <select name="category_id" class="form-select" required>
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $item?->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Unit <span class="text-danger">*</span></label>
                        <select name="unit_id" class="form-select" required>
                            <option value="">Select Unit</option>
                            @foreach($units as $unit)
                            <option value="{{ $unit->id }}" {{ old('unit_id', $item?->unit_id) == $unit->id ? 'selected' : '' }}>{{ $unit->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $item?->name) }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">SKU</label>
                        <input type="text" name="sku" class="form-control" value="{{ old('sku', $item?->sku) }}">
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description', $item?->description) }}</textarea>
                    </div>
                </div>

                <div class="m-section-divider">Pricing</div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Cost Price</label>
                        <input type="number" step="0.01" name="cost_price" class="form-control" value="{{ old('cost_price', $item?->cost_price) }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Sell Price</label>
                        <input type="number" step="0.01" name="sell_price" class="form-control" value="{{ old('sell_price', $item?->sell_price) }}">
                    </div>
                </div>

                <div class="m-section-divider">Stock Management</div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Minimum Stock</label>
                        <input type="number" name="min_stock" class="form-control" value="{{ old('min_stock', $item?->min_stock) }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Maximum Stock</label>
                        <input type="number" name="max_stock" class="form-control" value="{{ old('max_stock', $item?->max_stock) }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-check mt-4">
                            <input class="form-check-input" type="checkbox" name="is_reorder" value="1" id="isReorder" {{ old('is_reorder', $item?->is_reorder) ? 'checked' : '' }}>
                            <label class="form-check-label" for="isReorder">Enable Reorder</label>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="active" {{ old('status', $item?->status ?? 'active') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $item?->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="m-form-actions mt-3">
                    <button type="submit" class="btn btn-primary"><i class="bx bx-check me-1"></i> {{ isset($item) ? 'Update' : 'Create' }} Item</button>
                    <a href="{{ route('admin.inventory.items.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
