@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-slider"></i></div>
            <div>
                <h4 class="m-page-title">{{ isset($stockAdjustment) ? 'Edit Stock Adjustment' : 'Create Stock Adjustment' }}</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Inventory</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="{{ route('admin.inventory.stock-adjustments.index') }}">Stock Adjustments</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">{{ isset($stockAdjustment) ? 'Edit' : 'Create' }}</li>
                </ul>
            </div>
        </div>
        <a href="{{ route('admin.inventory.stock-adjustments.index') }}" class="btn btn-outline-secondary"><i class="bx bx-left-arrow-alt me-1"></i> Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ isset($stockAdjustment) ? route('admin.inventory.stock-adjustments.update', $stockAdjustment) : route('admin.inventory.stock-adjustments.store') }}" method="POST">
                @csrf
                @if(isset($stockAdjustment)) @method('PUT') @endif

                <div class="m-section-divider">Adjustment Details</div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Hotel <span class="text-danger">*</span></label>
                        <select name="hotel_id" class="form-select" required>
                            <option value="">Select Hotel</option>
                            @foreach($hotels as $hotel)
                            <option value="{{ $hotel->id }}" {{ old('hotel_id', $stockAdjustment?->hotel_id) == $hotel->id ? 'selected' : '' }}>{{ $hotel->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Item <span class="text-danger">*</span></label>
                        <select name="item_id" class="form-select" id="item_id" required>
                            <option value="">Select Item</option>
                            @foreach($items as $item)
                            <option value="{{ $item->id }}" {{ old('item_id', $stockAdjustment?->item_id) == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Adjustment Date <span class="text-danger">*</span></label>
                        <input type="date" name="adjustment_date" class="form-control" value="{{ old('adjustment_date', $stockAdjustment?->adjustment_date?->format('Y-m-d') ?? date('Y-m-d')) }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Adjustment Type <span class="text-danger">*</span></label>
                        <select name="adjustment_type" class="form-select" id="adjustment_type" required>
                            <option value="">Select Type</option>
                            <option value="addition" {{ old('adjustment_type', $stockAdjustment?->adjustment_type) == 'addition' ? 'selected' : '' }}>Addition</option>
                            <option value="subtraction" {{ old('adjustment_type', $stockAdjustment?->adjustment_type) == 'subtraction' ? 'selected' : '' }}>Subtraction</option>
                            <option value="damage" {{ old('adjustment_type', $stockAdjustment?->adjustment_type) == 'damage' ? 'selected' : '' }}>Damage</option>
                            <option value="expired" {{ old('adjustment_type', $stockAdjustment?->adjustment_type) == 'expired' ? 'selected' : '' }}>Expired</option>
                            <option value="theft" {{ old('adjustment_type', $stockAdjustment?->adjustment_type) == 'theft' ? 'selected' : '' }}>Theft</option>
                            <option value="correction" {{ old('adjustment_type', $stockAdjustment?->adjustment_type) == 'correction' ? 'selected' : '' }}>Correction</option>
                        </select>
                    </div>
                </div>

                <div class="m-section-divider">Stock Info</div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Quantity Before</label>
                        <input type="number" class="form-control" id="quantity_before" value="{{ old('quantity_before', $stockAdjustment?->quantity_before ?? 0) }}" readonly>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Adjustment Quantity <span class="text-danger">*</span></label>
                        <input type="number" name="adjustment_quantity" class="form-control" id="adjustment_quantity" value="{{ old('adjustment_quantity', $stockAdjustment?->adjustment_quantity) }}" min="0" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Quantity After</label>
                        <input type="number" class="form-control" id="quantity_after" value="{{ old('quantity_after', $stockAdjustment?->quantity_after ?? 0) }}" readonly>
                    </div>
                </div>

                <div class="m-section-divider">Value & Reason</div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Unit Cost</label>
                        <input type="number" name="unit_cost" class="form-control" id="unit_cost" value="{{ old('unit_cost', $stockAdjustment?->unit_cost) }}" min="0" step="0.01">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Total Value</label>
                        <input type="number" class="form-control" id="total_value" value="{{ old('total_value', $stockAdjustment?->total_value) }}" readonly step="0.01">
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Reason <span class="text-danger">*</span></label>
                        <textarea name="reason" class="form-control" rows="3" required>{{ old('reason', $stockAdjustment?->reason) }}</textarea>
                    </div>
                </div>

                <div class="m-form-actions mt-3">
                    <button type="submit" class="btn btn-primary"><i class="bx bx-check me-1"></i> {{ isset($stockAdjustment) ? 'Update' : 'Create' }} Adjustment</button>
                    <a href="{{ route('admin.inventory.stock-adjustments.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
function calculateQuantityAfter() {
    var quantityBefore = parseFloat($('#quantity_before').val()) || 0;
    var adjustmentQuantity = parseFloat($('#adjustment_quantity').val()) || 0;
    var adjustmentType = $('#adjustment_type').val();
    var quantityAfter = quantityBefore;

    if (adjustmentType === 'addition' || adjustmentType === 'correction') {
        quantityAfter = quantityBefore + adjustmentQuantity;
    } else if (adjustmentType === 'subtraction' || adjustmentType === 'damage' || adjustmentType === 'expired' || adjustmentType === 'theft') {
        quantityAfter = quantityBefore - adjustmentQuantity;
    }

    $('#quantity_after').val(quantityAfter);
}

function calculateTotalValue() {
    var adjustmentQuantity = parseFloat($('#adjustment_quantity').val()) || 0;
    var unitCost = parseFloat($('#unit_cost').val()) || 0;
    var totalValue = adjustmentQuantity * unitCost;
    $('#total_value').val(totalValue.toFixed(2));
}

$('#adjustment_quantity').on('input', function() {
    calculateQuantityAfter();
    calculateTotalValue();
});

$('#adjustment_type').on('change', function() {
    calculateQuantityAfter();
});

$('#unit_cost').on('input', function() {
    calculateTotalValue();
});
</script>
@endsection