@extends('admin.layouts.app')
@section('style')
@endsection
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Admin / HSN/SAC Master /</span> Edit
    </h4>
    <div class="card">
        <div class="card-header"><h5 class="mb-0">Edit HSN/SAC Code</h5></div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.hsn-sac-master.update', $hsn) }}">
                @csrf @method('PUT')
                <div class="mb-3">
                    <label class="form-label">Code</label>
                    <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code', $hsn->code) }}">
                    @error('code') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="2">{{ old('description', $hsn->description) }}</textarea>
                    @error('description') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">GST Rate (%)</label>
                    <input type="number" step="0.01" name="gst_rate" class="form-control @error('gst_rate') is-invalid @enderror" value="{{ old('gst_rate', $hsn->gst_rate) }}">
                    @error('gst_rate') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Cess Rate (%)</label>
                    <input type="number" step="0.01" name="cess_rate" class="form-control @error('cess_rate') is-invalid @enderror" value="{{ old('cess_rate', $hsn->cess_rate) }}">
                    @error('cess_rate') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="{{ route('admin.hsn-sac-master.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection
@section('script')
@endsection
