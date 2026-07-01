@extends('admin.layouts.app')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold mb-4">New Vehicle Sale</h4>
    @include('admin.layouts.elements.sweet_alerts')
    <div class="card"><div class="card-body">
        <form method="POST" action="{{ route('admin.sales.store') }}">
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
                    <label class="form-label">Vehicle Stock (Chassis)</label>
                    <select name="vehicle_stock_id" class="form-select @error('vehicle_stock_id') is-invalid @enderror">
                        <option value="">Select</option>
                        @foreach($vehicleStocks as $vs)
                        <option value="{{ $vs->id }}" {{ old('vehicle_stock_id')==$vs->id ? 'selected':'' }}>{{ $vs->chassis_number }}</option>
                        @endforeach
                    </select>
                    @error('vehicle_stock_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Sale Price</label>
                    <input type="number" step="0.01" name="sale_price" class="form-control @error('sale_price') is-invalid @enderror" value="{{ old('sale_price') }}">
                    @error('sale_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Booking Date</label>
                    <input type="date" name="booking_date" class="form-control @error('booking_date') is-invalid @enderror" value="{{ old('booking_date', date('Y-m-d')) }}">
                    @error('booking_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Booking Amount (Advance)</label>
                    <input type="number" step="0.01" name="booking_amount" class="form-control @error('booking_amount') is-invalid @enderror" value="{{ old('booking_amount') }}">
                    @error('booking_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" class="form-control">{{ old('notes') }}</textarea>
                </div>
            </div>
            <div class="mt-4"><button type="submit" class="btn btn-primary">Create Sale</button> <a href="{{ route('admin.sales.index') }}" class="btn btn-secondary">Cancel</a></div>
        </form>
    </div></div>
</div>
@endsection
