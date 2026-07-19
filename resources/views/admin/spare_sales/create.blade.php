@extends('admin.layouts.app')
@section('style')
<style>
.line-item-row { background: #f8f9fa; padding: 10px; border-radius: 6px; margin-bottom: 8px; }
</style>
@endsection
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold mb-4">New Spare Part Counter Sale</h4>
        <div class="card"><div class="card-body">
        <form method="POST" action="{{ route('admin.spare-sales.store') }}">
            @csrf
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <label class="form-label">Customer (optional)</label>
                    <select name="customer_id" class="form-select" id="customerSelect">
                        <option value="">Walk-in Customer</option>
                        @foreach($customers as $c)
                        <option value="{{ $c->id }}" data-state="{{ $c->state }}" {{ old('customer_id')==$c->id ? 'selected':'' }}>{{ $c->first_name }} {{ $c->last_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Sale Date</label>
                    <input type="date" name="sale_date" class="form-control @error('sale_date') is-invalid @enderror" value="{{ old('sale_date', date('Y-m-d')) }}">
                    @error('sale_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label">Payment Mode</label>
                    <select name="payment_mode" class="form-select @error('payment_mode') is-invalid @enderror">
                        <option value="cash" {{ old('payment_mode')=='cash'?'selected':'' }}>Cash</option>
                        <option value="bank_transfer" {{ old('payment_mode')=='bank_transfer'?'selected':'' }}>Bank Transfer</option>
                        <option value="cheque" {{ old('payment_mode')=='cheque'?'selected':'' }}>Cheque</option>
                        <option value="upi" {{ old('payment_mode')=='upi'?'selected':'' }}>UPI</option>
                        <option value="card" {{ old('payment_mode')=='card'?'selected':'' }}>Card</option>
                    </select>
                    @error('payment_mode')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label">GST Applicable</label>
                    <div class="form-check form-switch pt-2">
                        <input type="checkbox" name="is_gst" class="form-check-input" value="1" id="gstToggle" checked>
                    </div>
                </div>
            </div>

            <h5>Sale Items</h5>
            <div class="table-responsive mb-3">
                <table class="table table-bordered" id="itemsTable">
                    <thead><tr><th>Part</th><th>HSN</th><th>Qty</th><th>Rate</th><th>GST%</th><th>Total</th><th></th></tr></thead>
                    <tbody id="itemsContainer">
                        <tr class="line-item-row">
                            <td>
                                <select class="form-select part-select" name="items[0][spare_part_id]">
                                    <option value="">Select</option>
                                    @foreach($spareParts as $p)
                                    <option value="{{ $p->id }}" data-name="{{ $p->part_name }}" data-hsn="{{ $p->hsn_code }}" data-rate="{{ $p->selling_price }}" data-gst="{{ $p->gst_rate }}">{{ $p->part_name }} ({{ $p->part_no ?? '' }})</option>
                                    @endforeach
                                </select>
                            </td>
                            <td><input type="text" class="form-control hsn-code" name="items[0][hsn_code]" readonly></td>
                            <td><input type="number" step="0.01" class="form-control qty" name="items[0][quantity]" value="1" min="0.01"></td>
                            <td><input type="number" step="0.01" class="form-control rate" name="items[0][rate]" value="0"></td>
                            <td><input type="number" step="0.01" class="form-control gst-rate" name="items[0][gst_rate]" value="0" readonly></td>
                            <td><span class="line-total fw-bold">0.00</span></td>
                            <td><button type="button" class="btn btn-sm btn-danger remove-item"><i class="bx bx-x"></i></button></td>
                            <input type="hidden" class="part-name-input" name="items[0][part_name]">
                        </tr>
                    </tbody>
                </table>
            </div>
            <button type="button" class="btn btn-sm btn-secondary mb-3" id="addRow"><i class="bx bx-plus"></i> Add Item</button>

            <div class="row">
                <div class="col-md-4 offset-md-8">
                    <table class="table table-sm">
                        <tr><td>Subtotal:</td><td class="text-end"><span id="subtotalDisplay">0.00</span></td></tr>
                        <tr><td>GST:</td><td class="text-end"><span id="gstDisplay">0.00</span></td></tr>
                        <tr class="fw-bold"><td>Grand Total:</td><td class="text-end"><span id="grandTotalDisplay">0.00</span></td></tr>
                    </table>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Notes</label>
                <textarea name="notes" class="form-control">{{ old('notes') }}</textarea>
            </div>

            <button type="submit" class="btn btn-primary"><i class="bx bx-save"></i> Complete Sale</button>
            <a href="{{ route('admin.spare-sales.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div></div>
</div>

@section('script')
<script>
function recalc() {
    var subtotal=0, gst=0;
    var isGst = $('#gstToggle').is(':checked');
    $('#itemsContainer tr').each(function(){
        var qty=parseFloat($(this).find('.qty').val())||0;
        var rate=parseFloat($(this).find('.rate').val())||0;
        var gstRate=isGst ? (parseFloat($(this).find('.gst-rate').val())||0) : 0;
        var lineSub=rate*qty;
        var lineGst=lineSub*gstRate/100;
        var lineTotal=lineSub+lineGst;
        subtotal+=lineSub;
        gst+=lineGst;
        $(this).find('.line-total').text(lineTotal.toFixed(2));
    });
    $('#subtotalDisplay').text(subtotal.toFixed(2));
    $('#gstDisplay').text(gst.toFixed(2));
    $('#grandTotalDisplay').text((subtotal+gst).toFixed(2));
}

var rowIndex=1;
$('#addRow').click(function(){
    var html=`<tr class="line-item-row">
        <td><select class="form-select part-select" name="items[${rowIndex}][spare_part_id]"><option value="">Select</option>@foreach($spareParts as $p)<option value="{{ $p->id }}" data-name="{{ $p->part_name }}" data-hsn="{{ $p->hsn_code }}" data-rate="{{ $p->selling_price }}" data-gst="{{ $p->gst_rate }}">{{ $p->part_name }}</option>@endforeach</select></td>
        <td><input type="text" class="form-control hsn-code" name="items[${rowIndex}][hsn_code]" readonly></td>
        <td><input type="number" step="0.01" class="form-control qty" name="items[${rowIndex}][quantity]" value="1" min="0.01"></td>
        <td><input type="number" step="0.01" class="form-control rate" name="items[${rowIndex}][rate]" value="0"></td>
        <td><input type="number" step="0.01" class="form-control gst-rate" name="items[${rowIndex}][gst_rate]" value="0" readonly></td>
        <td><span class="line-total fw-bold">0.00</span></td>
        <td><button type="button" class="btn btn-sm btn-danger remove-item"><i class="bx bx-x"></i></button></td>
        <input type="hidden" class="part-name-input" name="items[${rowIndex}][part_name]">
    </tr>`;
    $('#itemsContainer').append(html);
    rowIndex++;
});

$(document).on('click', '.remove-item', function(){ if($('#itemsContainer tr').length>1) $(this).closest('tr').remove(); recalc(); });
$(document).on('change', '.part-select', function(){
    var opt=$(this).find('option:selected');
    var row=$(this).closest('tr');
    row.find('.rate').val(opt.data('rate'));
    row.find('.gst-rate').val(opt.data('gst'));
    row.find('.hsn-code').val(opt.data('hsn'));
    row.find('.part-name-input').val(opt.data('name'));
    recalc();
});
$('#gstToggle').change(recalc);
$(document).on('input', '.qty, .rate, .gst-rate', recalc);
</script>
@endsection
@endsection
