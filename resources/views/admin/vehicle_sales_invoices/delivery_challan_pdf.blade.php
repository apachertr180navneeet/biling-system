<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Delivery Challan - {{ $vehicleSalesInvoice->invoice_number }}</title>
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
            margin-bottom: 10px;
            border-bottom: 2px solid #059669;
            padding-bottom: 6px;
        }
        .company-name {
            font-size: 19px;
            font-weight: bold;
            color: #14532d;
            margin: 0;
            text-transform: uppercase;
        }
        .company-sub {
            font-size: 10px;
            color: #475569;
            margin-top: 2px;
        }
        .gst-badge {
            font-size: 10.5px;
            font-weight: bold;
            color: #047857;
            margin-top: 3px;
        }
        .challan-title {
            font-size: 22px;
            font-weight: bold;
            color: #059669;
            text-align: right;
            margin: 0;
            text-transform: uppercase;
        }
        .challan-subtitle {
            font-size: 10.5px;
            color: #047857;
            text-align: right;
            font-weight: bold;
            margin-top: 3px;
        }
        .box {
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            padding: 7px 9px;
            background-color: #f8fafc;
        }
        .box-title {
            font-size: 9px;
            font-weight: bold;
            color: #059669;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 2px;
        }
        .meta-table td {
            padding: 2.5px 0;
            font-size: 10px;
        }
        .text-right {
            text-align: right;
        }
        .fw-bold {
            font-weight: bold;
        }
        .vehicle-box {
            border: 1px solid #0f172a;
            border-radius: 4px;
            margin-top: 8px;
            margin-bottom: 8px;
        }
        .vehicle-box-header {
            background-color: #0f172a;
            color: #ffffff;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 5px 8px;
        }
        .specs-table {
            width: 100%;
            border-collapse: collapse;
        }
        .specs-table td {
            padding: 4px 7px;
            font-size: 10px;
            border: 1px solid #f1f5f9;
        }
        .accessories-box {
            border: 1px solid #a7f3d0;
            background-color: #f0fdf4;
            border-radius: 4px;
            padding: 7px 9px;
            margin-top: 8px;
            margin-bottom: 8px;
        }
        .accessories-title {
            font-size: 9.5px;
            font-weight: bold;
            color: #065f46;
            text-transform: uppercase;
            margin-bottom: 4px;
        }
        .checklist-table {
            width: 100%;
            border-collapse: collapse;
        }
        .checklist-table td {
            font-size: 9.5px;
            padding: 2px 4px;
            color: #1e293b;
        }
        .declaration {
            border: 1px solid #cbd5e1;
            background-color: #fafafa;
            border-radius: 4px;
            padding: 6px 8px;
            font-size: 9px;
            color: #475569;
            line-height: 1.4;
            margin-top: 8px;
        }
        .signature-table {
            width: 100%;
            margin-top: 25px;
        }
        .sig-line {
            border-top: 1px dashed #64748b;
            width: 80%;
            margin: 0 auto 4px auto;
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
                <div class="company-sub">Email: vijay.deora429@gmail.com | Mobile: 7568899148, 9829028792</div>
            </td>
            <td style="width: 40%; vertical-align: top;" class="text-right">
                <h1 class="challan-title">DELIVERY CHALLAN</h1>
                <div class="challan-subtitle">VEHICLE HANDOVER NOTE</div>
                @if(file_exists(public_path('assets/admin/img/logo.jpg')))
                    <img src="{{ public_path('assets/admin/img/logo.jpg') }}" style="max-height: 40px; margin-top: 3px;" alt="Logo">
                @endif
            </td>
        </tr>
    </table>

    <!-- Challan Meta & Customer Details -->
    <table class="table-full" style="margin-bottom: 6px;">
        <tr>
            <td style="width: 50%; vertical-align: top; padding-right: 4px;">
                <div class="box">
                    <div class="box-title">Challan & Invoice Information</div>
                    <table class="table-full meta-table">
                        <tr>
                            <td class="fw-bold">Challan No:</td>
                            <td class="text-right fw-bold" style="color: #059669;">DC-{{ $vehicleSalesInvoice->invoice_number }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Invoice No:</td>
                            <td class="text-right fw-bold">{{ $vehicleSalesInvoice->invoice_number }}</td>
                        </tr>
                        <tr>
                            <td>Delivery Date:</td>
                            <td class="text-right">{{ $vehicleSalesInvoice->invoice_date ? $vehicleSalesInvoice->invoice_date->format('d/m/Y') : '-' }}</td>
                        </tr>
                        @if($vehicleSalesInvoice->created_at)
                        <tr>
                            <td>Delivery Time:</td>
                            <td class="text-right">{{ $vehicleSalesInvoice->created_at->format('h:i A') }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td>Payment Mode:</td>
                            <td class="text-right fw-bold">{{ strtoupper($vehicleSalesInvoice->payment_mode ?? 'CASH') }}</td>
                        </tr>
                        @if($vehicleSalesInvoice->finance_name)
                        <tr>
                            <td>HPN / Finance:</td>
                            <td class="text-right fw-bold" style="color: #2563eb;">{{ $vehicleSalesInvoice->finance_name }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </td>
            <td style="width: 50%; vertical-align: top; padding-left: 4px;">
                <div class="box">
                    <div class="box-title">Consignee / Customer Details</div>
                    <div style="font-size: 11px; font-weight: bold; color: #0f172a;">{{ $vehicleSalesInvoice->customer_name }}</div>
                    <div style="font-size: 9px; color: #475569; line-height: 1.35; margin-top: 2px;">
                        @php
                            $customerGstin = $vehicleSalesInvoice->customer_gstin ?: ($vehicleSalesInvoice->customer->gstin ?? null);
                        @endphp
                        @if(!empty($customerGstin)) <strong style="color: #047857;">GSTIN:</strong> {{ $customerGstin }} <br> @endif
                        @if($vehicleSalesInvoice->customer_mobile) <strong>Mobile:</strong> {{ $vehicleSalesInvoice->customer_mobile }} <br> @endif
                        @if($vehicleSalesInvoice->customer_address) <strong>Address:</strong> {{ $vehicleSalesInvoice->customer_address }} <br> @endif
                        @if($vehicleSalesInvoice->customer_age) <strong>Age:</strong> {{ $vehicleSalesInvoice->customer_age }} Yrs | @endif
                        @if($vehicleSalesInvoice->customer_occupation) <strong>Occupation:</strong> {{ $vehicleSalesInvoice->customer_occupation }} @endif
                    </div>
                </div>
            </td>
        </tr>
    </table>

    <!-- Vehicle Details Box -->
    <div class="vehicle-box">
        <div class="vehicle-box-header">Vehicle Details & Identifiers</div>
        <table class="specs-table">
            <tr>
                <td style="width: 50%;"><strong>Vehicle Model:</strong> {{ $vehicleSalesInvoice->vehicleInventory->vehicle_description ?? 'EV Vehicle' }}</td>
                <td style="width: 50%;"><strong>Color:</strong> <span style="color: #059669; font-weight: bold;">{{ $color_name ?? ($vehicleSalesInvoice->vehicleInventory->color_name ?? '-') }}</span></td>
            </tr>
            <tr>
                <td><strong>Chassis Number:</strong> <span style="font-weight: bold; color: #047857;">{{ $vehicleSalesInvoice->vehicleInventory->chassis_number ?? '-' }}</span></td>
                <td><strong>Motor / Engine No:</strong> <span style="font-weight: bold;">{{ $vehicleSalesInvoice->vehicleInventory->motor_number ?? ($vehicleSalesInvoice->vehicleInventory->engine_number ?? '-') }}</span></td>
            </tr>
            <tr>
                <td><strong>Battery Type:</strong> {{ $battery_type ?? 'LITHIUM' }} ({{ $battery_make ?? 'LITHIUM' }})</td>
                <td><strong>Battery Number:</strong> {{ $vehicleSalesInvoice->vehicleInventory->battery_number ?? '-' }}</td>
            </tr>
            <tr>
                <td><strong>Charger Number:</strong> {{ $vehicleSalesInvoice->vehicleInventory->charger_number ?? '-' }}</td>
                <td><strong>Controller Number:</strong> {{ $vehicleSalesInvoice->vehicleInventory->controller_number ?? '-' }}</td>
            </tr>
            <tr>
                <td><strong>Convertor Number:</strong> {{ $vehicleSalesInvoice->vehicleInventory->convertor_number ?? '-' }}</td>
                <td><strong>Manual Number:</strong> {{ $vehicleSalesInvoice->vehicleInventory->manual_number ?? '-' }}</td>
            </tr>
        </table>
    </div>

    <!-- Accessories Checklist -->
    <div class="accessories-box">
        <div class="accessories-title">Accessories & Items Handover Checklist:</div>
        <table class="checklist-table">
            <tr>
                <td style="width: 33%;">[ &#10003; ] Stepny / Spare Tyre</td>
                <td style="width: 33%;">[ &#10003; ] Jack & Handle</td>
                <td style="width: 34%;">[ &#10003; ] Tool Kit & Spanners</td>
            </tr>
            <tr>
                <td>[ &#10003; ] Stereo / Sound System</td>
                <td>[ &#10003; ] Side Mirrors (Pair)</td>
                <td>[ &#10003; ] Smart EV Charger</td>
            </tr>
            <tr>
                <td>[ &#10003; ] Battery Set Installed</td>
                <td>[ &#10003; ] Keys (2 Sets)</td>
                <td>[ &#10003; ] Manual & Warranty Card</td>
            </tr>
        </table>
    </div>

    @if($vehicleSalesInvoice->warranty_notes)
    <div style="background-color: #f8fafc; border-left: 3px solid #059669; padding: 4px 8px; margin-top: 4px; font-size: 8.5px;">
        <strong style="color: #14532d;">Warranty & Notes:</strong> {{ $vehicleSalesInvoice->warranty_notes }}
    </div>
    @endif

    <!-- Declaration -->
    <div class="declaration">
        <strong>Customer Declaration & Acknowledgement:</strong><br>
        I/We hereby acknowledge receipt of the above-mentioned vehicle along with standard tools, accessories, charger, battery and documents in good running condition and order. All serial numbers have been physically checked and verified. Our responsibility ceases upon delivery.
    </div>

    <!-- Signatures -->
    <table class="signature-table">
        <tr>
            <td style="width: 50%; text-align: center; vertical-align: bottom;">
                <div style="height: 35px;"></div>
                <div class="sig-line"></div>
                <div style="font-size: 10.5px; font-weight: bold; color: #0f172a;">Customer's Signature</div>
                <div style="font-size: 9px; color: #64748b;">({{ $vehicleSalesInvoice->customer_name }})</div>
            </td>
            <td style="width: 50%; text-align: center; vertical-align: bottom;">
                <div style="height: 35px;"></div>
                <div class="sig-line"></div>
                <div style="font-size: 10.5px; font-weight: bold; color: #0f172a;">Authorized Signatory</div>
                <div style="font-size: 9px; color: #64748b;">SHREE KRISHNA AUTO GREEN</div>
            </td>
        </tr>
    </table>

</body>
</html>
