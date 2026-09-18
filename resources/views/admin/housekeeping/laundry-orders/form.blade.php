@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-package"></i></div>
            <div>
                <h4 class="m-page-title">{{ isset($laundryOrder) ? 'Edit Laundry Order' : 'Create Laundry Order' }}</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Housekeeping</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="{{ route('admin.housekeeping.laundry-orders.index') }}">Laundry Orders</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">{{ isset($laundryOrder) ? 'Edit' : 'Create' }}</li>
                </ul>
            </div>
        </div>
        <a href="{{ route('admin.housekeeping.laundry-orders.index') }}" class="btn btn-outline-secondary"><i class="bx bx-left-arrow-alt me-1"></i> Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ isset($laundryOrder) ? route('admin.housekeeping.laundry-orders.update', $laundryOrder) : route('admin.housekeeping.laundry-orders.store') }}" method="POST">
                @csrf
                @if(isset($laundryOrder)) @method('PUT') @endif

                <div class="m-section-divider">Order Details</div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Hotel <span class="text-danger">*</span></label>
                        <select name="hotel_id" class="form-select" required>
                            <option value="">Select Hotel</option>
                            @foreach($hotels as $hotel)
                            <option value="{{ $hotel->id }}" {{ old('hotel_id', $laundryOrder?->hotel_id) == $hotel->id ? 'selected' : '' }}>{{ $hotel->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Order Date <span class="text-danger">*</span></label>
                        <input type="date" name="order_date" class="form-control" value="{{ old('order_date', $laundryOrder?->order_date?->format('Y-m-d') ?? date('Y-m-d')) }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Expected Return Date</label>
                        <input type="date" name="expected_return_date" class="form-control" value="{{ old('expected_return_date', $laundryOrder?->expected_return_date?->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Vendor Name</label>
                        <input type="text" name="vendor_name" class="form-control" value="{{ old('vendor_name', $laundryOrder?->vendor_name) }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Total Items <span class="text-danger">*</span></label>
                        <input type="number" name="total_items" class="form-control" value="{{ old('total_items', $laundryOrder?->total_items ?? 0) }}" min="0" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Total Weight (kg)</label>
                        <input type="number" name="total_weight" class="form-control" value="{{ old('total_weight', $laundryOrder?->total_weight) }}" min="0" step="0.01">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Total Cost</label>
                        <input type="number" name="total_cost" class="form-control" value="{{ old('total_cost', $laundryOrder?->total_cost) }}" min="0" step="0.01">
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="3">{{ old('notes', $laundryOrder?->notes) }}</textarea>
                    </div>
                </div>

                <div class="m-section-divider">Order Items</div>
                <div id="order-items-container">
                    @if(isset($laundryOrder) && $laundryOrder->items->count())
                    @foreach($laundryOrder->items as $index => $item)
                    <div class="row order-item-row mb-2">
                        <div class="col-md-5">
                            <select name="items[{{ $index }}][laundry_item_id]" class="form-select" required>
                                <option value="">Select Item</option>
                                @foreach($laundryItems as $li)
                                <option value="{{ $li->id }}" {{ $item->pivot->laundry_item_id == $li->id ? 'selected' : '' }}>{{ $li->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="number" name="items[{{ $index }}][quantity_sent]" class="form-control" placeholder="Quantity" value="{{ $item->pivot->quantity_sent }}" min="1" required>
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-outline-danger btn-sm remove-item"><i class="bx bx-trash"></i></button>
                        </div>
                    </div>
                    @endforeach
                    @else
                    <div class="row order-item-row mb-2">
                        <div class="col-md-5">
                            <select name="items[0][laundry_item_id]" class="form-select" required>
                                <option value="">Select Item</option>
                                @foreach($laundryItems as $li)
                                <option value="{{ $li->id }}">{{ $li->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
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
                    <button type="submit" class="btn btn-primary"><i class="bx bx-check me-1"></i> {{ isset($laundryOrder) ? 'Update' : 'Create' }} Order</button>
                    <a href="{{ route('admin.housekeeping.laundry-orders.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
var itemIndex = {{ isset($laundryOrder) ? $laundryOrder->items->count() : 1 }};

$('#add-item-btn').on('click', function() {
    var optionsHtml = '<option value="">Select Item</option>';
    @foreach($laundryItems as $li)
    optionsHtml += '<option value="{{ $li->id }}">{{ $li->name }}</option>';
    @endforeach

    var html = '<div class="row order-item-row mb-2">' +
        '<div class="col-md-5"><select name="items[' + itemIndex + '][laundry_item_id]" class="form-select" required>' + optionsHtml + '</select></div>' +
        '<div class="col-md-3"><input type="number" name="items[' + itemIndex + '][quantity_sent]" class="form-control" placeholder="Quantity" min="1" required></div>' +
        '<div class="col-md-2"><button type="button" class="btn btn-outline-danger btn-sm remove-item"><i class="bx bx-trash"></i></button></div>' +
        '</div>';

    $('#order-items-container').append(html);
    itemIndex++;
});

$(document).on('click', '.remove-item', function() {
    if ($('.order-item-row').length > 1) {
        $(this).closest('.order-item-row').remove();
    }
});
</script>
@endsection
