@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-line-chart"></i></div>
            <div>
                <h4 class="m-page-title">Revenue Dashboard</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Finance</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">Revenue Dashboard</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.finance.reports.dashboard') }}">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Hotel</label>
                        <select name="hotel_id" class="form-select">
                            <option value="">All Hotels</option>
                            @foreach($hotels as $h)
                            <option value="{{ $h->id }}" {{ $hotel && $hotel->id == $h->id ? 'selected' : '' }}>{{ $h->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">From Date</label>
                        <input type="date" name="from_date" class="form-control" value="{{ $from }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">To Date</label>
                        <input type="date" name="to_date" class="form-control" value="{{ $to }}">
                    </div>
                    <div class="col-md-3 mb-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2"><i class="bx bx-search me-1"></i> Filter</button>
                        <a href="{{ route('admin.finance.reports.dashboard') }}" class="btn btn-outline-secondary">Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1 fw-semibold" style="font-size:12px; text-transform:uppercase; letter-spacing:0.5px;">Occupancy Rate</p>
                            <h3 class="mb-1">{{ $occupancyRate }}%</h3>
                            @if($occupancyChange !== null)
                            <span class="badge {{ $occupancyChange >= 0 ? 'bg-success' : 'bg-danger' }} bg-opacity-10 text-{{ $occupancyChange >= 0 ? 'success' : 'danger' }}">
                                <i class="bx bx-{{ $occupancyChange >= 0 ? 'up' : 'down' }}-arrow-alt"></i> {{ abs($occupancyChange) }}%
                            </span>
                            @endif
                        </div>
                        <div class="avatar bg-label-primary">
                            <span class="avatar-initial rounded"><i class="bx bx-building-house"></i></span>
                        </div>
                    </div>
                    <small class="text-muted">{{ number_format($totalRoomNights) }} room-nights of {{ number_format($totalRooms) }} total available</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1 fw-semibold" style="font-size:12px; text-transform:uppercase; letter-spacing:0.5px;">ADR</p>
                            <h3 class="mb-1">{{ $currencySymbol }}{{ number_format($adr, 2) }}</h3>
                            @if($adrChange !== null)
                            <span class="badge {{ $adrChange >= 0 ? 'bg-success' : 'bg-danger' }} bg-opacity-10 text-{{ $adrChange >= 0 ? 'success' : 'danger' }}">
                                <i class="bx bx-{{ $adrChange >= 0 ? 'up' : 'down' }}-arrow-alt"></i> {{ abs($adrChange) }}%
                            </span>
                            @endif
                        </div>
                        <div class="avatar bg-label-success">
                            <span class="avatar-initial rounded"><i class="bx bx-dollar"></i></span>
                        </div>
                    </div>
                    <small class="text-muted">Avg Daily Rate per room sold</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1 fw-semibold" style="font-size:12px; text-transform:uppercase; letter-spacing:0.5px;">RevPAR</p>
                            <h3 class="mb-1">{{ $currencySymbol }}{{ number_format($revpar, 2) }}</h3>
                            @if($revparChange !== null)
                            <span class="badge {{ $revparChange >= 0 ? 'bg-success' : 'bg-danger' }} bg-opacity-10 text-{{ $revparChange >= 0 ? 'success' : 'danger' }}">
                                <i class="bx bx-{{ $revparChange >= 0 ? 'up' : 'down' }}-arrow-alt"></i> {{ abs($revparChange) }}%
                            </span>
                            @endif
                        </div>
                        <div class="avatar bg-label-warning">
                            <span class="avatar-initial rounded"><i class="bx bx-trending-up"></i></span>
                        </div>
                    </div>
                    <small class="text-muted">Revenue per available room</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1 fw-semibold" style="font-size:12px; text-transform:uppercase; letter-spacing:0.5px;">Total Revenue</p>
                            <h3 class="mb-1">{{ $currencySymbol }}{{ number_format($totalRevenue, 2) }}</h3>
                            <span class="text-muted" style="font-size:12px;">{{ $totalReservations }} reservations</span>
                        </div>
                        <div class="avatar bg-label-info">
                            <span class="avatar-initial rounded"><i class="bx bx-wallet"></i></span>
                        </div>
                    </div>
                    <small class="text-muted">{{ $totalSoldRooms }} rooms sold in period</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Occupancy & Revenue Trend</h5>
                    <small class="text-muted">{{ \Carbon\Carbon::parse($from)->format('M d') }} - {{ \Carbon\Carbon::parse($to)->format('M d, Y') }}</small>
                </div>
                <div class="card-body">
                    <div id="occupancyChart"></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Revenue by Room Type</h5>
                </div>
                <div class="card-body">
                    <div id="roomTypeChart"></div>
                    <div class="mt-3">
                        @foreach($roomTypeBreakdown as $rt)
                        <div class="d-flex justify-content-between mb-2">
                            <span class="fw-semibold">{{ $rt->name }}</span>
                            <span>{{ $currencySymbol }}{{ number_format($rt->revenue, 0) }} <small class="text-muted">({{ $rt->count }})</small></span>
                        </div>
                        @endforeach
                        @if($roomTypeBreakdown->isEmpty())
                        <p class="text-muted text-center">No data</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Booking Source Mix</h5>
                </div>
                <div class="card-body">
                    <div id="sourceChart"></div>
                    <div class="mt-3">
                        @foreach($bookingSourceData as $src)
                        <div class="d-flex justify-content-between mb-2">
                            <span class="fw-semibold">{{ ucfirst(str_replace('-', ' ', $src->booking_source)) }}</span>
                            <span>{{ $currencySymbol }}{{ number_format($src->revenue, 0) }} <small class="text-muted">({{ $src->count }})</small></span>
                        </div>
                        @endforeach
                        @if($bookingSourceData->isEmpty())
                        <p class="text-muted text-center">No data</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Key Metrics Summary</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tbody>
                            <tr>
                                <td class="text-muted">Total Rooms</td>
                                <td class="fw-semibold text-end">{{ number_format($totalRooms) }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Total Room-Nights (Period)</td>
                                <td class="fw-semibold text-end">{{ number_format($totalRooms * (\Carbon\Carbon::parse($from)->diffInDays(\Carbon\Carbon::parse($to)) + 1)) }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Rooms Sold</td>
                                <td class="fw-semibold text-end">{{ number_format($totalSoldRooms) }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Total Reservations</td>
                                <td class="fw-semibold text-end">{{ number_format($totalReservations) }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Avg Stay (Nights)</td>
                                <td class="fw-semibold text-end">{{ $avgNights }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Avg Guests/Reservation</td>
                                <td class="fw-semibold text-end">{{ $avgGuests }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Cancellation Rate</td>
                                <td class="fw-semibold text-end">{{ $cancelRate }}%</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Avg Room Rate (ADR)</td>
                                <td class="fw-semibold text-end">{{ $currencySymbol }}{{ number_format($adr, 2) }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">RevPAR</td>
                                <td class="fw-semibold text-end">{{ $currencySymbol }}{{ number_format($revpar, 2) }}</td>
                            </tr>
                            <tr class="border-top">
                                <td class="text-muted fw-bold">Total Revenue</td>
                                <td class="fw-bold text-end text-success">{{ $currencySymbol }}{{ number_format($totalRevenue, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var occDates = {!! json_encode($occupancyData['dates']) !!};
    var occOccupied = {!! json_encode($occupancyData['occupied']) !!};
    var occAvailable = {!! json_encode($occupancyData['available']) !!};
    var revRevenues = {!! json_encode($revenueData['revenues']) !!};

    var occOptions = {
        series: [
            { name: 'Occupied', type: 'column', data: occOccupied },
            { name: 'Available', type: 'column', data: occAvailable },
            { name: 'Revenue', type: 'line', data: revRevenues }
        ],
        chart: { height: 350, type: 'line', stacked: false, toolbar: { show: true } },
        stroke: { width: [0, 0, 2], curve: 'smooth' },
        plotOptions: { bar: { columnWidth: '50%' } },
        colors: ['#14624f', '#dce4df', '#bd8c3a'],
        xaxis: { categories: occDates },
        yaxis: [
            { title: { text: 'Rooms' }, seriesName: 'Occupied' },
            { show: false },
            { title: { text: 'Revenue ({{ $currencySymbol }})' }, seriesName: 'Revenue', opposite: true }
        ],
        legend: { position: 'top', horizontalAlign: 'left' },
        tooltip: { shared: true, intersect: false },
        dataLabels: { enabled: false }
    };

    var occChart = new ApexCharts(document.querySelector('#occupancyChart'), occOptions);
    occChart.render();

    var rtLabels = {!! json_encode($roomTypeBreakdown->pluck('name')->toArray()) !!};
    var rtRevenue = {!! json_encode($roomTypeBreakdown->pluck('revenue')->toArray()) !!};

    if (rtLabels.length > 0) {
        var rtOptions = {
            series: rtRevenue,
            chart: { type: 'donut', height: 220 },
            labels: rtLabels,
            colors: ['#14624f', '#bd8c3a', '#1a73e8', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4'],
            legend: { show: false },
            dataLabels: { enabled: true, formatter: function(val) { return val.toFixed(0) + '%'; } }
        };
        var rtChart = new ApexCharts(document.querySelector('#roomTypeChart'), rtOptions);
        rtChart.render();
    }

    var srcLabels = {!! json_encode($bookingSourceData->pluck('booking_source')->toArray()) !!};
    var srcRevenue = {!! json_encode($bookingSourceData->pluck('revenue')->toArray()) !!};

    if (srcLabels.length > 0) {
        var srcLabelsFormatted = srcLabels.map(function(l) { return l.charAt(0).toUpperCase() + l.slice(1).replace('-', ' '); });
        var srcOptions = {
            series: srcRevenue,
            chart: { type: 'donut', height: 220 },
            labels: srcLabelsFormatted,
            colors: ['#14624f', '#bd8c3a', '#1a73e8', '#f59e0b', '#ef4444'],
            legend: { show: false },
            dataLabels: { enabled: true, formatter: function(val) { return val.toFixed(0) + '%'; } }
        };
        var srcChart = new ApexCharts(document.querySelector('#sourceChart'), srcOptions);
        srcChart.render();
    }
});
</script>
@endsection
