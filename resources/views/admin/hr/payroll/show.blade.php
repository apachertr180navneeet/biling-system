@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-dollar"></i></div>
            <div>
                <h4 class="m-page-title">Payroll #{{ $payroll->payroll_number }}</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">HR</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="{{ route('admin.hr.payroll.index') }}">Payroll</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">View</li>
                </ul>
            </div>
        </div>
        <div class="d-flex gap-2">
            @if(auth()->user()->hasPermission('hr_payroll.edit') && $payroll->status !== 'paid' && $payroll->status !== 'cancelled')
            <form action="{{ route('admin.hr.payroll.status', $payroll) }}" method="POST" class="d-inline">
                @csrf
                @method('PATCH')
                @if($payroll->status === 'draft')
                    <button type="submit" class="btn btn-primary"><i class="bx bx-cog me-1"></i> Process Payroll</button>
                @elseif($payroll->status === 'processing')
                    <button type="submit" class="btn btn-success"><i class="bx bx-check-circle me-1"></i> Approve Payroll</button>
                @elseif($payroll->status === 'approved')
                    <button type="submit" class="btn btn-success"><i class="bx bx-credit-card me-1"></i> Mark as Paid</button>
                @endif
            </form>
            @endif
            <a href="{{ route('admin.hr.payroll.index') }}" class="btn btn-outline-secondary"><i class="bx bx-left-arrow-alt me-1"></i> Back</a>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3"><strong>Payroll Number:</strong><br>{{ $payroll->payroll_number }}</div>
                <div class="col-md-3"><strong>Period:</strong><br>{{ $payroll->period }}</div>
                <div class="col-md-3"><strong>Start Date:</strong><br>{{ $payroll->start_date->format('d-m-Y') }}</div>
                <div class="col-md-3"><strong>End Date:</strong><br>{{ $payroll->end_date->format('d-m-Y') }}</div>
            </div>
            <div class="row mt-3">
                <div class="col-md-3"><strong>Total Employees:</strong><br>{{ $payroll->total_employees }}</div>
                <div class="col-md-3"><strong>Total Earnings:</strong><br>{{ number_format($payroll->total_earnings, 2) }}</div>
                <div class="col-md-3"><strong>Total Deductions:</strong><br>{{ number_format($payroll->total_deductions, 2) }}</div>
                <div class="col-md-3"><strong>Net Pay:</strong><br><strong>{{ number_format($payroll->total_net_pay, 2) }}</strong></div>
            </div>
            <div class="row mt-3">
                <div class="col-md-3">
                    <strong>Status:</strong><br>
                    @php $statusColors = ['draft' => 'warning', 'processing' => 'info', 'approved' => 'primary', 'paid' => 'success', 'cancelled' => 'danger']; @endphp
                    <span class="badge bg-label-{{ $statusColors[$payroll->status] ?? 'secondary' }}">{{ ucfirst($payroll->status) }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h5>Payroll Items</h5>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Employee</th>
                            <th class="text-end">Basic Salary</th>
                            <th class="text-end">Earned Basic</th>
                            <th class="text-end">Earned HRA</th>
                            <th class="text-end">Allowances</th>
                            <th class="text-end">Total Earnings</th>
                            <th class="text-end">PF Deduction</th>
                            <th class="text-end">ESI Deduction</th>
                            <th class="text-end">Total Deductions</th>
                            <th class="text-end">Net Pay</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payroll->items as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->employee?->first_name }} {{ $item->employee?->last_name }}</td>
                            <td class="text-end">{{ number_format($item->basic_salary, 2) }}</td>
                            <td class="text-end">{{ number_format($item->earned_basic, 2) }}</td>
                            <td class="text-end">{{ number_format($item->earned_hra, 2) }}</td>
                            <td class="text-end">{{ number_format($item->earned_allowances, 2) }}</td>
                            <td class="text-end">{{ number_format($item->total_earnings, 2) }}</td>
                            <td class="text-end">{{ number_format($item->pf_deduction, 2) }}</td>
                            <td class="text-end">{{ number_format($item->esi_deduction, 2) }}</td>
                            <td class="text-end">{{ number_format($item->total_deductions, 2) }}</td>
                            <td class="text-end"><strong>{{ number_format($item->net_pay, 2) }}</strong></td>
                            <td>
                                @php $itemColors = ['pending' => 'warning', 'paid' => 'success']; @endphp
                                <span class="badge bg-label-{{ $itemColors[$item->status] ?? 'secondary' }}">{{ ucfirst($item->status) }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="table-active">
                            <td colspan="2"><strong>Total</strong></td>
                            <td class="text-end"><strong>{{ number_format($payroll->items->sum('basic_salary'), 2) }}</strong></td>
                            <td class="text-end"><strong>{{ number_format($payroll->items->sum('earned_basic'), 2) }}</strong></td>
                            <td class="text-end"><strong>{{ number_format($payroll->items->sum('earned_hra'), 2) }}</strong></td>
                            <td class="text-end"><strong>{{ number_format($payroll->items->sum('earned_allowances'), 2) }}</strong></td>
                            <td class="text-end"><strong>{{ number_format($payroll->items->sum('total_earnings'), 2) }}</strong></td>
                            <td class="text-end"><strong>{{ number_format($payroll->items->sum('pf_deduction'), 2) }}</strong></td>
                            <td class="text-end"><strong>{{ number_format($payroll->items->sum('esi_deduction'), 2) }}</strong></td>
                            <td class="text-end"><strong>{{ number_format($payroll->items->sum('total_deductions'), 2) }}</strong></td>
                            <td class="text-end"><strong>{{ number_format($payroll->items->sum('net_pay'), 2) }}</strong></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection