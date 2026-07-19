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
            <a href="{{ route('admin.sales.edit', $sale) }}" class="btn btn-sm btn-primary"><i class="bx bx-edit"></i> Edit</a>
            <a href="{{ route('admin.sales.index') }}" class="btn btn-sm btn-secondary">Back</a>
        </div>
    </div>

    <div class="card"><div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h5>Customer Details</h5>
                <p><strong>Name:</strong> {{ $sale->customer->first_name ?? '' }} {{ $sale->customer->last_name ?? '' }}<br>
                <strong>Mobile:</strong> {{ $sale->customer->phone ?? '-' }}<br>
                <strong>Address:</strong> {{ $sale->customer->address ?? '-' }}</p>
            </div>
            <div class="col-md-6">
                <h5>Vehicle Details</h5>
                <p><strong>Vehicle:</strong> {{ $sale->vehicle_description ?? '-' }}<br>
                <strong>Sale Price:</strong> {{ number_format($sale->sale_price, 2) }}<br>
                <strong>Reg Number:</strong> {{ $sale->reg_number ?? '-' }}</p>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-12">
                <h5>Lifecycle</h5>
                <table class="table table-bordered">
                    <tr>
                        <th>Step</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                    <tr class="{{ $sale->status == 'booking' || in_array($sale->status, ['allotment','registration','delivery','completed']) ? 'table-success' : '' }}">
                        <td>Booking</td>
                        <td><span class="badge bg-success">Completed</span></td>
                        <td>{{ $sale->booking_date->format('d-m-Y') }}</td>
                    </tr>
                    <tr class="{{ $sale->status == 'allotment' ? 'table-success' : ($sale->status == 'booking' ? '' : 'table-success') }}">
                        <td>Allotment</td>
                        <td>
                            @if(in_array($sale->status, ['allotment','registration','delivery','completed']))
                            <span class="badge bg-success">Completed</span>
                            @elseif($sale->status == 'booking')
                            <span class="badge bg-warning">Pending</span>
                            @endif
                        </td>
                        <td>{{ $sale->allotment_date ? $sale->allotment_date->format('d-m-Y') : '-' }}</td>
                    </tr>
                    <tr class="{{ $sale->status == 'registration' ? 'table-success' : (in_array($sale->status, ['delivery','completed']) ? 'table-success' : '') }}">
                        <td>Registration</td>
                        <td>
                            @if(in_array($sale->status, ['registration','delivery','completed']))
                            <span class="badge bg-success">Completed</span>
                            @elseif($sale->status == 'booking' || $sale->status == 'allotment')
                            <span class="badge bg-warning">Pending</span>
                            @endif
                        </td>
                        <td>{{ $sale->registration_date ? $sale->registration_date->format('d-m-Y') : '-' }}</td>
                    </tr>
                    <tr class="{{ $sale->status == 'delivery' || $sale->status == 'completed' ? 'table-success' : '' }}">
                        <td>Delivery</td>
                        <td>
                            @if(in_array($sale->status, ['delivery','completed']))
                            <span class="badge bg-success">Completed</span>
                            @else
                            <span class="badge bg-warning">Pending</span>
                            @endif
                        </td>
                        <td>{{ $sale->delivery_date ? $sale->delivery_date->format('d-m-Y') : '-' }}</td>
                    </tr>
                    <tr class="{{ $sale->status == 'completed' ? 'table-success' : '' }}">
                        <td>Completed</td>
                        <td>
                            @if($sale->status == 'completed')
                            <span class="badge bg-success">Completed</span>
                            @else
                            <span class="badge bg-warning">Pending</span>
                            @endif
                        </td>
                        <td>{{ $sale->status == 'completed' ? $sale->delivery_date->format('d-m-Y') : '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>
        @if($sale->notes)
        <div class="row">
            <div class="col-12"><h5>Notes</h5><p>{{ $sale->notes }}</p></div>
        </div>
        @endif
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
});
</script>
@endsection
@endsection
