@extends('admin.layouts.app')
@section('style')
<style>
@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap');

.challan-wrapper {
    background: #f8fafc;
    padding: 30px 15px;
    display: flex;
    justify-content: center;
}

.challan-card {
    width: 800px;
    max-width: 100%;
    background: #fff;
    box-shadow: 0 15px 35px rgba(22, 101, 52, 0.05);
    border-radius: 12px;
    font-family: 'Outfit', -apple-system, sans-serif;
    color: #1e293b;
    position: relative;
    overflow: hidden;
    border: 1px solid #e2e8f0;
}

.watermark {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    opacity: 0.06;
    z-index: 0;
    pointer-events: none;
    width: 65%;
    display: flex;
    justify-content: center;
    align-items: center;
}

.watermark img {
    width: 100%;
    height: auto;
}

.challan-card::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 6px;
    background: linear-gradient(90deg, #059669, #0d9488, #15803d);
}

.challan-padding {
    padding: 24px;
    position: relative;
    z-index: 1;
}

/* Header */
.company-section {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 12px;
    border-bottom: 2px solid #059669;
    padding-bottom: 12px;
}

.company-details h2 {
    font-size: 24px;
    font-weight: 800;
    color: #14532d;
    margin: 0 0 4px 0;
    letter-spacing: -0.5px;
}

.company-details p {
    font-size: 12.5px;
    color: #475569;
    line-height: 1.45;
    margin: 0;
}

.company-gst {
    font-size: 13px;
    font-weight: 700;
    color: #047857;
    margin-top: 3px;
}

.challan-title-block {
    text-align: right;
}

.challan-title-block h1 {
    font-size: 28px;
    font-weight: 900;
    color: #059669;
    letter-spacing: 1px;
    margin: 0;
    line-height: 1.1;
    text-transform: uppercase;
}

.challan-subtitle {
    font-size: 12px;
    font-weight: 700;
    color: #047857;
    margin-top: 4px;
}

/* Info Grid */
.meta-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
    margin-bottom: 14px;
}

.meta-box {
    background-color: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 10px 14px;
}

.meta-box-title {
    font-size: 11px;
    font-weight: 700;
    color: #059669;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 6px;
    border-bottom: 1px solid #e2e8f0;
    padding-bottom: 4px;
}

.meta-table {
    width: 100%;
}

.meta-table td {
    padding: 3px 0;
    font-size: 12.5px;
}

.meta-label {
    color: #64748b;
    font-weight: 500;
}

.meta-value {
    color: #0f172a;
    font-weight: 600;
    text-align: right;
}

/* Vehicle Details */
.vehicle-section {
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    margin-bottom: 14px;
    overflow: hidden;
}

.vehicle-header {
    background-color: #0f172a;
    color: #ffffff;
    padding: 8px 14px;
    font-size: 13px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.specs-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 8px 16px;
    padding: 12px 14px;
    background: #ffffff;
}

.specs-cell {
    font-size: 12.5px;
    color: #334155;
    padding: 4px 0;
    border-bottom: 1px dashed #f1f5f9;
}

.specs-cell b {
    color: #475569;
    font-weight: 600;
    min-width: 110px;
    display: inline-block;
}

.specs-cell strong {
    color: #0f172a;
}

/* Accessories Checklist */
.accessories-section {
    background: #f0fdf4;
    border: 1px solid #a7f3d0;
    border-radius: 8px;
    padding: 10px 14px;
    margin-bottom: 14px;
}

.accessories-title {
    font-size: 11.5px;
    font-weight: 700;
    color: #14532d;
    text-transform: uppercase;
    margin-bottom: 8px;
}

.checklist-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 6px 12px;
    font-size: 11.5px;
    color: #1e293b;
}

.check-item {
    display: flex;
    align-items: center;
    gap: 6px;
}

.check-item .box-check {
    display: inline-block;
    width: 13px;
    height: 13px;
    border: 1.5px solid #059669;
    border-radius: 2px;
    background: #fff;
    text-align: center;
    line-height: 12px;
    font-size: 10px;
    color: #059669;
    font-weight: bold;
}

/* Declaration */
.declaration-box {
    border: 1px solid #e2e8f0;
    background-color: #fafafa;
    border-radius: 8px;
    padding: 10px 14px;
    margin-bottom: 16px;
    font-size: 11px;
    color: #475569;
    line-height: 1.5;
}

.declaration-box strong {
    color: #0f172a;
}

/* Signature */
.signature-section {
    display: flex;
    justify-content: space-between;
    margin-top: 24px;
    padding-top: 8px;
}

.sig-box {
    width: 42%;
    text-align: center;
}

.sig-line {
    border-top: 1px dashed #64748b;
    margin-bottom: 6px;
}

.sig-title {
    font-size: 12px;
    font-weight: 700;
    color: #0f172a;
}

.sig-sub {
    font-size: 11px;
    color: #64748b;
}

