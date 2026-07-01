@extends('admin.layouts.app')
@section('style')
@endsection
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Admin / Vehicle Colors /</span> Create
    </h4>
    <div class="card">
        <div class="card-header"><h5 class="mb-0">New Color</h5></div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.vehicle-colors.store') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Variant</label>
                    <select name="variant_id" class="form-control @error('variant_id') is-invalid @enderror">
                        <option value="">Select Variant</option>
                        @foreach($variants as $variant)
                        <option value="{{ $variant->id }}" {{ old('variant_id') == $variant->id ? 'selected' : '' }}>{{ $variant->model->brand->name ?? '' }} {{ $variant->model->name ?? '' }} {{ $variant->name }}</option>
                        @endforeach
                    </select>
                    @error('variant_id') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Color Name</label>
                    <input type="text" name="color_name" class="form-control @error('color_name') is-invalid @enderror" value="{{ old('color_name') }}">
                    @error('color_name') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Color Code (hex)</label>
                    <input type="text" name="color_code" class="form-control @error('color_code') is-invalid @enderror" value="{{ old('color_code') }}" placeholder="#FFFFFF">
                    @error('color_code') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>
                <button type="submit" class="btn btn-primary">Save</button>
                <a href="{{ route('admin.vehicle-colors.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection
@section('script')
@endsection
