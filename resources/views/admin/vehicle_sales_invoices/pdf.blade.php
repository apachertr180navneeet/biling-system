<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Vehicle Sales Invoice - {{ $vehicleSalesInvoice->invoice_number }}</title>
    <style>
        @page {
            margin: 20px 25px;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            color: #1e293b;
            font-size: 11px;
            line-height: 1.35;
        }
        #watermark {
            position: fixed;
            top: 25%;
            left: 15%;
            width: 70%;
            text-align: center;
            opacity: 0.06;
            z-index: -1000;
        }
        #watermark img {
            width: 100%;
            height: auto;
        }
        .table-full {
            width: 100%;
            border-collapse: collapse;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            border-bottom: 2px solid #059669;
            padding-bottom: 6px;
        }
        .company-name {
            font-size: 20px;
            font-weight: bold;
            color: #14532d;
            margin: 0;
            text-transform: uppercase;
        }
        .company-sub {
            font-size: 10.5px;
            color: #475569;
            margin-top: 2px;
        }
        .gst-badge {
            font-size: 10.5px;
            font-weight: bold;
            color: #047857;
            margin-top: 3px;
        }
        .invoice-title {
            font-size: 26px;
            font-weight: bold;
            color: #059669;
            text-align: right;
            margin: 0;
            text-transform: uppercase;
        }
        .invoice-subtitle {
            font-size: 11px;
            color: #047857;
            text-align: right;
            font-weight: bold;
            margin-top: 4px;
        }
        .box {
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            padding: 8px 10px;
            background-color: #f8fafc;
        }
        .box-title {
            font-size: 9.5px;
            font-weight: bold;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 2px;
        }
        .meta-table td {
            padding: 3px 0;
            font-size: 10.5px;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .fw-bold {
            font-weight: bold;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            margin-bottom: 10px;
            border: 1px solid #cbd5e1;
        }
        .items-table th {
            background-color: #0f172a;
            color: #ffffff;
            font-size: 10.5px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 6px 8px;
            border: 1px solid #0f172a;
        }
        .items-table td {
            padding: 7px 8px;
            border: 1px solid #e2e8f0;
            font-size: 10.5px;
            vertical-align: top;
        }
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            background-color: #ecfdf5;
            border: 1px solid #a7f3d0;
            border-radius: 4px;
        }
        .summary-table td {
            padding: 4px 6px;
            font-size: 10.5px;
            color: #065f46;
        }
        .summary-total {
            border-top: 2px solid #059669;
            font-size: 13px;
            font-weight: bold;
            color: #047857;
        }
        .terms-list {
            margin: 0;
            padding-left: 14px;
            font-size: 9.5px;
            color: #475569;
        }
        .terms-list li {
            margin-bottom: 3px;
        }
        .signature-table {
            width: 100%;
            margin-top: 22px;
        }
        .sig-line {
            border-top: 1px dashed #94a3b8;
            width: 70%;
            margin: 0 auto 4px auto;
        }
        .specs-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
            font-size: 10px;
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
        }
        .specs-table td {
            padding: 3px 5px;
            border: 1px solid #f1f5f9;
        }
    </style>
