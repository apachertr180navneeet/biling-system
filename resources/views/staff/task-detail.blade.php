@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-task"></i></div>
            <div>
                <h4 class="m-page-title">Task #{{ $task->request_number }}</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="{{ route('staff.dashboard') }}">Staff Portal</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">Task Detail</li>
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

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title"><i class="bx bx-info-circle"></i> Task Details</h5>
                    <hr>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Request #:</strong> {{ $task->request_number }}</p>
                            <p><strong>Category:</strong> <span class="badge bg-light text-dark">{{ ucfirst(str_replace('_', ' ', $task->category)) }}</span></p>
                            <p><strong>Subject:</strong> {{ $task->subject }}</p>
                            <p><strong>Room:</strong> {{ $task->room?->room_number ?? '-' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Priority:</strong>
                                <span class="badge bg-{{ $task->priority === 'urgent' ? 'danger' : ($task->priority === 'high' ? 'warning' : ($task->priority === 'medium' ? 'info' : 'secondary')) }}">
                                    {{ ucfirst($task->priority) }}
                                </span>
                            </p>
                            <p><strong>Status:</strong>
                                <span class="badge bg-{{ $task->status === 'completed' ? 'success' : ($task->status === 'in_progress' ? 'warning' : ($task->status === 'cancelled' ? 'danger' : 'primary')) }}">
                                    {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                </span>
                            </p>
                            <p><strong>Guest:</strong> {{ $task->guest?->first_name ?? '-' }} {{ $task->guest?->last_name ?? '' }}</p>
                            <p><strong>Reservation:</strong> {{ $task->reservation?->reservation_number ?? '-' }}</p>
                        </div>
                    </div>
                    @if($task->description)
                    <div class="mt-3">
                        <strong>Description:</strong>
                        <p class="bg-light p-3 rounded">{{ $task->description }}</p>
                    </div>
                    @endif
                </div>
            </div>

            @if($task->status !== 'completed' && $task->status !== 'cancelled')
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title"><i class="bx bx-edit"></i> Update Status</h5>
                    <hr>
                    <form action="{{ route('staff.update-status', $task) }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">New Status</label>
                                <select name="status" class="form-select" required>
                                    @if($task->status === 'assigned')
                                    <option value="in_progress">Start Working (In Progress)</option>
                                    @endif
                                    @if($task->status === 'in_progress')
                                    <option value="completed">Mark Complete</option>
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Notes</label>
                                <input type="text" name="notes" class="form-control" placeholder="Optional notes">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Resolution Notes (if completing)</label>
                            <textarea name="resolution_notes" class="form-control" rows="3" placeholder="Describe how the issue was resolved..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Update Task</button>
                    </form>
                </div>
            </div>
            @endif
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title"><i class="bx bx-time"></i> Timeline</h5>
                    <hr>
                    <div class="mb-3">
                        <small class="text-muted">Created</small>
                        <p class="mb-0">{{ $task->created_at->format('M d, Y h:i A') }}</p>
                    </div>
                    @if($task->assignedTo)
                    <div class="mb-3">
                        <small class="text-muted">Assigned to</small>
                        <p class="mb-0">{{ $task->assignedTo->name }}</p>
                        <small class="text-muted">{{ $task->assigned_at?->format('M d, Y h:i A') }}</small>
                    </div>
                    @endif
                    @if($task->started_at)
                    <div class="mb-3">
                        <small class="text-muted">Started</small>
                        <p class="mb-0">{{ $task->started_at->format('M d, Y h:i A') }}</p>
                    </div>
                    @endif
                    @if($task->completed_at)
                    <div class="mb-3">
                        <small class="text-muted">Completed</small>
                        <p class="mb-0 text-success fw-bold">{{ $task->completed_at->format('M d, Y h:i A') }}</p>
                    </div>
                    @endif
                </div>
            </div>

            @if($task->resolution_notes)
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><i class="bx bx-check-circle"></i> Resolution</h5>
                    <hr>
                    <p>{{ $task->resolution_notes }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
