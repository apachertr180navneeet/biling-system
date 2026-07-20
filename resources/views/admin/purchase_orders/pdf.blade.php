<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Purchase Order - {{ $order->order_number }}</title>
<style>
@page { size: A4; margin: 15mm; }
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: 'Helvetica', 'Arial', sans-serif; color: #1e293b; font-size: 12px; line-height: 1.5; }

.watermark {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%) rotate(-30deg);
    opacity: 0.06;
    z-index: 0;
    pointer-events: none;
    width: 60%;
}
.watermark img { width: 100%; height: auto; }

.header-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; border-bottom: 3px solid #059669; padding-bottom: 10px; }
.company-name { font-size: 22px; font-weight: 800; color: #14532d; letter-spacing: -0.5px; }
.company-details { font-size: 10px; color: #475569; line-height: 1.6; }
.logo-cell { text-align: right; vertical-align: middle; width: 120px; }
.logo-cell img { max-height: 80px; width: auto; }

.title-bar { background: #059669; color: #fff; text-align: center; padding: 8px 0; font-size: 18px; font-weight: 800; letter-spacing: 2px; margin-bottom: 15px; border-radius: 4px; }

.meta-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
.meta-table td { padding: 4px 8px; font-size: 11px; vertical-align: top; }
.meta-label { font-weight: 600; color: #64748b; width: 130px; }
.meta-value { color: #0f172a; }

.supplier-box { border: 1px solid #e2e8f0; border-left: 4px solid #10b981; padding: 10px 12px; margin-bottom: 15px; border-radius: 4px; }
.supplier-box h3 { font-size: 10px; text-transform: uppercase; letter-spacing: 1px; color: #64748b; margin-bottom: 5px; }
.supplier-box p { font-size: 11px; color: #1e293b; margin: 2px 0; }

.items-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
.items-table th { background: #0f172a; color: #fff; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; padding: 8px 10px; text-align: left; }
.items-table td { padding: 8px 10px; border-bottom: 1px solid #e2e8f0; font-size: 11px; }
.items-table tr:nth-child(even) td { background: #f8fafc; }
.items-table th.text-right, .items-table td.text-right { text-align: right; }
.items-table th.text-center, .items-table td.text-center { text-align: center; }
.items-table tfoot th { background: #f1f5f9; color: #0f172a; font-weight: 700; }

.notes-box { border: 1px solid #e2e8f0; padding: 10px 12px; margin-bottom: 15px; border-radius: 4px; background: #fffbeb; }
.notes-box h4 { font-size: 11px; font-weight: 700; color: #92400e; margin-bottom: 4px; }
.notes-box p { font-size: 11px; color: #78350f; }

.footer-section { margin-top: 20px; border-top: 2px solid #e2e8f0; padding-top: 10px; }
.footer-signatures { display: flex; justify-content: space-between; }
.signature-block { width: 45%; text-align: center; }
.signature-block .line { border-top: 1px solid #94a3b8; margin-top: 50px; padding-top: 5px; font-size: 10px; color: #64748b; font-weight: 600; }

.status-badge { display: inline-block; padding: 2px 10px; border-radius: 99px; font-size: 10px; font-weight: 700; text-transform: uppercase; }
.status-pending { background: #fef3c7; color: #92400e; }
.status-partial { background: #dbeafe; color: #1e40af; }
.status-received { background: #d1fae5; color: #065f46; }
</style>
</head>
<body>

<!-- Watermark -->
<div class="watermark">
    <img src="{{ public_path('assets/admin/img/logo.jpg') }}" alt="Watermark">
</div>

<!-- Company Header -->
<table class="header-table">
<tr>
    <td style="vertical-align: top;">
        <div class="company-name">SHREE KRISHNA AUTO GREEN</div>
        <div class="company-details">
            NH 65, Near Roadways Bus Stand, Jodhpur, Rajasthan - 342001<br>
            GSTIN: 08ANQPD4555N1ZE | PAN: ANQPD4555N<br>
            Email: vijay.deora429@gmail.com | Mobile: 7568899148
        </div>
    </td>
    <td class="logo-cell">
        <img src="{{ public_path('assets/admin/img/logo.jpg') }}" alt="Logo">
    </td>
</tr>
</table>

<!-- Title -->
<div class="title-bar">PURCHASE ORDER</div>

<!-- Order Meta -->
<table class="meta-table">
<tr>
    <td>
        <table>
            <tr><td class="meta-label">PO Number:</td><td class="meta-value">{{ $order->order_number }}</td></tr>
            <tr><td class="meta-label">Order Date:</td><td class="meta-value">{{ $order->order_date->format('d/m/Y') }}</td></tr>
            <tr><td class="meta-label">Expected Date:</td><td class="meta-value">{{ $order->expected_date ? $order->expected_date->format('d/m/Y') : '-' }}</td></tr>
        </table>
    </td>
    <td>
        <table>
            <tr><td class="meta-label">Status:</td><td class="meta-value">
                @if($order->status == 'pending')
                    <span class="status-badge status-pending">Pending</span>
                @elseif($order->status == 'partial')
                    <span class="status-badge status-partial">Partial</span>
                @elseif($order->status == 'received')
                    <span class="status-badge status-received">Received</span>
                @else
                    <span class="status-badge">{{ ucfirst($order->status) }}</span>
                @endif
            </td></tr>
            <tr><td class="meta-label">Created By:</td><td class="meta-value">{{ $order->createdBy->full_name ?? 'System' }}</td></tr>
            <tr><td class="meta-label">Total Amount:</td><td class="meta-value" style="font-weight:700; color:#059669; font-size:13px;">₹ {{ number_format($order->total_amount, 2) }}</td></tr>
        </table>
    </td>
</tr>
</table>

<!-- Supplier Details -->
<div class="supplier-box">
    <h3>Supplier Details</h3>
    <p><strong>{{ $order->supplier->name ?? '-' }}</strong></p>
    @if($order->supplier->address)<p>{{ $order->supplier->address }}</p>@endif
    @if($order->supplier->contact_person)<p>Contact: {{ $order->supplier->contact_person }}</p>@endif
    @if($order->supplier->phone)<p>Phone: {{ $order->supplier->phone }}</p>@endif
    @if($order->supplier->email)<p>Email: {{ $order->supplier->email }}</p>@endif
    @if($order->supplier->gstin)<p>GSTIN: {{ $order->supplier->gstin }}</p>@endif
</div>

<!-- Items Table -->
<table class="items-table">
<thead>
    <tr>
        <th style="width:5%;" class="text-center">#</th>
        <th style="width:12%;">Part No.</th>
        <th style="width:33%;">Part Name</th>
        <th style="width:10%;" class="text-center">Qty</th>
        <th style="width:15%;" class="text-right">Unit Price</th>
        <th style="width:15%;" class="text-right">Total</th>
        <th style="width:10%;" class="text-center">Received</th>
    </tr>
</thead>
<tbody>
    @foreach($order->items as $i => $item)
    <tr>
        <td class="text-center" style="font-weight:600;">{{ $i + 1 }}</td>
        <td>{{ $item->sparePart->part_no ?? '-' }}</td>
        <td style="font-weight:600;">{{ $item->sparePart->name ?? '-' }}</td>
        <td class="text-center">{{ $item->quantity }}</td>
        <td class="text-right">₹ {{ number_format($item->unit_price, 2) }}</td>
        <td class="text-right" style="font-weight:600;">₹ {{ number_format($item->total_price, 2) }}</td>
        <td class="text-center">{{ $item->received_quantity }}</td>
    </tr>
    @endforeach
</tbody>
<tfoot>
    <tr>
        <th colspan="5" style="text-align:right;">Grand Total:</th>
        <th class="text-right" style="color:#059669; font-size:13px;">₹ {{ number_format($order->total_amount, 2) }}</th>
        <th></th>
    </tr>
</tfoot>
</table>

<!-- Notes -->
@if($order->notes)
<div class="notes-box">
    <h4>Notes</h4>
    <p>{{ $order->notes }}</p>
</div>
@endif

<!-- Signatures -->
<div class="footer-section">
<div class="footer-signatures">
    <div class="signature-block">
        <div class="line">Authorized Signature</div>
    </div>
    <div class="signature-block">
        <div class="line">Supplier Signature</div>
    </div>
</div>
</div>

</body>
</html>
