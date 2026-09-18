@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-wrench"></i></div>
            <div>
                <h4 class="m-page-title">Maintenance</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="{{ route('staff.dashboard') }}">Staff Portal</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">Maintenance</li>
                </ul>
            </div>
        </div>
        <a href="{{ route('staff.dashboard') }}" class="btn btn-outline-secondary"><i class="bx bx-left-arrow-alt me-1"></i> Back</a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">Maintenance Requests</h5>
                </div>
                <div class="card-body">
                    @if($maintenanceTasks->isEmpty())
                    <div class="text-center py-4 text-muted">
                        <i class="bx bx-check-circle" style="font-size: 40px;"></i>
                        <p class="mt-2">No pending maintenance requests</p>
                    </div>
                    @else
                    @foreach($maintenanceTasks as $task)
                    <div class="d-flex justify-content-between align-items-center p-2 mb-2 bg-light rounded">
                        <div>
                            <a href="{{ route('staff.task-detail', $task) }}" class="text-decoration-none">
                                <strong>{{ $task->request_number }}</strong>
                            </a>
                            <br>
                            <small class="text-muted">{{ $task->subject }}</small>
                            <br>
                            <small class="text-muted">Room {{ $task->room?->room_number ?? '-' }}</small>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-{{ $task->priority === 'urgent' ? 'danger' : ($task->priority === 'high' ? 'warning' : 'info') }}">
                                {{ ucfirst($task->priority) }}
                            </span>
                            <br>
                            <small class="text-muted">{{ $task->created_at->diffForHumans() }}</small>
                            @if(!$task->assigned_to)
                            <form action="{{ route('staff.claim-task', $task) }}" method="POST" class="mt-1">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success">Claim</button>
                            </form>
                            @endif
                        </div>
                    </div>
                    @endforeach
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">Work Orders</h5>
                </div>
                <div class="card-body">
                    @if($workOrders->isEmpty())
                    <div class="text-center py-4 text-muted">
                        <i class="bx bx-check-circle" style="font-size: 40px;"></i>
                        <p class="mt-2">No pending work orders</p>
                    </div>
                    @else
                    @foreach($workOrders as $wo)
                    <div class="d-flex justify-content-between align-items-center p-2 mb-2 bg-light rounded">
                        <div>
                            <strong>{{ $wo->wo_number }}</strong>
                            <br>
                            <small class="text-muted">{{ $wo->description ?? $wo->asset?->name ?? '-' }}</small>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-{{ $wo->priority === 'high' ? 'danger' : ($wo->priority === 'medium' ? 'warning' : 'info') }}">
                                {{ ucfirst($wo->priority ?? 'medium') }}
                            </span>
                            <br>
                            <span class="badge bg-warning">{{ ucfirst(str_replace('_', ' ', $wo->status)) }}</span>
                        </div>
                    </div>
                    @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
