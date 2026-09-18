@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-receipt"></i></div>
            <div>
                <h4 class="m-page-title">{{ isset($order) ? 'Edit Order' : 'New Order' }}</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Restaurant POS</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="{{ route('admin.restaurant.orders.index') }}">Orders</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">{{ isset($order) ? 'Edit' : 'Create' }}</li>
                </ul>
            </div>
        </div>
        <a href="{{ route('admin.restaurant.orders.index') }}" class="btn btn-outline-secondary"><i class="bx bx-left-arrow-alt me-1"></i> Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ isset($order) ? route('admin.restaurant.orders.update', $order) : route('admin.restaurant.orders.store') }}" method="POST">
                @csrf
                @if(isset($order)) @method('PUT') @endif

                <div class="m-section-divider">Order Details</div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Hotel <span class="text-danger">*</span></label>
                        <select name="hotel_id" class="form-select" id="hotel_id" required>
                            <option value="">Select Hotel</option>
                            @foreach($hotels as $hotel)
                            <option value="{{ $hotel->id }}" {{ old('hotel_id', $order?->hotel_id) == $hotel->id ? 'selected' : '' }}>{{ $hotel->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Order Type <span class="text-danger">*</span></label>
                        <select name="order_type" class="form-select" required>
                            <option value="dine_in" {{ old('order_type', $order?->order_type ?? 'dine_in') == 'dine_in' ? 'selected' : '' }}>Dine In</option>
                            <option value="takeaway" {{ old('order_type', $order?->order_type) == 'takeaway' ? 'selected' : '' }}>Takeaway</option>
                            <option value="room_service" {{ old('order_type', $order?->order_type) == 'room_service' ? 'selected' : '' }}>Room Service</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Table</label>
                        <select name="restaurant_table_id" class="form-select">
                            <option value="">Select Table</option>
                            @foreach($tables as $table)
                            <option value="{{ $table->id }}" {{ old('restaurant_table_id', $order?->restaurant_table_id) == $table->id ? 'selected' : '' }}>{{ $table->table_number }} ({{ $table->capacity }} seats)</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Guest Name</label>
                        <input type="text" name="guest_name" class="form-control" value="{{ old('guest_name', $order?->guest_name) }}">
                    </div>
                </div>

                <div class="m-section-divider">Order Items (KOT)</div>
                <div id="order-items-container">
                    @if(isset($order) && $order->items->count())
                    @foreach($order->items as $index => $item)
                    <div class="row order-item-row mb-2">
                        <div class="col-md-4">
                            <select name="items[{{ $index }}][restaurant_menu_item_id]" class="form-select item-select" required>
                                <option value="">Select Item</option>
                                @foreach($menuItems as $mi)
                                <option value="{{ $mi->id }}" data-price="{{ $mi->price }}" {{ $item->restaurant_menu_item_id == $mi->id ? 'selected' : '' }}>{{ $mi->name }} - {{ number_format($mi->price, 2) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="number" name="items[{{ $index }}][quantity]" class="form-control item-qty" placeholder="Qty" value="{{ $item->quantity }}" min="1" required>
                        </div>
                        <div class="col-md-2">
                            <input type="number" name="items[{{ $index }}][unit_price]" class="form-control item-price" placeholder="Price" value="{{ $item->unit_price }}" min="0" step="0.01" required>
                        </div>
                        <div class="col-md-3">
                            <input type="text" name="items[{{ $index }}][special_instructions]" class="form-control" placeholder="Special instructions" value="{{ $item->special_instructions }}">
                        </div>
                        <div class="col-md-1">
                            <button type="button" class="btn btn-outline-danger btn-sm remove-item"><i class="bx bx-trash"></i></button>
                        </div>
                    </div>
                    @endforeach
                    @else
                    <div class="row order-item-row mb-2">
                        <div class="col-md-4">
                            <select name="items[0][restaurant_menu_item_id]" class="form-select item-select" required>
                                <option value="">Select Item</option>
                                @foreach($menuItems as $mi)
                                <option value="{{ $mi->id }}" data-price="{{ $mi->price }}">{{ $mi->name }} - {{ number_format($mi->price, 2) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="number" name="items[0][quantity]" class="form-control item-qty" placeholder="Qty" min="1" required>
                        </div>
                        <div class="col-md-2">
                            <input type="number" name="items[0][unit_price]" class="form-control item-price" placeholder="Price" min="0" step="0.01" required>
                        </div>
                        <div class="col-md-3">
                            <input type="text" name="items[0][special_instructions]" class="form-control" placeholder="Special instructions">
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

                <div class="m-section-divider">Payment</div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Total Amount <span class="text-danger">*</span></label>
                        <input type="number" name="total_amount" class="form-control" id="total_amount" value="{{ old('total_amount', $order?->total_amount ?? 0) }}" min="0" step="0.01" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Tax Amount</label>
                        <input type="number" name="tax_amount" class="form-control" value="{{ old('tax_amount', $order?->tax_amount ?? 0) }}" min="0" step="0.01">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Discount Amount</label>
                        <input type="number" name="discount_amount" class="form-control" value="{{ old('discount_amount', $order?->discount_amount ?? 0) }}" min="0" step="0.01">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Net Amount <span class="text-danger">*</span></label>
                        <input type="number" name="net_amount" class="form-control" id="net_amount" value="{{ old('net_amount', $order?->net_amount ?? 0) }}" min="0" step="0.01" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Payment Method</label>
                        <select name="payment_method" class="form-select">
                            <option value="">Select</option>
                            <option value="cash" {{ old('payment_method', $order?->payment_method) == 'cash' ? 'selected' : '' }}>Cash</option>
                            <option value="card" {{ old('payment_method', $order?->payment_method) == 'card' ? 'selected' : '' }}>Card</option>
                            <option value="upi" {{ old('payment_method', $order?->payment_method) == 'upi' ? 'selected' : '' }}>UPI</option>
                            <option value="room_charge" {{ old('payment_method', $order?->payment_method) == 'room_charge' ? 'selected' : '' }}>Room Charge</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Payment Status <span class="text-danger">*</span></label>
                        <select name="payment_status" class="form-select" required>
                            <option value="pending" {{ old('payment_status', $order?->payment_status ?? 'pending') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="paid" {{ old('payment_status', $order?->payment_status) == 'paid' ? 'selected' : '' }}>Paid</option>
                            <option value="partially_paid" {{ old('payment_status', $order?->payment_status) == 'partially_paid' ? 'selected' : '' }}>Partially Paid</option>
                        </select>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="2">{{ old('notes', $order?->notes) }}</textarea>
                    </div>
                </div>

                <div class="m-form-actions mt-3">
                    <button type="submit" class="btn btn-primary"><i class="bx bx-check me-1"></i> {{ isset($order) ? 'Update' : 'Create' }} Order</button>
                    <a href="{{ route('admin.restaurant.orders.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
var itemIndex = {{ isset($order) ? $order->items->count() : 1 }};

$('#add-item-btn').on('click', function() {
    var optionsHtml = '<option value="">Select Item</option>';
    @foreach($menuItems as $mi)
    optionsHtml += '<option value="{{ $mi->id }}" data-price="{{ $mi->price }}">{{ $mi->name }} - {{ number_format($mi->price, 2) }}</option>';
    @endforeach

    var html = '<div class="row order-item-row mb-2">' +
        '<div class="col-md-4"><select name="items[' + itemIndex + '][restaurant_menu_item_id]" class="form-select item-select" required>' + optionsHtml + '</select></div>' +
        '<div class="col-md-2"><input type="number" name="items[' + itemIndex + '][quantity]" class="form-control item-qty" placeholder="Qty" min="1" required></div>' +
        '<div class="col-md-2"><input type="number" name="items[' + itemIndex + '][unit_price]" class="form-control item-price" placeholder="Price" min="0" step="0.01" required></div>' +
        '<div class="col-md-3"><input type="text" name="items[' + itemIndex + '][special_instructions]" class="form-control" placeholder="Special instructions"></div>' +
        '<div class="col-md-1"><button type="button" class="btn btn-outline-danger btn-sm remove-item"><i class="bx bx-trash"></i></button></div>' +
        '</div>';

    $('#order-items-container').append(html);
    itemIndex++;
});

$(document).on('click', '.remove-item', function() {
    if ($('.order-item-row').length > 1) {
        $(this).closest('.order-item-row').remove();
        calculateTotal();
    }
});

$(document).on('change', '.item-select', function() {
    var price = $(this).find(':selected').data('price');
    $(this).closest('.order-item-row').find('.item-price').val(price);
    calculateTotal();
});

$(document).on('input', '.item-qty, .item-price', function() {
    calculateTotal();
});

function calculateTotal() {
    var total = 0;
    $('.order-item-row').each(function() {
        var qty = parseFloat($(this).find('.item-qty').val()) || 0;
        var price = parseFloat($(this).find('.item-price').val()) || 0;
        total += qty * price;
    });
    $('#total_amount').val(total.toFixed(2));
    var tax = parseFloat($('input[name="tax_amount"]').val()) || 0;
    var discount = parseFloat($('input[name="discount_amount"]').val()) || 0;
    $('#net_amount').val((total + tax - discount).toFixed(2));
}
</script>
@endsection
