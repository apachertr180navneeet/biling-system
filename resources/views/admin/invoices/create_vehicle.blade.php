@extends('admin.layouts.app')
@section('style')
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
                    <label class="form-label">Vehicle Stock (Available)</label>
                    <select name="vehicle_stock_id" class="form-control @error('vehicle_stock_id') is-invalid @enderror" id="stockSelect">
                        <option value="">Select Chassis</option>
                        @foreach($stocks as $s)
                        <option value="{{ $s->id }}" data-price="{{ $s->purchase_price }}" {{ old('vehicle_stock_id') == $s->id ? 'selected' : '' }}>
                            {{ $s->chassis_number }} - {{ $s->color->variant->model->brand->name ?? '' }} {{ $s->color->variant->model->name ?? '' }} {{ $s->color->variant->name ?? '' }} ({{ $s->color->color_name ?? '' }})
                        </option>
                        @endforeach
                    </select>
                    @error('vehicle_stock_id') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Invoice Date</label>
                    <input type="date" name="invoice_date" class="form-control @error('invoice_date') is-invalid @enderror" value="{{ old('invoice_date', date('Y-m-d')) }}">
                    @error('invoice_date') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Selling Price</label>
                    <input type="number" step="0.01" name="selling_price" class="form-control @error('selling_price') is-invalid @enderror" id="sellingPrice" value="{{ old('selling_price', 0) }}">
                    @error('selling_price') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">GST Applicable</label>
                    <div class="form-check form-switch">
                        <input type="checkbox" name="is_gst" class="form-check-input" value="1" {{ old('is_gst', '1') == '1' ? 'checked' : '' }} id="gstToggle">
                    </div>
                </div>
                <div class="mb-3" id="gstInfo" style="display:none">
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
    $('#sellingPrice, #gstToggle, #customerSelect').on('change keyup', calcGst);
    $('#stockSelect').change(function() {
        var price = $(this).find(':selected').data('price') || 0;
        $('#sellingPrice').val(price);
        calcGst();
    });
});
</script>
@endsection
