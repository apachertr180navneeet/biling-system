@extends('admin.layouts.app')
@section('style')
@endsection
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Admin / Vehicle Variants /</span> Edit
    </h4>
    <div class="card">
        <div class="card-header"><h5 class="mb-0">Edit Variant</h5></div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.vehicle-variants.update', $vehicleVariant) }}">
                @csrf @method('PUT')
                <div class="mb-3">
                    <label class="form-label">Model</label>
                    <select name="model_id" class="form-control @error('model_id') is-invalid @enderror">
                        <option value="">Select Model</option>
                        @foreach($models as $model)
                        <option value="{{ $model->id }}" {{ old('model_id', $vehicleVariant->model_id) == $model->id ? 'selected' : '' }}>{{ $model->brand->name ?? '' }} {{ $model->name }}</option>
                        @endforeach
                    </select>
                    @error('model_id') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Variant Name</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $vehicleVariant->name) }}">
                    @error('name') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Fuel Type</label>
                    <input type="text" name="fuel_type" class="form-control @error('fuel_type') is-invalid @enderror" value="{{ old('fuel_type', $vehicleVariant->fuel_type) }}">
                    @error('fuel_type') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Transmission</label>
                    <input type="text" name="transmission" class="form-control @error('transmission') is-invalid @enderror" value="{{ old('transmission', $vehicleVariant->transmission) }}">
                    @error('transmission') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Ex-Showroom Price</label>
                    <input type="number" step="0.01" name="ex_showroom_price" class="form-control @error('ex_showroom_price') is-invalid @enderror" value="{{ old('ex_showroom_price', $vehicleVariant->ex_showroom_price) }}">
                    @error('ex_showroom_price') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">HSN Code</label>
                    <input type="text" name="hsn_code" class="form-control @error('hsn_code') is-invalid @enderror" value="{{ old('hsn_code', $vehicleVariant->hsn_code) }}">
                    @error('hsn_code') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="{{ route('admin.vehicle-variants.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection
@section('script')
@endsection
