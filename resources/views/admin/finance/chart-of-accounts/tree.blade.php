@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-dollar"></i></div>
            <div>
                <h4 class="m-page-title">Chart of Accounts - Tree View</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Finance</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">Tree View</li>
                </ul>
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.finance.chart-of-accounts.index') }}" class="btn btn-outline-secondary"><i class="bx bx-list-ul me-1"></i> List View</a>
            <a href="{{ route('admin.finance.chart-of-accounts.create') }}" class="btn btn-primary m-btn-add"><i class="bx bx-plus"></i> Add Account</a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="accordion" id="coaAccordion">
                @foreach(['asset' => 'Assets', 'liability' => 'Liabilities', 'equity' => 'Equity', 'revenue' => 'Revenue', 'expense' => 'Expenses'] as $type => $label)
                @php
                    $typeAccounts = $accounts->where('type', $type);
                @endphp
                @if($typeAccounts->count() > 0)
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#{{ $type }}">
                            <strong>{{ $label }}</strong>
                        </button>
                    </h2>
                    <div id="{{ $type }}" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" data-bs-parent="#coaAccordion">
                        <div class="accordion-body">
                            @foreach($typeAccounts as $account)
                            <div class="ms-2 mb-2">
                                <strong>{{ $account->code }}</strong> - {{ $account->name }} <span class="badge bg-label-{{ $account->status == 'active' ? 'success' : 'warning' }}">{{ $account->status }}</span>
                                @if($account->children->count() > 0)
                                <ul class="ms-4 mt-1">
                                    @foreach($account->children as $child)
                                    <li><span class="text-muted">{{ $child->code }}</span> - {{ $child->name }}</li>
                                    @endforeach
                                </ul>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
