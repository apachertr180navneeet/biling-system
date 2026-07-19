@extends('admin.layouts.app')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold">Sale #{{ $sale->sale_number }}</h4>
        <div>
            @if($sale->status == 'booking')
            <button class="btn btn-sm btn-info update-status" data-status="allotment">Allot Vehicle</button>
            @endif
            @if($sale->status == 'allotment')
            <button class="btn btn-sm btn-primary update-status" data-status="registration">Mark Registered</button>
            @endif
            @if($sale->status == 'registration')
            <button class="btn btn-sm btn-success update-status" data-status="delivery">Mark Delivered</button>
            @endif
            @if($sale->status == 'delivery')
            <button class="btn btn-sm btn-dark update-status" data-status="completed">Complete Sale</button>
            @endif
            @if(in_array($sale->status, ['booking','allotment']))
            <a href="{{ route('admin.invoices.create-vehicle') }}?customer_id={{ $sale->customer_id }}&vehicle_description={{ urlencode($sale->vehicle_description) }}&sale_id={{ $sale->id }}&sale_price={{ $sale->sale_price }}" class="btn btn-sm btn-warning">Generate Invoice</a>
            @endif
            <a href="{{ route('admin.sales.index') }}" class="btn btn-sm btn-secondary">Back</a>
        </div>
    </div>
    @include('admin.layouts.elements.sweet_alerts')
    <div class="card"><div class="card-body">
        <form method="POST" action="{{ route('admin.sales.update', $sale) }}">
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Customer</label>
                    <select name="customer_id" class="form-select">
                        @foreach($customers as $c)
                        <option value="{{ $c->id }}" {{ $sale->customer_id==$c->id ? 'selected':'' }}>{{ $c->first_name }} {{ $c->last_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Vehicle Description</label>
                    <select name="vehicle_description" id="vehicle_description" class="form-select">
                        <option value="">Select Vehicle</option>
                        @foreach($variants as $v)
                            @php
                                $vName = ($v->model->brand->name ?? '') . ' ' . ($v->model->name ?? '') . ' ' . $v->name;
                                $stock = $inventoryStock[$vName] ?? 0;
                            @endphp
                            <option value="{{ $vName }}" data-price="{{ $v->ex_showroom_price }}" data-stock="{{ $stock }}" {{ old('vehicle_description', $sale->vehicle_description) == $vName ? 'selected' : '' }}>
                                {{ $vName }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Sale Price</label>
                    <input type="number" step="0.01" name="sale_price" id="sale_price" class="form-control" value="{{ $sale->sale_price }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Booking Date</label>
                    <input type="date" name="booking_date" class="form-control" value="{{ $sale->booking_date->format('Y-m-d') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Booking Amount</label>
                    <input type="number" step="0.01" name="booking_amount" class="form-control" value="{{ $sale->booking_amount }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Allotment Date</label>
                    <input type="date" name="allotment_date" class="form-control" value="{{ $sale->allotment_date ? $sale->allotment_date->format('Y-m-d') : '' }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Registration Date</label>
                    <input type="date" name="registration_date" class="form-control" value="{{ $sale->registration_date ? $sale->registration_date->format('Y-m-d') : '' }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Reg Number (RTO)</label>
                    <input type="text" name="reg_number" class="form-control" value="{{ $sale->reg_number }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Delivery Date</label>
                    <input type="date" name="delivery_date" class="form-control" value="{{ $sale->delivery_date ? $sale->delivery_date->format('Y-m-d') : '' }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="booking" {{ $sale->status=='booking'?'selected':'' }}>Booking</option>
                        <option value="allotment" {{ $sale->status=='allotment'?'selected':'' }}>Allotment</option>
                        <option value="registration" {{ $sale->status=='registration'?'selected':'' }}>Registration</option>
                        <option value="delivery" {{ $sale->status=='delivery'?'selected':'' }}>Delivery</option>
                        <option value="completed" {{ $sale->status=='completed'?'selected':'' }}>Completed</option>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" class="form-control">{{ $sale->notes }}</textarea>
                </div>
            </div>
            <div class="mt-4"><button type="submit" class="btn btn-primary"><i class="bx bx-save"></i> Update Sale</button></div>
        </form>
    </div></div>
</div>

<form id="statusForm" method="POST">@csrf</form>
@section('script')
<script>
$(function(){
    $('.update-status').click(function(){
        if(!confirm('Change status to '+$(this).data('status')+'?')) return;
        var form=$('#statusForm');
        form.append('<input type="hidden" name="status" value="'+$(this).data('status')+'">');
        $.post('{{ route("admin.sales.update-status", $sale) }}', form.serialize()).done(function(r){
            if(r.success) location.reload();
        }).fail(function(){alert('Error updating status');});
    });

    $('#vehicle_description').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        if (!selectedOption.val()) return;
        
        const price = selectedOption.data('price');
        const stock = parseInt(selectedOption.data('stock') || 0);
        
        if (price !== undefined && price !== '') {
            $('#sale_price').val(price);
        }
        
        if (stock <= 0) {
            if (typeof setFlesh === 'function') {
                setFlesh('error', 'Stock not available for this vehicle.');
            } else {
                alert('Stock not available for this vehicle.');
            }
        }
    });
});
</script>
@endsection
@endsection
