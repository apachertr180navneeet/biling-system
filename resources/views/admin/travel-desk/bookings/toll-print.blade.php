<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Toll Invoice - {{ $booking->booking_number }}</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; padding: 24px; font-family: Arial, Helvetica, sans-serif; color: #1f2937; background: #f3f4f6; font-size: 13px; }
        .invoice { max-width: 860px; margin: 0 auto; background: #fff; border: 1px solid #d1d5db; padding: 28px; }
        .header { display: flex; justify-content: space-between; gap: 24px; border-bottom: 2px solid #2563eb; padding-bottom: 18px; margin-bottom: 22px; }
        .brand h1 { margin: 0 0 6px; color: #2563eb; font-size: 24px; }
        .brand p, .meta p { margin: 3px 0; color: #4b5563; }
        .meta { text-align: right; min-width: 220px; }
        .meta h2 { margin: 0 0 8px; font-size: 18px; color: #111827; }
        .section { margin-bottom: 22px; }
        .section-title { margin-bottom: 10px; padding-bottom: 5px; border-bottom: 1px solid #e5e7eb; color: #2563eb; font-weight: 700; font-size: 14px; }
        .grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px 24px; }
        .item span { display: block; color: #6b7280; font-size: 11px; text-transform: uppercase; margin-bottom: 3px; }
        .item strong { color: #111827; font-weight: 600; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th { background: #2563eb; color: #fff; text-align: left; padding: 10px; }
        td { padding: 10px; border-bottom: 1px solid #e5e7eb; }
        .text-right { text-align: right; }
        .total-row td { font-weight: 700; background: #eff6ff; color: #111827; }
        .footer { margin-top: 32px; padding-top: 12px; border-top: 1px solid #e5e7eb; text-align: center; color: #6b7280; font-size: 11px; }
        .actions { max-width: 860px; margin: 0 auto 16px; text-align: right; }
        .btn { border: 0; border-radius: 4px; background: #2563eb; color: #fff; padding: 9px 14px; cursor: pointer; font-size: 13px; }
        @media print {
            body { background: #fff; padding: 0; }
            .actions { display: none; }
            .invoice { max-width: none; border: 0; padding: 18px; }
        }
    </style>
</head>
<body>
    @php
        $charges = [
            'Base Fare' => (float) $booking->base_price,
            'Distance Charges' => (float) $booking->distance_charges,
            'Hourly Charges' => (float) $booking->hourly_charges,
            'Toll / Additional Charges' => (float) $booking->additional_charges,
            'Tax' => (float) $booking->tax_amount,
        ];
        $balance = (float) $booking->total_amount - (float) $booking->advance_paid;
    @endphp

    <div class="actions">
        <button type="button" class="btn" onclick="window.print()">Print</button>
    </div>

    <div class="invoice">
        <div class="header">
            <div class="brand">
                <h1>{{ $booking->hotel->name ?? 'Hotel' }}</h1>
                <p>{{ $booking->hotel->address ?? '' }}</p>
                <p>{{ $booking->hotel->city ?? '' }} {{ $booking->hotel->state ?? '' }} {{ $booking->hotel->zipcode ?? '' }}</p>
                <p>Phone: {{ $booking->hotel->phone ?? 'N/A' }} | Email: {{ $booking->hotel->email ?? 'N/A' }}</p>
            </div>
            <div class="meta">
                <h2>Toll Invoice</h2>
                <p><strong>Booking #:</strong> {{ $booking->booking_number }}</p>
                <p><strong>Date:</strong> {{ now()->format('d-m-Y') }}</p>
                <p><strong>Status:</strong> {{ ucfirst(str_replace('_', ' ', $booking->status)) }}</p>
            </div>
        </div>

        <div class="section">
            <div class="section-title">Guest & Trip Details</div>
            <div class="grid">
                <div class="item"><span>Guest</span><strong>{{ $booking->guest_name }}</strong></div>
                <div class="item"><span>Phone</span><strong>{{ $booking->guest_phone }}</strong></div>
                <div class="item"><span>Transport Type</span><strong>{{ $booking->transportType->name ?? 'N/A' }}</strong></div>
                <div class="item"><span>Trip Type</span><strong>{{ ucfirst(str_replace('_', ' ', $booking->trip_type)) }}</strong></div>
                <div class="item"><span>Pickup</span><strong>{{ $booking->pickup_location }}</strong></div>
                <div class="item"><span>Drop</span><strong>{{ $booking->drop_location }}</strong></div>
                <div class="item"><span>Pickup Date/Time</span><strong>{{ $booking->pickup_datetime?->format('d-m-Y H:i') ?? 'N/A' }}</strong></div>
                <div class="item"><span>Distance / Hours</span><strong>{{ $booking->estimated_distance_km ?? '0.00' }} KM / {{ $booking->estimated_hours ?? '0.00' }} hrs</strong></div>
            </div>
        </div>

        <div class="section">
            <div class="section-title">Vehicle & Driver</div>
            <div class="grid">
                <div class="item"><span>Vehicle Number</span><strong>{{ $booking->vehicle_number ?? 'N/A' }}</strong></div>
                <div class="item"><span>Driver</span><strong>{{ $booking->driver_name ?? 'N/A' }}</strong></div>
                <div class="item"><span>Driver Phone</span><strong>{{ $booking->driver_phone ?? 'N/A' }}</strong></div>
                <div class="item"><span>Room Charge</span><strong>{{ $booking->is_room_charge === 'yes' ? 'Yes' : 'No' }}</strong></div>
            </div>
        </div>

        <div class="section">
            <div class="section-title">Charges</div>
            <table>
                <thead>
                    <tr>
                        <th>Description</th>
                        <th class="text-right">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($charges as $label => $amount)
                    <tr>
                        <td>{{ $label }}</td>
                        <td class="text-right">Rs. {{ number_format($amount, 2) }}</td>
                    </tr>
                    @endforeach
                    @if((float) $booking->discount_amount > 0)
                    <tr>
                        <td>Discount</td>
                        <td class="text-right">- Rs. {{ number_format((float) $booking->discount_amount, 2) }}</td>
                    </tr>
                    @endif
                    <tr class="total-row">
                        <td>Total Amount</td>
                        <td class="text-right">Rs. {{ number_format((float) $booking->total_amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Advance Paid</td>
                        <td class="text-right">Rs. {{ number_format((float) $booking->advance_paid, 2) }}</td>
                    </tr>
                    <tr class="total-row">
                        <td>Balance Due</td>
                        <td class="text-right">Rs. {{ number_format($balance, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        @if($booking->special_instructions)
        <div class="section">
            <div class="section-title">Notes</div>
            <p>{{ $booking->special_instructions }}</p>
        </div>
        @endif

        <div class="footer">
            Generated on {{ now()->format('d-m-Y H:i') }} by {{ $booking->createdBy->name ?? 'System' }}
        </div>
    </div>
</body>
</html>
