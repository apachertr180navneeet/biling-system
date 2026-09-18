@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-task"></i></div>
            <div>
                <h4 class="m-page-title">My Tasks</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="{{ route('staff.dashboard') }}">Staff Portal</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">My Tasks</li>
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

    <div class="card">
        <div class="card-body">
            @if($tasks->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="bx bx-check-circle" style="font-size: 50px;"></i>
                <h5 class="mt-3">No tasks assigned to you</h5>
                <p>Check unassigned tasks to claim new work</p>
                <a href="{{ route('staff.unassigned-tasks') }}" class="btn btn-primary">View Available Tasks</a>
            </div>
            @else
            @foreach($tasks as $task)
            <div class="card mb-3 {{ $task->priority === 'urgent' ? 'border-danger' : ($task->priority === 'high' ? 'border-warning' : '') }}">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-1">
                                <a href="{{ route('staff.task-detail', $task) }}" class="text-decoration-none">{{ $task->request_number }}</a>
                            </h6>
                            <p class="mb-1">{{ $task->subject }}</p>
                            <small class="text-muted">
                                {{ ucfirst(str_replace('_', ' ', $task->category)) }} | Room {{ $task->room?->room_number ?? '-' }}
                                @if($task->guest) | Guest: {{ $task->guest->first_name }} {{ $task->guest->last_name }} @endif
                            </small>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-{{ $task->priority === 'urgent' ? 'danger' : ($task->priority === 'high' ? 'warning' : ($task->priority === 'medium' ? 'info' : 'secondary')) }}">
                                {{ ucfirst($task->priority) }}
                            </span>
                            <span class="badge bg-{{ $task->status === 'completed' ? 'success' : ($task->status === 'in_progress' ? 'warning' : 'primary') }}">
                                {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                            </span>
                            <br>
                            <small class="text-muted">{{ $task->created_at->diffForHumans() }}</small>
                        </div>
                    </div>

                    @if($task->status !== 'completed' && $task->status !== 'cancelled')
                    <div class="mt-3 d-flex gap-2">
                        @if($task->status === 'assigned')
                        <form action="{{ route('staff.update-status', $task) }}" method="POST">
                            @csrf
                            <input type="hidden" name="status" value="in_progress">
                            <button type="submit" class="btn btn-sm btn-warning">Start Working</button>
                        </form>
                        @endif
                        @if($task->status === 'in_progress')
                        <form action="{{ route('staff.update-status', $task) }}" method="POST">
                            @csrf
                            <input type="hidden" name="status" value="completed">
                            <input type="hidden" name="resolution_notes" value="Task completed successfully">
                            <button type="submit" class="btn btn-sm btn-success">Mark Complete</button>
                        </form>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
            @endif
        </div>
    </div>
</div>
@endsection
