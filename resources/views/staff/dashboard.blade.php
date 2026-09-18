@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-briefcase"></i></div>
            <div>
                <h4 class="m-page-title">Staff Dashboard</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">Staff Portal</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <i class="bx bx-task" style="font-size: 30px;"></i>
                    <h3 class="mt-2 mb-0">{{ $todayStats['my_assigned'] }}</h3>
                    <small>My Assigned Tasks</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <i class="bx bx-message-square-dots" style="font-size: 30px;"></i>
                    <h3 class="mt-2 mb-0">{{ $todayStats['unassigned'] }}</h3>
                    <small>Unassigned Tasks</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <i class="bx bx-check-circle" style="font-size: 30px;"></i>
                    <h3 class="mt-2 mb-0">{{ $todayStats['completed_today'] }}</h3>
                    <small>Completed Today</small>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between">
                    <h5 class="mb-0">My Tasks</h5>
                    <a href="{{ route('staff.my-tasks') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    @if($myTasks->isEmpty())
                    <div class="text-center py-3 text-muted">
                        <i class="bx bx-check-circle" style="font-size: 30px;"></i>
                        <p class="mt-2">No pending tasks assigned to you</p>
                    </div>
                    @else
                    @foreach($myTasks->take(5) as $task)
                    <div class="d-flex justify-content-between align-items-center p-2 mb-2 bg-light rounded">
                        <div>
                            <a href="{{ route('staff.task-detail', $task) }}" class="text-decoration-none">
                                <strong>{{ $task->request_number }}</strong>
                            </a>
                            <br>
                            <small class="text-muted">{{ ucfirst(str_replace('_', ' ', $task->category)) }} | Room {{ $task->room?->room_number ?? '-' }}</small>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-{{ $task->priority === 'urgent' ? 'danger' : ($task->priority === 'high' ? 'warning' : 'info') }}">
                                {{ ucfirst($task->priority) }}
                            </span>
                            <br>
                            <small class="text-muted">{{ $task->created_at->diffForHumans() }}</small>
                        </div>
                    </div>
                    @endforeach
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between">
                    <h5 class="mb-0">Unassigned Tasks</h5>
                    <a href="{{ route('staff.unassigned-tasks') }}" class="btn btn-sm btn-outline-warning">View All</a>
                </div>
                <div class="card-body">
                    @if($unassignedTasks->isEmpty())
                    <div class="text-center py-3 text-muted">
                        <i class="bx bx-party" style="font-size: 30px;"></i>
                        <p class="mt-2">No unassigned tasks - all caught up!</p>
                    </div>
                    @else
                    @foreach($unassignedTasks->take(5) as $task)
                    <div class="d-flex justify-content-between align-items-center p-2 mb-2 bg-light rounded">
                        <div>
                            <strong>{{ $task->request_number }}</strong>
                            <br>
                            <small class="text-muted">{{ ucfirst(str_replace('_', ' ', $task->category)) }} | {{ $task->subject }}</small>
                            <br>
                            <small class="text-muted">Room {{ $task->room?->room_number ?? '-' }}</small>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-{{ $task->priority === 'urgent' ? 'danger' : ($task->priority === 'high' ? 'warning' : 'info') }}">
                                {{ ucfirst($task->priority) }}
                            </span>
                            <form action="{{ route('staff.claim-task', $task) }}" method="POST" class="mt-1">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success">Claim</button>
                            </form>
                        </div>
                    </div>
                    @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <a href="{{ route('staff.housekeeping') }}" class="btn btn-outline-primary w-100 py-3">
                                <i class="bx bx-cleaning-service" style="font-size: 24px;"></i><br>
                                Housekeeping
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('staff.maintenance') }}" class="btn btn-outline-warning w-100 py-3">
                                <i class="bx bx-wrench" style="font-size: 24px;"></i><br>
                                Maintenance
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('staff.my-tasks') }}" class="btn btn-outline-success w-100 py-3">
                                <i class="bx bx-task" style="font-size: 24px;"></i><br>
                                My Tasks
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('staff.unassigned-tasks') }}" class="btn btn-outline-info w-100 py-3">
                                <i class="bx bx-message-square-dots" style="font-size: 24px;"></i><br>
                                Available Tasks
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
