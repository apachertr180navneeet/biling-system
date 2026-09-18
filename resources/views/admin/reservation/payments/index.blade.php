@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-dollar"></i></div>
            <div>
                <h4 class="m-page-title">Payments — {{ $reservation->reservation_number }}</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Reservations</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="{{ route('admin.reservation.bookings.index') }}">Bookings</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">Payments</li>
                </ul>
            </div>
        </div>
        <a href="{{ route('admin.reservation.bookings.index') }}" class="btn btn-outline-secondary"><i class="bx bx-left-arrow-alt me-1"></i> Back</a>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header"><h5>Reservation Summary</h5></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless mb-0">
                                <tr><td class="fw-semibold" style="width:140px">Guest</td><td>{{ $reservation->guest->full_name ?? '-' }}</td></tr>
                                <tr><td class="fw-semibold">Hotel</td><td>{{ $reservation->hotel->name ?? '-' }}</td></tr>
                                <tr><td class="fw-semibold">Check-in</td><td>{{ $reservation->check_in_date?->format('d-m-Y') ?? '-' }}</td></tr>
                                <tr><td class="fw-semibold">Check-out</td><td>{{ $reservation->check_out_date?->format('d-m-Y') ?? '-' }}</td></tr>
                                <tr><td class="fw-semibold">Nights</td><td>{{ $reservation->nights }}</td></tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless mb-0">
                                <tr><td class="fw-semibold" style="width:140px">Rooms</td><td>
                                    @foreach($reservation->rooms as $rr)
                                    <span class="badge bg-label-primary me-1">{{ $rr->room?->room_number }} ({{ $rr->roomType?->name }})</span>
                                    @endforeach
                                </td></tr>
                                <tr><td class="fw-semibold">Total Amount</td><td class="fw-bold">{{ number_format($reservation->total_amount, 2) }}</td></tr>
                                <tr><td class="fw-semibold">Paid Amount</td><td class="text-success fw-bold">{{ number_format($reservation->paid_amount, 2) }}</td></tr>
                                <tr><td class="fw-semibold">Balance</td><td class="text-danger fw-bold">{{ number_format($reservation->balance, 2) }}</td></tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h5>Payment History</h5></div>
                <div class="card-body">
                    @if($reservation->payments->count() > 0)
                    <table class="table table-bordered table-striped">
                        <thead><tr><th>#</th><th>Date</th><th>Amount</th><th>Method</th><th>Reference</th><th>Notes</th><th>Status</th><th>Actions</th></tr></thead>
                        <tbody>
                            @foreach($reservation->payments as $index => $payment)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $payment->payment_date?->format('d-m-Y') ?? '-' }}</td>
                                <td>{{ number_format($payment->amount, 2) }}</td>
                                <td><span class="badge bg-label-info">{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</span></td>
                                <td>{{ $payment->reference_number ?? '-' }}</td>
                                <td>{{ $payment->notes ?? '-' }}</td>
                                <td><span class="badge bg-label-{{ $payment->status == 'completed' ? 'success' : 'warning' }}">{{ ucfirst($payment->status) }}</span></td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-outline-danger btn-delete-item" data-url="{{ route('admin.reservation.payments.destroy', [$reservation, $payment]) }}" data-name="this payment"><i class="bx bx-trash"></i></button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <p class="text-muted text-center mb-0">No payments recorded yet.</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header"><h5>Record Payment</h5></div>
                <div class="card-body">
                    <form action="{{ route('admin.reservation.payments.store', $reservation) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Payment Date <span class="text-danger">*</span></label>
                            <input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Amount <span class="text-danger">*</span></label>
                            <input type="number" name="amount" class="form-control" step="0.01" min="0.01" max="{{ $reservation->balance }}" value="{{ $reservation->balance }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Payment Method <span class="text-danger">*</span></label>
                            <select name="payment_method" class="form-select" required>
                                <option value="cash">Cash</option>
                                <option value="card">Card</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="online">Online</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Reference Number</label>
                            <input type="text" name="reference_number" class="form-control" placeholder="Transaction/Receipt #">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" class="form-control" rows="2"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100"><i class="bx bx-check me-1"></i> Record Payment</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
