@extends('admin.layouts.app')
@section('style')
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

.invoice-wrapper {
    background: #f3f4f6;
    padding: 30px 15px;
    display: flex;
    justify-content: center;
}

.invoice-card {
    width: 900px;
    background: #fff;
    box-shadow: 0 10px 25px rgba(0,0,0,0.05);
    border-radius: 8px;
    font-family: 'Inter', -apple-system, sans-serif;
    color: #1f2937;
    position: relative;
    overflow: hidden;
    border: 1px solid #e5e7eb;
}

/* Decorative geometric corners similar to the design template */
.invoice-card::before {
    content: "";
    position: absolute;
    top: 0;
    right: 0;
    width: 150px;
    height: 150px;
    background: linear-gradient(135deg, transparent 50%, #dbeafe 50%, #2563eb 90%);
    opacity: 0.15;
    pointer-events: none;
}

.invoice-card::after {
    content: "";
    position: absolute;
    bottom: 0;
    left: 0;
    width: 150px;
    height: 150px;
    background: linear-gradient(315deg, transparent 50%, #dbeafe 50%, #2563eb 90%);
    opacity: 0.15;
    pointer-events: none;
}

.invoice-padding {
    padding: 40px;
}

.company-section {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 30px;
}

.company-details h2 {
    font-size: 24px;
    font-weight: 700;
    color: #1e3a8a;
    margin: 0 0 8px 0;
}

.company-details p {
    font-size: 13px;
    color: #4b5563;
    line-height: 1.5;
    margin: 0;
}

.invoice-title-block {
    text-align: right;
}

.invoice-title-block h1 {
    font-size: 42px;
    font-weight: 800;
    color: #1e3a8a;
    letter-spacing: 1px;
    margin: 0;
    line-height: 1;
}

.invoice-meta-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    margin-bottom: 25px;
}

.meta-col {
    padding: 15px 20px;
}

.meta-col:first-child {
    border-right: 1px solid #e5e7eb;
}

.meta-table {
    width: 100%;
}

.meta-table td {
    padding: 4px 0;
    font-size: 13px;
}

.meta-label {
    color: #6b7280;
    width: 40%;
}

.meta-value {
    color: #111827;
    font-weight: 600;
}

.address-block {
    display: grid;
    grid-template-columns: 1fr 1fr;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    overflow: hidden;
    margin-bottom: 30px;
}

.address-box {
    padding: 15px 20px;
}

.address-box:first-child {
    border-right: 1px solid #e5e7eb;
}

.address-title {
    background-color: #f3f4f6;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #374151;
    padding: 8px 20px;
    margin: -15px -20px 12px -20px;
    border-bottom: 1px solid #e5e7eb;
}

.address-box h4 {
    font-size: 15px;
    font-weight: 700;
    margin: 0 0 6px 0;
    color: #111827;
}

.address-box p {
    font-size: 13px;
    color: #4b5563;
    line-height: 1.5;
    margin: 0;
}

.items-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 25px;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    overflow: hidden;
}

.items-table th {
    background-color: #1e3a8a;
    color: #ffffff;
    font-weight: 600;
    font-size: 13px;
    text-transform: uppercase;
    padding: 12px 16px;
    text-align: left;
}

.items-table td {
    padding: 16px;
    border-bottom: 1px solid #e5e7eb;
    vertical-align: top;
    font-size: 13px;
}

.items-table tr:last-child td {
    border-bottom: none;
}

.item-name {
    font-size: 15px;
    font-weight: 700;
    color: #111827;
}

.item-desc-badge {
    background-color: #dcfce7;
    color: #15803d;
    font-size: 11px;
    font-weight: 600;
    padding: 2px 8px;
    border-radius: 4px;
    margin-left: 8px;
    display: inline-block;
}

.warranty-box {
    background-color: #f0fdf4;
    border-left: 3px solid #16a34a;
    padding: 10px 14px;
    border-radius: 4px;
    margin-top: 10px;
    font-size: 12px;
}

.warranty-box strong {
    color: #14532d;
    display: block;
    margin-bottom: 4px;
}

.warranty-box div {
    color: #166534;
    line-height: 1.4;
}

.vehicle-specs-table {
    width: 100%;
    margin-top: 12px;
    border: 1px solid #e5e7eb;
    background-color: #fafafa;
    border-radius: 4px;
}

