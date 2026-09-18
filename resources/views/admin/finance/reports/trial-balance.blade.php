@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-balance"></i></div>
            <div>
                <h4 class="m-page-title">Trial Balance</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Finance</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Reports</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">Trial Balance</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.finance.reports.trial-balance') }}">
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
                        <a href="{{ route('admin.finance.reports.trial-balance') }}" class="btn btn-outline-secondary">Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Account Name</th>
                        <th class="text-end">Total Debit</th>
                        <th class="text-end">Total Credit</th>
                        <th class="text-end">Balance</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($trialBalance as $item)
                    <tr>
                        <td>{{ $item->code }}</td>
                        <td>{{ $item->name }}</td>
                        <td class="text-end">{{ number_format($item->total_debit, 2) }}</td>
                        <td class="text-end">{{ number_format($item->total_credit, 2) }}</td>
                        <td class="text-end">{{ number_format($item->balance, 2) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center text-muted">No data found</td></tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="table-active">
                        <td colspan="2"><strong>Total</strong></td>
                        <td class="text-end"><strong>{{ number_format($totalDebit ?? 0, 2) }}</strong></td>
                        <td class="text-end"><strong>{{ number_format($totalCredit ?? 0, 2) }}</strong></td>
                        <td class="text-end"><strong>{{ number_format($totalDebit - $totalCredit ?? 0, 2) }}</strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
