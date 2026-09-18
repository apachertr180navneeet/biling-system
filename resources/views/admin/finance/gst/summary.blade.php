@extends('admin.layouts.app')

@section('title', 'GST Summary')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/admin/css/custom-datatable.css') }}">
<style>
    .gst-card { border: none; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
    .tax-block { padding: 20px; border-radius: 10px; margin-bottom: 15px; }
    .tax-block.output { background: linear-gradient(135deg, #198754, #20c997); color: #fff; }
    .tax-block.input { background: linear-gradient(135deg, #0d6efd, #6610f2); color: #fff; }
    .tax-block.net { background: linear-gradient(135deg, #ffc107, #fd7e14); color: #1a1a2e; }
    .tax-block h5 { font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
    .tax-block .amount { font-size: 1.5rem; font-weight: 700; }
    .tax-detail { display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid rgba(255,255,255,0.2); }
    .tax-detail:last-child { border-bottom: none; }
    .monthly-table th { background: #f8f9fa; font-weight: 600; }
</style>
@endsection

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1">GST Summary</h4>
                <p class="text-muted mb-0">Output Tax vs Input Tax vs Net Liability</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.finance.gst.index') }}" class="btn btn-label-primary">
                    <i class="mdi mdi-file-document-outline me-1"></i> GST Returns
                </a>
                <a href="{{ route('admin.finance.gst.summary') }}" class="btn btn-label-secondary">
                    <i class="mdi mdi-refresh me-1"></i> Reset
                </a>
            </div>
        </div>

        <!-- Date Filter -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.finance.gst.summary') }}" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">From Date</label>
                        <input type="date" name="from_date" class="form-control" value="{{ $from }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">To Date</label>
                        <input type="date" name="to_date" class="form-control" value="{{ $to }}">
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="mdi mdi-filter me-1"></i> Apply Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>

        @if($company && $company->is_gst_registered)
        <!-- Company GST Info -->
        <div class="alert alert-info mb-4">
            <div class="d-flex align-items-center">
                <i class="mdi mdi-information-outline me-2 fs-4"></i>
                <div>
                    <strong>GSTIN:</strong> {{ $company->gstin ?? 'Not Set' }} |
                    <strong>PAN:</strong> {{ $company->pan ?? 'Not Set' }} |
                    <strong>State:</strong> {{ $company->state_name ?? 'Not Set' }} ({{ $company->state_code ?? '--' }})
                </div>
            </div>
        </div>
        @else
        <div class="alert alert-warning mb-4">
            <i class="mdi mdi-alert-outline me-2"></i>
            Company is not GST registered or GSTIN not configured. Please update <a href="{{ route('admin.company.edit', \App\Models\Company::first()) }}">Company Profile</a>.
        </div>
        @endif

        <!-- Tax Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="tax-block output">
                    <h5>Output Tax (On Sales)</h5>
                    <div class="amount">₹{{ number_format($totalOutputTax, 2) }}</div>
                    <div class="mt-2">
                        <div class="tax-detail">
                            <span>CGST</span>
                            <span>₹{{ number_format($totalOutputCgst, 2) }}</span>
                        </div>
                        <div class="tax-detail">
                            <span>SGST</span>
                            <span>₹{{ number_format($totalOutputSgst, 2) }}</span>
                        </div>
                        <div class="tax-detail">
                            <span>IGST</span>
                            <span>₹{{ number_format($totalOutputIgst, 2) }}</span>
                        </div>
                        <div class="tax-detail">
                            <span>Taxable Value</span>
                            <span>₹{{ number_format($outputTax['taxable_value'], 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="tax-block input">
                    <h5>Input Tax Credit</h5>
                    <div class="amount">₹{{ number_format($totalInputTax, 2) }}</div>
                    <div class="mt-2">
                        <div class="tax-detail">
                            <span>CGST</span>
                            <span>₹{{ number_format($totalInputCgst, 2) }}</span>
                        </div>
                        <div class="tax-detail">
                            <span>SGST</span>
                            <span>₹{{ number_format($totalInputSgst, 2) }}</span>
                        </div>
                        <div class="tax-detail">
                            <span>IGST</span>
                            <span>₹{{ number_format($totalInputIgst, 2) }}</span>
                        </div>
                        <div class="tax-detail">
                            <span>Taxable Value</span>
                            <span>₹{{ number_format($inputTax['taxable_value'], 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="tax-block net">
                    <h5>Net Tax Liability</h5>
                    <div class="amount">₹{{ number_format($netLiability, 2) }}</div>
                    <div class="mt-2">
                        <div class="tax-detail">
                            <span>Net CGST</span>
                            <span>₹{{ number_format($netCgst, 2) }}</span>
                        </div>
                        <div class="tax-detail">
                            <span>Net SGST</span>
                            <span>₹{{ number_format($netSgst, 2) }}</span>
                        </div>
                        <div class="tax-detail">
                            <span>Net IGST</span>
                            <span>₹{{ number_format($netIgst, 2) }}</span>
                        </div>
                        @if($inputCreditAvailable > 0)
                        <div class="tax-detail">
                            <span>Input Credit Available</span>
                            <span class="fw-bold">₹{{ number_format($inputCreditAvailable, 2) }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Breakdown -->
        <div class="card gst-card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Monthly Breakdown (Last 6 Months)</h5>
            </div>
            <div class="card-body">
                <table class="table monthly-table">
                    <thead>
                        <tr>
                            <th>Month</th>
                            <th class="text-end">Output Tax</th>
                            <th class="text-end">Input Tax</th>
                            <th class="text-end">Net Liability</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($monthlySummary as $month)
                        <tr>
                            <td>{{ $month['month'] }}</td>
                            <td class="text-end">₹{{ number_format($month['output_tax'], 2) }}</td>
                            <td class="text-end">₹{{ number_format($month['input_tax'], 2) }}</td>
                            <td class="text-end">
                                <span class="badge bg-{{ $month['net_liability'] > 0 ? 'warning' : 'success' }}">
                                    ₹{{ number_format($month['net_liability'], 2) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">No data available for the selected period</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Chart -->
        <div class="card gst-card">
            <div class="card-header">
                <h5 class="mb-0">Tax Liability Trend</h5>
            </div>
            <div class="card-body">
                <div id="monthlyTaxChart"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('assets/admin/vendor/libs/apex-charts/apexcharts.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const months = @json(array_column($monthlySummary, 'month'));
    const outputTax = @json(array_column($monthlySummary, 'output_tax'));
    const inputTax = @json(array_column($monthlySummary, 'input_tax'));
    const netLiability = @json(array_column($monthlySummary, 'net_liability'));

    const options = {
        series: [
            { name: 'Output Tax', type: 'column', data: outputTax },
            { name: 'Input Tax', type: 'column', data: inputTax },
            { name: 'Net Liability', type: 'line', data: netLiability }
        ],
        chart: { height: 350, type: 'bar', toolbar: { show: false } },
        plotOptions: { bar: { horizontal: false, columnWidth: '45%' } },
        dataLabels: { enabled: false },
        stroke: { width: [0, 0, 3], curve: 'smooth' },
        xaxis: { categories: months },
        yaxis: { title: { text: 'Amount (₹)' } },
        colors: ['#198754', '#0d6efd', '#ffc107'],
        fill: { opacity: 0.85 },
        legend: { position: 'top' },
        tooltip: {
            y: { formatter: val => '₹' + val.toLocaleString('en-IN', {minimumFractionDigits: 2}) }
        }
    };

    new ApexCharts(document.querySelector('#monthlyTaxChart'), options).render();
});
</script>
@endsection
