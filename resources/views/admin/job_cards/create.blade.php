@extends('admin.layouts.app')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold mb-4">Create Job Card</h4>
    @include('admin.layouts.elements.sweet_alerts')
    <div class="card"><div class="card-body">
        <form method="POST" action="{{ route('admin.job-cards.store') }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Customer</label>
                    <select name="customer_id" class="form-select @error('customer_id') is-invalid @enderror">
                        <option value="">Select</option>
                        @foreach($customers as $c)
                        <option value="{{ $c->id }}" {{ old('customer_id')==$c->id ? 'selected':'' }}>{{ $c->first_name }} {{ $c->last_name }} ({{ $c->mobile }})</option>
                        @endforeach
                    </select>
                    @error('customer_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Vehicle Stock (optional)</label>
                    <select name="vehicle_stock_id" class="form-select" id="vehicle_stock_id">
                        <option value="">Select</option>
                        @foreach($vehicleStocks as $vs)
                        <option value="{{ $vs->id }}" {{ old('vehicle_stock_id')==$vs->id ? 'selected':'' }}>{{ $vs->chassis_number }}</option>
                        @endforeach
                    </select>
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
                        <input type="checkbox" name="is_gst" class="form-check-input" value="1" {{ old('is_gst') ? 'checked' : '' }}>
                        <label class="form-check-label">GST Applicable</label>
                    </div>
                </div>
            </div>
            <div class="mt-4"><button type="submit" class="btn btn-primary">Create Job Card</button> <a href="{{ route('admin.job-cards.index') }}" class="btn btn-secondary">Cancel</a></div>
        </form>
    </div></div>
</div>
@endsection
