@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-transfer"></i></div>
            <div>
                <h4 class="m-page-title">{{ isset($grn) ? 'Edit GRN' : 'Create GRN' }}</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Inventory</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="{{ route('admin.inventory.grns.index') }}">GRN</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">{{ isset($grn) ? 'Edit' : 'Create' }}</li>
                </ul>
            </div>
        </div>
        <a href="{{ route('admin.inventory.grns.index') }}" class="btn btn-outline-secondary"><i class="bx bx-left-arrow-alt me-1"></i> Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ isset($grn) ? route('admin.inventory.grns.update', $grn) : route('admin.inventory.grns.store') }}" method="POST">
                @csrf
                @if(isset($grn)) @method('PUT') @endif

                <div class="m-section-divider">GRN Details</div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Hotel <span class="text-danger">*</span></label>
                        <select name="hotel_id" class="form-select" required>
                            <option value="">Select Hotel</option>
                            @foreach($hotels as $hotel)
                            <option value="{{ $hotel->id }}" {{ old('hotel_id', $grn?->hotel_id) == $hotel->id ? 'selected' : '' }}>{{ $hotel->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Purchase Order <span class="text-danger">*</span></label>
                        <select name="purchase_order_id" class="form-select" required>
                            <option value="">Select Purchase Order</option>
                            @foreach($purchaseOrders as $po)
                            <option value="{{ $po->id }}" {{ old('purchase_order_id', $grn?->purchase_order_id) == $po->id ? 'selected' : '' }}>{{ $po->po_number }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Supplier <span class="text-danger">*</span></label>
                        <select name="supplier_id" class="form-select" required>
                            <option value="">Select Supplier</option>
                            @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ old('supplier_id', $grn?->supplier_id) == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">GRN Date <span class="text-danger">*</span></label>
                        <input type="date" name="grn_date" class="form-control" value="{{ old('grn_date', $grn?->grn_date?->format('Y-m-d') ?? date('Y-m-d')) }}" required>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Remarks</label>
                        <textarea name="remarks" class="form-control" rows="3">{{ old('remarks', $grn?->remarks) }}</textarea>
                    </div>
                </div>

                <div class="m-section-divider">GRN Items</div>
                <div id="grn-items-container">
                    @if(isset($grn) && $grn->items->count())
                    @foreach($grn->items as $index => $item)
                    <div class="row grn-item-row mb-2">
                        <div class="col-md-2">
                            <select name="items[{{ $index }}][item_id]" class="form-select item-select" required>
                                <option value="">Select Item</option>
                                @foreach($items as $itm)
                                <option value="{{ $itm->id }}" data-price="{{ $itm->cost_price }}" {{ $item->item_id == $itm->id ? 'selected' : '' }}>{{ $itm->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-1">
                            <input type="number" name="items[{{ $index }}][quantity_ordered]" class="form-control item-ordered" placeholder="Ordered" value="{{ $item->quantity_ordered }}" readonly>
                        </div>
                        <div class="col-md-2">
                            <input type="number" name="items[{{ $index }}][quantity_received]" class="form-control item-received" placeholder="Received" value="{{ $item->quantity_received }}" min="0" required>
                        </div>
                        <div class="col-md-2">
                            <input type="number" name="items[{{ $index }}][quantity_accepted]" class="form-control item-accepted" placeholder="Accepted" value="{{ $item->quantity_accepted }}" min="0" required>
                        </div>
                        <div class="col-md-1">
                            <input type="number" name="items[{{ $index }}][quantity_rejected]" class="form-control item-rejected" placeholder="Rejected" value="{{ $item->quantity_rejected }}" min="0">
                        </div>
                        <div class="col-md-1">
                            <input type="number" name="items[{{ $index }}][unit_cost]" class="form-control item-unit-cost" placeholder="Cost" value="{{ $item->unit_cost }}" min="0" step="0.01" required>
                        </div>
                        <div class="col-md-2">
                            <input type="number" name="items[{{ $index }}][total_cost]" class="form-control item-total-cost" placeholder="Total" value="{{ $item->total_cost }}" readonly>
                        </div>
                        <div class="col-md-1">
                            <button type="button" class="btn btn-outline-danger btn-sm remove-item"><i class="bx bx-trash"></i></button>
                        </div>
                    </div>
                    @endforeach
                    @else
                    <div class="row grn-item-row mb-2">
                        <div class="col-md-2">
                            <select name="items[0][item_id]" class="form-select item-select" required>
                                <option value="">Select Item</option>
                                @foreach($items as $itm)
                                <option value="{{ $itm->id }}" data-price="{{ $itm->cost_price }}">{{ $itm->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-1">
                            <input type="number" name="items[0][quantity_ordered]" class="form-control item-ordered" placeholder="Ordered" readonly>
                        </div>
                        <div class="col-md-2">
                            <input type="number" name="items[0][quantity_received]" class="form-control item-received" placeholder="Received" min="0" required>
                        </div>
                        <div class="col-md-2">
                            <input type="number" name="items[0][quantity_accepted]" class="form-control item-accepted" placeholder="Accepted" min="0" required>
                        </div>
                        <div class="col-md-1">
                            <input type="number" name="items[0][quantity_rejected]" class="form-control item-rejected" placeholder="Rejected" min="0">
                        </div>
                        <div class="col-md-1">
                            <input type="number" name="items[0][unit_cost]" class="form-control item-unit-cost" placeholder="Cost" min="0" step="0.01" required>
                        </div>
                        <div class="col-md-2">
                            <input type="number" name="items[0][total_cost]" class="form-control item-total-cost" placeholder="Total" readonly>
                        </div>
                        <div class="col-md-1">
                            <button type="button" class="btn btn-outline-danger btn-sm remove-item"><i class="bx bx-trash"></i></button>
                        </div>
                    </div>
                    @endif
                </div>
                <div class="mb-3">
                    <button type="button" class="btn btn-outline-primary btn-sm" id="add-item-btn"><i class="bx bx-plus"></i> Add Item</button>
                </div>

                <div class="m-section-divider">Summary</div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Subtotal</label>
                        <input type="number" name="subtotal" id="subtotal" class="form-control" value="{{ old('subtotal', $grn?->subtotal ?? 0) }}" min="0" step="0.01" readonly>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Tax Amount</label>
                        <input type="number" name="tax_amount" id="tax_amount" class="form-control" value="{{ old('tax_amount', $grn?->tax_amount ?? 0) }}" min="0" step="0.01">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Total Amount</label>
                        <input type="number" name="total_amount" id="total_amount" class="form-control" value="{{ old('total_amount', $grn?->total_amount ?? 0) }}" min="0" step="0.01" readonly>
                    </div>
                </div>

                <div class="m-form-actions mt-3">
                    <button type="submit" class="btn btn-primary"><i class="bx bx-check me-1"></i> {{ isset($grn) ? 'Update' : 'Create' }} GRN</button>
                    <a href="{{ route('admin.inventory.grns.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
var itemIndex = {{ isset($grn) ? $grn->items->count() : 1 }};

function recalculateRow(row) {
    var received = parseFloat(row.find('.item-received').val()) || 0;
    var cost = parseFloat(row.find('.item-unit-cost').val()) || 0;
    row.find('.item-total-cost').val((received * cost).toFixed(2));
}

function recalculateSummary() {
    var subtotal = 0;
    $('.grn-item-row').each(function() {
        subtotal += parseFloat($(this).find('.item-total-cost').val()) || 0;
    });
    var tax = parseFloat($('#tax_amount').val()) || 0;
    $('#subtotal').val(subtotal.toFixed(2));
    $('#total_amount').val((subtotal + tax).toFixed(2));
}

$(document).on('change', '.item-select', function() {
    var selectedOption = $(this).find(':selected');
    var price = selectedOption.data('price');
    if (price) {
        $(this).closest('.grn-item-row').find('.item-unit-cost').val(price);
        recalculateRow($(this).closest('.grn-item-row'));
        recalculateSummary();
    }
});

$(document).on('input', '.item-received, .item-unit-cost', function() {
    recalculateRow($(this).closest('.grn-item-row'));
    recalculateSummary();
});

$('#tax_amount').on('input', function() {
    recalculateSummary();
});

$('#add-item-btn').on('click', function() {
    var optionsHtml = '<option value="">Select Item</option>';
    @foreach($items as $itm)
    optionsHtml += '<option value="{{ $itm->id }}" data-price="{{ $itm->cost_price }}">{{ $itm->name }}</option>';
    @endforeach

    var html = '<div class="row grn-item-row mb-2">' +
        '<div class="col-md-2"><select name="items[' + itemIndex + '][item_id]" class="form-select item-select" required>' + optionsHtml + '</select></div>' +
        '<div class="col-md-1"><input type="number" name="items[' + itemIndex + '][quantity_ordered]" class="form-control item-ordered" placeholder="Ordered" readonly></div>' +
        '<div class="col-md-2"><input type="number" name="items[' + itemIndex + '][quantity_received]" class="form-control item-received" placeholder="Received" min="0" required></div>' +
        '<div class="col-md-2"><input type="number" name="items[' + itemIndex + '][quantity_accepted]" class="form-control item-accepted" placeholder="Accepted" min="0" required></div>' +
        '<div class="col-md-1"><input type="number" name="items[' + itemIndex + '][quantity_rejected]" class="form-control item-rejected" placeholder="Rejected" min="0"></div>' +
        '<div class="col-md-1"><input type="number" name="items[' + itemIndex + '][unit_cost]" class="form-control item-unit-cost" placeholder="Cost" min="0" step="0.01" required></div>' +
        '<div class="col-md-2"><input type="number" name="items[' + itemIndex + '][total_cost]" class="form-control item-total-cost" placeholder="Total" readonly></div>' +
        '<div class="col-md-1"><button type="button" class="btn btn-outline-danger btn-sm remove-item"><i class="bx bx-trash"></i></button></div>' +
        '</div>';

    $('#grn-items-container').append(html);
    itemIndex++;
});

$(document).on('click', '.remove-item', function() {
    if ($('.grn-item-row').length > 1) {
        $(this).closest('.grn-item-row').remove();
        recalculateSummary();
    }
});
</script>
@endsection
