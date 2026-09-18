@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-duplicate"></i></div>
            <div>
                <h4 class="m-page-title">{{ isset($laundryItem) ? 'Edit Laundry Item' : 'Create Laundry Item' }}</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Housekeeping</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="{{ route('admin.housekeeping.laundry-items.index') }}">Laundry Items</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">{{ isset($laundryItem) ? 'Edit' : 'Create' }}</li>
                </ul>
            </div>
        </div>
        <a href="{{ route('admin.housekeeping.laundry-items.index') }}" class="btn btn-outline-secondary"><i class="bx bx-left-arrow-alt me-1"></i> Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ isset($laundryItem) ? route('admin.housekeeping.laundry-items.update', $laundryItem) : route('admin.housekeeping.laundry-items.store') }}" method="POST">
                @csrf
                @if(isset($laundryItem)) @method('PUT') @endif

                <div class="m-section-divider">Laundry Item Details</div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Hotel <span class="text-danger">*</span></label>
                        <select name="hotel_id" class="form-select" required>
                            <option value="">Select Hotel</option>
                            @foreach($hotels as $hotel)
                            <option value="{{ $hotel->id }}" {{ old('hotel_id', $laundryItem?->hotel_id) == $hotel->id ? 'selected' : '' }}>{{ $hotel->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $laundryItem?->name) }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Item Type <span class="text-danger">*</span></label>
                        <select name="item_type" class="form-select" required>
                            <option value="linen" {{ old('item_type', $laundryItem?->item_type ?? 'linen') == 'linen' ? 'selected' : '' }}>Linen</option>
                            <option value="towel" {{ old('item_type', $laundryItem?->item_type) == 'towel' ? 'selected' : '' }}>Towel</option>
                            <option value="uniform" {{ old('item_type', $laundryItem?->item_type) == 'uniform' ? 'selected' : '' }}>Uniform</option>
                            <option value="other" {{ old('item_type', $laundryItem?->item_type) == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Unit <span class="text-danger">*</span></label>
                        <select name="unit" class="form-select" required>
                            <option value="pieces" {{ old('unit', $laundryItem?->unit ?? 'pieces') == 'pieces' ? 'selected' : '' }}>Pieces</option>
                            <option value="kg" {{ old('unit', $laundryItem?->unit) == 'kg' ? 'selected' : '' }}>Kilograms</option>
                            <option value="pairs" {{ old('unit', $laundryItem?->unit) == 'pairs' ? 'selected' : '' }}>Pairs</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Quantity <span class="text-danger">*</span></label>
                        <input type="number" name="quantity" class="form-control" value="{{ old('quantity', $laundryItem?->quantity ?? 0) }}" min="0" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="active" {{ old('status', $laundryItem?->status ?? 'active') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $laundryItem?->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description', $laundryItem?->description) }}</textarea>
                    </div>
                </div>

                <div class="m-form-actions mt-3">
                    <button type="submit" class="btn btn-primary"><i class="bx bx-check me-1"></i> {{ isset($laundryItem) ? 'Update' : 'Create' }} Item</button>
                    <a href="{{ route('admin.housekeeping.laundry-items.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
