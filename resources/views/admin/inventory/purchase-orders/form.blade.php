@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-cart"></i></div>
            <div>
                <h4 class="m-page-title">{{ isset($purchaseOrder) ? 'Edit Purchase Order' : 'Create Purchase Order' }}</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Inventory</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="{{ route('admin.inventory.purchase-orders.index') }}">Purchase Orders</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">{{ isset($purchaseOrder) ? 'Edit' : 'Create' }}</li>
                </ul>
            </div>
        </div>
        <a href="{{ route('admin.inventory.purchase-orders.index') }}" class="btn btn-outline-secondary"><i class="bx bx-left-arrow-alt me-1"></i> Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ isset($purchaseOrder) ? route('admin.inventory.purchase-orders.update', $purchaseOrder) : route('admin.inventory.purchase-orders.store') }}" method="POST">
                @csrf
                @if(isset($purchaseOrder)) @method('PUT') @endif

                <div class="m-section-divider">PO Details</div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Hotel <span class="text-danger">*</span></label>
                        <select name="hotel_id" class="form-select" required>
                            <option value="">Select Hotel</option>
                            @foreach($hotels as $hotel)
                            <option value="{{ $hotel->id }}" {{ old('hotel_id', $purchaseOrder?->hotel_id) == $hotel->id ? 'selected' : '' }}>{{ $hotel->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Supplier <span class="text-danger">*</span></label>
                        <select name="supplier_id" class="form-select" required>
                            <option value="">Select Supplier</option>
                            @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ old('supplier_id', $purchaseOrder?->supplier_id) == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">PO Date <span class="text-danger">*</span></label>
                        <input type="date" name="po_date" class="form-control" value="{{ old('po_date', $purchaseOrder?->po_date?->format('Y-m-d') ?? date('Y-m-d')) }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Expected Delivery Date</label>
                        <input type="date" name="expected_delivery_date" class="form-control" value="{{ old('expected_delivery_date', $purchaseOrder?->expected_delivery_date?->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="3">{{ old('notes', $purchaseOrder?->notes) }}</textarea>
                    </div>
                </div>

                <div class="m-section-divider">PO Items</div>
                <div id="po-items-container">
                    @if(isset($purchaseOrder) && $purchaseOrder->items->count())
                    @foreach($purchaseOrder->items as $index => $item)
                    <div class="row po-item-row mb-2">
                        <div class="col-md-4">
                            <select name="items[{{ $index }}][item_id]" class="form-select item-select" required>
                                <option value="">Select Item</option>
                                @foreach($items as $itm)
                                <option value="{{ $itm->id }}" data-price="{{ $itm->cost_price }}" {{ $item->item_id == $itm->id ? 'selected' : '' }}>{{ $itm->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="number" name="items[{{ $index }}][quantity]" class="form-control item-quantity" placeholder="Qty" value="{{ $item->quantity }}" min="1" required>
                        </div>
                        <div class="col-md-2">
                            <input type="number" name="items[{{ $index }}][unit_cost]" class="form-control item-unit-cost" placeholder="Unit Cost" value="{{ $item->unit_cost }}" min="0" step="0.01" required>
                        </div>
                        <div class="col-md-2">
                            <input type="number" name="items[{{ $index }}][total_cost]" class="form-control item-total-cost" placeholder="Total" value="{{ $item->total_cost }}" readonly>
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-outline-danger btn-sm remove-item"><i class="bx bx-trash"></i></button>
                        </div>
                    </div>
                    @endforeach
                    @else
                    <div class="row po-item-row mb-2">
                        <div class="col-md-4">
                            <select name="items[0][item_id]" class="form-select item-select" required>
                                <option value="">Select Item</option>
                                @foreach($items as $itm)
                                <option value="{{ $itm->id }}" data-price="{{ $itm->cost_price }}">{{ $itm->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="number" name="items[0][quantity]" class="form-control item-quantity" placeholder="Qty" min="1" required>
                        </div>
                        <div class="col-md-2">
                            <input type="number" name="items[0][unit_cost]" class="form-control item-unit-cost" placeholder="Unit Cost" min="0" step="0.01" required>
                        </div>
                        <div class="col-md-2">
                            <input type="number" name="items[0][total_cost]" class="form-control item-total-cost" placeholder="Total" readonly>
                        </div>
                        <div class="col-md-2">
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
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Subtotal</label>
                        <input type="number" name="subtotal" id="subtotal" class="form-control" value="{{ old('subtotal', $purchaseOrder?->subtotal ?? 0) }}" min="0" step="0.01" readonly>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Tax Amount</label>
                        <input type="number" name="tax_amount" id="tax_amount" class="form-control" value="{{ old('tax_amount', $purchaseOrder?->tax_amount ?? 0) }}" min="0" step="0.01">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Discount Amount</label>
                        <input type="number" name="discount_amount" id="discount_amount" class="form-control" value="{{ old('discount_amount', $purchaseOrder?->discount_amount ?? 0) }}" min="0" step="0.01">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Total Amount</label>
                        <input type="number" name="total_amount" id="total_amount" class="form-control" value="{{ old('total_amount', $purchaseOrder?->total_amount ?? 0) }}" min="0" step="0.01" readonly>
                    </div>
                </div>

                <div class="m-form-actions mt-3">
                    <button type="submit" class="btn btn-primary"><i class="bx bx-check me-1"></i> {{ isset($purchaseOrder) ? 'Update' : 'Create' }} Purchase Order</button>
                    <a href="{{ route('admin.inventory.purchase-orders.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
var itemIndex = {{ isset($purchaseOrder) ? $purchaseOrder->items->count() : 1 }};

function recalculateRow(row) {
    var qty = parseFloat(row.find('.item-quantity').val()) || 0;
    var cost = parseFloat(row.find('.item-unit-cost').val()) || 0;
    row.find('.item-total-cost').val((qty * cost).toFixed(2));
}

function recalculateSummary() {
    var subtotal = 0;
    $('.po-item-row').each(function() {
        subtotal += parseFloat($(this).find('.item-total-cost').val()) || 0;
    });
    var tax = parseFloat($('#tax_amount').val()) || 0;
    var discount = parseFloat($('#discount_amount').val()) || 0;
    $('#subtotal').val(subtotal.toFixed(2));
    $('#total_amount').val((subtotal + tax - discount).toFixed(2));
}

$(document).on('change', '.item-select', function() {
    var selectedOption = $(this).find(':selected');
    var price = selectedOption.data('price');
    if (price) {
        $(this).closest('.po-item-row').find('.item-unit-cost').val(price);
        recalculateRow($(this).closest('.po-item-row'));
        recalculateSummary();
    }
});

$(document).on('input', '.item-quantity, .item-unit-cost', function() {
    recalculateRow($(this).closest('.po-item-row'));
    recalculateSummary();
});

$('#tax_amount, #discount_amount').on('input', function() {
    recalculateSummary();
});

$('#add-item-btn').on('click', function() {
    var optionsHtml = '<option value="">Select Item</option>';
    @foreach($items as $itm)
    optionsHtml += '<option value="{{ $itm->id }}" data-price="{{ $itm->cost_price }}">{{ $itm->name }}</option>';
    @endforeach

    var html = '<div class="row po-item-row mb-2">' +
        '<div class="col-md-4"><select name="items[' + itemIndex + '][item_id]" class="form-select item-select" required>' + optionsHtml + '</select></div>' +
        '<div class="col-md-2"><input type="number" name="items[' + itemIndex + '][quantity]" class="form-control item-quantity" placeholder="Qty" min="1" required></div>' +
        '<div class="col-md-2"><input type="number" name="items[' + itemIndex + '][unit_cost]" class="form-control item-unit-cost" placeholder="Unit Cost" min="0" step="0.01" required></div>' +
        '<div class="col-md-2"><input type="number" name="items[' + itemIndex + '][total_cost]" class="form-control item-total-cost" placeholder="Total" readonly></div>' +
        '<div class="col-md-2"><button type="button" class="btn btn-outline-danger btn-sm remove-item"><i class="bx bx-trash"></i></button></div>' +
        '</div>';

    $('#po-items-container').append(html);
    itemIndex++;
});

$(document).on('click', '.remove-item', function() {
    if ($('.po-item-row').length > 1) {
        $(this).closest('.po-item-row').remove();
        recalculateSummary();
    }
});
</script>
@endsection