.vehicle-specs-table td {
    padding: 6px 12px;
    font-size: 12px;
    border-bottom: 1px solid #f3f4f6;
    border-right: 1px solid #f3f4f6;
}

.vehicle-specs-table tr td:last-child {
    border-right: none;
}

.vehicle-specs-table tr:last-child td {
    border-bottom: none;
}

.bottom-section {
    display: flex;
    justify-content: space-between;
    margin-top: 10px;
}

.terms-box {
    width: 58%;
}

.terms-box h4 {
    font-size: 13px;
    font-weight: 700;
    color: #374151;
    margin: 0 0 8px 0;
}

.terms-box ol {
    margin: 0;
    padding-left: 18px;
    font-size: 12px;
    color: #6b7280;
    line-height: 1.6;
}

.summary-card {
    width: 38%;
    background-color: #eff6ff;
    border: 1px solid #bfdbfe;
    border-radius: 6px;
    padding: 15px 20px;
}

.summary-table {
    width: 100%;
}

.summary-table td {
    padding: 6px 0;
    font-size: 13px;
}

.summary-label {
    color: #1e3a8a;
    font-weight: 500;
}

.summary-value {
    text-align: right;
    color: #1e3a8a;
    font-weight: 600;
}

.summary-total-row td {
    border-top: 1px solid #bfdbfe;
    padding-top: 10px;
    margin-top: 4px;
}

.summary-total-label {
    font-size: 15px;
    font-weight: 800;
    color: #1e3a8a;
}

.summary-total-value {
    font-size: 16px;
    font-weight: 800;
    color: #1e3a8a;
    text-align: right;
}

.signature-row {
    display: flex;
    justify-content: space-between;
    margin-top: 40px;
    border-top: 1px solid #e5e7eb;
    padding-top: 20px;
}

.sig-box {
    width: 45%;
    text-align: center;
}

.sig-line {
    border-top: 1px dashed #9ca3af;
    margin-bottom: 8px;
    width: 80%;
    margin-left: auto;
    margin-right: auto;
}

.sig-text {
    font-size: 12px;
    font-weight: 600;
    color: #4b5563;
}

.text-center {
    text-align: center;
}

.text-right {
    text-align: right;
}

