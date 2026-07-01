@extends('admin.layouts.app')
@section('style')
<style>
.item-row { border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 10px; }
</style>
@endsection
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Admin / Invoices /</span> Parts Invoice
    </h4>
    <div class="card">
        <div class="card-header"><h5 class="mb-0">New Parts Invoice</h5></div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.invoices.store-parts') }}" id="partsInvoiceForm">
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
                    <label class="form-label">Invoice Date</label>
                    <input type="date" name="invoice_date" class="form-control @error('invoice_date') is-invalid @enderror" value="{{ old('invoice_date', date('Y-m-d')) }}">
                    @error('invoice_date') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">GST Applicable</label>
                    <div class="form-check form-switch">
                        <input type="checkbox" name="is_gst" class="form-check-input" value="1" {{ old('is_gst', '1') == '1' ? 'checked' : '' }} id="gstToggle">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="2">{{ old('notes') }}</textarea>
                    @error('notes') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>

                <hr>
                <h5>Invoice Items</h5>
                @error('items') <div class="text-danger small mb-2">{{ $message }}</div> @enderror
                <div id="itemsContainer">
                    <div class="item-row row">
                        <div class="col-md-4">
                            <select name="items[0][spare_part_id]" class="form-control part-select">
                                <option value="">Select Part</option>
                                @foreach($spareParts as $part)
                                <option value="{{ $part->id }}" data-price="{{ $part->selling_price }}" data-gst="{{ $part->is_gst_applicable ? $part->gst_rate : 0 }}" data-hsn="{{ $part->hsn_code }}">{{ $part->part_no }} - {{ $part->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-1">
                            <input type="text" name="items[0][description]" class="form-control desc" placeholder="Desc" value="Spare Part">
                        </div>
                        <div class="col-md-1">
                            <input type="number" name="items[0][quantity]" class="form-control qty" min="1" value="1">
                        </div>
                        <div class="col-md-2">
                            <input type="number" step="0.01" name="items[0][unit_price]" class="form-control unit-price" min="0" value="0">
                        </div>
                        <div class="col-md-1">
                            <input type="number" step="0.01" name="items[0][gst_rate]" class="form-control gst-rate" min="0" value="18">
                        </div>
                        <div class="col-md-2">
                            <input type="text" class="form-control line-total" readonly value="0.00">
                        </div>
                        <div class="col-md-1">
                            <button type="button" class="btn btn-sm btn-danger remove-item">X</button>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-secondary mt-2" id="addItem">+ Add Item</button>

                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <div id="gstSummary" style="display:none" class="alert alert-info">
                            <strong>GST Type:</strong> <span id="gstTypeDisplay">CGST + SGST</span><br>
                            <strong>Subtotal:</strong> <span id="subtotalDisplay">0.00</span><br>
                            <strong>GST:</strong> <span id="gstDisplay">0.00</span><br>
                            <strong>Grand Total:</strong> <span id="grandTotalDisplay">0.00</span>
                        </div>
                    </div>
                    <div class="col-md-6 text-end">
                        <h4>Total: <span id="invoiceTotal">0.00</span></h4>
                    </div>
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
    var itemIndex = 1;
    $('#addItem').click(function() {
        var html = '<div class="item-row row">' +
            '<div class="col-md-4"><select name="items[' + itemIndex + '][spare_part_id]" class="form-control part-select"><option value="">Select Part</option>@foreach($spareParts as $part)<option value="{{ $part->id }}" data-price="{{ $part->selling_price }}" data-gst="{{ $part->is_gst_applicable ? $part->gst_rate : 0 }}" data-hsn="{{ $part->hsn_code }}">{{ $part->part_no }} - {{ $part->name }}</option>@endforeach</select></div>' +
            '<div class="col-md-1"><input type="text" name="items[' + itemIndex + '][description]" class="form-control desc" value="Spare Part"></div>' +
            '<div class="col-md-1"><input type="number" name="items[' + itemIndex + '][quantity]" class="form-control qty" min="1" value="1"></div>' +
            '<div class="col-md-2"><input type="number" step="0.01" name="items[' + itemIndex + '][unit_price]" class="form-control unit-price" min="0" value="0"></div>' +
            '<div class="col-md-1"><input type="number" step="0.01" name="items[' + itemIndex + '][gst_rate]" class="form-control gst-rate" min="0" value="18"></div>' +
            '<div class="col-md-2"><input type="text" class="form-control line-total" readonly value="0.00"></div>' +
            '<div class="col-md-1"><button type="button" class="btn btn-sm btn-danger remove-item">X</button></div>' +
        '</div>';
        $('#itemsContainer').append(html);
        itemIndex++;
    });
    $(document).on('click', '.remove-item', function() {
        if ($('.item-row').length > 1) $(this).closest('.item-row').remove();
        calcInvoice();
    });
    $(document).on('change keyup', '.qty, .unit-price, .gst-rate', function() {
        var row = $(this).closest('.item-row');
        var qty = parseFloat(row.find('.qty').val()) || 0;
        var price = parseFloat(row.find('.unit-price').val()) || 0;
        var gst = parseFloat(row.find('.gst-rate').val()) || 0;
        var taxable = qty * price;
        var gstAmt = taxable * gst / 100;
        row.find('.line-total').val((taxable + gstAmt).toFixed(2));
        calcInvoice();
    });
    $(document).on('change', '.part-select', function() {
        var row = $(this).closest('.item-row');
        var selected = $(this).find(':selected');
        row.find('.unit-price').val(selected.data('price') || 0);
        row.find('.gst-rate').val(selected.data('gst') || 0);
        row.find('.qty').trigger('keyup');
    });
    $('#gstToggle').change(calcInvoice);
    $('#customerSelect').change(calcInvoice);

    function calcInvoice() {
        var subtotal = 0, totalGst = 0, totalAmount = 0;
        $('.item-row').each(function() {
            var qty = parseFloat($(this).find('.qty').val()) || 0;
            var price = parseFloat($(this).find('.unit-price').val()) || 0;
            var gst = parseFloat($(this).find('.gst-rate').val()) || 0;
            var taxable = qty * price;
            var gstAmt = taxable * gst / 100;
            subtotal += taxable;
            totalGst += gstAmt;
            totalAmount += taxable + gstAmt;
        });
        var isGst = $('#gstToggle').is(':checked');
        if (isGst && subtotal > 0) {
            var customerState = $('#customerSelect').find(':selected').data('state');
            var sellerState = '{{ config("app.seller_state", "Delhi") }}';
            $('#gstTypeDisplay').text(customerState && customerState !== sellerState ? 'IGST' : 'CGST + SGST');
            $('#subtotalDisplay').text(subtotal.toFixed(2));
            $('#gstDisplay').text(totalGst.toFixed(2));
            $('#grandTotalDisplay').text(Math.round(totalAmount).toFixed(2));
            $('#gstSummary').show();
        } else {
            $('#gstSummary').hide();
        }
        $('#invoiceTotal').text(Math.round(totalAmount).toFixed(2));
    }
});
</script>
@endsection
