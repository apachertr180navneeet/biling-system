@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-dish"></i></div>
            <div>
                <h4 class="m-page-title">{{ isset($menuItem) ? 'Edit Menu Item' : 'Create Menu Item' }}</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Restaurant POS</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="{{ route('admin.restaurant.menu-items.index') }}">Menu Items</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">{{ isset($menuItem) ? 'Edit' : 'Create' }}</li>
                </ul>
            </div>
        </div>
        <a href="{{ route('admin.restaurant.menu-items.index') }}" class="btn btn-outline-secondary"><i class="bx bx-left-arrow-alt me-1"></i> Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ isset($menuItem) ? route('admin.restaurant.menu-items.update', $menuItem) : route('admin.restaurant.menu-items.store') }}" method="POST">
                @csrf
                @if(isset($menuItem)) @method('PUT') @endif

                <div class="m-section-divider">Menu Item Details</div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Hotel <span class="text-danger">*</span></label>
                        <select name="hotel_id" class="form-select" required>
                            <option value="">Select Hotel</option>
                            @foreach($hotels as $hotel)
                            <option value="{{ $hotel->id }}" {{ old('hotel_id', $menuItem?->hotel_id) == $hotel->id ? 'selected' : '' }}>{{ $hotel->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $menuItem?->name) }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Category <span class="text-danger">*</span></label>
                        <select name="category" class="form-select" required>
                            <option value="main_course" {{ old('category', $menuItem?->category ?? 'main_course') == 'main_course' ? 'selected' : '' }}>Main Course</option>
                            <option value="appetizer" {{ old('category', $menuItem?->category) == 'appetizer' ? 'selected' : '' }}>Appetizer</option>
                            <option value="dessert" {{ old('category', $menuItem?->category) == 'dessert' ? 'selected' : '' }}>Dessert</option>
                            <option value="beverage" {{ old('category', $menuItem?->category) == 'beverage' ? 'selected' : '' }}>Beverage</option>
                            <option value="special" {{ old('category', $menuItem?->category) == 'special' ? 'selected' : '' }}>Special</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Price <span class="text-danger">*</span></label>
                        <input type="number" name="price" class="form-control" value="{{ old('price', $menuItem?->price) }}" min="0" step="0.01" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tax Rate (%)</label>
                        <input type="number" name="tax_rate" class="form-control" value="{{ old('tax_rate', $menuItem?->tax_rate ?? 0) }}" min="0" max="100" step="0.01">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Preparation Time (minutes)</label>
                        <input type="number" name="preparation_time" class="form-control" value="{{ old('preparation_time', $menuItem?->preparation_time) }}" min="0">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Available</label>
                        <div class="form-check form-switch mt-2">
                            <input class="form-check-input" type="checkbox" name="is_available" value="1" {{ old('is_available', $menuItem?->is_available ?? 1) ? 'checked' : '' }}>
                            <label class="form-check-label">Available for Order</label>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="active" {{ old('status', $menuItem?->status ?? 'active') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $menuItem?->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description', $menuItem?->description) }}</textarea>
                    </div>
                </div>

                <div class="m-form-actions mt-3">
                    <button type="submit" class="btn btn-primary"><i class="bx bx-check me-1"></i> {{ isset($menuItem) ? 'Update' : 'Create' }} Menu Item</button>
                    <a href="{{ route('admin.restaurant.menu-items.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