@media print {
    @page {
        size: A4 portrait;
        margin: 8mm 10mm;
    }
    html, body {
        height: 100%;
        margin: 0 !important;
        padding: 0 !important;
        background: #fff;
    }
    .invoice-wrapper {
        background: #fff;
        padding: 0 !important;
        margin: 0 !important;
    }
    .invoice-card {
        box-shadow: none;
        border: none;
        width: 100% !important;
        max-width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
        transform: scale(0.97);
        transform-origin: top left;
    }
    .invoice-card::before, .invoice-card::after {
        display: none;
    }
    .invoice-padding {
        padding: 5px 0 !important;
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
    /* Prevent page break inside elements to keep invoice on one page */
    .company-section, .invoice-meta-grid, .address-block, .items-table, .bottom-section, .signature-row {
        page-break-inside: avoid;
    }
    .items-table th {
        padding: 8px 12px !important;
    }
    .items-table td {
        padding: 10px 12px !important;
    }
    .vehicle-specs-table td {
        padding: 4px 8px !important;
    }
    .warranty-box {
        margin-top: 6px !important;
        padding: 6px 10px !important;
    }
    .summary-card {
        padding: 10px 15px !important;
    }
    .summary-table td {
        padding: 3px 0 !important;
    }
    .signature-row {
        margin-top: 25px !important;
        padding-top: 15px !important;
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

    <div class="invoice-wrapper">
        <div class="invoice-card">
            <div class="invoice-padding">
                
                <!-- Header -->
                <div class="company-section">
                    <div class="company-details">
                        <h2>SHREE KRISHNA AUTO GREEN</h2>
                        <p>NEAR MAHAMANDIR CIRCLE, MANDORE ROAD</p>
                        <p>JODHPUR (RAJASTHAN)</p>
                        <p style="margin-top: 4px; font-weight: 600;">GSTIN : 08ANQPD4555N1ZE</p>
                        <p>Contact : 7586899148, 9829028792</p>
                    </div>
                    <div class="invoice-title-block">
                        <h1>INVOICE</h1>
                        <h2 style="font-size: 20px; font-weight: 700; margin: 8px 0 0 0; color: #4b5563;">YO Bykes</h2>
                        <p style="font-size: 12px; color: #6b7280; font-style: italic; margin: 2px 0 0 0;">Life Fully Charged!</p>
                    </div>
                </div>

                <!-- Meta Info Grid -->
                <div class="invoice-meta-grid">
                    <div class="meta-col">
                        <table class="meta-table">
                            <tr>
                                <td class="meta-label">Invoice#</td>
                                <td class="meta-value">INV-{{ str_pad($vehicleSalesInvoice->id, 6, '0', STR_PAD_LEFT) }}</td>
                            </tr>
                            <tr>
                                <td class="meta-label">Invoice Date</td>
                                <td class="meta-value">{{ $vehicleSalesInvoice->invoice_date->format('d M Y') }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="meta-col">
                        <table class="meta-table">
                            <tr>
                                <td class="meta-label">Payment Mode</td>
                                <td class="meta-value">{{ $vehicleSalesInvoice->payment_mode ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="meta-label">Date of Sale</td>
                                <td class="meta-value">{{ $vehicleSalesInvoice->invoice_date->format('d M Y') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Bill To / Customer Details Block -->
                <div class="address-block">
                    <div class="address-box">
                        <div class="address-title">Bill To</div>
                        <h4>{{ $vehicleSalesInvoice->customer_name }}</h4>
                        <p>{{ $vehicleSalesInvoice->customer_address ?? '-' }}</p>
                        <p style="margin-top: 6px;"><b>Mobile:</b> {{ $vehicleSalesInvoice->customer_mobile ?? '-' }}</p>
                        <p><b>Residence Tel:</b> {{ $vehicleSalesInvoice->customer_residence_phone ?? '-' }}</p>
                    </div>
                    <div class="address-box">
                        <div class="address-title">Additional Info</div>
                        <table style="width: 100%; font-size: 13px;">
                            <tr>
                                <td style="color: #6b7280; padding: 3px 0;">Age:</td>
                                <td style="font-weight: 600; color: #111827;">{{ $vehicleSalesInvoice->customer_age ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td style="color: #6b7280; padding: 3px 0;">Occupation:</td>
                                <td style="font-weight: 600; color: #111827;">{{ $vehicleSalesInvoice->customer_occupation ?? 'BUSINESS' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Items Table -->
                <table class="items-table">
                    <thead>
                        <tr>
                            <th style="width: 6%;">#</th>
                            <th style="width: 58%;">Item & Description</th>
                            <th style="width: 8%; text-align: center;">Qty</th>
                            <th style="width: 14%; text-align: right;">Rate</th>
                            <th style="width: 14%; text-align: right;">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>
                                <span class="item-name">{{ strtoupper($vehicleSalesInvoice->vehicleInventory->vehicle_description) }}</span>
                                <span class="item-desc-badge">{{ strtoupper($battery_type) }}</span>

                                <div class="warranty-box">
                                    <strong>WARRANTY DETAILS</strong>
                                    <div>{!! nl2br(e($vehicleSalesInvoice->warranty_notes)) !!}</div>
                                </div>

                                <table class="vehicle-specs-table">
                                    <tr>
                                        <td><b>Model</b></td>
                                        <td>{{ $vehicleSalesInvoice->vehicleInventory->vehicle_description }}</td>
                                        <td><b>Colour</b></td>
                                        <td>{{ $color_name }}</td>
                                    </tr>
                                    <tr>
                                        <td><b>Chassis No</b></td>
                                        <td style="font-weight: 700; color: #111827;">{{ $vehicleSalesInvoice->vehicleInventory->chassis_number }}</td>
                                        <td><b>Battery No</b></td>
                                        <td>{{ $vehicleSalesInvoice->vehicleInventory->battery_number ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td><b>Charger No</b></td>
                                        <td>{{ $vehicleSalesInvoice->vehicleInventory->charger_number ?? '-' }}</td>
                                        <td><b>Controller No</b></td>
                                        <td>{{ $vehicleSalesInvoice->vehicleInventory->controller_number ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td><b>Convertor No</b></td>
                                        <td>{{ $vehicleSalesInvoice->vehicleInventory->convertor_number ?? '-' }}</td>
                                        <td><b>Manual No</b></td>
                                        <td>{{ $vehicleSalesInvoice->vehicleInventory->manual_number ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td><b>Type of Battery</b></td>
                                        <td>{{ $battery_type }}</td>
                                        <td><b>Make of Battery</b></td>
                                        <td>{{ $battery_make }}</td>
                                    </tr>
                                    <tr>
                                        <td><b>Motor No</b></td>
                                        <td>{{ $vehicleSalesInvoice->vehicleInventory->motor_number ?? '-' }}</td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </table>
                            </td>
                            <td class="text-center" style="font-weight: 600;">1</td>
                            <td class="text-right" style="font-weight: 600;">{{ number_format($vehicleSalesInvoice->rate, 2) }}</td>
                            <td class="text-right" style="font-weight: 600;">{{ number_format($vehicleSalesInvoice->rate, 2) }}</td>
                        </tr>
                    </tbody>
                </table>

                <!-- Bottom Section -->
                <div class="bottom-section">
                    <div class="terms-box">
                        <h4>TERMS & CONDITIONS</h4>
                        <ol>
                            <li>Received vehicle, tool kit, charger, jack, stepny and Battery in good and running condition.</li>
                            <li>Our responsibility ceases upon delivery & claim for loss/ shortage etc. will not be entertained thereafter.</li>
                            <li>Goods Once sold will not be taken back or exchanged under any circumstances.</li>
                            <li>Warranty as per Company's policy given in owner's manual. 12Month motor and Controller.</li>
                            <li>Subject to JODHPUR Jurisdiction only.</li>
                            <li>Getting any work done on the vehicle outside of our authorized office workshop will void the entire warranty.</li>
                        </ol>
                        <p style="font-size: 13px; font-weight: 600; color: #4b5563; margin-top: 15px;">Thanks for shopping with us.</p>
                    </div>

                    <div class="summary-card">
                        <table class="summary-table">
                            <tr>
                                <td class="summary-label">Sub Total</td>
                                <td class="summary-value">{{ number_format($vehicleSalesInvoice->sub_total, 2) }}</td>
                            </tr>
                            <tr>
                                <td class="summary-label">Others</td>
                                <td class="summary-value">0.00</td>
                            </tr>
                            <tr>
                                <td class="summary-label">SGST @ 2.5%</td>
                                <td class="summary-value">{{ number_format($vehicleSalesInvoice->sgst_amount, 2) }}</td>
                            </tr>
                            <tr>
                                <td class="summary-label">CGST @ 2.5%</td>
                                <td class="summary-value">{{ number_format($vehicleSalesInvoice->cgst_amount, 2) }}</td>
                            </tr>
                            <tr>
                                <td class="summary-label">Total</td>
                                <td class="summary-value">{{ number_format($vehicleSalesInvoice->total, 2) }}</td>
                            </tr>
                            <tr>
                                <td class="summary-label" style="font-size: 11px;">Less :- NEMMP 2020</td>
                                <td class="summary-value">{{ number_format($vehicleSalesInvoice->nemmp_incentive, 2) }}</td>
                            </tr>
                            <tr>
                                <td class="summary-label">Discount</td>
                                <td class="summary-value">{{ number_format($vehicleSalesInvoice->discount, 2) }}</td>
                            </tr>
                            <tr>
                                <td class="summary-label">TOTAL DISC. (-)</td>
                                <td class="summary-value">{{ number_format($vehicleSalesInvoice->nemmp_incentive + $vehicleSalesInvoice->discount, 2) }}</td>
                            </tr>
                            <tr class="summary-total-row">
                                <td class="summary-total-label">G. Total</td>
                                <td class="summary-total-value">{{ number_format($vehicleSalesInvoice->grand_total, 2) }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Signature Section -->
                <div class="signature-row">
                    <div class="sig-box">
                        <div class="sig-line"></div>
                        <div class="sig-text">CUSTOMER SIGNATURE</div>
                    </div>
                    <div class="sig-box">
                        <div class="sig-text" style="margin-bottom: 25px;">For SHREE KRISHNA AUTO GREEN</div>
                        <div class="sig-line" style="width: 50%;"></div>
                        <div class="sig-text">Prop.</div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
