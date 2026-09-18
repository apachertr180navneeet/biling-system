@extends('admin.layouts.app')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-cog"></i></div>
            <div>
                <h4 class="m-page-title">Approval Rules</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="{{ route('admin.approvals.index') }}">Approvals</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">Rules</li>
                </ul>
            </div>
        </div>
        <a href="{{ route('admin.approvals.index') }}" class="btn btn-outline-secondary"><i class="bx bx-left-arrow-alt me-1"></i> Back</a>
    </div>
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header"><h5 class="mb-0">Active Rules</h5></div>
                <div class="card-body">
                    @if($rules->isEmpty())
                        <p class="text-muted">No approval rules configured. All approvals go to super admin.</p>
                    @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead><tr><th>Module</th><th>Name</th><th>Min Amount</th><th>Max Amount</th><th>Role</th><th>Approver</th><th>Step</th><th>Status</th><th></th></tr></thead>
                            <tbody>
                            @foreach($rules as $rule)
                            <tr>
                                <td><span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $rule->module)) }}</span></td>
                                <td>{{ $rule->name }}</td>
                                <td>{{ $rule->min_amount ? number_format($rule->min_amount, 2) : '-' }}</td>
                                <td>{{ $rule->max_amount ? number_format($rule->max_amount, 2) : '-' }}</td>
                                <td>{{ $rule->role?->name ?? '-' }}</td>
                                <td>{{ $rule->approver?->name ?? '-' }}</td>
                                <td>{{ $rule->step_order }}</td>
                                <td><span class="badge bg-{{ $rule->status == 'active' ? 'success' : 'secondary' }}">{{ ucfirst($rule->status) }}</span></td>
                                <td>
                                    <form action="{{ route('admin.approvals.destroy-rule', $rule) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this rule?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger"><i class="bx bx-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header"><h5 class="mb-0">Add Rule</h5></div>
                <div class="card-body">
                    <form action="{{ route('admin.approvals.store-rule') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Module <span class="text-danger">*</span></label>
                            <select name="module" class="form-select" required>
                                <option value="">Select Module</option>
                                <option value="purchase_order">Purchase Order</option>
                                <option value="expense">Expense</option>
                                <option value="refund">Refund</option>
                                <option value="stock_adjustment">Stock Adjustment</option>
                                <option value="stock_transfer">Stock Transfer</option>
                                <option value="payroll">Payroll</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Rule Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required placeholder="e.g. PO over 10K">
                        </div>
                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="form-label">Min Amount</label>
                                <input type="number" step="0.01" name="min_amount" class="form-control">
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label">Max Amount</label>
                                <input type="number" step="0.01" name="max_amount" class="form-control">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Approver Role</label>
                            <select name="role_id" class="form-select">
                                <option value="">Select Role</option>
                                @foreach(\App\Models\Role::all() as $role)
                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Specific Approver</label>
                            <select name="approver_id" class="form-select">
                                <option value="">Select User</option>
                                @foreach(\App\Models\User::where('status', 'active')->get() as $u)
                                <option value="{{ $u->id }}">{{ $u->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100"><i class="bx bx-plus"></i> Add Rule</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
