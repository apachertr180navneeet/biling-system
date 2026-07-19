@extends('admin.layouts.app')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold mb-4">Edit Vehicle Master</h4>
        <div class="card"><div class="card-body">
        <form method="POST" action="{{ route('admin.vehicle-masters.update', $vehicle) }}">
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Variant Name</label>
                    <input type="text" name="variant_name" class="form-control @error('variant_name') is-invalid @enderror" value="{{ old('variant_name', $vehicle->variant_name) }}">
                    @error('variant_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Fuel Type</label>
                    <select name="fuel_type" class="form-select @error('fuel_type') is-invalid @enderror">
                        <option value="">Select</option>
                        @foreach(['Petrol','Diesel','CNG','Electric','Hybrid'] as $ft)
                        <option value="{{ $ft }}" {{ old('fuel_type', $vehicle->fuel_type)==$ft ? 'selected':'' }}>{{ $ft }}</option>
                        @endforeach
                    </select>
                    @error('fuel_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Transmission</label>
                    <select name="transmission" class="form-select @error('transmission') is-invalid @enderror">
                        <option value="">Select</option>
                        @foreach(['Manual','Automatic','AMT','CVT','DCT'] as $t)
                        <option value="{{ $t }}" {{ old('transmission', $vehicle->transmission)==$t ? 'selected':'' }}>{{ $t }}</option>
                        @endforeach
                    </select>
                    @error('transmission')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Ex-Showroom Price <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" name="ex_showroom_price" class="form-control @error('ex_showroom_price') is-invalid @enderror" value="{{ old('ex_showroom_price', $vehicle->ex_showroom_price) }}" required>
                    @error('ex_showroom_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Color Name</label>
                    <input type="text" name="color_name" class="form-control @error('color_name') is-invalid @enderror" value="{{ old('color_name', $vehicle->color_name) }}">
                    @error('color_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="mt-4"><button type="submit" class="btn btn-primary">Update</button> <a href="{{ route('admin.vehicle-masters.index') }}" class="btn btn-secondary">Cancel</a></div>
        </form>
    </div></div>
</div>
@endsection
