@extends('admin.layouts.app')
@section('style')
<style>
.invoice-container {
    background: #fff;
    padding: 20px;
    border: 1px solid #ccc;
    font-family: Arial, sans-serif;
    color: #000;
}
.invoice-table, .invoice-table th, .invoice-table td {
    border: 1px solid #000 !important;
}
.nested-specs-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
}
.nested-specs-table td {
    border: 1px solid #000 !important;
    padding: 4px 8px;
    font-size: 0.85rem;
}
.terms-list {
    padding-left: 15px;
    font-size: 0.75rem;
    line-height: 1.3;
}
.calculation-label {
    font-size: 0.85rem;
}

@media print {
    body * {
        visibility: hidden;
    }
    .invoice-container, .invoice-container * {
        visibility: visible;
    }
    .invoice-container {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        border: none;
        padding: 0;
    }
    .btn-print-group {
        display: none !important;
    }
    .layout-navbar, .layout-menu-toggle, .menu-vertical, .footer {
        display: none !important;
    }
    .content-wrapper {
        padding: 0 !important;
        margin: 0 !important;
    }
}
</style>
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4 btn-print-group">
        <h4 class="fw-bold mb-0">Invoice Detail</h4>
        <div>
            <button onclick="window.print();" class="btn btn-primary"><i class="bx bx-printer"></i> Print Invoice</button>
            <a href="{{ route('admin.vehicle-sales-invoices.index') }}" class="btn btn-secondary">Back to List</a>
        </div>
    </div>

    <div class="invoice-container shadow-sm mx-auto" style="max-width: 900px;">
        <!-- Header -->
        <div class="text-center fw-bold small border border-bottom-0 p-1" style="border: 1px solid #000 !important;">
            RETAIL INVOICE / CASH MEMO / TAX INVOICE
        </div>
        
        <div class="d-flex justify-content-between border border-bottom-0 p-3" style="border: 1px solid #000 !important;">
            <div>
                <h3 class="fw-bold m-0" style="color: #000;">SHREE KRISHNA AUTO GREEN</h3>
                <div class="small">NEAR MAHAMANDIR CIRCLE, MANDORE ROAD</div>
                <div class="small">JODHPUR(RAJASTHAN)</div>
                <div class="small fw-bold mt-1">GSTIN :- 08ANQPD4555N1ZE</div>
                <div class="small mt-2">Contact No. 7586899148, 9829028792</div>
            </div>
            <div class="text-end">
                <div class="fw-bold" style="font-size: 1.4rem;">YO Bykes</div>
                <div class="small text-muted italic">Life Fully Charged!</div>
                <div class="fw-bold mt-3" style="font-size: 1.1rem;">{{ str_pad($vehicleSalesInvoice->id, 2, '0', STR_PAD_LEFT) }}</div>
                <div class="small mt-1">{{ $vehicleSalesInvoice->invoice_date->format('d/m/Y') }}</div>
            </div>
        </div>

        <!-- Customer & Payment Details Block -->
        <table class="table invoice-table mb-0" style="width: 100%; border-collapse: collapse;">
            <tbody>
                <tr>
                    <td style="width: 60%; vertical-align: top; padding: 8px;">
                        <div><strong>CUSTOMER NAME:- </strong> {{ $vehicleSalesInvoice->customer_name }}</div>
                        <br>
                        <div><strong>Address Permanent: </strong> {{ $vehicleSalesInvoice->customer_address ?? '-' }}</div>
                        <br>
                        <div><strong>PAYMENT MODE: </strong> {{ $vehicleSalesInvoice->payment_mode ?? '-' }}</div>
                        <br>
                        <div><strong>Residence Tel. Ph. : </strong> {{ $vehicleSalesInvoice->customer_residence_phone ?? '-' }}</div>
                    </td>
                    <td style="width: 40%; vertical-align: top; padding: 8px;">
                        <div><strong>Age : </strong> {{ $vehicleSalesInvoice->customer_age ?? '-' }}</div>
                        <br>
                        <div><strong>Occupation : </strong> {{ $vehicleSalesInvoice->customer_occupation ?? 'BUSINESS' }}</div>
                        <br>
                        <div><strong>Mobile no : </strong> {{ $vehicleSalesInvoice->customer_mobile ?? '-' }}</div>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Invoice Items Table -->
        <table class="table invoice-table mb-0" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr class="text-center fw-bold">
                    <th style="width: 8%;">S.No.</th>
                    <th style="width: 62%;">Description</th>
                    <th style="width: 8%;">Qty</th>
                    <th style="width: 10%;">Rate</th>
                    <th style="width: 12%;">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-center" style="vertical-align: top; padding: 8px;">1</td>
                    <td style="vertical-align: top; padding: 8px;">
                        <strong class="text-success">{{ strtoupper($vehicleSalesInvoice->vehicleInventory->vehicle_description) }}</strong>
                        <div class="fw-bold small text-dark mt-1">{{ strtoupper($battery_type) }}</div>
                        
                        <div class="fw-bold small mt-3" style="font-size: 0.8rem; line-height: 1.4;">
                            {!! nl2br(e($vehicleSalesInvoice->warranty_notes)) !!}
                        </div>

                        <!-- Nested Specifications Block -->
                        <table class="nested-specs-table">
                            <tbody>
                                <tr>
                                    <td style="width: 40%;"><strong>Model :</strong></td>
                                    <td>{{ $vehicleSalesInvoice->vehicleInventory->vehicle_description }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Colour</strong></td>
                                    <td>{{ $color_name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>CHASSIS No :</strong></td>
                                    <td class="fw-bold">{{ $vehicleSalesInvoice->vehicleInventory->chassis_number }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Battery No :</strong></td>
                                    <td>{{ $vehicleSalesInvoice->vehicleInventory->battery_number ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Charger No :</strong></td>
                                    <td>{{ $vehicleSalesInvoice->vehicleInventory->charger_number ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Controller No :</strong></td>
                                    <td>{{ $vehicleSalesInvoice->vehicleInventory->controller_number ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Convertor No :</strong></td>
                                    <td>{{ $vehicleSalesInvoice->vehicleInventory->convertor_number ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Manual No :</strong></td>
                                    <td>{{ $vehicleSalesInvoice->vehicleInventory->manual_number ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Type of Battery</strong></td>
                                    <td>{{ $battery_type }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Make of Battery</strong></td>
                                    <td>{{ $battery_make }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Motor No :</strong></td>
                                    <td>{{ $vehicleSalesInvoice->vehicleInventory->motor_number ?? '-' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                    <td class="text-center" style="vertical-align: top; padding: 8px;">1</td>
                    <td class="text-end" style="vertical-align: top; padding: 8px;">{{ number_format($vehicleSalesInvoice->rate, 2) }}</td>
                    <td class="text-end" style="vertical-align: top; padding: 8px;">{{ number_format($vehicleSalesInvoice->rate, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Terms and Calculations Footer -->
        <table class="table invoice-table mb-0" style="width: 100%; border-collapse: collapse;">
            <tbody>
                <tr>
                    <td style="width: 60%; vertical-align: top; padding: 8px;">
                        <ol class="terms-list m-0">
                            <li>Received vehicle, tool kit, charger, jack, stepny and Battery in good and running condition.</li>
                            <li>Our responsibility ceases upon delivery & claim for loss/ shortage etc. will not be entertained thereafter.</li>
                            <li>Goods Once sold will not be taken back or exchanged under any circumstances.</li>
                            <li>Warranty as per Company's policy given in owner's manual. 12Month motor and Controller.</li>
                            <li>Subject to JODHPUR Jurisdiction only.</li>
                            <li>Getting any work done on the vehicle outside of our authorized office workshop will void the entire warranty.</li>
                        </ol>
                    </td>
                    <td style="width: 40%; padding: 0; vertical-align: top;">
                        <table class="table mb-0" style="width: 100%; border-collapse: collapse; border: none !important;">
                            <tbody>
                                <tr style="border-bottom: 1px solid #000;">
                                    <td class="calculation-label" style="padding: 4px 8px; border: none;">SUB TOTAL</td>
                                    <td class="text-end fw-bold" style="padding: 4px 8px; border: none;">{{ number_format($vehicleSalesInvoice->sub_total, 2) }}</td>
                                </tr>
                                <tr style="border-bottom: 1px solid #000;">
                                    <td class="calculation-label" style="padding: 4px 8px; border: none;">OTHERS</td>
                                    <td class="text-end text-muted" style="padding: 4px 8px; border: none;">0.00</td>
                                </tr>
                                <tr style="border-bottom: 1px solid #000;">
                                    <td class="calculation-label" style="padding: 4px 8px; border: none; color: blue; text-decoration: underline;">SGST @ 2.5</td>
                                    <td class="text-end" style="padding: 4px 8px; border: none;">{{ number_format($vehicleSalesInvoice->sgst_amount, 2) }}</td>
                                </tr>
                                <tr style="border-bottom: 1px solid #000;">
                                    <td class="calculation-label" style="padding: 4px 8px; border: none;">CGST @ 2.5</td>
                                    <td class="text-end" style="padding: 4px 8px; border: none;">{{ number_format($vehicleSalesInvoice->cgst_amount, 2) }}</td>
                                </tr>
                                <tr style="border-bottom: 1px solid #000;">
                                    <td class="calculation-label" style="padding: 4px 8px; border: none;">TOTAL</td>
                                    <td class="text-end fw-bold" style="padding: 4px 8px; border: none;">{{ number_format($vehicleSalesInvoice->total, 2) }}</td>
                                </tr>
                                <tr style="border-bottom: 1px solid #000;">
                                    <td class="calculation-label" style="padding: 4px 8px; border: none;">Less :- NEMMP 2020 INCENTIVE</td>
                                    <td class="text-end" style="padding: 4px 8px; border: none;">{{ number_format($vehicleSalesInvoice->nemmp_incentive, 2) }}</td>
                                </tr>
                                <tr style="border-bottom: 1px solid #000;">
                                    <td class="calculation-label" style="padding: 4px 8px; border: none;">Discount/offer/Incentive etc.</td>
                                    <td class="text-end" style="padding: 4px 8px; border: none;">{{ number_format($vehicleSalesInvoice->discount, 2) }}</td>
                                </tr>
                                <tr style="border-bottom: 1px solid #000;">
                                    <td class="calculation-label" style="padding: 4px 8px; border: none;">TOTAL DISC. (-)</td>
                                    <td class="text-end" style="padding: 4px 8px; border: none;">{{ number_format($vehicleSalesInvoice->nemmp_incentive + $vehicleSalesInvoice->discount, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="calculation-label fw-bold" style="padding: 4px 8px; border: none;">G. TOTAL</td>
                                    <td class="text-end fw-bold" style="padding: 4px 8px; border: none;">{{ number_format($vehicleSalesInvoice->grand_total, 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Signatures -->
        <table class="table invoice-table mb-0" style="width: 100%; border-collapse: collapse; border-top: none !important;">
            <tbody>
                <tr>
                    <td style="width: 50%; height: 100px; vertical-align: bottom; padding: 8px;">
                        <div class="small fw-bold">CUSTOMER SIGNATURE</div>
                    </td>
                    <td class="text-center" style="width: 50%; height: 100px; vertical-align: top; padding: 8px;">
                        <div class="small fw-bold">For SHREE KRISHNA AUTO GREEN</div>
                        <br><br>
                        <div class="small fw-bold mt-4">Prop.</div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
