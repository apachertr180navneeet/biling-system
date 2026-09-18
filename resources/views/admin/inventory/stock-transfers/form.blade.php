@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-transfer"></i></div>
            <div>
                <h4 class="m-page-title">{{ isset($stockTransfer) ? 'Edit Stock Transfer' : 'Create Stock Transfer' }}</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Inventory</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="{{ route('admin.inventory.stock-transfers.index') }}">Stock Transfers</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">{{ isset($stockTransfer) ? 'Edit' : 'Create' }}</li>
                </ul>
            </div>
        </div>
        <a href="{{ route('admin.inventory.stock-transfers.index') }}" class="btn btn-outline-secondary"><i class="bx bx-left-arrow-alt me-1"></i> Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ isset($stockTransfer) ? route('admin.inventory.stock-transfers.update', $stockTransfer) : route('admin.inventory.stock-transfers.store') }}" method="POST">
                @csrf
                @if(isset($stockTransfer)) @method('PUT') @endif

                <div class="m-section-divider">Transfer Details</div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Hotel <span class="text-danger">*</span></label>
                        <select name="hotel_id" class="form-select" required>
                            <option value="">Select Hotel</option>
                            @foreach($hotels as $hotel)
                            <option value="{{ $hotel->id }}" {{ old('hotel_id', $stockTransfer?->hotel_id) == $hotel->id ? 'selected' : '' }}>{{ $hotel->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Transfer Date <span class="text-danger">*</span></label>
                        <input type="date" name="transfer_date" class="form-control" value="{{ old('transfer_date', $stockTransfer?->transfer_date?->format('Y-m-d') ?? date('Y-m-d')) }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">From Location <span class="text-danger">*</span></label>
                        <input type="text" name="from_location" class="form-control" value="{{ old('from_location', $stockTransfer?->from_location) }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">To Location <span class="text-danger">*</span></label>
                        <input type="text" name="to_location" class="form-control" value="{{ old('to_location', $stockTransfer?->to_location) }}" required>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="3">{{ old('notes', $stockTransfer?->notes) }}</textarea>
                    </div>
                </div>

                <div class="m-section-divider">Transfer Items</div>
                <div id="transfer-items-container">
                    @if(isset($stockTransfer) && $stockTransfer->items->count())
                    @foreach($stockTransfer->items as $index => $item)
                    <div class="row transfer-item-row mb-2">
                        <div class="col-md-6">
                            <select name="items[{{ $index }}][item_id]" class="form-select" required>
                                <option value="">Select Item</option>
                                @foreach($items as $itemOption)
                                <option value="{{ $itemOption->id }}" {{ $item->item_id == $itemOption->id ? 'selected' : '' }}>{{ $itemOption->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input type="number" name="items[{{ $index }}][quantity_sent]" class="form-control" placeholder="Quantity" value="{{ $item->quantity_sent }}" min="1" required>
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-outline-danger btn-sm remove-item"><i class="bx bx-trash"></i></button>
                        </div>
                    </div>
                    @endforeach
                    @else
                    <div class="row transfer-item-row mb-2">
                        <div class="col-md-6">
                            <select name="items[0][item_id]" class="form-select" required>
                                <option value="">Select Item</option>
                                @foreach($items as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input type="number" name="items[0][quantity_sent]" class="form-control" placeholder="Quantity" min="1" required>
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

                <div class="m-form-actions mt-3">
                    <button type="submit" class="btn btn-primary"><i class="bx bx-check me-1"></i> {{ isset($stockTransfer) ? 'Update' : 'Create' }} Transfer</button>
                    <a href="{{ route('admin.inventory.stock-transfers.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
var itemIndex = {{ isset($stockTransfer) ? $stockTransfer->items->count() : 1 }};

$('#add-item-btn').on('click', function() {
    var optionsHtml = '<option value="">Select Item</option>';
    @foreach($items as $item)
    optionsHtml += '<option value="{{ $item->id }}">{{ $item->name }}</option>';
    @endforeach

    var html = '<div class="row transfer-item-row mb-2">' +
        '<div class="col-md-6"><select name="items[' + itemIndex + '][item_id]" class="form-select" required>' + optionsHtml + '</select></div>' +
        '<div class="col-md-4"><input type="number" name="items[' + itemIndex + '][quantity_sent]" class="form-control" placeholder="Quantity" min="1" required></div>' +
        '<div class="col-md-2"><button type="button" class="btn btn-outline-danger btn-sm remove-item"><i class="bx bx-trash"></i></button></div>' +
        '</div>';

    $('#transfer-items-container').append(html);
    itemIndex++;
});

$(document).on('click', '.remove-item', function() {
    if ($('.transfer-item-row').length > 1) {
        $(this).closest('.transfer-item-row').remove();
    }
});
</script>
@endsection