@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-line-chart"></i></div>
            <div>
                <h4 class="m-page-title">Profit & Loss Statement</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Finance</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Reports</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">Profit & Loss</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.finance.reports.profit-loss') }}">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">From Date</label>
                        <input type="date" name="from_date" class="form-control" value="{{ $from }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">To Date</label>
                        <input type="date" name="to_date" class="form-control" value="{{ $to }}">
                    </div>
                    <div class="col-md-4 mb-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2"><i class="bx bx-search me-1"></i> Filter</button>
                        <a href="{{ route('admin.finance.reports.profit-loss') }}" class="btn btn-outline-secondary">Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <h5 class="mb-3">Revenue</h5>
            <table class="table table-bordered table-striped">
                <thead><tr><th>Account</th><th class="text-end">Amount</th></tr></thead>
                <tbody>
                    @forelse($revenues ?? [] as $item)
                    <tr>
                        <td>{{ $item->code }} - {{ $item->name }}</td>
                        <td class="text-end">{{ number_format($item->balance, 2) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="2" class="text-center text-muted">No revenue data</td></tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="table-active">
                        <td><strong>Total Revenue</strong></td>
                        <td class="text-end"><strong>{{ number_format($totalRevenue ?? 0, 2) }}</strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <h5 class="mb-3">Cost of Goods Sold</h5>
            <table class="table table-bordered table-striped">
                <thead><tr><th>Account</th><th class="text-end">Amount</th></tr></thead>
                <tbody>
                    @forelse($cogs ?? [] as $item)
                    <tr>
                        <td>{{ $item->code }} - {{ $item->name }}</td>
                        <td class="text-end">{{ number_format($item->balance, 2) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="2" class="text-center text-muted">No COGS data</td></tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="table-active">
                        <td><strong>Total Cost of Goods Sold</strong></td>
                        <td class="text-end"><strong>{{ number_format($totalCogs ?? 0, 2) }}</strong></td>
                    </tr>
                </tfoot>
            </table>

            <div class="mt-3 p-3 bg-light rounded">
                <div class="d-flex justify-content-between">
                    <strong>Gross Profit</strong>
                    <strong>{{ number_format(($totalRevenue ?? 0) - ($totalCogs ?? 0), 2) }}</strong>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <h5 class="mb-3">Operating Expenses</h5>
            <table class="table table-bordered table-striped">
                <thead><tr><th>Account</th><th class="text-end">Amount</th></tr></thead>
                <tbody>
                    @forelse($operatingExpenses ?? [] as $item)
                    <tr>
                        <td>{{ $item->code }} - {{ $item->name }}</td>
                        <td class="text-end">{{ number_format($item->balance, 2) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="2" class="text-center text-muted">No operating expenses</td></tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="table-active">
                        <td><strong>Total Operating Expenses</strong></td>
                        <td class="text-end"><strong>{{ number_format($totalOperatingExpenses ?? 0, 2) }}</strong></td>
                    </tr>
                </tfoot>
            </table>

            <div class="mt-3 p-3 bg-light rounded">
                <div class="d-flex justify-content-between">
                    <strong>Operating Income</strong>
                    <strong>{{ number_format(($totalRevenue ?? 0) - ($totalCogs ?? 0) - ($totalOperatingExpenses ?? 0), 2) }}</strong>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <h5 class="mb-3">Non-Operating Expenses</h5>
            <table class="table table-bordered table-striped">
                <thead><tr><th>Account</th><th class="text-end">Amount</th></tr></thead>
                <tbody>
                    @forelse($nonOperatingExpenses ?? [] as $item)
                    <tr>
                        <td>{{ $item->code }} - {{ $item->name }}</td>
                        <td class="text-end">{{ number_format($item->balance, 2) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="2" class="text-center text-muted">No non-operating expenses</td></tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="table-active">
                        <td><strong>Total Non-Operating Expenses</strong></td>
                        <td class="text-end"><strong>{{ number_format($totalNonOperatingExpenses ?? 0, 2) }}</strong></td>
                    </tr>
                </tfoot>
            </table>

            <div class="mt-3 p-3 bg-label-primary rounded">
                <div class="d-flex justify-content-between">
                    <h5 class="mb-0 text-primary">Net Income</h5>
                    <h5 class="mb-0 text-primary">{{ number_format($netIncome ?? 0, 2) }}</h5>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
