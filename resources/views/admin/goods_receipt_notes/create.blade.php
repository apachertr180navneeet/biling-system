@extends('admin.layouts.app')
@section('style')
<style>
.item-row { border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 10px; }
</style>
@endsection
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Admin / Goods Receipt Notes /</span> Create
    </h4>
    <div class="card">
        <div class="card-header"><h5 class="mb-0">New GRN</h5></div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.goods-receipt-notes.store') }}" id="grnForm">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Purchase Order (optional)</label>
                    <select name="purchase_order_id" class="form-control @error('purchase_order_id') is-invalid @enderror" id="poSelect">
                        <option value="">Direct Receipt (No PO)</option>
                        @foreach($purchaseOrders as $po)
                        <option value="{{ $po->id }}" {{ old('purchase_order_id') == $po->id ? 'selected' : '' }}>{{ $po->order_number }} - {{ $po->supplier->name ?? '' }} ({{ $po->status }})</option>
                        @endforeach
                    </select>
                    @error('purchase_order_id') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Received Date</label>
                    <input type="date" name="received_date" class="form-control @error('received_date') is-invalid @enderror" value="{{ old('received_date', date('Y-m-d')) }}">
                    @error('received_date') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="2">{{ old('notes') }}</textarea>
                    @error('notes') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>

                <hr>
                <h5>Received Items</h5>
                @error('items') <div class="text-danger small mb-2">{{ $message }}</div> @enderror
                <div id="itemsContainer">
                    <div class="item-row row">
                        <div class="col-md-3">
                            <select name="items[0][spare_part_id]" class="form-control spare-part-select">
                                <option value="">Select Part</option>
                                @foreach(\App\Models\SparePart::with('category')->orderBy('name')->get() as $part)
                                <option value="{{ $part->id }}" data-price="{{ $part->purchase_price }}">{{ $part->part_no }} - {{ $part->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="number" name="items[0][accepted_quantity]" class="form-control acc-qty" placeholder="Accepted" min="0" value="0">
                        </div>
                        <div class="col-md-2">
                            <input type="number" name="items[0][rejected_quantity]" class="form-control rej-qty" placeholder="Rejected" min="0" value="0">
                        </div>
                        <div class="col-md-2">
                            <input type="number" step="0.01" name="items[0][unit_price]" class="form-control unit-price" placeholder="Unit Price" min="0" value="0">
                        </div>
                        <div class="col-md-2">
                            <input type="text" class="form-control line-total" readonly placeholder="Total" value="0.00">
                        </div>
                        <div class="col-md-1">
                            <button type="button" class="btn btn-sm btn-danger remove-item">X</button>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-secondary mt-2" id="addItem">+ Add Item</button>

                <hr>
                <button type="submit" class="btn btn-primary">Create GRN</button>
                <a href="{{ route('admin.goods-receipt-notes.index') }}" class="btn btn-secondary">Cancel</a>
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
            '<div class="col-md-3"><select name="items[' + itemIndex + '][spare_part_id]" class="form-control spare-part-select"><option value="">Select Part</option>@foreach(\App\Models\SparePart::with('category')->orderBy('name')->get() as $part)<option value="{{ $part->id }}" data-price="{{ $part->purchase_price }}">{{ $part->part_no }} - {{ $part->name }}</option>@endforeach</select></div>' +
            '<div class="col-md-2"><input type="number" name="items[' + itemIndex + '][accepted_quantity]" class="form-control acc-qty" placeholder="Accepted" min="0" value="0"></div>' +
            '<div class="col-md-2"><input type="number" name="items[' + itemIndex + '][rejected_quantity]" class="form-control rej-qty" placeholder="Rejected" min="0" value="0"></div>' +
            '<div class="col-md-2"><input type="number" step="0.01" name="items[' + itemIndex + '][unit_price]" class="form-control unit-price" placeholder="Unit Price" min="0" value="0"></div>' +
            '<div class="col-md-2"><input type="text" class="form-control line-total" readonly placeholder="Total" value="0.00"></div>' +
            '<div class="col-md-1"><button type="button" class="btn btn-sm btn-danger remove-item">X</button></div>' +
        '</div>';
        $('#itemsContainer').append(html);
        itemIndex++;
    });
    $(document).on('click', '.remove-item', function() {
        if ($('.item-row').length > 1) $(this).closest('.item-row').remove();
    });
    $(document).on('change keyup', '.acc-qty, .rej-qty, .unit-price', function() {
        var row = $(this).closest('.item-row');
        var acc = parseFloat(row.find('.acc-qty').val()) || 0;
        var rej = parseFloat(row.find('.rej-qty').val()) || 0;
        var price = parseFloat(row.find('.unit-price').val()) || 0;
        row.find('.line-total').val(((acc + rej) * price).toFixed(2));
    });
    $(document).on('change', '.spare-part-select', function() {
        var row = $(this).closest('.item-row');
        var selected = $(this).find(':selected');
        var price = selected.data('price') || 0;
        row.find('.unit-price').val(price);
        row.find('.acc-qty').trigger('keyup');
    });
    $('#poSelect').change(function() {
        var poId = $(this).val();
        if (poId) {
            // Could auto-fill items from PO here
        }
    });
});
</script>
@endsection