</head>
<body>

    @if(file_exists(public_path('assets/admin/img/logo.jpg')))
    <div id="watermark">
        <img src="{{ public_path('assets/admin/img/logo.jpg') }}" alt="Watermark">
    </div>
    @endif

    <!-- Header -->
    <table class="header-table">
        <tr>
            <td style="width: 60%; vertical-align: top;">
                <h1 class="company-name">SHREE KRISHNA AUTO GREEN</h1>
                <div class="company-sub">NEAR MAHAMANDIR CIRCLE, MAIN MANDORE ROAD, JODHPUR (RAJASTHAN)</div>
                <div class="gst-badge">GSTIN : 08ANQPD4555N1ZE | PAN : ANGPD4555N</div>
                <div class="company-sub">Email: vijay.deora429@gmail.com | Mobile: 7568899148</div>
            </td>
            <td style="width: 40%; vertical-align: top;" class="text-right">
                <h1 class="invoice-title">INVOICE</h1>
                <div class="invoice-subtitle">VEHICLE SALES</div>
                @if(file_exists(public_path('assets/admin/img/logo.jpg')))
                    <img src="{{ public_path('assets/admin/img/logo.jpg') }}" style="max-height: 45px; margin-top: 4px;" alt="Logo">
                @endif
            </td>
        </tr>
    </table>

    <!-- Invoice Meta & Customer Details -->
    <table class="table-full" style="margin-bottom: 8px;">
        <tr>
            <td style="width: 50%; vertical-align: top; padding-right: 4px;">
                <div class="box">
                    <div class="box-title">Invoice Information</div>
                    <table class="table-full meta-table">
                        <tr>
                            <td class="fw-bold">Invoice No:</td>
                            <td class="text-right fw-bold" style="color: #059669;">{{ $vehicleSalesInvoice->invoice_number }}</td>
                        </tr>
                        <tr>
                            <td>Date:</td>
                            <td class="text-right">{{ $vehicleSalesInvoice->invoice_date ? $vehicleSalesInvoice->invoice_date->format('d/m/Y') : '-' }}</td>
                        </tr>
                        <tr>
                            <td>Payment Mode:</td>
                            <td class="text-right fw-bold">{{ strtoupper($vehicleSalesInvoice->payment_mode ?? 'CASH') }}</td>
                        </tr>
                        @if($vehicleSalesInvoice->finance_name)
                        <tr>
                            <td>Finance Name:</td>
                            <td class="text-right fw-bold" style="color: #2563eb;">{{ $vehicleSalesInvoice->finance_name }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </td>
            <td style="width: 50%; vertical-align: top; padding-left: 4px;">
                <div class="box">
                    <div class="box-title">Bill To (Customer)</div>
                    <div style="font-size: 10px; font-weight: bold; color: #0f172a;">{{ $vehicleSalesInvoice->customer_name }}</div>
                    <div style="font-size: 8.5px; color: #475569;">
                        @if($vehicleSalesInvoice->customer_mobile) Mobile: {{ $vehicleSalesInvoice->customer_mobile }} <br> @endif
                        @if($vehicleSalesInvoice->customer_address) Address: {{ $vehicleSalesInvoice->customer_address }} <br> @endif
                        @if($vehicleSalesInvoice->customer_age) Age: {{ $vehicleSalesInvoice->customer_age }} Yrs | @endif
                        @if($vehicleSalesInvoice->customer_occupation) Occupation: {{ $vehicleSalesInvoice->customer_occupation }} @endif
                    </div>
                </div>
            </td>
        </tr>
    </table>

    <!-- Vehicle Details Table -->
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 50%;">Vehicle Description</th>
                <th style="width: 25%;">Identifiers</th>
                <th style="width: 25%;" class="text-right">Price / Details</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <div style="font-size: 11px; font-weight: bold; color: #0f172a;">
                        {{ $vehicleSalesInvoice->vehicleInventory->vehicle_description ?? 'EV Vehicle' }}
                    </div>
                    <div style="font-size: 8.5px; color: #047857; font-weight: bold; margin-top: 2px;">
                        Color: {{ $color_name ?? '-' }}
                    </div>

                    <table class="specs-table">
                        <tr>
                            <td><strong>Battery:</strong> {{ $battery_type ?? 'LITHIUM' }} ({{ $battery_make ?? 'LITHIUM' }})</td>
                            <td><strong>Battery No:</strong> {{ $vehicleSalesInvoice->vehicleInventory->battery_number ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Charger No:</strong> {{ $vehicleSalesInvoice->vehicleInventory->charger_number ?? '-' }}</td>
                            <td><strong>Motor No:</strong> {{ $vehicleSalesInvoice->vehicleInventory->motor_number ?? '-' }}</td>
                        </tr>
                    </table>

                    @if($vehicleSalesInvoice->warranty_notes)
                    <div style="background-color: #f0fdf4; border-left: 3px solid #10b981; padding: 4px; margin-top: 4px; font-size: 8px;">
                        <strong style="color: #14532d;">Warranty & Notes:</strong> {{ $vehicleSalesInvoice->warranty_notes }}
                    </div>
                    @endif
                </td>
                <td>
                    <div style="font-size: 8.5px; line-height: 1.4;">
                        <strong>Chassis No:</strong><br>{{ $vehicleSalesInvoice->vehicleInventory->chassis_number ?? '-' }}<br><br>
                        <strong>Engine/Motor No:</strong><br>{{ $vehicleSalesInvoice->vehicleInventory->engine_number ?? '-' }}
                    </div>
                </td>
                <td class="text-right" style="vertical-align: top;">
                    <div style="font-size: 9px; margin-bottom: 4px;">
                        <span>Rate:</span> <strong>₹{{ number_format($vehicleSalesInvoice->rate, 2) }}</strong>
                    </div>
                    @if($vehicleSalesInvoice->tax_regime === 'igst')
                        <div style="font-size: 8px; color: #64748b;">IGST ({{ $vehicleSalesInvoice->igst_rate ?? 0 }}%): ₹{{ number_format($vehicleSalesInvoice->igst_amount, 2) }}</div>
                    @else
                        <div style="font-size: 8px; color: #64748b;">CGST ({{ $vehicleSalesInvoice->cgst_rate ?? 0 }}%): ₹{{ number_format($vehicleSalesInvoice->cgst_amount, 2) }}</div>
                        <div style="font-size: 8px; color: #64748b;">SGST ({{ $vehicleSalesInvoice->sgst_rate ?? 0 }}%): ₹{{ number_format($vehicleSalesInvoice->sgst_amount, 2) }}</div>
                    @endif
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Calculations & Summary -->
    <table class="table-full" style="margin-bottom: 8px;">
        <tr>
            <!-- Left Panel: Bank Details & Terms -->
            <td style="width: 58%; vertical-align: top; padding-right: 4px;">
                <div class="box" style="margin-bottom: 6px;">
                    <div class="box-title">Bank Details</div>
                    <table class="table-full meta-table">
                        <tr><td>Account Name:</td><td class="fw-bold">SHREE KRISHNA AUTO GREEN</td></tr>
                        <tr><td>Bank Name:</td><td>State Bank of India, JODHPUR</td></tr>
                        <tr><td>Account No:</td><td class="fw-bold" style="color: #059669;">65261516842</td></tr>
                        <tr><td>IFSC Code:</td><td class="fw-bold">SBIN0050696</td></tr>
                    </table>
                </div>

                <div class="box">
                    <div class="box-title">Terms & Conditions</div>
                    <ol class="terms-list">
                        <li>Goods once sold will not be taken back or exchanged under any circumstances.</li>
                        <li>Warranty is covered solely as per the manufacturer's terms & policy.</li>
                        <li>Subject to Jodhpur Jurisdiction only.</li>
                    </ol>
                </div>
            </td>

            <!-- Right Panel: Financial Summary -->
            <td style="width: 42%; vertical-align: top; padding-left: 4px;">
                <table class="summary-table">
                    <tr>
                        <td>Sub Total:</td>
                        <td class="text-right fw-bold">₹{{ number_format($vehicleSalesInvoice->sub_total, 2) }}</td>
                    </tr>
                    @if($vehicleSalesInvoice->tax_regime === 'igst')
                    <tr>
                        <td>IGST:</td>
                        <td class="text-right">₹{{ number_format($vehicleSalesInvoice->igst_amount, 2) }}</td>
                    </tr>
                    @else
                    <tr>
                        <td>CGST Amount:</td>
                        <td class="text-right">₹{{ number_format($vehicleSalesInvoice->cgst_amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td>SGST Amount:</td>
                        <td class="text-right">₹{{ number_format($vehicleSalesInvoice->sgst_amount, 2) }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td>Total Amount:</td>
                        <td class="text-right fw-bold">₹{{ number_format($vehicleSalesInvoice->total, 2) }}</td>
                    </tr>
                    @if($vehicleSalesInvoice->nemmp_incentive > 0)
                    <tr>
                        <td>NEMMP Incentive (-):</td>
                        <td class="text-right text-danger">-₹{{ number_format($vehicleSalesInvoice->nemmp_incentive, 2) }}</td>
                    </tr>
                    @endif
                    @if($vehicleSalesInvoice->discount > 0)
                    <tr>
                        <td>Discount (-):</td>
                        <td class="text-right text-danger">-₹{{ number_format($vehicleSalesInvoice->discount, 2) }}</td>
                    </tr>
                    @endif
                    <tr class="summary-total">
                        <td>Grand Total:</td>
                        <td class="text-right">₹{{ number_format($vehicleSalesInvoice->grand_total, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Received Amount:</td>
                        <td class="text-right fw-bold" style="color: #047857;">₹{{ number_format($vehicleSalesInvoice->received_amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Balance Due:</td>
                        <td class="text-right fw-bold" style="color: #dc2626;">₹{{ number_format($vehicleSalesInvoice->balance, 2) }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Signature Section -->
    <table class="signature-table">
        <tr>
            <td style="width: 50%; text-align: center; vertical-align: bottom;">
                <div class="sig-line"></div>
                <div style="font-size: 9px; font-weight: bold; color: #475569;">Customer Signature</div>
            </td>
            <td style="width: 50%; text-align: center; vertical-align: bottom;">
                <div style="font-size: 8.5px; font-weight: bold; color: #14532d; margin-bottom: 25px;">For SHREE KRISHNA AUTO GREEN</div>
                <div class="sig-line"></div>
                <div style="font-size: 9px; font-weight: bold; color: #475569;">Authorized Signatory</div>
            </td>
        </tr>
    </table>

</body>
</html>