@media print {
    body {
        background: #fff !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    .challan-wrapper {
        padding: 0 !important;
        background: #fff !important;
    }
    .challan-card {
        border: none !important;
        box-shadow: none !important;
        width: 100% !important;
    }
    .btn-action-group, .layout-navbar, .layout-menu-toggle, .menu-vertical, .footer {
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
    <div class="d-flex justify-content-between align-items-center mb-4 btn-action-group">
        <h4 class="fw-bold mb-0"><i class="bx bx-package text-success me-1"></i> Delivery Challan</h4>
        <div>
            <a href="{{ route('admin.vehicle-sales-invoices.show', $vehicleSalesInvoice) }}" class="btn btn-outline-primary me-1"><i class="bx bx-file"></i> View Invoice</a>
            <button onclick="window.print();" class="btn btn-primary me-1"><i class="bx bx-printer"></i> Print Challan</button>
            <a href="{{ route('admin.vehicle-sales-invoices.delivery-challan.pdf', [$vehicleSalesInvoice, 'download' => 1]) }}" class="btn btn-danger me-1"><i class="bx bxs-file-pdf"></i> Download PDF</a>
            <a href="{{ route('admin.vehicle-sales-invoices.index') }}" class="btn btn-secondary">Back to List</a>
        </div>
    </div>

    <div class="challan-wrapper">
        <div class="challan-card">
            <!-- Background Watermark -->
            @if(file_exists(public_path('assets/admin/img/logo.jpg')))
            <div class="watermark">
                <img src="{{ asset('assets/admin/img/logo.jpg') }}" alt="Watermark Logo">
            </div>
            @endif

            <div class="challan-padding">
                <!-- Header -->
                <div class="company-section">
                    <div class="company-details">
                        <h2>SHREE KRISHNA AUTO GREEN</h2>
                        <p>NEAR MAHAMANDIR CIRCLE, MAIN MANDORE ROAD, JODHPUR (RAJASTHAN)</p>
                        <div class="company-gst">GSTIN : 08ANQPD4555N1ZE | PAN : ANGPD4555N</div>
                        <p>Mobile: 7568899148, 9829028792 | Email: vijay.deora429@gmail.com</p>
                    </div>
                    <div class="challan-title-block">
                        <h1>DELIVERY CHALLAN</h1>
                        <div class="challan-subtitle">VEHICLE HANDOVER NOTE</div>
                        @if(file_exists(public_path('assets/admin/img/logo.jpg')))
                            <img src="{{ asset('assets/admin/img/logo.jpg') }}" style="max-height: 48px; margin-top: 4px;" alt="Logo">
                        @endif
                    </div>
                </div>

                <!-- Info Grid -->
                <div class="meta-grid">
                    <div class="meta-box">
                        <div class="meta-box-title">Challan & Invoice Details</div>
                        <table class="meta-table">
                            <tr>
                                <td class="meta-label">Challan No:</td>
                                <td class="meta-value" style="color: #059669;">DC-{{ $vehicleSalesInvoice->invoice_number }}</td>
                            </tr>
                            <tr>
                                <td class="meta-label">Invoice No:</td>
                                <td class="meta-value">{{ $vehicleSalesInvoice->invoice_number }}</td>
                            </tr>
                            <tr>
                                <td class="meta-label">Delivery Date:</td>
                                <td class="meta-value">{{ $vehicleSalesInvoice->invoice_date ? $vehicleSalesInvoice->invoice_date->format('d/m/Y') : '-' }}</td>
                            </tr>
                            @if($vehicleSalesInvoice->created_at)
                            <tr>
                                <td class="meta-label">Delivery Time:</td>
                                <td class="meta-value">{{ $vehicleSalesInvoice->created_at->format('h:i A') }}</td>
                            </tr>
                            @endif
                            <tr>
                                <td class="meta-label">Payment Mode:</td>
                                <td class="meta-value">{{ strtoupper($vehicleSalesInvoice->payment_mode ?? 'CASH') }}</td>
                            </tr>
                            @if($vehicleSalesInvoice->finance_name)
                            <tr>
                                <td class="meta-label">HPN / Finance:</td>
                                <td class="meta-value" style="color: #2563eb;">{{ $vehicleSalesInvoice->finance_name }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>

                    <div class="meta-box">
                        <div class="meta-box-title">Customer / Consignee Details</div>
                        <div style="font-size: 13.5px; font-weight: 700; color: #0f172a; margin-bottom: 3px;">
                            {{ $vehicleSalesInvoice->customer_name }}
                        </div>
                        <div style="font-size: 12px; color: #475569; line-height: 1.4;">
                            @if($vehicleSalesInvoice->customer_mobile) <div><b>Mobile:</b> {{ $vehicleSalesInvoice->customer_mobile }}</div> @endif
                            @if($vehicleSalesInvoice->customer_address) <div><b>Address:</b> {{ $vehicleSalesInvoice->customer_address }}</div> @endif
                            @if($vehicleSalesInvoice->customer_age) <span><b>Age:</b> {{ $vehicleSalesInvoice->customer_age }} Yrs</span> | @endif
                            @if($vehicleSalesInvoice->customer_occupation) <span><b>Occupation:</b> {{ $vehicleSalesInvoice->customer_occupation }}</span> @endif
                        </div>
                    </div>
                </div>

                <!-- Vehicle Details -->
                <div class="vehicle-section">
                    <div class="vehicle-header">
                        <span>Vehicle Specification & Identification</span>
                        <span class="badge bg-success text-white">Delivered & Verified</span>
                    </div>
                    <div class="specs-grid">
                        <div class="specs-cell"><b>Vehicle Model:</b> <strong>{{ $vehicleSalesInvoice->vehicleInventory->vehicle_description ?? 'EV Vehicle' }}</strong></div>
                        <div class="specs-cell"><b>Color:</b> <strong style="color: #059669;">{{ $color_name ?? ($vehicleSalesInvoice->vehicleInventory->color_name ?? '-') }}</strong></div>
                        
                        <div class="specs-cell"><b>Chassis Number:</b> <strong style="color: #047857; font-size: 13px;">{{ $vehicleSalesInvoice->vehicleInventory->chassis_number ?? '-' }}</strong></div>
                        <div class="specs-cell"><b>Motor/Engine No:</b> <strong>{{ $vehicleSalesInvoice->vehicleInventory->motor_number ?? ($vehicleSalesInvoice->vehicleInventory->engine_number ?? '-') }}</strong></div>

                        <div class="specs-cell"><b>Battery Type & Make:</b> <strong>{{ $battery_type ?? 'LITHIUM' }} ({{ $battery_make ?? 'LITHIUM' }})</strong></div>
                        <div class="specs-cell"><b>Battery Number:</b> <strong>{{ $vehicleSalesInvoice->vehicleInventory->battery_number ?? '-' }}</strong></div>

                        <div class="specs-cell"><b>Charger Number:</b> <strong>{{ $vehicleSalesInvoice->vehicleInventory->charger_number ?? '-' }}</strong></div>
                        <div class="specs-cell"><b>Controller Number:</b> <strong>{{ $vehicleSalesInvoice->vehicleInventory->controller_number ?? '-' }}</strong></div>

                        <div class="specs-cell"><b>Convertor Number:</b> <strong>{{ $vehicleSalesInvoice->vehicleInventory->convertor_number ?? '-' }}</strong></div>
                        <div class="specs-cell"><b>Manual Number:</b> <strong>{{ $vehicleSalesInvoice->vehicleInventory->manual_number ?? '-' }}</strong></div>
                    </div>
                </div>

                <!-- Included Accessories & Documents Checklist -->
                <div class="accessories-section">
                    <div class="accessories-title"><i class="bx bx-check-shield text-success"></i> Items & Accessories Handover Checklist:</div>
                    <div class="checklist-grid">
                        <div class="check-item"><span class="box-check">&#10003;</span> Stepny Wheel / Spare Tyre</div>
                        <div class="check-item"><span class="box-check">&#10003;</span> Jack & Jack Handle</div>
                        <div class="check-item"><span class="box-check">&#10003;</span> Toolkit & Spanners</div>
                        <div class="check-item"><span class="box-check">&#10003;</span> Stereo / Music System</div>
                        <div class="check-item"><span class="box-check">&#10003;</span> Rear View Mirrors (Pair)</div>
                        <div class="check-item"><span class="box-check">&#10003;</span> Smart EV Charger & Cable</div>
                        <div class="check-item"><span class="box-check">&#10003;</span> Battery Set Installed</div>
                        <div class="check-item"><span class="box-check">&#10003;</span> Vehicle Keys (2 Sets)</div>
                        <div class="check-item"><span class="box-check">&#10003;</span> Owner Manual & Warranty Card</div>
                    </div>
                </div>

                @if($vehicleSalesInvoice->warranty_notes)
                <div style="background-color: #f8fafc; border-left: 3px solid #059669; padding: 8px 12px; margin-bottom: 14px; font-size: 11.5px; border-radius: 0 6px 6px 0;">
                    <strong style="color: #14532d;">Warranty & Special Notes:</strong> {{ $vehicleSalesInvoice->warranty_notes }}
                </div>
                @endif

                <!-- Acknowledgement Declaration -->
                <div class="declaration-box">
                    <strong>Customer Declaration & Acknowledgement:</strong><br>
                    I/We hereby acknowledge receipt of the above-mentioned vehicle in perfect running condition along with all standard tools, accessories, battery, charger, warranty booklet and keys. I have verified the Chassis Number, Motor/Engine Number and Battery details with the physical vehicle and find them in order. Our responsibility ceases upon delivery.
                </div>

                <!-- Signatures -->
                <div class="signature-section">
                    <div class="sig-box">
                        <div style="height: 45px;"></div>
                        <div class="sig-line"></div>
                        <div class="sig-title">Customer's Signature</div>
                        <div class="sig-sub">({{ $vehicleSalesInvoice->customer_name }})</div>
                    </div>
                    <div class="sig-box">
                        <div style="height: 45px;"></div>
                        <div class="sig-line"></div>
                        <div class="sig-title">Authorized Signatory</div>
                        <div class="sig-sub">SHREE KRISHNA AUTO GREEN</div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
