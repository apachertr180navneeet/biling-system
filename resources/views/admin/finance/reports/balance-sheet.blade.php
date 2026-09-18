@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-bar-chart-alt-2"></i></div>
            <div>
                <h4 class="m-page-title">Balance Sheet</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Finance</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Reports</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">Balance Sheet</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.finance.reports.balance-sheet') }}">
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
                        <a href="{{ route('admin.finance.reports.balance-sheet') }}" class="btn btn-outline-secondary">Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="mb-3">Assets</h5>

                    <h6 class="text-muted">Current Assets</h6>
                    <table class="table table-bordered table-striped mb-4">
                        <thead><tr><th>Account</th><th class="text-end">Balance</th></tr></thead>
                        <tbody>
                            @forelse($currentAssets ?? [] as $item)
                            <tr>
                                <td>{{ $item->code }} - {{ $item->name }}</td>
                                <td class="text-end">{{ number_format($item->balance, 2) }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="2" class="text-center text-muted">No data</td></tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr class="table-active">
                                <td><strong>Total Current Assets</strong></td>
                                <td class="text-end"><strong>{{ number_format($totalCurrentAssets ?? 0, 2) }}</strong></td>
                            </tr>
                        </tfoot>
                    </table>

                    <h6 class="text-muted">Fixed Assets</h6>
                    <table class="table table-bordered table-striped">
                        <thead><tr><th>Account</th><th class="text-end">Balance</th></tr></thead>
                        <tbody>
                            @forelse($fixedAssets ?? [] as $item)
                            <tr>
                                <td>{{ $item->code }} - {{ $item->name }}</td>
                                <td class="text-end">{{ number_format($item->balance, 2) }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="2" class="text-center text-muted">No data</td></tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr class="table-active">
                                <td><strong>Total Fixed Assets</strong></td>
                                <td class="text-end"><strong>{{ number_format($totalFixedAssets ?? 0, 2) }}</strong></td>
                            </tr>
                        </tfoot>
                    </table>

                    <div class="mt-3 p-3 bg-label-primary rounded">
                        <div class="d-flex justify-content-between">
                            <h5 class="mb-0 text-primary">Total Assets</h5>
                            <h5 class="mb-0 text-primary">{{ number_format($totalAssets ?? 0, 2) }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="mb-3">Liabilities & Equity</h5>

                    <h6 class="text-muted">Current Liabilities</h6>
                    <table class="table table-bordered table-striped mb-4">
                        <thead><tr><th>Account</th><th class="text-end">Balance</th></tr></thead>
                        <tbody>
                            @forelse($currentLiabilities ?? [] as $item)
                            <tr>
                                <td>{{ $item->code }} - {{ $item->name }}</td>
                                <td class="text-end">{{ number_format($item->balance, 2) }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="2" class="text-center text-muted">No data</td></tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr class="table-active">
                                <td><strong>Total Current Liabilities</strong></td>
                                <td class="text-end"><strong>{{ number_format($totalCurrentLiabilities ?? 0, 2) }}</strong></td>
                            </tr>
                        </tfoot>
                    </table>

                    <h6 class="text-muted">Long-Term Liabilities</h6>
                    <table class="table table-bordered table-striped mb-4">
                        <thead><tr><th>Account</th><th class="text-end">Balance</th></tr></thead>
                        <tbody>
                            @forelse($longTermLiabilities ?? [] as $item)
                            <tr>
                                <td>{{ $item->code }} - {{ $item->name }}</td>
                                <td class="text-end">{{ number_format($item->balance, 2) }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="2" class="text-center text-muted">No data</td></tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr class="table-active">
                                <td><strong>Total Long-Term Liabilities</strong></td>
                                <td class="text-end"><strong>{{ number_format($totalLongTermLiabilities ?? 0, 2) }}</strong></td>
                            </tr>
                        </tfoot>
                    </table>

                    <h6 class="text-muted">Equity</h6>
                    <table class="table table-bordered table-striped">
                        <thead><tr><th>Account</th><th class="text-end">Balance</th></tr></thead>
                        <tbody>
                            @php $hasEquity = false; @endphp
                            @foreach($equity ?? [] as $item)
                            @php $hasEquity = true; @endphp
                            <tr>
                                <td>{{ $item->code }} - {{ $item->name }}</td>
                                <td class="text-end">{{ number_format($item->balance, 2) }}</td>
                            </tr>
                            @endforeach
                            
                            @if(isset($netIncome) && $netIncome != 0)
                            @php $hasEquity = true; @endphp
                            <tr>
                                <td>Net Income (Current Period)</td>
                                <td class="text-end">{{ number_format($netIncome, 2) }}</td>
                            </tr>
                            @endif

                            @if(!$hasEquity)
                            <tr><td colspan="2" class="text-center text-muted">No data</td></tr>
                            @endif
                        </tbody>
                        <tfoot>
                            <tr class="table-active">
                                <td><strong>Total Equity</strong></td>
                                <td class="text-end"><strong>{{ number_format($totalEquity ?? 0, 2) }}</strong></td>
                            </tr>
                        </tfoot>
                    </table>

                    <div class="mt-3 p-3 bg-label-primary rounded">
                        <div class="d-flex justify-content-between">
                            <h5 class="mb-0 text-primary">Total Liabilities & Equity</h5>
                            <h5 class="mb-0 text-primary">{{ number_format($totalLiabilitiesEquity ?? 0, 2) }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
