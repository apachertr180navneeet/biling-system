@extends('admin.layouts.app')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Admin /</span> Vehicle Inventory
    </h4>
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="mb-0">Available Vehicles</h5>
            <a href="{{ route('admin.vehicle-purchase-orders.index') }}" class="btn btn-sm btn-primary">Vehicle POs</a>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Vehicle</th>
                        <th>Color</th>
                        <th>Year</th>
                        <th>Qty</th>
                        <th>Purchase Price</th>
                        <th>Status</th>
                        <th>PO Ref</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($inventories as $i)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $i->vehicle_description }}</td>
                        <td>{{ $i->color_name ?? '-' }}</td>
                        <td>{{ $i->mfg_year ?? '-' }}</td>
                        <td>{{ $i->quantity }}</td>
                        <td>{{ number_format($i->purchase_price, 2) }}</td>
                        <td>
                            @if($i->status == 'available')
                            <span class="badge bg-success">{{ $i->status }}</span>
                            @else
                            <span class="badge bg-secondary">{{ $i->status }}</span>
                            @endif
                        </td>
                        <td>
                            @if($i->purchaseOrder)
                            <a href="{{ route('admin.vehicle-purchase-orders.show', $i->purchaseOrder) }}">{{ $i->purchaseOrder->po_number }}</a>
                            @else
                            -
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center">No vehicles in inventory.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $inventories->links() }}</div>
    </div>
</div>
@endsection
