@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-bed"></i></div>
            <div>
                <h4 class="m-page-title">{{ isset($charge) ? 'Edit Room Service Charge' : 'Post Room Service to Folio' }}</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Restaurant POS</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="{{ route('admin.restaurant.room-service.index') }}">Room Service</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">{{ isset($charge) ? 'Edit' : 'Post' }}</li>
                </ul>
            </div>
        </div>
        <a href="{{ route('admin.restaurant.room-service.index') }}" class="btn btn-outline-secondary"><i class="bx bx-left-arrow-alt me-1"></i> Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ isset($charge) ? route('admin.restaurant.room-service.update', $charge) : route('admin.restaurant.room-service.store') }}" method="POST">
                @csrf
                @if(isset($charge)) @method('PUT') @endif

                <div class="m-section-divider">Charge Details</div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Hotel <span class="text-danger">*</span></label>
                        <select name="hotel_id" class="form-select" id="hotel_id" required>
                            <option value="">Select Hotel</option>
                            @foreach($hotels as $hotel)
                            <option value="{{ $hotel->id }}" {{ old('hotel_id', $charge?->hotel_id) == $hotel->id ? 'selected' : '' }}>{{ $hotel->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Reservation <span class="text-danger">*</span></label>
                        <select name="reservation_id" class="form-select" id="reservation_id" required>
                            <option value="">Select Reservation</option>
                            @foreach($reservations as $res)
                            <option value="{{ $res->id }}" data-hotel="{{ $res->hotel_id }}" {{ old('reservation_id', $charge?->reservation_id) == $res->id ? 'selected' : '' }}>{{ $res->reservation_number }} - {{ $res->guest->full_name ?? '' }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Restaurant Order</label>
                        <select name="restaurant_order_id" class="form-select" id="restaurant_order_id">
                            <option value="">Select Order (Optional)</option>
                            @foreach($orders as $order)
                            <option value="{{ $order->id }}" {{ old('restaurant_order_id', $charge?->restaurant_order_id) == $order->id ? 'selected' : '' }}>{{ $order->order_number }} - {{ number_format($order->net_amount, 2) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Amount <span class="text-danger">*</span></label>
                        <input type="number" name="amount" class="form-control" id="amount" value="{{ old('amount', $charge?->amount) }}" min="0" step="0.01" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Tax Amount</label>
                        <input type="number" name="tax_amount" class="form-control" id="tax_amount" value="{{ old('tax_amount', $charge?->tax_amount ?? 0) }}" min="0" step="0.01">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Total Amount <span class="text-danger">*</span></label>
                        <input type="number" name="total_amount" class="form-control" id="total_amount" value="{{ old('total_amount', $charge?->total_amount) }}" min="0" step="0.01" required>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="3">{{ old('notes', $charge?->notes) }}</textarea>
                    </div>
                </div>

                <div class="m-form-actions mt-3">
                    <button type="submit" class="btn btn-primary"><i class="bx bx-check me-1"></i> {{ isset($charge) ? 'Update' : 'Post' }} Charge</button>
                    <a href="{{ route('admin.restaurant.room-service.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
$('#amount, #tax_amount').on('input', function() {
    var amount = parseFloat($('#amount').val()) || 0;
    var tax = parseFloat($('#tax_amount').val()) || 0;
    $('#total_amount').val((amount + tax).toFixed(2));
});
</script>
@endsection
