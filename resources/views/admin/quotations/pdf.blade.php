<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Proforma Invoice - {{ $quotation->quotation_number }}</title>
    <style>
        @page {
            margin: 20px 25px;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            color: #1e293b;
            font-size: 10.5px;
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
        .invoice-title {
            font-size: 22px;
            font-weight: bold;
            color: #059669;
            text-align: right;
            margin: 0;
            text-transform: uppercase;
        }
        .invoice-subtitle {
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
            color: #64748b;
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
        .text-center {
            text-align: center;
        }
        .fw-bold {
            font-weight: bold;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
            margin-bottom: 8px;
            border: 1px solid #cbd5e1;
        }
        .items-table th {
            background-color: #0f172a;
            color: #ffffff;
            font-size: 9.5px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 5px 6px;
            border: 1px solid #0f172a;
        }
        .items-table td {
            padding: 6px;
            border: 1px solid #e2e8f0;
            font-size: 10px;
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
            padding: 3.5px 6px;
            font-size: 10px;
            color: #065f46;
        }
        .summary-total {
            border-top: 2px solid #059669;
            font-size: 12px;
            font-weight: bold;
            color: #047857;
        }
        .signature-table {
            width: 100%;
            margin-top: 20px;
        }
        .sig-line {
            border-top: 1px dashed #94a3b8;
            width: 70%;
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
                <h1 class="invoice-title">PROFORMA INVOICE</h1>
                <div class="invoice-subtitle">QUOTATION / ESTIMATE</div>
                @if(file_exists(public_path('assets/admin/img/logo.jpg')))
                    <img src="{{ public_path('assets/admin/img/logo.jpg') }}" style="max-height: 40px; margin-top: 3px;" alt="Logo">
                @endif
            </td>
        </tr>
    </table>

    <!-- Meta & Customer Details -->
    <table class="table-full" style="margin-bottom: 6px;">
        <tr>
            <td style="width: 50%; vertical-align: top; padding-right: 4px;">
                <div class="box">
                    <div class="box-title">Proforma / Quotation Info</div>
                    <table class="table-full meta-table">
                        <tr>
                            <td class="fw-bold">PI / Quotation No:</td>
                            <td class="text-right fw-bold" style="color: #059669;">{{ $quotation->quotation_number }}</td>
                        </tr>
                        <tr>
                            <td>Date:</td>
                            <td class="text-right">{{ $quotation->quotation_date ? $quotation->quotation_date->format('d/m/Y') : '-' }}</td>
                        </tr>
                        <tr>
                            <td>Type:</td>
                            <td class="text-right fw-bold">{{ strtoupper($quotation->type) }}</td>
                        </tr>
                        <tr>
                            <td>Tax Regime:</td>
                            <td class="text-right fw-bold">
                                @if($quotation->tax_regime === 'cgst_sgst')
                                    CGST + SGST (Intrastate)
                                @else
                                    IGST (Interstate)
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>Place of Supply:</td>
                            <td class="text-right">{{ $quotation->place_of_supply ?? '08-Rajasthan' }}</td>
                        </tr>
                    </table>
                </div>
            </td>
            <td style="width: 50%; vertical-align: top; padding-left: 4px;">
                <div class="box">
                    <div class="box-title">Customer / Buyer Details</div>
                    <div style="font-size: 10.5px; font-weight: bold; color: #0f172a;">{{ $quotation->customer_name }}</div>
                    <div style="font-size: 9px; color: #475569; line-height: 1.35; margin-top: 2px;">
                        @if($quotation->customer_mobile) <strong>Mobile:</strong> {{ $quotation->customer_mobile }} <br> @endif
                        @if($quotation->customer_address) <strong>Address:</strong> {{ $quotation->customer_address }} <br> @endif
                        @if($quotation->customer_gstin) <strong>GSTIN:</strong> <span style="color: #047857; font-weight: bold;">{{ $quotation->customer_gstin }}</span> <br> @endif
                        @if($quotation->customer_pan) <strong>PAN:</strong> {{ $quotation->customer_pan }} @endif
                    </div>
                </div>
            </td>
        </tr>
    </table>

    <!-- Items & GST Details Table -->
    @if($quotation->type === 'vehicle')
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 42%;">Vehicle Specifications / Variant</th>
                    <th class="text-right" style="width: 13%;">Rate (Base)</th>
                    <th class="text-right" style="width: 10%;">Discount</th>
                    <th class="text-right" style="width: 10%;">Incentive</th>
                    <th class="text-right" style="width: 12%;">Taxable Value</th>
                    <th class="text-right" style="width: 13%;">GST Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <div style="font-size: 10.5px; font-weight: bold; color: #0f172a;">
                            {{ $quotation->vehicleMaster->variant_name ?? 'EV Vehicle' }}
                        </div>
                        <div style="font-size: 8.5px; color: #047857; font-weight: bold; margin-top: 2px;">
                            Color: {{ $quotation->vehicleMaster->color_name ?? '-' }} | Fuel: {{ $quotation->vehicleMaster->fuel_type ?? 'Electric' }}
                        </div>
                        <div style="font-size: 8px; color: #64748b; margin-top: 2px;">
                            Battery Make: {{ $quotation->vehicleMaster->battery_make ?? 'LITHIUM' }} ({{ $quotation->vehicleMaster->battery_type ?? 'LITHIUM' }})
                        </div>
                        <div style="font-size: 8px; font-weight: bold; color: #0f172a; margin-top: 3px;">
                            ON ROAD PRICE INCLUDING GST, RTO & INSURANCE
                        </div>
                    </td>
                    <td class="text-right">₹{{ number_format($quotation->rate, 2) }}</td>
                    <td class="text-right" style="color: #dc2626;">-₹{{ number_format($quotation->discount, 2) }}</td>
                    <td class="text-right" style="color: #dc2626;">-₹{{ number_format($quotation->nemmp_incentive, 2) }}</td>
                    <td class="text-right fw-bold">₹{{ number_format($quotation->taxable_amount, 2) }}</td>
                    <td class="text-right">
                        @if($quotation->tax_regime === 'cgst_sgst')
                            <div style="font-size: 8px; color: #64748b;">CGST ({{ $quotation->cgst_rate ?? 2.5 }}%): ₹{{ number_format($quotation->cgst_amount, 2) }}</div>
                            <div style="font-size: 8px; color: #64748b;">SGST ({{ $quotation->sgst_rate ?? 2.5 }}%): ₹{{ number_format($quotation->sgst_amount, 2) }}</div>
                            <strong>₹{{ number_format($quotation->cgst_amount + $quotation->sgst_amount, 2) }}</strong>
                        @else
                            <div style="font-size: 8px; color: #64748b;">IGST ({{ $quotation->igst_rate ?? 5 }}%):</div>
                            <strong>₹{{ number_format($quotation->igst_amount, 2) }}</strong>
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>
    @else
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 4%;">#</th>
                    <th style="width: 38%;">Part Details</th>
                    <th class="text-right" style="width: 14%;">Rate</th>
                    <th class="text-center" style="width: 8%;">Qty</th>
                    <th class="text-center" style="width: 10%;">GST %</th>
                    <th class="text-right" style="width: 12%;">GST Amount</th>
                    <th class="text-right" style="width: 14%;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($quotation->items as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        <div style="font-weight: bold; color: #0f172a;">{{ $item->sparePart->name ?? '-' }}</div>
                        <div style="font-size: 8px; color: #64748b;">Part No: {{ $item->sparePart->part_no ?? '-' }}</div>
                    </td>
                    <td class="text-right">₹{{ number_format($item->rate, 2) }}</td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-center">{{ $item->tax_percentage }}%</td>
                    <td class="text-right">
                        @if($quotation->tax_regime === 'cgst_sgst')
                            <div style="font-size: 7.5px; color: #64748b;">CGST: ₹{{ number_format($item->cgst_amount, 2) }}</div>
                            <div style="font-size: 7.5px; color: #64748b;">SGST: ₹{{ number_format($item->sgst_amount, 2) }}</div>
                        @else
                            <div style="font-size: 7.5px; color: #64748b;">IGST: ₹{{ number_format($item->igst_amount, 2) }}</div>
                        @endif
                    </td>
                    <td class="text-right fw-bold">₹{{ number_format($item->amount, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- Specs / Bank Details & Summary Table -->
    <table class="table-full" style="margin-top: 4px;">
        <tr>
            <td style="width: 55%; vertical-align: top; padding-right: 6px;">
                @if($quotation->type === 'vehicle')
                    <div style="font-size: 8px; border: 1px solid #cbd5e1; padding: 5px; border-radius: 4px; background: #fafafa; margin-bottom: 5px;">
                        <strong style="font-size: 8.5px; color: #111; display: block; margin-bottom: 3px; border-bottom: 1px solid #e2e8f0; padding-bottom: 2px;">TECHNICAL SPECIFICATIONS:</strong>
                        <table style="width: 100%; border-collapse: collapse; font-size: 8px;">
                            <tr><td style="width: 48%; padding: 1px 0;"><strong>Model / Maker:</strong></td><td>{{ $quotation->model_maker_name ?? 'E- PASSENGER/ARZOO/PASSANGER' }}</td></tr>
                            <tr><td style="padding: 1px 0;"><strong>Gross Weight:</strong></td><td>{{ $quotation->gross_weight ?? '60 KG' }}</td></tr>
                            <tr><td style="padding: 1px 0;"><strong>Charging Time:</strong></td><td>{{ $quotation->charging_time ?? '3-4 HR' }}</td></tr>
                            <tr><td style="padding: 1px 0;"><strong>Performance:</strong></td><td>{{ $quotation->performance ?? 'HIGH SPEED 25 KM/HR' }}</td></tr>
                            <tr><td style="padding: 1px 0;"><strong>Charger Output:</strong></td><td>{{ $quotation->charger_output ?? 'DC 51V 105 AH (1 LITHIUM BATTERY)' }}</td></tr>
                            <tr><td style="padding: 1px 0;"><strong>Motor Output:</strong></td><td>{{ $quotation->motor_output ?? '1200 W' }}</td></tr>
                            <tr><td style="padding: 1px 0;"><strong>Seating Capacity:</strong></td><td>{{ $quotation->seating_capacity ?? '5' }}</td></tr>
                            <tr><td style="padding: 1px 0;"><strong>Type of Brake:</strong></td><td>{{ $quotation->type_of_break ?? 'DRUM BRAKE' }}</td></tr>
                            <tr><td style="padding: 1px 0;"><strong>Roof Top ABS / Windshield:</strong></td><td>{{ $quotation->roof_top_abs ?? 'YES' }} / {{ $quotation->front_fiber_wind_shield ?? 'YES' }}</td></tr>
                            <tr><td style="padding: 1px 0;"><strong>Accessories:</strong></td><td>STEPNY, JACK, TOOL KIT, STEREO, SIDE MIRRORS</td></tr>
                        </table>
                    </div>

                    <div style="font-size: 8px; border: 1px solid #cbd5e1; padding: 4px; border-radius: 4px; background: #fafafa;">
                        <strong style="font-size: 8.5px; color: #111; display: block; margin-bottom: 2px;">BANK DETAILS FOR PAYMENT:</strong>
                        <strong>A/C Holder:</strong> SHREE KRISHNA AUTO GREEN | <strong>A/C No:</strong> 65261516842<br>
                        <strong>Bank / Branch:</strong> SBI, STADIUM SHOPPING CENTRE | <strong>IFSC:</strong> SBIN0050696
                    </div>
                @elseif($quotation->remarks)
                    <div style="font-size: 8.5px; color: #475569; border: 1px solid #cbd5e1; padding: 6px; border-radius: 4px; background: #fafafa;">
                        <strong style="color: #0f172a;">Remarks / Terms:</strong><br>
                        {!! nl2br(e($quotation->remarks)) !!}
                    </div>
                @endif
            </td>
            <td style="width: 45%; vertical-align: top; padding-left: 6px;">
                <table class="summary-table">
                    <tr>
                        <td>Taxable Amount:</td>
                        <td class="text-right fw-bold">₹{{ number_format($quotation->taxable_amount, 2) }}</td>
                    </tr>
                    @if($quotation->tax_regime === 'cgst_sgst')
                        <tr>
                            <td>CGST ({{ $quotation->cgst_rate ?? 2.5 }}%):</td>
                            <td class="text-right">₹{{ number_format($quotation->cgst_amount, 2) }}</td>
                        </tr>
                        <tr>
                            <td>SGST ({{ $quotation->sgst_rate ?? 2.5 }}%):</td>
                            <td class="text-right">₹{{ number_format($quotation->sgst_amount, 2) }}</td>
                        </tr>
                    @else
                        <tr>
                            <td>IGST ({{ $quotation->igst_rate ?? 5 }}%):</td>
                            <td class="text-right">₹{{ number_format($quotation->igst_amount, 2) }}</td>
                        </tr>
                    @endif
                    @if($quotation->round_off != 0)
                    <tr>
                        <td>Round Off:</td>
                        <td class="text-right">₹{{ number_format($quotation->round_off, 2) }}</td>
                    </tr>
                    @endif
                    <tr class="summary-total">
                        <td>Total Amount:</td>
                        <td class="text-right">₹{{ number_format($quotation->total_amount, 2) }}</td>
                    </tr>
                </table>

                <div style="font-size: 8px; color: #475569; margin-top: 6px; border: 1px solid #e2e8f0; padding: 4px; border-radius: 4px;">
                    <strong>Terms & Validity:</strong>
                    <div style="line-height: 1.3; margin-top: 2px;">
                        • Prices are valid for 15 days from issue date.<br>
                        • Delivery subject to stock availability & full payment.<br>
                        • 12 months warranty on Motor & Controller, 36 months on Battery & Charger.
                    </div>
                </div>
            </td>
        </tr>
    </table>

    <!-- Signatures -->
    <table class="signature-table">
        <tr>
            <td style="width: 50%; text-align: center; vertical-align: bottom;">
                <div style="height: 35px;"></div>
                <div class="sig-line"></div>
                <div style="font-size: 10px; font-weight: bold; color: #0f172a;">Customer's Acceptance</div>
                <div style="font-size: 8.5px; color: #64748b;">({{ $quotation->customer_name }})</div>
            </td>
            <td style="width: 50%; text-align: center; vertical-align: bottom;">
                <div style="height: 35px;"></div>
                <div class="sig-line"></div>
                <div style="font-size: 10px; font-weight: bold; color: #0f172a;">Authorized Signatory</div>
                <div style="font-size: 8.5px; color: #64748b;">SHREE KRISHNA AUTO GREEN</div>
            </td>
        </tr>
    </table>

</body>
</html>
