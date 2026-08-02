<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Part Sales Invoice - {{ $partSalesInvoice->invoice_number }}</title>
    <style>
        @page {
            margin: 25px 30px;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            color: #1e293b;
            font-size: 10px;
            line-height: 1.3;
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
            padding-bottom: 5px;
        }
        .company-name {
            font-size: 18px;
            font-weight: bold;
            color: #14532d;
            margin: 0;
            text-transform: uppercase;
        }
        .company-sub {
            font-size: 9px;
            color: #475569;
            margin-top: 2px;
        }
        .gst-badge {
            font-size: 9px;
            font-weight: bold;
            color: #047857;
            margin-top: 3px;
        }
        .invoice-title {
            font-size: 24px;
            font-weight: bold;
            color: #059669;
            text-align: right;
            margin: 0;
            text-transform: uppercase;
        }
        .invoice-subtitle {
            font-size: 10px;
            color: #047857;
            text-align: right;
            font-weight: bold;
            margin-top: 4px;
        }
        .box {
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            padding: 6px 8px;
            background-color: #f8fafc;
        }
        .box-title {
            font-size: 8px;
            font-weight: bold;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 2px;
        }
        .meta-table td {
            padding: 2px 0;
            font-size: 9px;
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
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 5px 6px;
            border: 1px solid #0f172a;
        }
        .items-table td {
            padding: 5px 6px;
            border: 1px solid #e2e8f0;
            font-size: 9px;
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
            padding: 3px 6px;
            font-size: 9px;
            color: #065f46;
        }
        .summary-total {
            border-top: 2px solid #059669;
            font-size: 11px;
            font-weight: bold;
            color: #047857;
        }
        .terms-list {
            margin: 0;
            padding-left: 12px;
            font-size: 8px;
            color: #475569;
        }
        .terms-list li {
            margin-bottom: 2px;
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

    @php
    if (!function_exists('convertPartPdfNumberToWords')) {
        function convertPartPdfNumberToWords($number) {
            $no = floor($number);
            $point = round($number - $no, 2) * 100;
            $hundred = null;
            $digits_1 = strlen($no);
            $i = 0;
            $str = array();
            $words = array(
                '0' => '', '1' => 'One', '2' => 'Two',
                '3' => 'Three', '4' => 'Four', '5' => 'Five', '6' => 'Six',
                '7' => 'Seven', '8' => 'Eight', '9' => 'Nine',
                '10' => 'Ten', '11' => 'Eleven', '12' => 'Twelve',
                '13' => 'Thirteen', '14' => 'Fourteen',
                '15' => 'Fifteen', '16' => 'Sixteen', '17' => 'Seventeen',
                '18' => 'Eighteen', '19' =>'Nineteen', '20' => 'Twenty',
                '30' => 'Thirty', '40' => 'Forty', '50' => 'Fifty',
                '60' => 'Sixty', '70' => 'Seventy', '80' => 'Eighty',
                '90' => 'Ninety'
            );
            $digits = array('', 'Hundred', 'Thousand', 'Lakh', 'Crore');
            while ($i < $digits_1) {
                $divider = ($i == 2) ? 10 : 100;
                $number = floor($no % $divider);
                $no = floor($no / $divider);
                $i += ($divider == 10) ? 1 : 2;
                if ($number) {
                    $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
                    $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
                    $str [] = ($number < 21) ? $words[$number].' '. $digits[$counter]. $plural. $hundred : $words[floor($number / 10) * 10].' '.$words[$number % 10]. ' '.$digits[$counter].$plural.$hundred;
                } else $str[] = null;
            }
            $Rupees = implode('', array_reverse($str));
            $paise = ($point > 0) ? " and " . ($words[floor($point / 10) * 10] . " " . $words[$point % 10]) . ' Paise' : '';
            return ($Rupees ? $Rupees . 'Rupees ' : '') . $paise . ' Only';
        }
    }
    @endphp

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
                <div class="company-sub">NH 65 NEAR ROADWAYS BUS STAND JODHPUR, JODHPUR, Rajasthan, 342001</div>
                <div class="gst-badge">GSTIN : 08ANQPD4555N1ZE | PAN : ANGPD4555N</div>
                <div class="company-sub">Email: vijay.deora429@gmail.com | Mobile: 7568899148</div>
            </td>
            <td style="width: 40%; vertical-align: top;" class="text-right">
                <h1 class="invoice-title">INVOICE</h1>
                <div class="invoice-subtitle">PARTS SALES</div>
                @if(file_exists(public_path('assets/admin/img/logo.jpg')))
                    <img src="{{ public_path('assets/admin/img/logo.jpg') }}" style="max-height: 45px; margin-top: 4px;" alt="Logo">
                @endif
            </td>
        </tr>
    </table>

    <!-- Invoice Meta & Customer Details -->
    <table class="table-full" style="margin-bottom: 8px;">
        <tr>
            <td style="width: 35%; vertical-align: top; padding-right: 4px;">
                <div class="box">
                    <div class="box-title">Invoice Info</div>
                    <table class="table-full meta-table">
                        <tr>
                            <td class="fw-bold">Invoice No:</td>
                            <td class="text-right fw-bold" style="color: #059669;">{{ $partSalesInvoice->invoice_number }}</td>
                        </tr>
                        <tr>
                            <td>Date:</td>
                            <td class="text-right">{{ $partSalesInvoice->invoice_date ? $partSalesInvoice->invoice_date->format('d/m/Y') : '-' }}</td>
                        </tr>
                        <tr>
                            <td>Place of Supply:</td>
                            <td class="text-right">{{ $partSalesInvoice->place_of_supply ?? 'Rajasthan' }}</td>
                        </tr>
                        <tr>
                            <td>Payment Mode:</td>
                            <td class="text-right fw-bold">{{ strtoupper($partSalesInvoice->payment_mode ?? 'CASH') }}</td>
                        </tr>
                    </table>
                </div>
            </td>
            <td style="width: 32.5%; vertical-align: top; padding-left: 2px; padding-right: 2px;">
                <div class="box">
                    <div class="box-title">Bill To</div>
                    <div style="font-size: 10px; font-weight: bold; color: #0f172a;">{{ $partSalesInvoice->customer_name }}</div>
                    <div style="font-size: 8.5px; color: #475569;">
                        @if($partSalesInvoice->customer_mobile) Mobile: {{ $partSalesInvoice->customer_mobile }} <br> @endif
                        @if($partSalesInvoice->customer_address) Address: {{ $partSalesInvoice->customer_address }} <br> @endif
                        @if($partSalesInvoice->customer_gstin) GSTIN: {{ $partSalesInvoice->customer_gstin }} <br> @endif
                        @if($partSalesInvoice->customer_pan) PAN: {{ $partSalesInvoice->customer_pan }} @endif
                    </div>
                </div>
            </td>
            <td style="width: 32.5%; vertical-align: top; padding-left: 4px;">
                <div class="box">
                    <div class="box-title">Ship To</div>
                    <div style="font-size: 10px; font-weight: bold; color: #0f172a;">{{ $partSalesInvoice->customer_name }}</div>
                    <div style="font-size: 8.5px; color: #475569;">
                        @if($partSalesInvoice->customer_mobile) Mobile: {{ $partSalesInvoice->customer_mobile }} <br> @endif
                        @if($partSalesInvoice->customer_address) Address: {{ $partSalesInvoice->customer_address }} <br> @endif
                        Place of Supply: {{ $partSalesInvoice->place_of_supply ?? 'Rajasthan' }}
                    </div>
                </div>
            </td>
        </tr>
    </table>

    <!-- Items Table -->
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 5%;" class="text-center">#</th>
                <th style="width: 45%;">Items & Description</th>
                <th style="width: 10%;" class="text-right">Qty</th>
                <th style="width: 13%;" class="text-right">Rate</th>
                <th style="width: 12%;" class="text-right">Tax (GST)</th>
                <th style="width: 15%;" class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($partSalesInvoice->items as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>
                    <div style="font-size: 9.5px; font-weight: bold; color: #0f172a;">
                        {{ $item->sparePart->name ?? 'Spare Part' }}
                    </div>
                    @if(optional($item->sparePart)->part_no)
                        <div style="font-size: 8px; color: #64748b;">Part No: {{ $item->sparePart->part_no }}</div>
                    @endif
                    @if($item->serial_no_warranty_notes)
                        <div style="font-size: 8px; color: #047857;">Notes: {{ $item->serial_no_warranty_notes }}</div>
                    @endif
                </td>
                <td class="text-right fw-bold">{{ $item->quantity }}</td>
                <td class="text-right">₹{{ number_format($item->rate, 2) }}</td>
                <td class="text-right">
                    ₹{{ number_format($item->tax_amount, 2) }}
                    <div style="font-size: 7.5px; color: #64748b;">({{ number_format($item->tax_percentage, 0) }}%)</div>
                </td>
                <td class="text-right fw-bold">₹{{ number_format($item->total_amount, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Calculations & Summary -->
    <table class="table-full" style="margin-bottom: 8px;">
        <tr>
            <!-- Left Panel: Bank Details & Amount in Words -->
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

                <div class="box" style="margin-bottom: 6px;">
                    <div class="box-title">Total In Words</div>
                    <div style="font-size: 9px; font-weight: bold; color: #047857;">
                        {{ convertPartPdfNumberToWords($partSalesInvoice->total_amount) }}
                    </div>
                </div>

                <div class="box">
                    <div class="box-title">Terms & Conditions</div>
                    <ol class="terms-list">
                        <li>Goods once sold will not be taken back or exchanged.</li>
                        <li>Warranty as per manufacturer terms.</li>
                        <li>Subject to Jodhpur Jurisdiction.</li>
                    </ol>
                </div>
            </td>

            <!-- Right Panel: Financial Summary -->
            <td style="width: 42%; vertical-align: top; padding-left: 4px;">
                <table class="summary-table">
                    <tr>
                        <td>Taxable Amount:</td>
                        <td class="text-right fw-bold">₹{{ number_format($partSalesInvoice->taxable_amount, 2) }}</td>
                    </tr>
                    @if($partSalesInvoice->tax_regime === 'igst')
                    <tr>
                        <td>IGST Amount:</td>
                        <td class="text-right">₹{{ number_format($partSalesInvoice->igst_amount, 2) }}</td>
                    </tr>
                    @else
                    <tr>
                        <td>CGST Amount:</td>
                        <td class="text-right">₹{{ number_format($partSalesInvoice->cgst_amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td>SGST Amount:</td>
                        <td class="text-right">₹{{ number_format($partSalesInvoice->sgst_amount, 2) }}</td>
                    </tr>
                    @endif
                    @if($partSalesInvoice->round_off != 0)
                    <tr>
                        <td>Round Off:</td>
                        <td class="text-right">₹{{ number_format($partSalesInvoice->round_off, 2) }}</td>
                    </tr>
                    @endif
                    <tr class="summary-total">
                        <td>Total Amount:</td>
                        <td class="text-right">₹{{ number_format($partSalesInvoice->total_amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Received Amount:</td>
                        <td class="text-right fw-bold" style="color: #047857;">₹{{ number_format($partSalesInvoice->received_amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Balance Due:</td>
                        <td class="text-right fw-bold" style="color: #dc2626;">₹{{ number_format($partSalesInvoice->balance, 2) }}</td>
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
