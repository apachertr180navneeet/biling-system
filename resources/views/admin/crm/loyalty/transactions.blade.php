@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-history"></i></div>
            <div>
                <h4 class="m-page-title">Points Transactions - {{ $member->guest->full_name ?? '' }}</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Guest CRM</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="{{ route('admin.crm.loyalty.index') }}">Loyalty</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">Transactions</li>
                </ul>
            </div>
        </div>
        <a href="{{ route('admin.crm.loyalty.index') }}" class="btn btn-outline-secondary"><i class="bx bx-left-arrow-alt me-1"></i> Back</a>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <div class="text-muted mb-1">Member Number</div>
                    <h5 class="mb-0">{{ $member->member_number }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <div class="text-muted mb-1">Total Points</div>
                    <h5 class="mb-0 text-primary">{{ number_format($member->total_points) }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <div class="text-muted mb-1">Total Stays</div>
                    <h5 class="mb-0">{{ $member->total_stays }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <div class="text-muted mb-1">Total Spent</div>
                    <h5 class="mb-0">{{ number_format($member->total_spent, 2) }}</h5>
                </div>
            </div>
        </div>
    </div>

    @if(auth()->user()->hasPermission('loyalty_members.create'))
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Add Transaction</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.crm.loyalty.transactions.store', $member) }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-2 mb-3">
                        <label class="form-label">Type <span class="text-danger">*</span></label>
                        <select name="type" class="form-select" required>
                            <option value="earned">Earned</option>
                            <option value="redeemed">Redeemed</option>
                            <option value="adjusted">Adjusted</option>
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label">Points <span class="text-danger">*</span></label>
                        <input type="number" name="points" class="form-control" min="1" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Description <span class="text-danger">*</span></label>
                        <input type="text" name="description" class="form-control" required placeholder="e.g., Stay at Hotel ABC">
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label">Reference No</label>
                        <input type="text" name="reference_number" class="form-control">
                    </div>
                    <div class="col-md-3 mb-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary"><i class="bx bx-plus me-1"></i> Add Transaction</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endif

    <div class="card">
        <div class="card-body">
            <table class="table table-bordered table-striped" style="width:100%">
                <thead><tr><th>#</th><th>Date</th><th>Type</th><th>Points</th><th>Description</th><th>Reference</th></tr></thead>
                <tbody>
                    @forelse($transactions as $txn)
                    <tr>
                        <td>{{ $txn->id }}</td>
                        <td>{{ $txn->created_at->format('d-m-Y H:i') }}</td>
                        <td>
                            @if($txn->type == 'earned')
                            <span class="badge bg-label-success">Earned</span>
                            @elseif($txn->type == 'redeemed')
                            <span class="badge bg-label-warning">Redeemed</span>
                            @elseif($txn->type == 'adjusted')
                            <span class="badge bg-label-info">Adjusted</span>
                            @else
                            <span class="badge bg-label-secondary">Expired</span>
                            @endif
                        </td>
                        <td class="{{ $txn->points >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ $txn->points >= 0 ? '+' : '' }}{{ number_format($txn->points) }}
                        </td>
                        <td>{{ $txn->description }}</td>
                        <td>{{ $txn->reference_number ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted">No transactions found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
