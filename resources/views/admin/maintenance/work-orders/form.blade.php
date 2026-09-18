@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-task"></i></div>
            <div>
                <h4 class="m-page-title">{{ isset($workOrder) ? 'Edit Work Order' : 'Create Work Order' }}</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Maintenance</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="{{ route('admin.maintenance.work-orders.index') }}">Work Orders</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">{{ isset($workOrder) ? 'Edit' : 'Create' }}</li>
                </ul>
            </div>
        </div>
        <a href="{{ route('admin.maintenance.work-orders.index') }}" class="btn btn-outline-secondary"><i class="bx bx-left-arrow-alt me-1"></i> Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ isset($workOrder) ? route('admin.maintenance.work-orders.update', $workOrder) : route('admin.maintenance.work-orders.store') }}" method="POST">
                @csrf
                @if(isset($workOrder)) @method('PUT') @endif

                <div class="m-section-divider">Work Order Details</div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" value="{{ old('title', $workOrder?->title) }}" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Type <span class="text-danger">*</span></label>
                        <select name="type" class="form-select" required>
                            @foreach(['preventive' => 'Preventive', 'breakdown' => 'Breakdown'] as $val => $lbl)
                            <option value="{{ $val }}" {{ old('type', $workOrder?->type ?? 'breakdown') == $val ? 'selected' : '' }}>{{ $lbl }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Priority <span class="text-danger">*</span></label>
                        <select name="priority" class="form-select" required>
                            @foreach(['low' => 'Low', 'medium' => 'Medium', 'high' => 'High'] as $val => $lbl)
                            <option value="{{ $val }}" {{ old('priority', $workOrder?->priority ?? 'medium') == $val ? 'selected' : '' }}>{{ $lbl }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="m-section-divider">Assignment</div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Asset</label>
                        <select name="asset_id" class="form-select">
                            <option value="">Select Asset (Optional)</option>
                            @foreach($assets as $a)
                            <option value="{{ $a->id }}" {{ old('asset_id', $workOrder?->asset_id) == $a->id ? 'selected' : '' }}>{{ $a->asset_code }} - {{ $a->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Assigned Employee</label>
                        <select name="assigned_employee_id" class="form-select">
                            <option value="">Select Employee (Optional)</option>
                            @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" {{ old('assigned_employee_id', $workOrder?->assigned_employee_id) == $emp->id ? 'selected' : '' }}>{{ $emp->first_name }} {{ $emp->last_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select" required>
                            @foreach(['pending' => 'Pending', 'in_progress' => 'In Progress', 'completed' => 'Completed', 'cancelled' => 'Cancelled'] as $val => $lbl)
                            <option value="{{ $val }}" {{ old('status', $workOrder?->status ?? 'pending') == $val ? 'selected' : '' }}>{{ $lbl }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="m-section-divider">Schedule & Cost</div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Schedule Date</label>
                        <input type="date" name="schedule_date" class="form-control" value="{{ old('schedule_date', $workOrder?->schedule_date?->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Completion Date</label>
                        <input type="date" name="completion_date" class="form-control" value="{{ old('completion_date', $workOrder?->completion_date?->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Cost (₹)</label>
                        <input type="number" step="0.01" name="cost" class="form-control" value="{{ old('cost', $workOrder?->cost ?? '0.00') }}">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description', $workOrder?->description) }}</textarea>
                    </div>
                </div>

                <div class="m-form-actions mt-3">
                    <button type="submit" class="btn btn-primary"><i class="bx bx-check me-1"></i> {{ isset($workOrder) ? 'Update' : 'Create' }} Work Order</button>
                    <a href="{{ route('admin.maintenance.work-orders.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
