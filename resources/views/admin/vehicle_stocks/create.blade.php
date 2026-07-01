@extends('admin.layouts.app')
@section('style')
@endsection
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Admin / Vehicle Stock /</span> Create
    </h4>
    <div class="card">
        <div class="card-header"><h5 class="mb-0">New Stock Entry</h5></div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.vehicle-stocks.store') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Chassis Number</label>
                    <input type="text" name="chassis_number" class="form-control @error('chassis_number') is-invalid @enderror" value="{{ old('chassis_number') }}">
                    @error('chassis_number') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Engine Number</label>
                    <input type="text" name="engine_number" class="form-control @error('engine_number') is-invalid @enderror" value="{{ old('engine_number') }}">
                    @error('engine_number') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Color / Variant</label>
                    <select name="color_id" class="form-control @error('color_id') is-invalid @enderror">
                        <option value="">Select Color</option>
                        @foreach($colors as $color)
                        <option value="{{ $color->id }}" {{ old('color_id') == $color->id ? 'selected' : '' }}>{{ $color->variant->model->brand->name ?? '' }} {{ $color->variant->model->name ?? '' }} {{ $color->variant->name ?? '' }} - {{ $color->color_name }}</option>
                        @endforeach
                    </select>
                    @error('color_id') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Manufacturing Year</label>
                    <input type="number" name="mfg_year" class="form-control @error('mfg_year') is-invalid @enderror" value="{{ old('mfg_year', date('Y')) }}" min="1900" max="{{ date('Y') + 1 }}">
                    @error('mfg_year') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Purchase Date</label>
                    <input type="date" name="purchase_date" class="form-control @error('purchase_date') is-invalid @enderror" value="{{ old('purchase_date') }}">
                    @error('purchase_date') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Purchase Price</label>
                    <input type="number" step="0.01" name="purchase_price" class="form-control @error('purchase_price') is-invalid @enderror" value="{{ old('purchase_price', 0) }}">
                    @error('purchase_price') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control @error('status') is-invalid @enderror">
                        <option value="available" {{ old('status') == 'available' ? 'selected' : '' }}>Available</option>
                        <option value="sold" {{ old('status') == 'sold' ? 'selected' : '' }}>Sold</option>
                        <option value="transferred" {{ old('status') == 'transferred' ? 'selected' : '' }}>Transferred</option>
                    </select>
                    @error('status') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="3">{{ old('notes') }}</textarea>
                    @error('notes') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>
                <button type="submit" class="btn btn-primary">Save</button>
                <a href="{{ route('admin.vehicle-stocks.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection
@section('script')
@endsection
