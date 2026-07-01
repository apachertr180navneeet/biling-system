@extends('admin.layouts.app')
@section('style')
<style>
#gstInfo { display:none; }
</style>
@endsection
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Admin / Invoices /</span> Vehicle Invoice
    </h4>
    <div class="card">
        <div class="card-header"><h5 class="mb-0">New Vehicle Invoice</h5></div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.invoices.store-vehicle') }}" id="vehicleInvoiceForm">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Customer</label>
                    <select name="customer_id" class="form-control @error('customer_id') is-invalid @enderror" id="customerSelect">
                        <option value="">Select Customer</option>
                        @foreach($customers as $c)
                        <option value="{{ $c->id }}" data-state="{{ $c->state }}" {{ old('customer_id') == $c->id ? 'selected' : '' }}>{{ $c->first_name }} {{ $c->last_name }} ({{ $c->phone }})</option>
                        @endforeach
                    </select>
                    @error('customer_id') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">From Vehicle Inventory (optional)</label>
                    <select name="vehicle_inventory_id" class="form-control" id="inventorySelect">
                        <option value="">-- Type vehicle details manually --</option>
                        @foreach($vehicleInventories as $inv)
                        <option value="{{ $inv->id }}"
                            data-desc="{{ $inv->vehicle_description }}"
                            data-chassis="{{ $inv->chassis_number ?? '' }}"
                            data-engine="{{ $inv->engine_number ?? '' }}"
                            data-year="{{ $inv->mfg_year ?? '' }}">
                            {{ $inv->vehicle_description }} ({{ $inv->color_name ?? '' }} {{ $inv->mfg_year ?? '' }}, Qty: {{ $inv->quantity }})
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Vehicle Description</label>
                    <input type="text" name="vehicle_description" class="form-control @error('vehicle_description') is-invalid @enderror" id="vehicleDesc" value="{{ old('vehicle_description', request('vehicle_description')) }}" placeholder="e.g. Maruti Suzuki Swift LXi">
                    @error('vehicle_description') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Chassis Number</label>
                        <input type="text" name="chassis_number" class="form-control @error('chassis_number') is-invalid @enderror" id="chassisNum" value="{{ old('chassis_number') }}">
                        @error('chassis_number') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Engine Number</label>
                        <input type="text" name="engine_number" class="form-control @error('engine_number') is-invalid @enderror" id="engineNum" value="{{ old('engine_number') }}">
                        @error('engine_number') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Mfg Year</label>
                        <input type="number" name="mfg_year" class="form-control @error('mfg_year') is-invalid @enderror" id="mfgYear" value="{{ old('mfg_year', date('Y')) }}" min="1900" max="{{ date('Y') + 1 }}">
                        @error('mfg_year') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Invoice Date</label>
                    <input type="date" name="invoice_date" class="form-control @error('invoice_date') is-invalid @enderror" value="{{ old('invoice_date', date('Y-m-d')) }}">
                    @error('invoice_date') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Selling Price</label>
                    <input type="number" step="0.01" name="selling_price" class="form-control @error('selling_price') is-invalid @enderror" id="sellingPrice" value="{{ old('selling_price', request('sale_price', 0)) }}">
                    @error('selling_price') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">GST Applicable</label>
                    <div class="form-check form-switch">
                        <input type="checkbox" name="is_gst" class="form-check-input" value="1" {{ old('is_gst', '1') == '1' ? 'checked' : '' }} id="gstToggle">
                    </div>
                </div>
                <div class="mb-3" id="gstInfo">
                    <div class="alert alert-info">
                        <strong>GST Type:</strong> <span id="gstTypeDisplay">CGST + SGST</span><br>
                        <strong>GST Rate:</strong> 28%<br>
                        <strong>GST Amount:</strong> <span id="gstAmountDisplay">0.00</span><br>
                        <strong>Grand Total:</strong> <span id="grandTotalDisplay">0.00</span>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="2">{{ old('notes') }}</textarea>
                    @error('notes') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>
                <button type="submit" class="btn btn-primary">Create Invoice</button>
                <a href="{{ route('admin.invoices.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection
@section('script')
<script>
var vehiclePrices = @json($vehiclePrices);
$(document).ready(function() {
    function calcGst() {
        var price = parseFloat($('#sellingPrice').val()) || 0;
        var isGst = $('#gstToggle').is(':checked');
        if (isGst && price > 0) {
            var gstAmt = price * 0.28;
            var total = price + gstAmt;
            $('#gstAmountDisplay').text(gstAmt.toFixed(2));
            $('#grandTotalDisplay').text(Math.round(total).toFixed(2));
            var customerState = $('#customerSelect').find(':selected').data('state');
            var sellerState = '{{ config("app.seller_state", "Delhi") }}';
            $('#gstTypeDisplay').text(customerState && customerState !== sellerState ? 'IGST 28%' : 'CGST 14% + SGST 14%');
            $('#gstInfo').show();
        } else {
            $('#gstInfo').hide();
        }
    }
    $('#inventorySelect').change(function() {
        var opt = $(this).find(':selected');
        if (opt.val()) {
            var desc = opt.data('desc');
            $('#vehicleDesc').val(desc);
            $('#chassisNum').val(opt.data('chassis'));
            $('#engineNum').val(opt.data('engine'));
            $('#mfgYear').val(opt.data('year'));
            if (vehiclePrices[desc]) {
                $('#sellingPrice').val(vehiclePrices[desc]);
                calcGst();
            }
        }
    });
    $('#sellingPrice, #gstToggle, #customerSelect').on('change keyup', calcGst);
    calcGst();
});
</script>
@endsection
