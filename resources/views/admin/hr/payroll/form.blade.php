@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-dollar"></i></div>
            <div>
                <h4 class="m-page-title">{{ isset($payroll) ? 'Edit Payroll' : 'Create Payroll' }}</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">HR</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="{{ route('admin.hr.payroll.index') }}">Payroll</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">{{ isset($payroll) ? 'Edit' : 'Create' }}</li>
                </ul>
            </div>
        </div>
        <a href="{{ route('admin.hr.payroll.index') }}" class="btn btn-outline-secondary"><i class="bx bx-left-arrow-alt me-1"></i> Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ isset($payroll) ? route('admin.hr.payroll.update', $payroll) : route('admin.hr.payroll.store') }}" method="POST">
                @csrf
                @if(isset($payroll)) @method('PUT') @endif

                <div class="m-section-divider">Payroll Details</div>
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Period <span class="text-danger">*</span></label>
                        <input type="text" name="period" class="form-control" placeholder="e.g. 2026-07" value="{{ old('period', $payroll?->period) }}" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Start Date <span class="text-danger">*</span></label>
                        <input type="date" name="start_date" class="form-control" value="{{ old('start_date', $payroll?->start_date?->format('Y-m-d')) }}" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">End Date <span class="text-danger">*</span></label>
                        <input type="date" name="end_date" class="form-control" value="{{ old('end_date', $payroll?->end_date?->format('Y-m-d')) }}" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Payment Date <span class="text-danger">*</span></label>
                        <input type="date" name="payment_date" class="form-control" value="{{ old('payment_date', $payroll?->payment_date?->format('Y-m-d')) }}" required>
                    </div>
                </div>

                <div class="m-section-divider">Employee Selection</div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th style="width: 50px;"><input type="checkbox" id="select-all"></th>
                                <th>Employee Name</th>
                                <th>Department</th>
                                <th>Basic Salary</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($employees as $emp)
                            <tr>
                                <td><input type="checkbox" name="employee_ids[]" value="{{ $emp->id }}" class="employee-checkbox" {{ in_array($emp->id, old('employee_ids', isset($payroll) ? $payroll->employee_ids ?? [] : [])) ? 'checked' : '' }}></td>
                                <td>{{ $emp->first_name }} {{ $emp->last_name }}</td>
                                <td>{{ $emp->department?->name }}</td>
                                <td>{{ number_format($emp->basic_salary, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="m-form-actions mt-3">
                    <button type="submit" class="btn btn-primary"><i class="bx bx-check me-1"></i> {{ isset($payroll) ? 'Update' : 'Create' }} Payroll</button>
                    <a href="{{ route('admin.hr.payroll.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
$('#select-all').on('change', function() {
    $('.employee-checkbox').prop('checked', this.checked);
});
</script>
@endsection