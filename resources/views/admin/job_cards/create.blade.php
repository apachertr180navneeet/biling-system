@extends('admin.layouts.app')
@section('style')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
<style>
.select2-container--default .select2-selection--single { height: 38px; border: 1px solid #ddd; }
</style>
@endsection
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold mb-4">Create Job Card</h4>
        <div class="card"><div class="card-body">
        <form method="POST" action="{{ route('admin.job-cards.store') }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Customer</label>
                    <select name="customer_id" class="form-select @error('customer_id') is-invalid @enderror">
                        <option value="">Select</option>
                        @foreach($customers as $c)
                        <option value="{{ $c->id }}" {{ old('customer_id')==$c->id ? 'selected':'' }}>{{ $c->first_name }} {{ $c->last_name }} ({{ $c->phone }})</option>
                        @endforeach
                    </select>
                    @error('customer_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Vehicle Number</label>
                    <input type="text" name="vehicle_number" class="form-control" value="{{ old('vehicle_number') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Vehicle Model</label>
                    <input type="text" name="vehicle_model" class="form-control" value="{{ old('vehicle_model') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Kilometer Reading</label>
                    <input type="text" name="kilometer_reading" class="form-control" value="{{ old('kilometer_reading') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Service Date</label>
                    <input type="date" name="service_date" class="form-control @error('service_date') is-invalid @enderror" value="{{ old('service_date', date('Y-m-d')) }}">
                    @error('service_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <label class="form-label">Complaint</label>
                    <textarea name="complaint" class="form-control">{{ old('complaint') }}</textarea>
                </div>
                <div class="col-12">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" class="form-control">{{ old('notes') }}</textarea>
                </div>
                <div class="col-12">
                    <div class="form-check">
                        <input type="checkbox" name="is_gst" class="form-check-input" value="1" {{ old('is_gst') ? 'checked' : '' }} id="isGst">
                        <label class="form-check-label" for="isGst">GST Applicable</label>
                    </div>
                </div>
            </div>
            <div class="mt-4"><button type="submit" class="btn btn-primary">Create Job Card</button> <a href="{{ route('admin.job-cards.index') }}" class="btn btn-secondary">Cancel</a></div>
        </form>
    </div></div>
</div>
@endsection
