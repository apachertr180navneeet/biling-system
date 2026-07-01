@extends('admin.layouts.app')
@section('style')
@endsection
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Admin / Spare Part Stock /</span> Edit
    </h4>
    <div class="card">
        <div class="card-header"><h5 class="mb-0">Edit Stock Entry</h5></div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.spare-part-stocks.update', $sparePartStock) }}">
                @csrf @method('PUT')
                <div class="mb-3">
                    <label class="form-label">Spare Part</label>
                    <select name="spare_part_id" class="form-control @error('spare_part_id') is-invalid @enderror">
                        <option value="">Select Part</option>
                        @foreach($spareParts as $part)
                        <option value="{{ $part->id }}" {{ old('spare_part_id', $sparePartStock->spare_part_id) == $part->id ? 'selected' : '' }}>{{ $part->part_no }} - {{ $part->name }} ({{ $part->category->name ?? '' }})</option>
                        @endforeach
                    </select>
                    @error('spare_part_id') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Quantity</label>
                    <input type="number" name="quantity" class="form-control @error('quantity') is-invalid @enderror" value="{{ old('quantity', $sparePartStock->quantity) }}" min="0">
                    @error('quantity') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Minimum Quantity (Reorder Level)</label>
                    <input type="number" name="min_quantity" class="form-control @error('min_quantity') is-invalid @enderror" value="{{ old('min_quantity', $sparePartStock->min_quantity) }}" min="0">
                    @error('min_quantity') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Location (Shelf / Rack)</label>
                    <input type="text" name="location" class="form-control @error('location') is-invalid @enderror" value="{{ old('location', $sparePartStock->location) }}">
                    @error('location') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="{{ route('admin.spare-part-stocks.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection
@section('script')
@endsection
