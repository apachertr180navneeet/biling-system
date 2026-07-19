@extends('admin.layouts.app')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold mb-4">New Vehicle Sale</h4>
        <div class="card"><div class="card-body">
        <form method="POST" action="{{ route('admin.sales.store') }}">
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
                    <label class="form-label">Vehicle Description</label>
                    <select name="vehicle_description" id="vehicle_description" class="form-select @error('vehicle_description') is-invalid @enderror">
                        <option value="">Select Vehicle</option>
                        @foreach($variants as $v)
                            @php
                                $vName = ($v->model->brand->name ?? '') . ' ' . ($v->model->name ?? '') . ' ' . $v->name;
                                $stock = $inventoryStock[$vName] ?? 0;
                            @endphp
                            <option value="{{ $vName }}" data-price="{{ $v->ex_showroom_price }}" data-stock="{{ $stock }}" {{ old('vehicle_description') == $vName ? 'selected' : '' }}>
                                {{ $vName }}
                            </option>
                        @endforeach
                    </select>
                    @error('vehicle_description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Sale Price</label>
                    <input type="number" step="0.01" name="sale_price" id="sale_price" class="form-control @error('sale_price') is-invalid @enderror" value="{{ old('sale_price') }}">
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
@section('script')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const vehicleSelect = document.getElementById('vehicle_description');
        const salePriceInput = document.getElementById('sale_price');

        if (vehicleSelect && salePriceInput) {
            vehicleSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                if (!selectedOption.value) return;
                
                const price = selectedOption.getAttribute('data-price');
                const stock = parseInt(selectedOption.getAttribute('data-stock') || 0);
                
                if (price) {
                    salePriceInput.value = price;
                }
                
                if (stock <= 0) {
                    if (typeof setFlesh === 'function') {
                        setFlesh('error', 'Stock not available for this vehicle.');
                    } else {
                        Swal.fire('Error', 'Stock not available for this vehicle.', 'error');
                    }
                }
            });
        }
    });
</script>
@endsection
