@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
	<!-- Welcome Section -->
	<div class="row mb-4">
		<div class="col-12">
			<div class="card m-welcome-card">
				<div class="card-body d-flex align-items-center justify-content-between flex-wrap gap-3">
					<div class="d-flex align-items-center gap-3">
						<div class="avatar avatar-online" style="width:56px; height:56px;">
							@if(!empty($user->avatar) && file_exists(public_path($user->avatar)))
							<img src="{{ asset($user->avatar) }}" alt="{{ $user->full_name }}" class="rounded-circle" style="width:56px; height:56px; object-fit:cover;">
							@else
							<img src="{{ asset('assets/admin/img/avatars/1.png') }}" alt="{{ $user->full_name }}" class="rounded-circle" style="width:56px; height:56px; object-fit:cover;">
							@endif
						</div>
						<div>
							<h4 class="m-welcome-greeting mb-0">
								@php
									$hour = date('H');
									if ($hour < 12) $greeting = 'Good Morning';
									elseif ($hour < 17) $greeting = 'Good Afternoon';
									else $greeting = 'Good Evening';
								@endphp
								{{ $greeting }}, {{ $user->first_name }}! 👋
							</h4>
							<p class="m-welcome-subtitle mb-0 mt-1">
								You are logged in as <span class="badge bg-label-primary">{{ ucfirst($user->roleDetail?->name ?? $user->role) }}</span>
							</p>
						</div>
					</div>
					<div class="d-none d-md-block">
						<span class="m-date-badge" style="font-size: 0.875rem;">
							<i class="bx bx-calendar"></i>
							{{ date('l, d-m-Y') }}
						</span>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Stats Cards -->
	<div class="row mb-4">
		<div class="col-lg-3 col-md-6 col-12 mb-4">
			<div class="card m-stats-card m-stats-primary">
				<div class="card-body">
					<div class="d-flex align-items-center justify-content-between">
						<div>
							<p class="m-stats-label mb-1">Total Users</p>
							<h3 class="m-stats-value mb-0">{{ $stats['total_users'] }}</h3>
						</div>
						<div class="m-stats-icon">
							<i class="bx bx-group"></i>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-lg-3 col-md-6 col-12 mb-4">
			<div class="card m-stats-card m-stats-success">
				<div class="card-body">
					<div class="d-flex align-items-center justify-content-between">
						<div>
							<p class="m-stats-label mb-1">Active Users</p>
							<h3 class="m-stats-value mb-0">{{ $stats['active_users'] }}</h3>
						</div>
						<div class="m-stats-icon">
							<i class="bx bx-check-circle"></i>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-lg-3 col-md-6 col-12 mb-4">
			<div class="card m-stats-card m-stats-info">
				<div class="card-body">
					<div class="d-flex align-items-center justify-content-between">
						<div>
							<p class="m-stats-label mb-1">Companies</p>
							<h3 class="m-stats-value mb-0">{{ $stats['total_companies'] }}</h3>
						</div>
						<div class="m-stats-icon">
							<i class="bx bx-buildings"></i>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-lg-3 col-md-6 col-12 mb-4">
			<div class="card m-stats-card m-stats-warning">
				<div class="card-body">
					<div class="d-flex align-items-center justify-content-between">
						<div>
							<p class="m-stats-label mb-1">Your Role</p>
							<h3 class="m-stats-value mb-0" style="font-size: 1.25rem;">{{ ucfirst($user->roleDetail?->name ?? $user->role) }}</h3>
						</div>
						<div class="m-stats-icon">
							<i class="bx bx-shield"></i>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Hotel Performance KPIs -->
	<div class="row mb-4">
		<div class="col-12">
			<div class="d-flex justify-content-between align-items-center mb-3">
				<h5 class="mb-0"><i class="bx bx-bar-chart-alt-2 me-2" style="color: var(--m-primary);"></i>Hotel Performance <small class="text-muted fw-normal">Month to Date</small></h5>
				@if(auth()->user()->hasPermission('finance_reports.view'))
				<a href="{{ route('admin.finance.reports.dashboard') }}" class="btn btn-sm btn-outline-primary">
					<i class="bx bx-right-arrow-alt me-1"></i>Full Report
				</a>
				@endif
			</div>
		</div>
		<div class="col-lg-3 col-md-6 col-12 mb-4">
			<div class="card m-stats-card m-stats-primary">
				<div class="card-body">
					<div class="d-flex justify-content-between align-items-start">
						<div>
							<p class="m-stats-label mb-1">Occupancy Rate</p>
							<h3 class="m-stats-value mb-1">{{ $hotelKpis['month']['occupancy'] }}%</h3>
							@if($hotelKpis['changes']['occupancy'] !== null)
							<span class="badge {{ $hotelKpis['changes']['occupancy'] >= 0 ? 'bg-success' : 'bg-danger' }} bg-opacity-10 text-{{ $hotelKpis['changes']['occupancy'] >= 0 ? 'success' : 'danger' }}">
								<i class="bx bx-{{ $hotelKpis['changes']['occupancy'] >= 0 ? 'up' : 'down' }}-arrow-alt"></i> {{ abs($hotelKpis['changes']['occupancy']) }}%
							</span>
							@else
							<span class="opacity-75" style="font-size:11px;">vs last month</span>
							@endif
						</div>
						<div class="m-stats-icon">
							<i class="bx bx-building-house"></i>
						</div>
					</div>
					<small class="opacity-75">Today: {{ $hotelKpis['today']['occupancy'] }}%</small>
				</div>
			</div>
		</div>
		<div class="col-lg-3 col-md-6 col-12 mb-4">
			<div class="card m-stats-card m-stats-success">
				<div class="card-body">
					<div class="d-flex justify-content-between align-items-start">
						<div>
							<p class="m-stats-label mb-1">ADR</p>
							<h3 class="m-stats-value mb-1">{{ $currencySymbol }}{{ number_format($hotelKpis['month']['adr'], 2) }}</h3>
							@if($hotelKpis['changes']['adr'] !== null)
							<span class="badge {{ $hotelKpis['changes']['adr'] >= 0 ? 'bg-success' : 'bg-danger' }} bg-opacity-10 text-{{ $hotelKpis['changes']['adr'] >= 0 ? 'success' : 'danger' }}">
								<i class="bx bx-{{ $hotelKpis['changes']['adr'] >= 0 ? 'up' : 'down' }}-arrow-alt"></i> {{ abs($hotelKpis['changes']['adr']) }}%
							</span>
							@else
							<span class="opacity-75" style="font-size:11px;">vs last month</span>
							@endif
						</div>
						<div class="m-stats-icon">
							<i class="bx bx-dollar"></i>
						</div>
					</div>
					<small class="opacity-75">Today: {{ $currencySymbol }}{{ number_format($hotelKpis['today']['adr'], 2) }}</small>
				</div>
			</div>
		</div>
		<div class="col-lg-3 col-md-6 col-12 mb-4">
			<div class="card m-stats-card m-stats-warning">
				<div class="card-body">
					<div class="d-flex justify-content-between align-items-start">
						<div>
							<p class="m-stats-label mb-1">RevPAR</p>
							<h3 class="m-stats-value mb-1">{{ $currencySymbol }}{{ number_format($hotelKpis['month']['revpar'], 2) }}</h3>
							@if($hotelKpis['changes']['revpar'] !== null)
							<span class="badge {{ $hotelKpis['changes']['revpar'] >= 0 ? 'bg-success' : 'bg-danger' }} bg-opacity-10 text-{{ $hotelKpis['changes']['revpar'] >= 0 ? 'success' : 'danger' }}">
								<i class="bx bx-{{ $hotelKpis['changes']['revpar'] >= 0 ? 'up' : 'down' }}-arrow-alt"></i> {{ abs($hotelKpis['changes']['revpar']) }}%
							</span>
							@else
							<span class="opacity-75" style="font-size:11px;">vs last month</span>
							@endif
						</div>
						<div class="m-stats-icon">
							<i class="bx bx-trending-up"></i>
						</div>
					</div>
					<small class="opacity-75">Today: {{ $currencySymbol }}{{ number_format($hotelKpis['today']['revpar'], 2) }}</small>
				</div>
			</div>
		</div>
		<div class="col-lg-3 col-md-6 col-12 mb-4">
			<div class="card m-stats-card m-stats-info">
				<div class="card-body">
					<div class="d-flex justify-content-between align-items-start">
						<div>
							<p class="m-stats-label mb-1">Total Revenue (MTD)</p>
							<h3 class="m-stats-value mb-1">{{ $currencySymbol }}{{ number_format($hotelKpis['month']['revenue'], 0) }}</h3>
							<span class="opacity-75" style="font-size:11px;">{{ $hotelKpis['month']['rooms_sold'] }} rooms sold</span>
						</div>
						<div class="m-stats-icon">
							<i class="bx bx-wallet"></i>
						</div>
					</div>
					<small class="opacity-75">Today: {{ $currencySymbol }}{{ number_format($hotelKpis['today']['revenue'], 2) }}</small>
				</div>
			</div>
		</div>
	</div>

	<!-- Occupancy & Revenue Trend + Today's Activity -->
	<div class="row mb-4">
		<div class="col-lg-8 col-12 mb-4">
			<div class="card h-100">
				<div class="card-header d-flex justify-content-between align-items-center">
					<h5 class="mb-0">7-Day Occupancy & Revenue Trend</h5>
					<small class="text-muted">{{ $hotelKpis['recent_dates'] ? \Carbon\Carbon::parse(now()->subDays(6))->format('M d') : '' }} - {{ now()->format('M d, Y') }}</small>
				</div>
				<div class="card-body">
					@if(!empty($hotelKpis['recent_dates']))
					<div id="dashboardTrendChart"></div>
					@else
					<p class="text-muted text-center py-4">No data available</p>
					@endif
				</div>
			</div>
		</div>
		<div class="col-lg-4 col-12 mb-4">
			<div class="card h-100">
				<div class="card-header">
					<h5 class="mb-0">Today's Activity</h5>
				</div>
				<div class="card-body">
					<div class="d-flex align-items-center mb-3 p-3 rounded" style="background: var(--m-success-light);">
						<div class="avatar bg-label-success me-3">
							<span class="avatar-initial rounded"><i class="bx bx-log-in"></i></span>
						</div>
						<div>
							<p class="mb-0 fw-semibold">Check-ins Today</p>
							<h4 class="mb-0">{{ $hotelKpis['today_checkins'] }}</h4>
						</div>
					</div>
					<div class="d-flex align-items-center mb-3 p-3 rounded" style="background: var(--m-warning-light);">
						<div class="avatar bg-label-warning me-3">
							<span class="avatar-initial rounded"><i class="bx bx-log-out"></i></span>
						</div>
						<div>
							<p class="mb-0 fw-semibold">Check-outs Today</p>
							<h4 class="mb-0">{{ $hotelKpis['today_checkouts'] }}</h4>
						</div>
					</div>
					<div class="d-flex align-items-center mb-3 p-3 rounded" style="background: var(--m-info-light);">
						<div class="avatar bg-label-info me-3">
							<span class="avatar-initial rounded"><i class="bx bx-calendar-check"></i></span>
						</div>
						<div>
							<p class="mb-0 fw-semibold">Active Reservations</p>
							<h4 class="mb-0">{{ $hotelKpis['active_reservations'] }}</h4>
						</div>
					</div>
					<div class="d-flex align-items-center p-3 rounded" style="background: var(--m-primary-light);">
						<div class="avatar bg-label-primary me-3">
							<span class="avatar-initial rounded"><i class="bx bx-building-house"></i></span>
						</div>
						<div>
							<p class="mb-0 fw-semibold">Total Rooms</p>
							<h4 class="mb-0">{{ $hotelKpis['total_rooms'] }}</h4>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Quick Actions -->
	<div class="row">
		<div class="col-12">
			<div class="card">
				<div class="card-header">
					<h5 class="mb-0"><i class="bx bx-zap me-2" style="color: var(--m-warning);"></i>Quick Actions</h5>
				</div>
				<div class="card-body">
					<div class="row g-3">
						@if(auth()->user()->hasPermission('users.view'))
						<div class="col-lg-3 col-md-6 col-12">
							<a href="{{ route('admin.users.index') }}" class="m-quick-action">
								<div class="m-qa-icon m-qa-primary"><i class="bx bx-group"></i></div>
								<span>Manage Users</span>
							</a>
						</div>
						@endif
						@if(auth()->user()->hasPermission('branches.view'))
						<div class="col-lg-3 col-md-6 col-12">
							<a href="{{ route('admin.branches.index') }}" class="m-quick-action">
								<div class="m-qa-icon m-qa-success"><i class="bx bx-map-pin"></i></div>
								<span>Branches</span>
							</a>
						</div>
						@endif
						@if(auth()->user()->hasPermission('company.view'))
						<div class="col-lg-3 col-md-6 col-12">
							<a href="{{ route('admin.company.edit', \App\Models\Company::first()) }}" class="m-quick-action">
								<div class="m-qa-icon m-qa-info"><i class="bx bx-buildings"></i></div>
								<span>Company Profile</span>
							</a>
						</div>
						@endif
						@if(auth()->user()->hasPermission('roles.view'))
						<div class="col-lg-3 col-md-6 col-12">
							<a href="{{ route('admin.roles.index') }}" class="m-quick-action">
								<div class="m-qa-icon m-qa-warning"><i class="bx bx-shield"></i></div>
								<span>Roles & Permissions</span>
							</a>
						</div>
						@endif
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@section('script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    @if(!empty($hotelKpis['recent_dates']))
    var trendDates = {!! json_encode($hotelKpis['recent_dates']) !!};
    var trendOccupancy = {!! json_encode($hotelKpis['recent_occupancy']) !!};
    var trendRevenue = {!! json_encode($hotelKpis['recent_revenues']) !!};

    var trendOptions = {
        series: [
            { name: 'Occupancy %', type: 'column', data: trendOccupancy },
            { name: 'Revenue ({{ $currencySymbol }})', type: 'line', data: trendRevenue }
        ],
        chart: { height: 280, type: 'line', stacked: false, toolbar: { show: false }, sparkline: { enabled: false } },
        stroke: { width: [0, 2], curve: 'smooth' },
        plotOptions: { bar: { columnWidth: '50%', borderRadius: 4 } },
        colors: ['#14624f', '#bd8c3a'],
        xaxis: { categories: trendDates, labels: { style: { fontSize: '11px' } } },
        yaxis: [
            { title: { text: 'Occupancy %', style: { fontSize: '11px' } }, seriesName: 'Occupancy %', max: 100 },
            { title: { text: 'Revenue ({{ $currencySymbol }})', style: { fontSize: '11px' } }, seriesName: 'Revenue ({{ $currencySymbol }})', opposite: true }
        ],
        legend: { position: 'top', horizontalAlign: 'left', fontSize: '11px' },
        tooltip: { shared: true, intersect: false },
        dataLabels: { enabled: false },
        grid: { borderColor: '#f1f1f1', strokeDashArray: 3 }
    };

    var trendChart = new ApexCharts(document.querySelector('#dashboardTrendChart'), trendOptions);
    trendChart.render();
    @endif
});
</script>
@endsection
