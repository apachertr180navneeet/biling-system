<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice - {{ $reservation->reservation_number }}</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 12px; color: #333; margin: 0; padding: 20px; }
        .header { text-align: center; border-bottom: 2px solid #007bff; padding-bottom: 15px; margin-bottom: 20px; }
        .header h1 { margin: 0; color: #007bff; font-size: 24px; }
        .header p { margin: 5px 0 0; color: #666; }
        .section { margin-bottom: 20px; }
        .section-title { font-size: 14px; font-weight: bold; color: #007bff; border-bottom: 1px solid #ddd; padding-bottom: 5px; margin-bottom: 10px; }
        .row { display: flex; justify-content: space-between; margin-bottom: 5px; }
        .label { font-weight: bold; color: #555; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table th { background-color: #007bff; color: #fff; padding: 8px 12px; text-align: left; }
        table td { padding: 8px 12px; border-bottom: 1px solid #ddd; }
        table tr:nth-child(even) { background-color: #f8f9fa; }
        .total-row { font-weight: bold; font-size: 14px; background-color: #e9ecef !important; }
        .footer { margin-top: 30px; text-align: center; color: #666; font-size: 10px; border-top: 1px solid #ddd; padding-top: 10px; }
        .badge { padding: 3px 8px; border-radius: 3px; color: #fff; font-size: 10px; }
        .badge-success { background-color: #28a745; }
        .badge-warning { background-color: #ffc107; color: #333; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $hotel->name ?? 'Hotel' }}</h1>
        <p>{{ $hotel->address ?? '' }}, {{ $hotel->city ?? '' }}, {{ $hotel->state ?? '' }} {{ $hotel->zipcode ?? '' }}</p>
        <p>Phone: {{ $hotel->phone ?? '' }} | Email: {{ $hotel->email ?? '' }}</p>
    </div>

    <div class="section">
        <div class="section-title">Reservation Details</div>
        <div class="row">
            <div><span class="label">Reservation #:</span> {{ $reservation->reservation_number }}</div>
            <div><span class="label">Invoice Date:</span> {{ now()->format('d-m-Y') }}</div>
        </div>
        <div class="row">
            <div><span class="label">Guest:</span> {{ $reservation->guest->full_name ?? 'N/A' }}</div>
            <div><span class="label">Booking Source:</span> {{ ucfirst($reservation->booking_source) }}</div>
        </div>
        <div class="row">
            <div><span class="label">Check-in:</span> {{ $reservation->actual_check_in ? $reservation->actual_check_in->format('d-m-Y H:i') : ($reservation->check_in_date?->format('d-m-Y') ?? 'N/A') }}</div>
            <div><span class="label">Check-out:</span> {{ $reservation->actual_check_out ? $reservation->actual_check_out->format('d-m-Y H:i') : ($reservation->check_out_date?->format('d-m-Y') ?? 'N/A') }}</div>
        </div>
        <div class="row">
            <div><span class="label">Nights:</span> {{ $reservation->nights ?? 0 }}</div>
            <div><span class="label">Status:</span> <span class="badge badge-success">{{ ucfirst($reservation->status) }}</span></div>
        </div>
    </div>

    @if($reservation->rooms->count())
    <div class="section">
        <div class="section-title">Room Details</div>
        <table>
            <thead>
                <tr>
                    <th>Room #</th>
                    <th>Room Type</th>
                    <th>Rate/Night</th>
                    <th>Nights</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reservation->rooms as $rr)
                <tr>
                    <td>{{ $rr->room->room_number ?? 'N/A' }}</td>
                    <td>{{ $rr->roomType->name ?? 'N/A' }}</td>
                    <td>{{ number_format($rr->rate_per_night, 2) }}</td>
                    <td>{{ $rr->nights ?? 0 }}</td>
                    <td>{{ number_format($rr->total_amount, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="section">
        <div class="section-title">Charges & Payments</div>
        <table>
            <thead>
                <tr>
                    <th>Description</th>
                    <th style="text-align:right">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Room Charges</td>
                    <td style="text-align:right">{{ number_format($reservation->total_amount, 2) }}</td>
                </tr>
                @if($reservation->discount_amount > 0)
                <tr>
                    <td>Discount</td>
                    <td style="text-align:right">-{{ number_format($reservation->discount_amount, 2) }}</td>
                </tr>
                @endif
                @if($reservation->tax_amount > 0)
                <tr>
                    <td>Tax (18%)</td>
                    <td style="text-align:right">{{ number_format($reservation->tax_amount, 2) }}</td>
                </tr>
                @endif
                @if($checkOut && $checkOut->damage_charges > 0)
                <tr>
                    <td>Damage Charges</td>
                    <td style="text-align:right">{{ number_format($checkOut->damage_charges, 2) }}</td>
                </tr>
                @endif
                <tr class="total-row">
                    <td>Total Amount</td>
                    <td style="text-align:right">{{ number_format(($checkOut->final_bill_amount ?? $reservation->total_amount) + ($checkOut->damage_charges ?? 0), 2) }}</td>
                </tr>
                <tr>
                    <td>Payments Received</td>
                    <td style="text-align:right">-{{ number_format($checkOut->total_payments ?? $reservation->paid_amount, 2) }}</td>
                </tr>
                <tr class="total-row">
                    <td>Balance Due</td>
                    <td style="text-align:right">{{ number_format($checkOut->balance_due ?? $reservation->balance, 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    @if($checkOut)
    <div class="section">
        <div class="section-title">Check-out Summary</div>
        <div class="row">
            <div><span class="label">Check-out Time:</span> {{ $checkOut->check_out_time?->format('d-m-Y H:i') ?? 'N/A' }}</div>
            <div><span class="label">Room Condition:</span> {{ ucfirst($checkOut->room_condition ?? 'N/A') }}</div>
        </div>
        @if($checkOut->feedback_rating)
        <div class="row">
            <div><span class="label">Guest Rating:</span> {{ $checkOut->feedback_rating }}/5</div>
            <div><span class="label">Feedback:</span> {{ $checkOut->feedback_notes ?? 'N/A' }}</div>
        </div>
        @endif
    </div>
    @endif

    <div class="footer">
        <p>Thank you for staying with us!</p>
        <p>{{ $hotel->name ?? '' }} | Generated on {{ now()->format('d-m-Y H:i') }}</p>
    </div>
</body>
</html>
