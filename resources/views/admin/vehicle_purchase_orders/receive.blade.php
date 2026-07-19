@extends('admin.layouts.app')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold mb-4">Receive Items - {{ $vehiclePurchaseOrder->po_number }}</h4>
        <div class="card"><div class="card-body">
        <p><strong>Supplier:</strong> {{ $vehiclePurchaseOrder->supplier->name ?? '-' }}</p>
        <p><strong>Order Date:</strong> {{ $vehiclePurchaseOrder->order_date->format('d-m-Y') }}</p>
        <hr>
        <form method="POST" action="{{ route('admin.vehicle-purchase-orders.receive-store', $vehiclePurchaseOrder) }}">
            @csrf
            <table class="table table-bordered">
                <thead><tr><th>#</th><th>Vehicle</th><th>Ordered Qty</th><th>Previously Received</th><th>Receive Qty</th></tr></thead>
                <tbody>
                    @foreach($vehiclePurchaseOrder->items as $i => $item)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $item->vehicle_description }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ $item->received_quantity }}</td>
                        <td>
                            <input type="hidden" name="items[{{ $i }}][id]" value="{{ $item->id }}">
                            <input type="number" name="items[{{ $i }}][received_qty]" class="form-control" value="{{ $item->quantity - $item->received_quantity }}" min="0" max="{{ $item->quantity }}">
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="mt-3">
                <button type="submit" class="btn btn-primary"><i class="bx bx-check"></i> Receive Items</button>
                <a href="{{ route('admin.vehicle-purchase-orders.show', $vehiclePurchaseOrder) }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div></div>
</div>
@endsection
