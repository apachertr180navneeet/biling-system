@extends('admin.layouts.app')
@section('style')
@media print {
    .no-print { display: none !important; }
    .card { box-shadow: none !important; border: none !important; }
}
</style>
@endsection
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="no-print mb-3">
        <a href="{{ route('admin.invoices.index') }}" class="btn btn-secondary">Back</a>
        <button onclick="window.print()" class="btn btn-primary">Print</button>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="text-center mb-4">
                <h3>{{ config('app.name') }}</h3>
                <h5>{{ $invoice->is_gst ? 'TAX INVOICE' : 'RETAIL INVOICE / CASH MEMO' }}</h5>
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <strong>Invoice No:</strong> {{ $invoice->invoice_number }}<br>
                    <strong>Date:</strong> {{ $invoice->invoice_date->format('d-m-Y') }}<br>
                    <strong>Type:</strong> {{ ucfirst($invoice->invoice_type) }} Sale
                </div>
                <div class="col-md-6 text-end">
                    <strong>Customer:</strong> {{ $invoice->customer->first_name ?? '' }} {{ $invoice->customer->last_name ?? '' }}<br>
                    <strong>Phone:</strong> {{ $invoice->customer->phone ?? '-' }}<br>
                    <strong>Address:</strong> {{ $invoice->customer->address ?? '-' }}, {{ $invoice->customer->state ?? '-' }}<br>
                    @if($invoice->customer->gstin)<strong>GSTIN:</strong> {{ $invoice->customer->gstin }}@endif
                </div>
            </div>

            @if($invoice->invoice_type == 'vehicle')
            <h5>Vehicle Details</h5>
            <table class="table table-bordered mb-4">
                <tr>
                    <td><strong>Vehicle</strong></td>
                    <td>{{ $invoice->vehicle_description }}</td>
                </tr>
                <tr>
                    <td><strong>Chassis No.</strong></td>
                    <td>{{ $invoice->chassis_number ?? '-' }}</td>
                </tr>
                <tr>
                    <td><strong>Engine No.</strong></td>
                    <td>{{ $invoice->engine_number ?? '-' }}</td>
                </tr>
                <tr>
                    <td><strong>Mfg Year</strong></td>
                    <td>{{ $invoice->mfg_year ?? '-' }}</td>
                </tr>
            </table>
            @endif

            <h5>Invoice Items</h5>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Description</th>
                        <th>Qty</th>
                        <th>Unit Price</th>
                        <th>Taxable Value</th>
                        <th>GST Rate</th>
                        <th>GST Amt</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @if($invoice->invoice_type == 'vehicle')
                    <tr>
                        <td>1</td>
                        <td>{{ $invoice->vehicle_description }}</td>
                        <td>1</td>
                        <td>{{ number_format($invoice->subtotal, 2) }}</td>
                        <td>{{ number_format($invoice->subtotal, 2) }}</td>
                        <td>@if($invoice->is_gst)28% @else 0% @endif</td>
                        <td>{{ number_format($invoice->gst_amount, 2) }}</td>
                        <td>{{ number_format($invoice->total_amount, 2) }}</td>
                    </tr>
                    @else
                    @foreach($invoice->items as $i => $item)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $item->description }}@if($item->sparePart) ({{ $item->sparePart->part_no }})@endif</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ number_format($item->unit_price, 2) }}</td>
                        <td>{{ number_format($item->taxable_value, 2) }}</td>
                        <td>{{ $item->gst_rate }}%</td>
                        <td>{{ number_format($item->gst_amount, 2) }}</td>
                        <td>{{ number_format($item->total, 2) }}</td>
                    </tr>
                    @endforeach
                    @endif
                </tbody>
            </table>

            <div class="row">
                <div class="col-md-6">
                    @if($invoice->is_gst && $invoice->gst_type == 'cgst_sgst')
                    <p class="mb-1"><strong>CGST @ {{ $invoice->invoice_type == 'vehicle' ? '14%' : '' }}:</strong> {{ number_format($invoice->gst_amount / 2, 2) }}</p>
                    <p class="mb-1"><strong>SGST @ {{ $invoice->invoice_type == 'vehicle' ? '14%' : '' }}:</strong> {{ number_format($invoice->gst_amount / 2, 2) }}</p>
                    @elseif($invoice->is_gst && $invoice->gst_type == 'igst')
                    <p class="mb-1"><strong>IGST @ {{ $invoice->invoice_type == 'vehicle' ? '28%' : '' }}:</strong> {{ number_format($invoice->gst_amount, 2) }}</p>
                    @endif
                    @if($invoice->cess_amount > 0)
                    <p class="mb-1"><strong>Cess:</strong> {{ number_format($invoice->cess_amount, 2) }}</p>
                    @endif
                </div>
                <div class="col-md-6 text-end">
                    <h6>Subtotal: {{ number_format($invoice->subtotal, 2) }}</h6>
                    @if($invoice->is_gst)
                    <h6>GST: {{ number_format($invoice->gst_amount, 2) }}</h6>
                    <h6>Cess: {{ number_format($invoice->cess_amount, 2) }}</h6>
                    @endif
                    <h6>Round Off: {{ number_format($invoice->round_off, 2) }}</h6>
                    <h4>Grand Total: {{ number_format($invoice->grand_total, 2) }}</h4>
                </div>
            </div>

            @if($invoice->notes)
            <div class="mt-3">
                <strong>Notes:</strong> {{ $invoice->notes }}
            </div>
            @endif

            <div class="row mt-5">
                <div class="col-md-6 text-center">
                    <br><br>
                    <strong>Customer Signature</strong>
                </div>
                <div class="col-md-6 text-center">
                    <br><br>
                    <strong>Authorised Signatory</strong>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
